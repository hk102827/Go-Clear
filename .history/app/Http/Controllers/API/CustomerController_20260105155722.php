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

    // Auto booking id (same for pickup + drop)
    $bookingId = (string) Str::uuid();

    // Pickup
    $pickup = SavedAddress::firstOrCreate(
        [
            'user_id' => $user->id,
            'booking_id' => $bookingId,
            'address' => $request->from_address,
            'postcode' => $request->from_postcode,
        ],
        [
            'label' => $service->name . ' Pickup',
            'latitude' => $request->from_latitude,
            'longitude' => $request->from_longitude,
            'distance_meters' => $request->distance_meters,
            'duration_seconds' => $request->duration_seconds,
        ]
    );

    // Delivery
    $delivery = SavedAddress::firstOrCreate(
        [
            'user_id' => $user->id,
            'booking_id' => $bookingId,
            'address' => $request->to_address,
            'postcode' => $request->to_postcode,
        ],
        [
            'label' => $service->name . ' Delivery',
            'latitude' => $request->to_latitude,
            'longitude' => $request->to_longitude,
            'distance_meters' => $request->distance_meters,
            'duration_seconds' => $request->duration_seconds,
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Destination saved successfully',
        'data' => [
            'booking_id' => $bookingId,
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
