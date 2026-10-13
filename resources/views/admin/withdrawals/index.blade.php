{{-- resources/views/admin/withdrawals/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Withdrawal Management</h1>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Status Filter Tabs --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ $status === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending
                    <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                        {{ \App\Models\Withdrawal::where('status', 'pending')->count() }}
                    </span>
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'approved']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ $status === 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Approved
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ $status === 'rejected' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Rejected
                </a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'all']) }}"
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ $status === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All
                </a>
            </nav>
        </div>
    </div>

    {{-- Withdrawals Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($withdrawals as $withdrawal)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $withdrawal->driver->name }}</div>
                        <div class="text-sm text-gray-500">{{ $withdrawal->driver->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        £{{ number_format($withdrawal->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $withdrawal->bankDetails->bank_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($withdrawal->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($withdrawal->status === 'approved')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $withdrawal->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.withdrawals.show', $withdrawal->id) }}" 
                           class="text-blue-600 hover:text-blue-900 mr-3">
                            View
                        </a>
                        @if($withdrawal->status === 'pending')
                            <button onclick="approveWithdrawal({{ $withdrawal->id }})" 
                                    class="text-green-600 hover:text-green-900 mr-3">
                                Approve
                            </button>
                            <button onclick="openRejectModal({{ $withdrawal->id }})" 
                                    class="text-red-600 hover:text-red-900">
                                Reject
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No withdrawals found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $withdrawals->appends(['status' => $status])->links() }}
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Reject Withdrawal</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                <textarea name="rejection_reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Enter reason for rejection..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function approveWithdrawal(id) {
    if (confirm('Are you sure you want to approve this withdrawal?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/withdrawals/${id}/approve`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/withdrawals/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
@endsection