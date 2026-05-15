<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionUpdated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with(['userSubscriptions' => fn ($q) => $q->with('package')->latest('starts_at')->limit(1)])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.users.index', compact('users'));
    }

    public function ban(User $user)
    {
        abort_if($user->is(auth()->user()), 403, 'You cannot ban yourself.');
        abort_if($user->isAdmin(), 403, 'Admins cannot be banned.');

        $user->update(['banned_at' => now()]);

        return back()->with('success', "\"{$user->name}\" has been banned.");
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);

        return back()->with('success', "\"{$user->name}\" has been unbanned.");
    }

    public function extendSubscription(Request $request, User $user)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:3650'],
        ]);

        $days = (int) $request->days;

        $subscription = $user->userSubscriptions()->latest('starts_at')->first();

        if (!$subscription) {
            return back()->with('error', "\"{$user->name}\" has no subscription to extend.");
        }

        $base = ($subscription->expires_at && $subscription->expires_at->isFuture())
            ? $subscription->expires_at
            : now();

        $subscription->update([
            'status'     => 'active',
            'expires_at' => $base->addDays($days),
        ]);

        $subscription->load('package');
        Mail::to($user->email, $user->name)
            ->queue(new SubscriptionUpdated($user, $subscription, 'extended', $days));

        return back()->with('success', "\"{$user->name}\"'s subscription extended by {$days} day(s).");
    }
}
