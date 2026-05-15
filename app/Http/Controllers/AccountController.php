<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
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
            $user->userSubscriptions()->create([
                'subscription_package_id' => $newPackage->id,
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Plan updated to {$newPackage->name} successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Subscription update failed for User {$user->id}: " . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong while updating your plan. Please try again.');
        }
    }
}
