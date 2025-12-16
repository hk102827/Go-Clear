@extends('layouts.app')

@section('title', $user->name)
@section('page-title', $user->name . ' - ' . ucfirst($user->role))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT SIDEBAR --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow overflow-hidden">

            {{-- USER BASIC PROFILE --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=80"
                         alt="{{ $user->name }}"
                         class="w-20 h-20 rounded-full mb-4">

                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-sm mt-1">{{ ucfirst($user->role) }}</p>

                    <span class="mt-2 px-3 py-1 text-xs rounded-full
                        @if($user->status === 'active') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            {{-- USER INFO --}}
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Email</p>
                    <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Phone</p>
                    <p class="text-gray-900 font-medium">{{ $user->phone }}</p>
                </div>

                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Address</p>
                    <p class="text-gray-900 font-medium">{{ $user->address ?? 'Not provided' }}</p>
                </div>

                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Postcode</p>
                    <p class="text-gray-900 font-medium">{{ $user->postcode ?? 'Not provided' }}</p>
                </div>

                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Joined</p>
                    <p class="text-gray-900 font-medium">
                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                    </p>
                </div>

                @if($user->email_verified_at)
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Email Verified</p>
                        <p class="text-gray-900 font-medium">
                            {{ $user->email_verified_at->format('M d, Y') }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="px-6 py-3 border-t border-gray-200 space-y-2">
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white rounded-lg
                            @if($user->status === 'active') bg-red-600 hover:bg-red-700
                            @else bg-green-600 hover:bg-green-700 @endif">
                        {{ $user->status === 'active' ? 'Block User' : 'Unblock User' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDE CONTENT --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- DRIVER DOCUMENTS SECTION --}}
        @if($user->role === 'driver' && $user->driverProfile)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Driver Documents</h3>

            <div class="grid grid-cols-2 gap-4">

                {{-- Driving License Front --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">License Front</p>
                    <img src="{{ asset('storage/driver_documents/' . $user->driverProfile->driving_license_front) }}"
                         class="w-full rounded border">
                </div>

                {{-- Driving License Back --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">License Back</p>
                    <img src="{{ asset('storage/driver_documents/' . $user->driverProfile->driving_license_back) }}"
                         class="w-full rounded border">
                </div>

                {{-- Vehicle Photo --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Vehicle Photo</p>
                    <img src="{{ asset('storage/driver_documents/' . $user->driverProfile->vehicle_photo) }}"
                         class="w-full rounded border">
                </div>

                {{-- Insurance Proof --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Insurance Proof</p>
                    <img src="{{ asset('storage/driver_documents/' . $user->driverProfile->insurance_proof) }}"
                         class="w-full rounded border">
                </div>

                {{-- Waste Carrier License --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Waste Carrier License</p>
                    <img src="{{ asset('storage/driver_documents/' . $user->driverProfile->waste_carrier_license) }}"
                         class="w-full rounded border">
                </div>

                {{-- Verification Status --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Verification Status</p>
                    <p class="text-gray-900 font-medium">{{ ucfirst($user->driverProfile->verification_status) }}</p>
                </div>

                {{-- Rejection Reason --}}
                @if($user->driverProfile->rejection_reason)
                <div class="col-span-2">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Rejection Reason</p>
                    <p class="text-red-600 font-medium">{{ $user->driverProfile->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- JOBS --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Jobs</h3>
            @if($user->role === 'driver' && $user->jobsAsDriver->count() > 0)
                @foreach($user->jobsAsDriver->take(5) as $job)
                    <div class="flex justify-between items-center p-3 border rounded-lg mb-2">
                        <div>
                            <p class="font-medium">#{{ $job->job_number }}</p>
                            <p class="text-sm text-gray-600">{{ $job->service->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                            {{ ucfirst($job->status) }}
                        </span>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No jobs found</p>
            @endif
        </div>

        {{-- TRANSACTIONS --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transactions</h3>
            @if($user->transactionsAsCustomer->count() > 0 || $user->transactionsAsDriver->count() > 0)
                @foreach(collect($user->transactionsAsCustomer)->merge($user->transactionsAsDriver)->take(5) as $transaction)
                    <div class="flex justify-between items-center p-3 border rounded-lg mb-2">
                        <div>
                            <p class="font-medium">${{ number_format($transaction->amount, 2) }}</p>
                            <p class="text-sm text-gray-600">{{ $transaction->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No transactions found</p>
            @endif
        </div>

    </div>
</div>
@endsection
