<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VerifyOtpController extends Controller
{
    public function show()
    {
        return view('auth.verify-otp', [
            'email' => session('otp_email', ''),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->otp, $record->token)) {
            return back()
                ->withErrors(['otp' => 'Invalid code. Please try again.'])
                ->withInput();
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()
                ->withErrors(['otp' => 'This code has expired. Please request a new one.'])
                ->withInput();
        }

        session()->put('password_reset_email', $request->email);
        session()->put('password_reset_verified', true);

        return redirect()->route('password.reset');
    }
}
