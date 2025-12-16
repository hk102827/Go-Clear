@extends('layouts.app')

@section('title', 'Job #' . $job->job_number)
@section('page-title', 'Job #' . $job->job_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Job #{{ $job->job_number }}</h2>
                    <p class="text-gray-600">{{ $job->service->name ?? 'N/A' }}</p>
                </div>
                <span class="px-3 py-1 text-sm rounded-full @if($job->status === 'completed') bg-green-100 text-green-800 @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800 @elseif($job->status === 'assigned') bg-yellow-100 text-yellow-800 @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($job->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-2">Pickup Location</h3>
                    <p class="text-gray-600">{{ $job->pickup_address }}</p>
                    <p class="text-sm text-gray-500">{{ $job->pickup_postcode }}</p>
                    @if($job->pickup_latitude && $job->pickup_longitude)
                        <p class="text-xs text-gray-500">{{ $job->pickup_latitude }}, {{ $job->pickup_longitude }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase mb-2">Delivery Location</h3>
                    <p class="text-gray-600">{{ $job->delivery_address }}</p>
                    <p class="text-sm text-gray-500">{{ $job->delivery_postcode }}</p>
                    @if($job->delivery_latitude && $job->delivery_longitude)
                        <p class="text-xs text-gray-500">{{ $job->delivery_latitude }}, {{ $job->delivery_longitude }}</p>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-semibold text-gray-900 uppercase mb-2">Description</h3>
                <p class="text-gray-600">{{ $job->description ?? 'No description provided' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Estimated Price</p>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($job->estimated_price, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Final Price</p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ is_numeric($job->final_price) ? '$' . number_format($job->final_price, 2) : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Distance</p>
                    <p class="text-gray-900 font-medium">{{ $job->distance ?? 'N/A' }} miles</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Duration</p>
                    <p class="text-gray-900 font-medium">{{ $job->duration ?? 'N/A' }} minutes</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Scheduled</p>
                    <p class="text-gray-900 font-medium">{{ $job->scheduled_at ? $job->scheduled_at->format('M d, Y H:i') : 'Not scheduled' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Created</p>
                    <p class="text-gray-900 font-medium">{{ $job->created_at ? $job->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        @if($job->status !== 'completed' && $job->status !== 'cancelled')
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
                <form method="POST" action="{{ route('admin.jobs.update-status', $job) }}" class="flex space-x-2">
                    @csrf
                    <select name="status" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" @if($job->status === 'pending') selected @endif>Pending</option>
                        <option value="assigned" @if($job->status === 'assigned') selected @endif>Assigned</option>
                        <option value="in_progress" @if($job->status === 'in_progress') selected @endif>In Progress</option>
                        <option value="completed" @if($job->status === 'completed') selected @endif>Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update
                    </button>
                </form>
            </div>
        @endif

        @if($job->cancellation_reason)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-red-900 mb-2">Cancellation Reason</h3>
                <p class="text-red-800">{{ $job->cancellation_reason }}</p>
            </div>
        @endif
    </div>

    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer</h3>
            <div class="flex items-center mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($job->customer->name) }}&size=40" alt="{{ $job->customer->name }}" class="w-10 h-10 rounded-full mr-3">
                <div>
                    <p class="font-medium text-gray-900">{{ $job->customer->name }}</p>
                    <p class="text-sm text-gray-600">{{ $job->customer->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $job->customer) }}" class="w-full px-4 py-2 text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                View Profile
            </a>
        </div>

        @if($job->driver)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Driver</h3>
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($job->driver->name) }}&size=40" alt="{{ $job->driver->name }}" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ $job->driver->name }}</p>
                        <p class="text-sm text-gray-600">{{ $job->driver->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $job->driver) }}" class="w-full px-4 py-2 text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                    View Profile
                </a>
            </div>
        @endif

        @if($job->transaction)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction</h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Amount</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($job->transaction->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Status</p>
                        <p class="text-gray-900 font-medium">{{ ucfirst($job->transaction->status) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.transactions.show', $job->transaction) }}" class="mt-4 w-full px-4 py-2 text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">
                    View Transaction
                </a>
            </div>
        @endif

        @if($job->rating)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating</h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Score</p>
                        <p class="text-lg font-bold text-gray-900">{{ $job->rating->rating }}/5 ⭐</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Comment</p>
                        <p class="text-gray-600">{{ $job->rating->comment ?? 'No comment' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
