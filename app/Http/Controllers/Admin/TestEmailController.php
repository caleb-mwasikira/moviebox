<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentConfirmed;
use App\Mail\SubscriptionUpdated;
use App\Models\MpesaPayment;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Notifications\ProfileInvitationNotification;
use App\Notifications\SubscriptionExpiryReminder;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class TestEmailController extends Controller
{
    private array $templates = [
        'welcome'               => 'Welcome / Account Created',
        'payment_confirmed'     => 'Payment Confirmed',
        'subscription_renewed'  => 'Subscription Renewed',
        'subscription_extended' => 'Subscription Extended (by Admin)',
        'subscription_expiring' => 'Subscription Expiring Soon',
        'profile_invite'        => 'Profile Invitation',
    ];

    public function show()
    {
        return view('pages.admin.test-email', ['templates' => $this->templates]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'template' => ['required', 'string', 'in:' . implode(',', array_keys($this->templates))],
        ]);

        $recipient = $request->input('email');
        $template  = $request->input('template');

        /** @var User $user */
        $user = Auth::user();

        $fakePackage = new SubscriptionPackage([
            'name'     => 'Premium',
            'currency' => 'KES',
            'price'    => 599,
            'interval' => 'month',
        ]);

        try {
            if ($template === 'welcome') {
                Notification::route('mail', $recipient)->notify(new WelcomeNotification($user));
                return $this->success($template, $recipient);
            }

            if ($template === 'profile_invite') {
                Notification::route('mail', $recipient)->notify(
                    new ProfileInvitationNotification($user->name, route('profiles.invite', 'PREVIEW_TOKEN'))
                );
                return $this->success($template, $recipient);
            }

            if ($template === 'subscription_expiring') {
                $sub = $this->makeFakeSub($fakePackage, 3);
                Notification::route('mail', $recipient)->notify(new SubscriptionExpiryReminder($sub, $user));
                return $this->success($template, $recipient);
            }

            $mailable = match ($template) {
                'payment_confirmed'     => $this->makePaymentConfirmed($user, $fakePackage),
                'subscription_renewed'  => $this->makeSubscriptionUpdated($user, $fakePackage, 'renewed'),
                'subscription_extended' => $this->makeSubscriptionUpdated($user, $fakePackage, 'extended', 30),
            };

            Mail::to($recipient)->send($mailable);

            return $this->success($template, $recipient);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send: ' . $e->getMessage());
        }
    }

    private function success(string $template, string $recipient)
    {
        return back()->with('success', 'Test email "' . $this->templates[$template] . '" sent to ' . $recipient . '.');
    }

    private function makePaymentConfirmed(User $user, SubscriptionPackage $package): PaymentConfirmed
    {
        $payment = new MpesaPayment([
            'phone'                => '254712345678',
            'amount'               => 599,
            'mpesa_receipt_number' => 'QKJ3XYZ89A',
            'status'               => 'paid',
        ]);
        $payment->created_at = Carbon::now();
        $payment->setRelation('package', $package);

        return new PaymentConfirmed($user, $payment);
    }

    private function makeSubscriptionUpdated(User $user, SubscriptionPackage $package, string $action, ?int $days = null): SubscriptionUpdated
    {
        return new SubscriptionUpdated($user, $this->makeFakeSub($package), $action, $days);
    }

    private function makeFakeSub(SubscriptionPackage $package, int $daysUntilExpiry = 30): UserSubscriptions
    {
        $sub = new UserSubscriptions(['status' => 'active', 'starts_at' => Carbon::now()]);
        $sub->expires_at = Carbon::now()->addDays($daysUntilExpiry);
        $sub->setRelation('package', $package);

        return $sub;
    }
}
