<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverProfile;
use App\Models\Job;
use App\Models\Service;
use App\Models\SavedAddress;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;



class CustomerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_jobs' => $user->customerJobs()->count(),
            'pending_jobs' => $user->customerJobs()->where('status', 'pending')->count(),
            'active_jobs' => $user->customerJobs()->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed_jobs' => $user->customerJobs()->where('status', 'completed')->count(),
        ];

        $active_jobs = $user->customerJobs()
            ->with(['driver', 'service'])
            ->whereIn('status', ['accepted', 'in_progress'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'active_jobs' => $active_jobs
            ]
        ]);
    }

public function setDestination(Request $request)
{
    $request->validate([
        'service_id' => 'required|exists:services,id',
        'from_address' => 'required|string',
        'from_postcode' => 'nullable|string',
        'from_latitude' => 'required|numeric',
        'from_longitude' => 'required|numeric',
        'to_address' => 'required|string',
        'to_postcode' => 'nullable|string',
        'to_latitude' => 'required|numeric',
        'to_longitude' => 'required|numeric',
        'distance_meters' => 'nullable|integer|min:0',
        'duration_seconds' => 'nullable|integer|min:0',
    ]);

    $user = $request->user();
    $service = Service::findOrFail($request->service_id);

    // normalize
    $fromAddress = trim(preg_replace('/\s+/', ' ', $request->from_address));
    $toAddress   = trim(preg_replace('/\s+/', ' ', $request->to_address));

    $fromPostcode = $request->from_postcode ? trim($request->from_postcode) : '';
    $toPostcode   = $request->to_postcode ? trim($request->to_postcode) : '';

    /** 1️⃣ Create/Update PICKUP (no booking_id yet) */
    $pickup = SavedAddress::updateOrCreate(
        [
            'user_id' => $user->id,
            'address' => $fromAddress,
            'postcode' => $fromPostcode,
        ],
        [
            'label' => $service->name . ' Pickup',
            'latitude' => $request->from_latitude,
            'longitude' => $request->from_longitude,
            'distance_meters' => $request->distance_meters,
            'duration_seconds' => $request->duration_seconds,
        ]
    );

    /** 2️⃣ booking_id = pickup ka id */
    if ($pickup->booking_id === null) {
        $pickup->booking_id = $pickup->id;
        $pickup->save();
    }

    $bookingIdNumber = $pickup->booking_id; // ✅ ab number aa gaya

    /** 3️⃣ Create/Update DELIVERY with SAME booking_id */
    $delivery = SavedAddress::updateOrCreate(
        [
            'user_id' => $user->id,
            'address' => $toAddress,
            'postcode' => $toPostcode,
        ],
        [
            'booking_id' => $bookingIdNumber,
            'label' => $service->name . ' Delivery',
            'latitude' => $request->to_latitude,
            'longitude' => $request->to_longitude,
            'distance_meters' => $request->distance_meters,
            'duration_seconds' => $request->duration_seconds,
        ]
    );

    /** 4️⃣ Format booking id (001 style) */
    $bookingIdFormatted = str_pad($bookingIdNumber, 3, '0', STR_PAD_LEFT);

    return response()->json([
        'success' => true,
        'message' => 'Destination saved successfully',
        'data' => [
            'booking_id' => $bookingIdFormatted,
            'booking_number' => $bookingIdNumber,
            'pickup' => $pickup,
            'delivery' => $delivery,
            'distance_meters' => $request->distance_meters,
            'duration_seconds' => $request->duration_seconds,
        ]
    ]);
}

    public function nearbyDrivers(Request $request)
    {

        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'service_id' => 'required|exists:services,id'
        ]);

        $drivers = DriverProfile::where('is_online', true)
            ->where('verification_status', 'verified')
            ->whereHas('services', fn($q)=>$q->where('service_id',$request->service_id))
            ->selectRaw("
                *, (3959 * acos(
                    cos(radians(?)) *
                    cos(radians(current_latitude)) *
                    cos(radians(current_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(current_latitude))
                )) AS distance
            ", [$request->latitude, $request->longitude, $request->latitude])
            ->having('distance','<=',20)
            ->orderBy('distance')
            ->get();

        return response()->json(['success'=>true,'data'=>$drivers]);
    }

public function trackDriver(Job $job)
{
    return response()->json([
        'success'=>true,
        'data'=>[
            'lat'=>$job->driver->driverProfile->current_latitude,
            'lng'=>$job->driver->driverProfile->current_longitude,
            'updated_at'=>$job->driver->driverProfile->last_location_update
        ]
    ]);
}

public function receipt(Job $job)
{
    return response()->json([
        'job'=>$job,
        'transaction'=>$job->transaction
    ]);
}

}
