<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\EmailVerificationOtp;
use App\Notifications\WelcomeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        return view('auth.verify-email', [
            'email' => $request->user()->email,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user   = $request->user();
        $record = DB::table('email_verification_tokens')
            ->where('email', $user->email)
            ->first();

        if (! $record || ! Hash::check($request->otp, $record->token)) {
            return back()->withErrors(['otp' => 'Invalid code. Please try again.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['otp' => 'This code has expired. Request a new one.']);
        }

        $user->markEmailAsVerified();
        $user->notify(new WelcomeNotification());

        DB::table('email_verification_tokens')->where('email', $user->email)->delete();

        return redirect()->route('profiles.select')->with('status', 'email-verified');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }

        $this->sendOtp($user);

        return back()->with('status', 'verification-sent');
    }

    public static function sendOtp(\App\Models\User $user): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('email_verification_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        $user->notify(new EmailVerificationOtp($otp));
    }
}
