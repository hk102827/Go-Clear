@extends('layouts.app')

@section('title', $service->name)
@section('page-title', 'Service - ' . $service->name)
@php
    $pricingLabels = [
        'per_mile' => 'Per Mile',
        'per_hour' => 'Per Hour',
        'per_ton_kg' => 'Per Ton / Kg',
    ];
@endphp


@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-4">
                @if($service->icon)
                    <p class="text-6xl mb-4">{{ $service->icon }}</p>
                @endif
                <h2 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h2>
                <span class="mt-2 inline-block px-3 py-1 text-xs rounded-full @if($service->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="mt-6 space-y-3">
                <a href="{{ route('admin.services.edit', $service) }}" class="block px-4 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Service
                </a>
                <form method="POST" action="{{ route('admin.services.toggle-status', $service) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-center @if($service->is_active) bg-red-600 text-white hover:bg-red-700 @else bg-green-600 text-white hover:bg-green-700 @endif rounded-lg">
                        {{ $service->is_active ? 'Deactivate' : 'Activate' }} Service
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-center bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete Service
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
            <p class="text-gray-600">{{ $service->description }}</p>
        </div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Information</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs uppercase text-gray-500 font-semibold">Created</p>
            <p class="text-gray-900 font-medium">{{ $service->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500 font-semibold">Last Updated</p>
            <p class="text-gray-900 font-medium">{{ $service->updated_at->format('M d, Y') }}</p>
        </div>

        <!-- ✅ ADD THIS -->
        <div>
            <p class="text-xs uppercase text-gray-500 font-semibold">Pricing Type</p>
            <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                {{ $pricingLabels[$service->pricing_type] ?? '-' }}
            </span>
        </div>
    </div>
</div>


        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Drivers Offering This Service</h3>
                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">{{ $service->drivers->count() }}</span>
            </div>
            @if($service->drivers->count() > 0)
                <div class="space-y-2">
                    @foreach($service->drivers as $driver)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&size=32" alt="{{ $driver->name }}" class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $driver->name }}</p>
                                    <p class="text-sm text-gray-600">${{ $driver->pivot->price }} - {{ ucfirst($driver->pivot->pricing_type) }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full @if($driver->pivot->is_available) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ $driver->pivot->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No drivers offering this service yet</p>
            @endif
        </div>

        @if($service->jobs->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Jobs</h3>
                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">{{ $service->jobs->count() }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($service->jobs->take(5) as $job)
                        <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div>
                                <p class="font-medium text-gray-900">#{{ $job->job_number }}</p>
                                <p class="text-sm text-gray-600">{{ $job->customer->name ?? 'N/A' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full @if($job->status === 'completed') bg-green-100 text-green-800 @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
