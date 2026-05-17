<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function show(string $uuid)
    {
        $package = SubscriptionPackage::where('uuid', $uuid)
            ->where('is_active', true)
            ->firstOrFail();

        /** @var User $user */
        $user           = Auth::user();
        $currentPackage = $user->currentSubscription?->package;

        $alreadyUsedFreeTrial = false;
        if ($package->price == 0) {
            $alreadyUsedFreeTrial = $this->hasUsedFreeTrial($user);
        }

        return view('pages.payment', compact('package', 'currentPackage', 'alreadyUsedFreeTrial'));
    }

    public function activateFree(string $uuid)
    {
        $package = SubscriptionPackage::where('uuid', $uuid)
            ->where('is_active', true)
            ->where('price', 0)
            ->firstOrFail();

        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($this->hasUsedFreeTrial($user)) {
            return back()->with('error', 'You have already used your free trial. Please choose a paid plan.');
        }

        $interval = strtolower($package->interval);

        DB::transaction(function () use ($user, $package, $interval) {
            $existing = $user->userSubscriptions()
                ->where('status', 'active')
                ->latest('id')
                ->first();

            if ($existing) {
                $base      = $existing->expires_at->isFuture() ? $existing->expires_at->copy() : now();
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
                $user->userSubscriptions()->create([
                    'subscription_package_id' => $package->id,
                    'starts_at'               => now(),
                    'expires_at'              => $expiresAt,
                    'status'                  => 'active',
                ]);
            }
        });

        return redirect()->route('account.billing')->with('success', 'Your free subscription is now active.');
    }

    private function hasUsedFreeTrial(User $user): bool
    {
        $freePackageIds = SubscriptionPackage::where('price', 0)->pluck('id');

        return $user->userSubscriptions()
            ->whereIn('subscription_package_id', $freePackageIds)
            ->exists();
    }
}
