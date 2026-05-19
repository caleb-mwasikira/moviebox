<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionUpdated;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\SubscriptionPackage;
use App\Models\WatchProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        $user      = $request->user();
        $profileId = session('active_profile_id');

        // ── Profiles management data ──────────────────────────────────
        $profiles = $user->profiles()->visible()->orderBy('created_at')->get();

        $sharedProfiles = Profile::where('invited_user_id', $user->id)
            ->where('user_id', '!=', $user->id)
            ->active()
            ->with('user')
            ->get();

        $max    = $user->maxProfiles();
        $colors = Profile::COLORS;

        // ── Watch history ─────────────────────────────────────────────
        $query = $profileId
            ? WatchProgress::where('profile_id', $profileId)
            : WatchProgress::where('user_id', $user->id)->whereNull('profile_id');

        $recentlyWatched = $query
            ->where('position', '>=', 30)
            ->orderByDesc('position')
            ->limit(80)
            ->get();

        $recentlyWatched->loadMorph('watchable', [
            Movie::class   => ['posters'],
            Episode::class => ['season.series.posters'],
        ]);

        $recentlyWatched = $recentlyWatched
            ->filter(fn($item) => $item->watchable !== null)
            ->unique(fn($item) => $item->watchable_type === 'movie'
                ? 'movie_' . $item->watchable_id
                : 'series_' . $item->watchable->season->series->id)
            ->sortByDesc('watched_at')
            ->take(8)
            ->values();

        return view('pages.account.profile', compact(
            'profiles', 'sharedProfiles', 'max', 'colors', 'recentlyWatched'
        ));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_path' => $path]);

        return back()->with('status', 'avatar-updated');
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return back()->with('status', 'avatar-updated');
    }

    public function personalInfo()
    {
        return view('pages.account.personal-info');
    }

    public function updatePersonalInfo(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $request->password,
        ]);

        return back()->with('status', 'password-updated');
    }

    public function billingDetails()
    {
        $packages = SubscriptionPackage::where('is_active', 'true')
            ->orderBy('sort_order')
            ->get();

        return view('pages.account.billing-details', ['packages' => $packages]);
    }

    public function updateSubscriptionPlan(SubscriptionPackage $newPackage)
    {
        $user = auth()->user();

        // Prevent re-subscribing to the same active plan
        if ($user->currentSubscription && $user->currentSubscription->subscription_package_id === $newPackage->id) {
            return redirect()->back()->with('error', "You are already subscribed to the {$newPackage->name} plan.");
        }

        try {
            DB::beginTransaction();

            // Terminate existing active subscriptions
            $user->userSubscriptions()
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                    'expires_at' => now(),
                ]);

            // Calculate new expiry date
            $expiresAt = match (strtolower($newPackage->interval)) {
                'week'  => now()->addWeek(),
                'month' => now()->addMonth(),
                'year'  => now()->addYear(),
                default => throw new \Exception("Invalid interval: {$newPackage->interval}"),
            };

            // Create the new subscription record
            $newSub = $user->userSubscriptions()->create([
                'subscription_package_id' => $newPackage->id,
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            DB::commit();

            $newSub->load('package');
            Mail::to($user->email, $user->name)
                ->queue(new SubscriptionUpdated($user, $newSub, 'renewed'));

            return redirect()->back()->with('success', "Plan updated to {$newPackage->name} successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Subscription update failed for User {$user->id}: " . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while updating your plan. Please try again.');
        }
    }
}
