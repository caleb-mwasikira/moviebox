@extends('layouts.admin')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-black uppercase tracking-tighter text-gray-900 dark:text-white">
            {{ isset($package) ? 'Edit' : 'Create' }} <span class="text-accent">Package</span>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Configure the pricing and technical constraints for your streaming tiers.</p>
    </div>

    <form action="{{ isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store') }}" method="POST">
        @csrf
        @if(isset($package)) @method('PUT') @endif

        <div class="space-y-8">

            {{-- Section 1: Core Identity --}}
            <div class="bg-white shadow-3xl p-8 dark:bg-gray-700 border-l-4 border-accent">
                <h3 class="text-xs font-bold uppercase tracking-widest text-accent mb-6">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Package Name</label>
                        <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}"
                            class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none transition-all">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Display Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}"
                            class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none">
                    </div>
                </div>
            </div>

            {{-- Section 2: Billing & Currency --}}
            <div class="bg-white shadow-3xl p-8 dark:bg-gray-700">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">Pricing & Currency</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Currency (ISO)</label>
                        <input type="text" name="currency" value="{{ old('currency', $package->currency ?? 'KSH') }}"
                            class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none" placeholder="USD">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Price</label>
                        <input type="number" name="price" value="{{ old('price', $package->price ?? '') }}"
                            class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none" placeholder="1999">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Billing Interval</label>
                        <select name="interval" class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none">
                            <option value="week" {{ (old('interval', $package->interval ?? '') == 'week') ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ (old('interval', $package->interval ?? '') == 'month') ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ (old('interval', $package->interval ?? '') == 'year') ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 3: Technical Features --}}
            <div class="bg-white shadow-3xl p-8 dark:bg-gray-700">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">Feature Constraints</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Resolution</label>
                        <select name="max_resolution" class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none">
                            <option value="720p" {{ ($package->max_resolution ?? '') == '720p' ? 'selected' : '' }}>720p (HD)</option>
                            <option value="1080p" {{ ($package->max_resolution ?? '') == '1080p' ? 'selected' : '' }}>1080p (Full HD)</option>
                            <option value="4K" {{ ($package->max_resolution ?? '') == '4K' ? 'selected' : '' }}>4K (Ultra HD)</option>
                        </select>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Simultaneous Screens</label>
                        <input type="number" name="max_screens" value="{{ old('max_screens', $package->max_screens ?? 1) }}"
                            class="w-full border border-gray-200 dark:bg-gray-900/40 dark:border-gray-600 p-4 text-gray-900 dark:text-white focus:border-accent outline-none">
                    </div>

                    {{-- Simple Checkboxes --}}
                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-gray-100 dark:border-gray-600">

                        <label class="flex items-center gap-x-3 cursor-pointer group">
                            <input type="checkbox" name="has_ads" value="1" {{ old('has_ads', $package->has_ads ?? false) ? 'checked' : '' }}
                                class="w-6 h-6 border-2 border-gray-300 dark:border-gray-600 bg-transparent text-accent focus:ring-0 focus:ring-offset-0 transition-colors">
                            <span class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 group-hover:text-accent transition-colors">Show Ads</span>
                        </label>

                        <label class="flex items-center gap-x-3 cursor-pointer group">
                            <input type="checkbox" name="download_enabled" value="0" {{ old('download_enabled', $package->download_enabled ?? true) ? 'checked' : '' }}
                                class="w-6 h-6 border-2 border-gray-300 dark:border-gray-600 bg-transparent text-accent focus:ring-0 focus:ring-offset-0 transition-colors">
                            <span class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 group-hover:text-accent transition-colors">Downloads</span>
                        </label>

                        <label class="flex items-center gap-x-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}
                                class="w-6 h-6 border-2 border-gray-300 dark:border-gray-600 bg-transparent text-accent focus:ring-0 focus:ring-offset-0 transition-colors">
                            <span class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 group-hover:text-accent transition-colors">Published</span>
                        </label>

                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 flex items-center gap-x-6">
            <button type="submit" class="bg-gray-900 dark:bg-white dark:text-gray-900 px-12 py-5 font-bold uppercase hover:bg-accent hover:text-gray-900 transition-all shadow-xl">
                {{ isset($package) ? 'Update Package' : 'Create Package' }}
            </button>
            <a href="{{ route('admin.packages.index') }}" class="text-xs font-bold uppercase text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection