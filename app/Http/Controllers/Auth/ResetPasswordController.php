<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    use PasswordValidationRules;

    public function show()
    {
        if (! session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function store(Request $request)
    {
        if (! session('password_reset_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate(['password' => $this->passwordRules()]);

        $email = session('password_reset_email');
        $user  = User::where('email', $email)->firstOrFail();

        $user->update(['password' => $request->password]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        Auth::login($user);

        return redirect('/')->with('status', 'Password reset successfully.');
    }
}
