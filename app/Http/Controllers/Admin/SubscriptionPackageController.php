<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::orderBy('sort_order')->get();
        return view('pages.admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('pages.admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'interval' => 'required|in:month,year',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'max_screens' => 'required|integer|min:1',
            'max_resolution' => 'required|string',
            'has_ads' => 'boolean',
            'download_enabled' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['slug'] = Str::slug($request->name);
        // Handle boolean defaults if checkboxes are missing from request
        $validated['has_ads'] = $request->has('has_ads');
        $validated['download_enabled'] = $request->has('download_enabled');
        $validated['is_active'] = $request->has('is_active');

        SubscriptionPackage::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(SubscriptionPackage $package)
    {
        return view('pages.admin.packages.create', compact('package'));
    }

    public function update(Request $request, SubscriptionPackage $package)
    {
        // Similar validation as store...
        $package->update($request->all());
        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(SubscriptionPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }
}