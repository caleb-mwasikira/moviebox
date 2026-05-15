<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;

class SubscriptionPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = SubscriptionPackage::where('is_active', 'true')
        ->orderBy('sort_order')
        ->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPackage $subscriptionPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        //
    }
}
