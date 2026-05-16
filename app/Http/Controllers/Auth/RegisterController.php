<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use PasswordValidationRules, ProfileValidationRules;

    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        Auth::login($user);

        // Create a default profile for the new account
        $profile = $user->profiles()->create([
            'name'            => $user->name,
            'avatar_color'    => Profile::COLORS[0],
            'invited_user_id' => $user->id, // marks this as the owner's own profile
            'status'          => 'active',
        ]);
        session(['active_profile_id' => $profile->id]);

        EmailVerificationController::sendOtp($user);

        return redirect()->route('verification.notice');
    }
}
