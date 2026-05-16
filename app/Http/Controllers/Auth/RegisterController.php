<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Controller;
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

        EmailVerificationController::sendOtp($user);

        return redirect()->route('verification.notice');
    }
}
