<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;

class PaymentController extends Controller
{
    public function show(string $uuid)
    {
        $package = SubscriptionPackage::where('uuid', $uuid)
            ->where('is_active', true)
            ->firstOrFail();

        $currentPackage = auth()->user()->currentSubscription?->package;

        return view('pages.payment', compact('package', 'currentPackage'));
    }
}
