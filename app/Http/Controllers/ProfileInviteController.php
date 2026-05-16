<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileInviteController extends Controller
{
    public function show(Request $request, string $token)
    {
        $profile = $this->findPendingProfile($token);
        abort_if(! $profile, 404);

        $authenticated      = (bool) $request->user();
        $hasExistingAccount = ! $authenticated && User::where('email', $profile->invite_email)->exists();
        $colors             = Profile::COLORS;

        if (! $authenticated) {
            session(['url.intended' => route('profiles.invite', $token)]);
        }

        return view('pages.profiles.invite-accept',
            compact('profile', 'token', 'colors', 'authenticated', 'hasExistingAccount'));
    }

    public function accept(Request $request, string $token)
    {
        $profile = $this->findPendingProfile($token);
        abort_if(! $profile, 404);

        if ($request->user()) {
            return $this->acceptAsAuthenticatedUser($request, $profile, $token);
        }

        $existingUser = User::where('email', $profile->invite_email)->first();

        return $existingUser
            ? $this->acceptAsExistingUser($request, $profile, $token, $existingUser)
            : $this->acceptAsNewUser($request, $profile);
    }

    // ── Private helpers ────────────────────────────────────────────

    private function findPendingProfile(string $token): ?Profile
    {
        return Profile::where('status', Profile::STATUS_PENDING)
            ->whereNotNull('invite_token')
            ->get()
            ->first(fn($p) => Hash::check($token, $p->invite_token));
    }

    /** Already logged-in user accepts — just link the profile. */
    private function acceptAsAuthenticatedUser(Request $request, Profile $profile, string $token)
    {
        if ($profile->user_id === $request->user()->id) {
            return redirect()->route('profiles.invite', $token)
                ->with('error', 'You cannot accept an invitation sent from your own account.');
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:50'],
            'avatar_color' => ['required', 'string', 'max:20'],
            'pin'          => ['nullable', 'digits:4'],
        ]);

        $this->linkProfile($profile, $request->user()->id, $validated);
        session(['active_profile_id' => $profile->id]);

        return redirect()->route('home')
            ->with('status', 'Profile set up! You can now start watching.');
    }

    /** Invite email matches an existing account — authenticate then link. */
    private function acceptAsExistingUser(Request $request, Profile $profile, string $token, User $user)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:50'],
            'avatar_color' => ['required', 'string', 'max:20'],
            'pin'          => ['nullable', 'digits:4'],
            'password'     => ['required', 'string'],
        ]);

        if (! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        if ($profile->user_id === $user->id) {
            return redirect()->route('profiles.invite', $token)
                ->with('error', 'You cannot accept an invitation sent from your own account.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->linkProfile($profile, $user->id, $validated);
        session(['active_profile_id' => $profile->id]);

        return redirect()->route('home')
            ->with('status', 'Welcome back! Your profile has been linked.');
    }

    /** No account yet — create one, verify email, log in, and link the profile. */
    private function acceptAsNewUser(Request $request, Profile $profile)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:50'],
            'avatar_color'          => ['required', 'string', 'max:20'],
            'pin'                   => ['nullable', 'digits:4'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create the account; mark email verified immediately (invite proves ownership)
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $profile->invite_email,
            'password' => Hash::make($validated['password']),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        // Give Alice her own owner profile too
        $user->profiles()->create([
            'name'            => $validated['name'],
            'avatar_color'    => Profile::COLORS[0],
            'invited_user_id' => $user->id,
            'status'          => Profile::STATUS_ACTIVE,
        ]);

        $this->linkProfile($profile, $user->id, $validated);

        Auth::login($user);
        $request->session()->regenerate();

        // Start Alice on John's shared profile so she can watch immediately
        session(['active_profile_id' => $profile->id]);

        return redirect()->route('home')
            ->with('status', 'Welcome to ' . config('app.name') . '! Your account is ready.');
    }

    private function linkProfile(Profile $profile, int $userId, array $data): void
    {
        $profile->update([
            'name'            => $data['name'],
            'avatar_color'    => $data['avatar_color'],
            'invited_user_id' => $userId,
            'invite_token'    => null,
            'status'          => Profile::STATUS_ACTIVE,
            'pin'             => ! empty($data['pin']) ? Hash::make($data['pin']) : null,
        ]);
    }
}
