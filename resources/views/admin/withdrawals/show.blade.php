{{-- resources/views/admin/withdrawals/show.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.withdrawals.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Withdrawals
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Withdrawal Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Driver Information --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Driver Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Driver Name</label>
                        <p class="text-gray-900 font-medium">{{ $withdrawal->driver->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $withdrawal->driver->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Phone</label>
                        <p class="text-gray-900">{{ $withdrawal->driver->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Current Balance</label>
                        <p class="text-gray-900 font-semibold">£{{ number_format($currentBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Withdrawal Information --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Withdrawal Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Withdrawal Amount</label>
                        <p class="text-gray-900 font-bold text-2xl">£{{ number_format($withdrawal->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <div class="mt-1">
                            @if($withdrawal->status === 'pending')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Requested On</label>
                        <p class="text-gray-900">{{ $withdrawal->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @if($withdrawal->processed_at)
                    <div>
                        <label class="text-sm text-gray-500">Processed On</label>
                        <p class="text-gray-900">{{ $withdrawal->processed_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @endif
                    @if($withdrawal->admin)
                    <div>
                        <label class="text-sm text-gray-500">Processed By</label>
                        <p class="text-gray-900">{{ $withdrawal->admin->name }}</p>
                    </div>
                    @endif
                    @if($withdrawal->rejection_reason)
                    <div>
                        <label class="text-sm text-gray-500">Rejection Reason</label>
                        <p class="text-red-600">{{ $withdrawal->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <hr class="my-6">

        {{-- Bank Details --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Bank Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Account Holder Name</label>
                    <p class="text-gray-900 font-medium">{{ $withdrawal->bankDetails->account_holder_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Bank Name</label>
                    <p class="text-gray-900">{{ $withdrawal->bankDetails->bank_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Account Number</label>
                    <p class="text-gray-900">{{ $withdrawal->bankDetails->account_number }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">IFSC Code</label>
                    <p class="text-gray-900">{{ $withdrawal->bankDetails->ifsc_code }}</p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($withdrawal->status === 'pending')
        <div class="mt-8 flex gap-4">
            <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Are you sure you want to approve this withdrawal?')"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Approve Withdrawal
                </button>
            </form>

            <button onclick="openRejectModal()"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Reject Withdrawal
            </button>
        </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Reject Withdrawal</h3>
        <form method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}">
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
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
@endsection