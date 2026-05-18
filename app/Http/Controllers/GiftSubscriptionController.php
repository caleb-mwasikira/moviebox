<?php

namespace App\Http\Controllers;

use App\Mail\GiftReceived;
use App\Models\GiftSubscription;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GiftSubscriptionController extends Controller
{
    public function show(string $uuid)
    {
        $package = SubscriptionPackage::where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_giftable', true)
            ->firstOrFail();

        return view('pages.gift.show', compact('package'));
    }

    /**
     * Create a gift subscription. For free packages, activates immediately and emails the recipient.
     * For paid packages, returns JSON so the frontend can initiate M-Pesa and poll.
     */
    public function store(Request $request, string $uuid)
    {
        $package = SubscriptionPackage::where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_giftable', true)
            ->firstOrFail();

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
            'message'         => ['nullable', 'string', 'max:500'],
        ]);

        /** @var \App\Models\User $sender */
        $sender = Auth::user();

        if ($sender->email === $validated['recipient_email']) {
            return back()->withErrors(['recipient_email' => 'You cannot send a gift to yourself.'])->withInput();
        }

        $gift = GiftSubscription::create([
            'sender_id'               => $sender->id,
            'subscription_package_id' => $package->id,
            'recipient_email'         => $validated['recipient_email'],
            'token'                   => Str::random(48),
            'message'                 => $validated['message'] ?? null,
            'status'                  => 'pending',
        ]);

        if ($package->price == 0) {
            $gift->update([
                'status'     => 'sent',
                'expires_at' => now()->addDays(30),
            ]);
            Mail::to($gift->recipient_email)->queue(new GiftReceived($gift));

            return redirect()->route('gift.sent', $gift->token)
                ->with('success', 'Gift sent! ' . $gift->recipient_email . ' will receive an email with instructions to claim it.');
        }

        // For paid packages, return gift id + token so the JS layer can link the payment and redirect on success.
        return response()->json(['gift_id' => $gift->id, 'token' => $gift->token]);
    }

    public function sent(string $token)
    {
        $gift = GiftSubscription::where('token', $token)
            ->where('sender_id', Auth::id())
            ->with('package', 'sender')
            ->firstOrFail();

        return view('pages.gift.sent', compact('gift'));
    }

    public function accept(string $token)
    {
        $gift = GiftSubscription::where('token', $token)
            ->with('package', 'sender')
            ->firstOrFail();

        return view('pages.gift.accept', compact('gift'));
    }

    public function claim(string $token)
    {
        $gift = GiftSubscription::where('token', $token)->firstOrFail();

        if ($gift->isClaimed()) {
            return back()->with('error', 'This gift has already been claimed.');
        }

        if (! $gift->isClaimable()) {
            return back()->with('error', 'This gift link has expired or is no longer valid.');
        }

        /** @var \App\Models\User $recipient */
        $recipient = Auth::user();
        $package   = $gift->package;
        $interval  = strtolower($package->interval);

        DB::transaction(function () use ($recipient, $package, $interval, $gift) {
            /** @var \App\Models\UserSubscriptions|null $existing */
            $existing = $recipient->userSubscriptions()
                ->where('status', 'active')
                ->latest('id')
                ->first();

            if ($existing) {
                $base      = $existing->expires_at?->isFuture() ? $existing->expires_at->copy() : now();
                $expiresAt = match ($interval) {
                    'week'  => $base->addWeek(),
                    'month' => $base->addMonth(),
                    'year'  => $base->addYear(),
                    default => $base->addMonth(),
                };
                $existing->update([
                    'subscription_package_id' => $package->id,
                    'expires_at'              => $expiresAt,
                ]);
            } else {
                $expiresAt = match ($interval) {
                    'week'  => now()->addWeek(),
                    'month' => now()->addMonth(),
                    'year'  => now()->addYear(),
                    default => now()->addMonth(),
                };
                $recipient->userSubscriptions()->create([
                    'subscription_package_id' => $package->id,
                    'starts_at'               => now(),
                    'expires_at'              => $expiresAt,
                    'status'                  => 'active',
                ]);
            }

            $gift->update([
                'status'        => 'claimed',
                'claimed_by_id' => $recipient->id,
                'claimed_at'    => now(),
            ]);
        });

        return redirect()->route('account.billing')
            ->with('success', 'Gift claimed! Your ' . $package->name . ' subscription is now active. Enjoy streaming!');
    }

    public function updateEmail(Request $request, GiftSubscription $gift)
    {
        if ($gift->sender_id !== Auth::id()) {
            abort(403);
        }

        if ($gift->isClaimed()) {
            return back()->with('error', 'This gift has already been claimed and cannot be modified.');
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
        ]);

        if (Auth::user()->email === $validated['recipient_email']) {
            return back()->withErrors(['recipient_email' => 'You cannot send a gift to yourself.']);
        }

        $gift->update(['recipient_email' => $validated['recipient_email']]);

        if ($gift->status === 'sent') {
            Mail::to($gift->recipient_email)->queue(new GiftReceived($gift));
        }

        return back()->with('success', 'Recipient email updated. A new claim email has been sent.');
    }
}
