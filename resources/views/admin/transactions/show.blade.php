@extends('layouts.app')

@section('title', 'Transaction #' . $transaction->id)
@section('page-title', 'Transaction #' . $transaction->id)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</h2>
                    <p class="text-gray-600">Transaction ID: #{{ $transaction->id }}</p>
                </div>
                <span class="px-4 py-2 text-sm rounded-full @if($transaction->status === 'completed') bg-green-100 text-green-800 @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Transaction Type</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($transaction->type ?? 'N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Payment Method</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($transaction->payment_method ?? 'N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Created</p>
                    <p class="text-gray-900">{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Updated</p>
                    <p class="text-gray-900">{{ $transaction->updated_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        @if($transaction->job)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Associated Job</h3>
                <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-900">Job #{{ $transaction->job->job_number }}</p>
                        <p class="text-sm text-gray-600">{{ $transaction->job->service->name ?? 'N/A' }}</p>
                    </div>
                    <a href="{{ route('admin.jobs.show', $transaction->job) }}" class="px-4 py-2 text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                        View Job
                    </a>
                </div>
            </div>
        @endif
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer</h3>
            @if($transaction->customer)
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->customer->name) }}&size=40" alt="{{ $transaction->customer->name }}" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $transaction->customer->name }}</p>
                        <p class="text-sm text-gray-600">{{ $transaction->customer->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $transaction->customer) }}" class="w-full px-4 py-2 text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                    View Profile
                </a>
            @else
                <p class="text-gray-500">No customer information</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Driver</h3>
            @if($transaction->driver)
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->driver->name) }}&size=40" alt="{{ $transaction->driver->name }}" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $transaction->driver->name }}</p>
                        <p class="text-sm text-gray-600">{{ $transaction->driver->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $transaction->driver) }}" class="w-full px-4 py-2 text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                    View Profile
                </a>
            @else
                <p class="text-gray-500">No driver information</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount</span>
                    <span class="font-semibold text-gray-900">${{ number_format($transaction->amount, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <span class="text-gray-600">Status</span>
                    <span class="font-semibold @if($transaction->status === 'completed') text-green-600 @elseif($transaction->status === 'pending') text-yellow-600 @else text-red-600 @endif">{{ ucfirst($transaction->status) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
