@extends('layouts.app')

@section('title', 'Verification - ' . ($driverProfile->user->name ?? 'Driver N/A'))
@section('page-title', 'Driver Verification - ' . ($driverProfile->user->name ?? 'Driver N/A'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($driverProfile->user->name ?? 'Driver N/A') }}&size=80" alt="{{ $driverProfile->user->name ?? 'Driver N/A' }}" class="w-20 h-20 rounded-full mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $driverProfile->user->name ?? 'Driver N/A' }}</h2>
                    <p class="text-gray-600 text-sm mt-1">Driver</p>
                    <span class="mt-2 px-3 py-1 text-xs rounded-full @if($driverProfile->verification_status === 'approved') bg-green-100 text-green-800 @elseif($driverProfile->verification_status === 'rejected') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($driverProfile->verification_status) }}
                    </span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Email</p>
                    <p class="text-gray-900 font-medium">{{ $driverProfile->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Phone</p>
                    <p class="text-gray-900 font-medium">{{ $driverProfile->user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Submitted</p>
                    <p class="text-gray-900 font-medium">{{ $driverProfile->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification Details</h3>
            
            <div class="grid grid-cols-2 gap-4">

                {{-- Driving License Front --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">License Front</p>
                    <div class="relative h-48 overflow-hidden rounded border bg-gray-50">
                        <img src="{{ asset('storage/driver_documents/' . $driverProfile->driving_license_front) }}"
                             class="w-full h-full object-contain cursor-pointer hover:opacity-90 transition"
                             onclick="openImageModal(this.src)">
                    </div>
                </div>

                {{-- Driving License Back --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">License Back</p>
                    <div class="relative h-48 overflow-hidden rounded border bg-gray-50">
                        <img src="{{ asset('storage/driver_documents/' . $driverProfile->driving_license_back) }}"
                             class="w-full h-full object-contain cursor-pointer hover:opacity-90 transition"
                             onclick="openImageModal(this.src)">
                    </div>
                </div>

                {{-- Vehicle Photo --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Vehicle Photo</p>
                    <div class="relative h-48 overflow-hidden rounded border bg-gray-50">
                        <img src="{{ asset('storage/driver_documents/' . $driverProfile->vehicle_photo) }}"
                             class="w-full h-full object-contain cursor-pointer hover:opacity-90 transition"
                             onclick="openImageModal(this.src)">
                    </div>
                </div>

                {{-- Insurance Proof --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Insurance Proof</p>
                    <div class="relative h-48 overflow-hidden rounded border bg-gray-50">
                        <img src="{{ asset('storage/driver_documents/' . $driverProfile->insurance_proof) }}"
                             class="w-full h-full object-contain cursor-pointer hover:opacity-90 transition"
                             onclick="openImageModal(this.src)">
                    </div>
                </div>

                {{-- Waste Carrier License --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold mb-2">Waste Carrier License</p>
                    <div class="relative h-48 overflow-hidden rounded border bg-gray-50">
                        <img src="{{ asset('storage/driver_documents/' . $driverProfile->waste_carrier_license) }}"
                             class="w-full h-full object-contain cursor-pointer hover:opacity-90 transition"
                             onclick="openImageModal(this.src)">
                    </div>
                </div>

                {{-- Verification Status --}}
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Verification Status</p>
                    <p class="text-gray-900 font-medium">{{ ucfirst($driverProfile->verification_status) }}</p>
                </div>

                {{-- Rejection Reason --}}
                @if($driverProfile->rejection_reason)
                <div class="col-span-2">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Rejection Reason</p>
                    <p class="text-red-600 font-medium">{{ $driverProfile->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($driverProfile->user && $driverProfile->user->driverServices->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Services Offered</h3>
            <div class="space-y-2">
                @foreach($driverProfile->user->driverServices as $service)
                    <div class="flex justify-between items-center p-3 border-l-4 border-blue-500 bg-blue-50">
                        <div>
                            <p class="font-medium text-gray-900">{{ $service->service->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">${{ $service->pivot->price ?? 0 }} - {{ ucfirst($service->pivot->pricing_type ?? 'N/A') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full @if($service->pivot->is_available ?? false) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                            {{ ($service->pivot->is_available ?? false) ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($driverProfile->verification_status !== 'approved' && $driverProfile->verification_status !== 'rejected')
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.verifications.approve', $driverProfile) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                            Approve Verification
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.verifications.in-progress', $driverProfile) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 rounded-lg">
                            Mark as In Progress
                        </button>
                    </form>

                    <details class="border border-gray-200 rounded-lg">
                        <summary class="px-4 py-3 cursor-pointer font-medium text-red-600">Reject Verification</summary>
                        <form method="POST" action="{{ route('admin.verifications.reject', $driverProfile) }}" class="p-4 border-t border-gray-200">
                            @csrf
                            <div class="mb-4">
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-900 mb-2">Reason for Rejection</label>
                                <textarea name="rejection_reason" id="rejection_reason" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" rows="4"></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                                Reject & Send Notification
                            </button>
                        </form>
                    </details>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                <p class="text-gray-600">
                    @if($driverProfile->verification_status === 'approved')
                        This driver has been approved for verification.
                    @else
                        This driver has been rejected. Reason: {{ $driverProfile->rejection_reason ?? 'N/A' }}
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-7xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-4xl font-bold z-10">&times;</button>
        <img id="modalImage" src="" class="max-w-full max-h-screen object-contain rounded">
    </div>
</div>

<script>
function openImageModal(src) {
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('modalImage').src = src;
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>

@endsection