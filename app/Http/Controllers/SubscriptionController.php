<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;

class SubscriptionController extends Controller
{
    public function expired()
    {
        if (auth()->user()->currentSubscription) {
            return redirect()->route('home');
        }

        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.subscription.expired', compact('packages'));
    }
}
