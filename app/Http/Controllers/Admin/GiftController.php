<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\GiftReceived;
use App\Models\GiftSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GiftController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $query = GiftSubscription::with('sender', 'package', 'claimedBy')
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhereHas('sender', fn ($q) => $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('claimedBy', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'expired') {
                $query->where('status', 'sent')->where('expires_at', '<', now());
            } elseif ($status === 'pending') {
                $query->where('status', 'sent')
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
            } else {
                $query->where('status', $status);
            }
        }

        $gifts = $query->paginate(25)->withQueryString();

        $summary = [
            'total'   => GiftSubscription::count(),
            'pending' => GiftSubscription::where('status', 'sent')
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                ->count(),
            'claimed' => GiftSubscription::where('status', 'claimed')->count(),
            'expired' => GiftSubscription::where('status', 'sent')->where('expires_at', '<', now())->count(),
            'payment_pending' => GiftSubscription::where('status', 'pending')->count(),
        ];

        return view('pages.admin.gifts.index', compact('gifts', 'summary'));
    }

    public function resend(GiftSubscription $gift)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        if (! $gift->isClaimable()) {
            return back()->with('error', 'This gift cannot be resent — it is either already claimed or has expired.');
        }

        Mail::to($gift->recipient_email)->queue(new GiftReceived($gift->load('sender', 'package')));

        return back()->with('success', "Gift email resent to {$gift->recipient_email}.");
    }
}
