<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Notifications\ProfileInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountProfileController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $colors = Profile::COLORS;

        // All profiles this user owns (own + invited members)
        $profiles = $user->profiles()->visible()->orderBy('created_at')->get();

        // Profiles this user has been invited into (other people's accounts)
        $sharedProfiles = Profile::where('invited_user_id', $user->id)
            ->where('user_id', '!=', $user->id)
            ->active()
            ->with('user')
            ->get();

        $max = $user->maxProfiles();

        return view('pages.account.profiles.index', compact('profiles', 'sharedProfiles', 'max', 'colors'));
    }

    public function createInvite(Request $request)
    {
        $user       = $request->user();
        $maxMembers = max(0, $user->maxProfiles() - 1);
        $memberCount = $user->profiles()->visible()
            ->where(fn($q) => $q->where('invited_user_id', '!=', $user->id)->orWhereNull('invited_user_id'))
            ->count();

        return view('pages.account.profiles.invite', compact('memberCount', 'maxMembers'));
    }

    /** Invite a new member by email */
    public function store(Request $request)
    {
        $user = $request->user();

        // -1 because owner profile doesn't count toward the limit
        $memberCount = $user->profiles()->visible()->where('user_id', $user->id)
            ->where(fn($q) => $q->where('invited_user_id', '!=', $user->id)->orWhereNull('invited_user_id'))
            ->count();

        $maxMembers = max(0, $user->maxProfiles() - 1);

        if ($memberCount >= $maxMembers) {
            return back()->with('error', 'You have reached the maximum number of profiles for your plan.');
        }

        $validated = $request->validate([
            'invite_email' => ['required', 'email', 'max:255'],
        ]);

        $email = $validated['invite_email'];

        // Prevent inviting yourself
        if ($email === $user->email) {
            return back()->with('error', 'You cannot invite yourself.');
        }

        // Prevent duplicate pending invite
        $existing = $user->profiles()->where('invite_email', $email)->first();
        if ($existing) {
            return back()->with('error', 'An invitation has already been sent to ' . $email . '.');
        }

        // Generate a raw token, store hashed
        $rawToken = Str::random(64);

        $profile = $user->profiles()->create([
            'name'         => $email,
            'avatar_color' => Profile::COLORS[array_rand(Profile::COLORS)],
            'invite_email' => $email,
            'invite_token' => Hash::make($rawToken),
            'status'       => Profile::STATUS_PENDING,
        ]);

        // Send invite email
        $acceptUrl = route('profiles.invite', $rawToken);

        (new AnonymousNotifiable())
            ->route('mail', $email)
            ->notify(new ProfileInvitationNotification($user->name, $acceptUrl));

        return back()->with('success', 'Invitation sent to ' . $email . '.');
    }

    /** Resend invite email */
    public function resendInvite(Request $request, Profile $profile)
    {
        abort_unless($profile->user_id === $request->user()->id, 403);
        abort_unless($profile->isPending(), 422);

        $rawToken  = Str::random(64);
        $acceptUrl = route('profiles.invite', $rawToken);

        $profile->update(['invite_token' => Hash::make($rawToken)]);

        (new AnonymousNotifiable())
            ->route('mail', $profile->invite_email)
            ->notify(new ProfileInvitationNotification($request->user()->name, $acceptUrl));

        return back()->with('success', 'Invitation resent to ' . $profile->invite_email . '.');
    }

    /** Enable a disabled profile */
    public function enable(Request $request, Profile $profile)
    {
        abort_unless($profile->user_id === $request->user()->id, 403);
        abort_if($profile->isOwnerProfile(), 403);

        $profile->update(['status' => Profile::STATUS_ACTIVE]);

        return back()->with('success', $profile->displayName() . ' has been enabled.');
    }

    /** Disable an active profile */
    public function disable(Request $request, Profile $profile)
    {
        abort_unless($profile->user_id === $request->user()->id, 403);
        abort_if($profile->isOwnerProfile(), 403);

        $profile->update(['status' => Profile::STATUS_DISABLED]);

        // Clear session if this profile is currently active
        if ((int) session('active_profile_id') === $profile->id) {
            session()->forget('active_profile_id');
        }

        return back()->with('success', $profile->displayName() . ' has been disabled.');
    }

    /** Delete a profile */
    public function destroy(Request $request, Profile $profile)
    {
        abort_unless($profile->user_id === $request->user()->id, 403);
        abort_if($profile->isOwnerProfile(), 403);

        if ((int) session('active_profile_id') === $profile->id) {
            session()->forget('active_profile_id');
        }

        $label = $profile->displayName();
        $profile->delete();

        return back()->with('success', $label . ' has been removed.');
    }

    /** Switch the active profile (for main user switching between their own profiles) */
    public function activate(Request $request, Profile $profile)
    {
        $user = $request->user();
        $ownedByUser   = $profile->user_id === $user->id;
        $invitedToUser = $profile->invited_user_id === $user->id;

        abort_unless($ownedByUser || $invitedToUser, 403);
        abort_unless($profile->isActive(), 422);

        session(['active_profile_id' => $profile->id]);

        return back()->with('success', 'Switched to ' . $profile->name . '.');
    }

    /** Let an invited user update their own profile within someone else's account */
    public function updateOwn(Request $request, Profile $profile)
    {
        abort_unless($profile->invited_user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:50'],
            'avatar_color' => ['required', 'string', 'max:20'],
            'pin'          => ['nullable', 'digits:4'],
            'remove_pin'   => ['nullable', 'boolean'],
        ]);

        $data = [
            'name'         => $validated['name'],
            'avatar_color' => $validated['avatar_color'],
        ];

        if ($request->boolean('remove_pin')) {
            $data['pin'] = null;
        } elseif (! empty($validated['pin'])) {
            $data['pin'] = Hash::make($validated['pin']);
        }

        $profile->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
