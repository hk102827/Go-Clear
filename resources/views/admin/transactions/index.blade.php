@extends('layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Transactions Management')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Transactions List</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $transaction->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->customer->name ?? 'N/A') }}&size=32" alt="{{ $transaction->customer->name ?? 'N/A' }}" class="w-8 h-8 rounded-full mr-2">
                                <span class="text-sm text-gray-600">{{ $transaction->customer->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $transaction->driver->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($transaction->type ?? 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full @if($transaction->status === 'completed') bg-green-100 text-green-800 @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No transactions found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
