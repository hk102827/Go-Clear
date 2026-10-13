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
            'total_jobs'     => $user->customerJobs()->count(),
            'pending_jobs'   => $user->customerJobs()->where('status', 'pending')->count(),
            'active_jobs'    => $user->customerJobs()->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed_jobs' => $user->customerJobs()->where('status', 'completed')->count(),
            'rejected_jobs'  => $user->customerJobs()->where('status', 'rejected')->count(),   // ← NEW LINE
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
        'service_id'  => 'required|exists:services,id',
        'booking_id'  => 'required|integer',
        'latitude'    => 'required|numeric|between:-90,90',
        'longitude'   => 'required|numeric|between:-180,180',
        'radius_km'   => 'nullable|numeric|min:1|max:200',
        'loads'       => 'nullable|integer|min:1|max:50',
    ]);

    $lat = (float) $request->latitude;
    $lng = (float) $request->longitude;
    $radius = (float) ($request->radius_km ?? 20);
    $serviceId = (int) $request->service_id;
    $bookingId = (int) $request->booking_id;
    $loads = (int) ($request->loads ?? 1);

    // ✅ pickup OPTIONAL (no user_id filter)
    $pickup = SavedAddress::where('booking_id', $bookingId)
        ->whereRaw("LOWER(label) LIKE '%pickup%'")
        ->first();

    $routeDistanceMeters  = (int) ($pickup->distance_meters ?? 0);
    $routeDurationSeconds = (int) ($pickup->duration_seconds ?? 0);

    $routeMiles = $routeDistanceMeters / 1609.34;
    $routeHours = $routeDurationSeconds / 3600;

    $rows = \DB::table('driver_locations as dl')
        ->join('driver_profiles as dp', 'dp.id', '=', 'dl.driver_profile_id')
        ->join('users as u', 'u.id', '=', 'dp.user_id')
        ->leftJoin('driver_pricings as pr', 'pr.driver_id', '=', 'dp.user_id')
        ->join('driver_services as ds', function ($join) use ($serviceId) {
            $join->on('ds.driver_id', '=', 'dp.user_id')
                ->where('ds.service_id', '=', $serviceId)
                ->where('ds.is_available', '=', 1);
        })
        ->where('dp.is_online', 1)
        ->where('dp.verification_status', 'verified')
        ->selectRaw("
            dp.id as driver_profile_id,
            dp.user_id as driver_user_id,
            u.name as name,
            dp.rating as rating,
            dp.vehicle_type,
            dp.vehicle_make_model,
            dl.lat,
            dl.lng,
            dl.recorded_at,
            ds.pricing_type,

            CASE ds.pricing_type
                WHEN 'fixed' THEN pr.fixed_price
                WHEN 'per_mile' THEN pr.price_per_mile
                WHEN 'per_hour' THEN pr.price_per_hour
                WHEN 'per_load' THEN pr.price_per_load
                WHEN 'per_ton_kg' THEN pr.price_per_load
                ELSE pr.fixed_price
            END as rate,

            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(dl.lat)) *
                    cos(radians(dl.lng) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(dl.lat))
                )
            ) AS distance_km
        ", [$lat, $lng, $lat])
        ->having('distance_km', '<=', $radius)
        ->orderBy('distance_km')
        ->get();

    $data = $rows->map(function ($r) use ($routeMiles, $routeHours, $loads) {
        $distanceKm = (float) $r->distance_km;
        $distanceMiles = $distanceKm * 0.621371;
        $etaMinutes = max(1, (int) ceil(($distanceKm / 30) * 60));

        $pricingType = $r->pricing_type ?? 'fixed';
        $rate = (float) ($r->rate ?? 0);

        $estimatedPrice = match ($pricingType) {
            'per_mile' => $rate * $routeMiles,
            'per_hour' => $rate * $routeHours,
            'per_load', 'per_ton_kg' => $rate * $loads,
            default => $rate,
        };

        return [
            'driver_profile_id' => (int) $r->driver_profile_id,
            'driver_user_id'    => (int) $r->driver_user_id,
            'name'              => $r->name ?? 'Driver',
            'rating'            => (float) ($r->rating ?? 0),
            'vehicle'           => trim(($r->vehicle_type ?? '') . ' ' . ($r->vehicle_make_model ?? '')),
            'lat'               => (float) $r->lat,
            'lng'               => (float) $r->lng,
            'distance_miles'    => round($distanceMiles, 2),
            'eta_minutes'       => $etaMinutes,
            'pricing_type'      => $pricingType,
            'rate'              => round($rate, 2),
            'estimated_price'   => round($estimatedPrice, 2),
            'route_miles'       => round($routeMiles, 2),
            'route_hours'       => round($routeHours, 2),
            'loads'             => $loads,
            'recorded_at'       => $r->recorded_at,
        ];
    });

    return response()->json([
        'success' => true,
        'booking_id' => $bookingId,
        'pickup_found' => (bool) $pickup,
        'route' => [
            'distance_meters' => $routeDistanceMeters,
            'duration_seconds' => $routeDurationSeconds,
        ],
        'count' => $data->count(),
        'data' => $data,
    ]);
}




public function trackDriver(Job $job)
{
    if ($job->customer_id !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // driver not assigned yet
    if (!$job->driver) {
        return response()->json([
            'success' => true,
            'data' => [
                'status' => $job->status,
                'driver_assigned' => false,
                'lat' => null,
                'lng' => null,
                'updated_at' => null,
            ]
        ]);
    }

    $profile = $job->driver->driverProfile;

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Driver profile not found'
        ], 404);
    }

    $loc = \App\Models\DriverLocation::where('driver_profile_id', $profile->id)
        ->latest('recorded_at')
        ->first();

    return response()->json([
        'success' => true,
        'data' => [
            'status' => $job->status,
            'driver_assigned' => true,
            'lat' => $loc?->lat ?? $profile->current_latitude,
            'lng' => $loc?->lng ?? $profile->current_longitude,
            'updated_at' => $loc?->recorded_at ?? $profile->last_location_update,
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


public function customerHistory(Request $request)
{
    $customerId = auth()->id();

    $query = Job::where('customer_id', $customerId)
        ->with(['transaction', 'driver','service'])
        ->latest();

    if ($request->status === 'completed') {
        $query->where('status', 'completed');
    }

    if ($request->status === 'cancelled') {
        $query->where('status', 'cancelled');
    }

    $jobs = $query->get()->map(function ($job) {
        return [
            'id' => $job->id,
            'service_type' => $job->service->name,
            'date_time' => $job->completed_at
                ? $job->completed_at->format('d M Y - h:i A')
                : $job->created_at->format('d M Y - h:i A'),

            'status' => ucfirst($job->status),

            'provider_name' => $job->driver->name ?? null,

            'price' => (float) (
                $job->final_price
                ?? $job->estimated_price
                ?? 0
            ),

            'payment_status' => optional($job->transaction)->status === 'completed'
                ? 'Paid'
                : 'Unpaid',

                'actions' => [
                    'can_rebook' => in_array($job->status, ['completed', 'cancelled', 'rejected']),  // ← added 'rejected'
                    'can_download_receipt' => $job->status === 'completed'
                ]
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $jobs
    ]);
}


}
