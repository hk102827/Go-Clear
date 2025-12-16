@extends('layouts.app')

@section('title', 'Driver Verifications')
@section('page-title', 'Driver Verification Management')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Driver Verifications</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">License</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($verifications as $verification)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($verification->user->name ?? 'N/A') }}&size=32" alt="{{ $verification->user->name ?? 'N/A' }}" class="w-8 h-8 rounded-full mr-3">
                                <span class="text-sm font-medium text-gray-900">{{ $verification->user->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $verification->user->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $verification->license_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full @if($verification->verification_status === 'approved') bg-green-100 text-green-800 @elseif($verification->verification_status === 'rejected') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($verification->verification_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $verification->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.verifications.show', $verification) }}" class="text-blue-600 hover:text-blue-900">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No driver verifications found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $verifications->links() }}
    </div>
</div>
@endsection
