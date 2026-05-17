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
        $archived = SubscriptionPackage::onlyTrashed()->orderBy('name')->get();
        return view('pages.admin.packages.index', compact('packages', 'archived'));
    }

    public function create()
    {
        return view('pages.admin.packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'interval'       => 'required|in:week,month,year',
            'currency'       => 'nullable|string|max:3',
            'description'    => 'nullable|string',
            'max_screens'    => 'required|integer|min:1',
            'max_resolution' => 'required|string',
            'has_ads'        => 'boolean',
            'download_enabled' => 'boolean',
            'is_active'      => 'boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        $data['slug']             = Str::slug($request->input('name'));
        $data['has_ads']          = $request->has('has_ads');
        $data['download_enabled'] = $request->has('download_enabled');
        $data['is_active']        = $request->has('is_active');
        $data['currency']         = ($data['currency'] ?? null) ?: 'KES';

        SubscriptionPackage::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(SubscriptionPackage $package)
    {
        return view('pages.admin.packages.create', compact('package'));
    }

    public function update(Request $request, SubscriptionPackage $package)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'interval'       => 'required|in:week,month,year',
            'currency'       => 'nullable|string|max:3',
            'description'    => 'nullable|string',
            'max_screens'    => 'required|integer|min:1',
            'max_resolution' => 'required|string',
            'has_ads'        => 'boolean',
            'download_enabled' => 'boolean',
            'is_active'      => 'boolean',
            'sort_order'     => 'nullable|integer',
        ]);

        $data['slug']             = Str::slug($request->input('name'));
        $data['has_ads']          = $request->has('has_ads');
        $data['download_enabled'] = $request->has('download_enabled');
        $data['is_active']        = $request->has('is_active');
        $data['currency']         = ($data['currency'] ?? null) ?: 'KES';

        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(SubscriptionPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package archived and hidden from customers.');
    }

    public function restore(string $uuid)
    {
        $package = SubscriptionPackage::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $package->restore();
        return redirect()->route('admin.packages.index')->with('success', '"' . $package->name . '" has been restored.');
    }
}