<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Social login failed. Please try again.']);
        }

        $email = $socialUser->getEmail();

        // Find by provider + ID first (returning social user)
        $user = User::where('social_provider', $provider)
            ->where('social_id', $socialUser->getId())
            ->first();

        if (! $user && $email) {
            // Existing email/password account — link it
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'social_provider' => $provider,
                    'social_id'       => $socialUser->getId(),
                ]);
            }
        }

        if (! $user) {
            if (! $email) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'No email address returned by ' . ucfirst($provider) . '. Please use email/password login.']);
            }

            $user = User::create([
                'name'              => $socialUser->getName() ?: explode('@', $email)[0],
                'email'             => $email,
                'password'          => bcrypt(Str::random(32)),
                'social_provider'   => $provider,
                'social_id'         => $socialUser->getId(),
                'email_verified_at' => now(),
            ]);

            $profile = $user->profiles()->create([
                'name'            => $user->name,
                'avatar_color'    => Profile::COLORS[0],
                'invited_user_id' => $user->id,
                'status'          => 'active',
            ]);
            session(['active_profile_id' => $profile->id]);
        }

        if ($user->isBanned()) {
            return redirect()->route('login')
                ->with('banned', 'Your account has been suspended. Please contact support.');
        }

        Auth::login($user, true);

        if (! session('active_profile_id')) {
            $profile = $user->profiles()->where('invited_user_id', $user->id)->first()
                ?? $user->profiles()->first();
            if ($profile) {
                session(['active_profile_id' => $profile->id]);
            }
        }

        return redirect()->intended(route('profiles.select'));
    }
}
