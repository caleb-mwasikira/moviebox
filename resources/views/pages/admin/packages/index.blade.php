@extends('layouts.admin')

@section('admin-content')
<div class="bg-white shadow-3xl p-8 dark:bg-gray-700">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Packages</h2>
            <p class="text-sm text-gray-500">Manage pricing tiers and streaming features.</p>
        </div>
        <a href="{{ route('admin.packages.create') }}" class="bg-accent px-6 py-3 text-sm font-bold uppercase text-gray-900 hover:bg-accent/90">
            Create New Package
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-600">
                    <th class="py-4 px-2 text-xs font-bold uppercase text-gray-500">Name</th>
                    <th class="py-4 px-2 text-xs font-bold uppercase text-gray-500">Price</th>
                    <th class="py-4 px-2 text-xs font-bold uppercase text-gray-500">Features</th>
                    <th class="py-4 px-2 text-xs font-bold uppercase text-gray-500">Status</th>
                    <th class="py-4 px-2 text-xs font-bold uppercase text-gray-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                @foreach($packages as $package)
                <tr>
                    <td class="py-4 px-2 font-bold text-gray-900 dark:text-white">{{ $package->name }}</td>
                    <td class="py-4 px-2 text-sm text-gray-600 dark:text-gray">
                        {{ strtoupper($package->currency) }} {{ number_format($package->price, 2) }} / {{ $package->interval }}
                    </td>
                    <td class="py-4 px-2 text-xs text-gray-500">
                        {{ $package->max_resolution }} • {{ $package->max_screens }} Screens
                    </td>
                    <td class="py-4 px-2">
                        <span class="px-2 py-1 text-[10px] font-bold uppercase {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-4 px-2 text-right space-x-3">
                        <a href="{{ route('admin.packages.edit', $package) }}" class="text-xs font-bold uppercase text-accent hover:underline">Edit</a>
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold uppercase text-red-500 hover:underline" onclick="return confirm('Delete this package?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection