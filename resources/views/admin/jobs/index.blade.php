@extends('layouts.app')

@section('title', 'Jobs')
@section('page-title', 'Jobs Management')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Jobs List</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($jobs as $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $job->job_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $job->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $job->driver->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $job->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($job->final_price ?? $job->estimated_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full @if($job->status === 'completed') bg-green-100 text-green-800 @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800 @elseif($job->status === 'assigned') bg-yellow-100 text-yellow-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $job->created_at ? $job->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No jobs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
