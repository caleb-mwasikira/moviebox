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

        $currentPackage = auth()->check()
            ? auth()->user()->currentSubscription?->package
            : null;

        return view('pages.pricing', compact('packages', 'currentPackage'));
    }
}
