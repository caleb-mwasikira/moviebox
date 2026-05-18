<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;

class PricingController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $user           = auth()->user();
        $currentPackage = $user?->currentSubscription?->package;
        $hasEverSubscribed = $user && !$currentPackage
            ? $user->userSubscriptions()->exists()
            : false;

        return view('pages.pricing', compact('packages', 'currentPackage', 'hasEverSubscribed'));
    }
}
