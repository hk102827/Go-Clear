@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Platform Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <!-- NEW FIELD: App Name -->
            <div class="mb-6">
                <label for="app_name" class="block text-sm font-medium text-gray-900 mb-2">App Name</label>
                <input type="text" id="app_name" name="app_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('app_name') border-red-500 @enderror" value="{{ old('app_name', $settings['app_name'] ?? 'Go Clears') }}">
                @error('app_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                <p class="text-xs text-gray-500 mt-1">Technical/system name used internally</p>
            </div>

            <div class="mb-6">
                <label for="platform_name" class="block text-sm font-medium text-gray-900 mb-2">Platform Name</label>
                <input type="text" id="platform_name" name="platform_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('platform_name') border-red-500 @enderror" value="{{ old('platform_name', $settings['platform_name'] ?? 'Go-Clear') }}">
                @error('platform_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                <p class="text-xs text-gray-500 mt-1">Display name shown to users</p>
            </div>

            <div class="mb-6">
                <label for="platform_email" class="block text-sm font-medium text-gray-900 mb-2">Platform Email</label>
                <input type="email" id="platform_email" name="platform_email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('platform_email') border-red-500 @enderror" value="{{ old('platform_email', $settings['platform_email'] ?? '') }}">
                @error('platform_email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-6">
                <label for="commission_percentage" class="block text-sm font-medium text-gray-900 mb-2">Commission Percentage (%)</label>
                <input type="number" id="commission_percentage" name="commission_percentage" step="0.01" min="0" max="100" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('commission_percentage') border-red-500 @enderror" value="{{ old('commission_percentage', $settings['commission_percentage'] ?? '10') }}">
                @error('commission_percentage')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                <p class="text-xs text-gray-500 mt-1">Commission charged on every completed transaction</p>
            </div>

            <div class="mb-6">
                <label for="currency" class="block text-sm font-medium text-gray-900 mb-2">Currency</label>
                <select id="currency" name="currency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('currency') border-red-500 @enderror">
                    <option value="USD" @if((old('currency', $settings['currency'] ?? 'USD')) === 'USD') selected @endif>USD ($)</option>
                    <option value="EUR" @if((old('currency', $settings['currency'] ?? 'USD')) === 'EUR') selected @endif>EUR (€)</option>
                    <option value="GBP" @if((old('currency', $settings['currency'] ?? 'USD')) === 'GBP') selected @endif>GBP (£)</option>
                    <option value="CAD" @if((old('currency', $settings['currency'] ?? 'USD')) === 'CAD') selected @endif>CAD ($)</option>
                    <option value="AUD" @if((old('currency', $settings['currency'] ?? 'USD')) === 'AUD') selected @endif>AUD ($)</option>
                    <option value="PKR" @if((old('currency', $settings['currency'] ?? 'USD')) === 'PKR') selected @endif>PKR (₨)</option>
                </select>
                @error('currency')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Platform Information</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>App Name:</strong> Used for technical/internal purposes</li>
                    <li>• <strong>Platform Name:</strong> Displayed to users on frontend</li>
                    <li>• Commission is automatically charged on all completed jobs</li>
                    <li>• Currency setting affects all prices displayed in the system</li>
                    <li>• Changes take effect immediately</li>
                </ul>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Settings
                </button>
                <button type="reset" class="px-6 py-2 border border-gray-300 text-gray-900 rounded-lg hover:bg-gray-50">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Statistics</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ date('Y-m-d') }}</p>
                <p class="text-sm text-gray-600">Current Date</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">v1.0.0</p>
                <p class="text-sm text-gray-600">Platform Version</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $settings['app_name'] ?? config('app.name', 'Go-Clear') }}</p>
                <p class="text-sm text-gray-600">Application Name</p>
            </div>
        </div>
    </div>
</div>
@endsection