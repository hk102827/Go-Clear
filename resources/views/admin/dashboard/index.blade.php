@extends('layouts.app')

@section('title', 'Dashboard')
{{-- @section('page-title', 'Dashboard') --}}

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Drivers</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalDrivers }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Active Jobs</p>
                <p class="text-3xl font-bold text-gray-900">{{ $activeJobs }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8.16 2.75a.75.75 0 00-1.32 0l-3.5 9.5A.75.75 0 004.5 13h11a.75.75 0 00.66-1.25l-3.5-9.5z"></path></svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Jobs</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentJobs as $job)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">#{{ $job->job_number }}</p>
                            <p class="text-sm text-gray-600">{{ $job->service->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full @if($job->status === 'completed') bg-green-100 text-green-800 @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($job->status) }}
                        </span>
                    </div>
                    @php
    $dt = $transaction->created_at ?? null;
    // if it's a Carbon/DateTime use format, otherwise try parse string; if null show 'N/A'
    $createdAtLabel = 'N/A';
    if ($dt instanceof \DateTimeInterface) {
        $createdAtLabel = $dt->format('M d, Y H:i');
    } elseif (is_string($dt) && trim($dt) !== '') {
        try {
            $createdAtLabel = \Carbon\Carbon::parse($dt)->format('M d, Y H:i');
        } catch (\Throwable $e) {
            $createdAtLabel = 'N/A';
        }
    }
@endphp

<p class="text-xs text-gray-500 mt-2">{{ $createdAtLabel }}</p>

                </div>
            @empty
                <p class="px-6 py-4 text-gray-500">No jobs yet</p>
            @endforelse
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Jobs →</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentTransactions as $transaction)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                            <p class="text-sm text-gray-600">{{ $transaction->customer->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full @if($transaction->status === 'completed') bg-green-100 text-green-800 @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
            @empty
                <p class="px-6 py-4 text-gray-500">No transactions yet</p>
            @endforelse
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Transactions →</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Customers</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $totalCustomers }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Completed Jobs</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $completedJobs }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Services</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $totalServices }}</p>
    </div>
</div>
@endsection
