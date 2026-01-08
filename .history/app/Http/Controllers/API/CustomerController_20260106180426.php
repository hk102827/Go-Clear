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

    $user = $request->user();

    // service pricing type (admin controlled)
    $service = Service::findOrFail($serviceId);
    $pricingType = $service->pricing_type ?? 'fixed';

    // pickup for route distance/duration
    $pickup = SavedAddress::where('user_id', $user->id)
        ->where('booking_id', $bookingId)
        ->where('label', 'like', '%Pickup%')
        ->first();

    if (!$pickup) {
        return response()->json([
            'success' => false,
            'message' => 'Pickup not found for this booking_id'
        ], 404);
    }

    $routeDistanceMeters = (int) ($pickup->distance_meters ?? 0);
    $routeDurationSeconds = (int) ($pickup->duration_seconds ?? 0);

    $routeMiles = $routeDistanceMeters / 1609.34;
    $routeHours = $routeDurationSeconds / 3600;

    $drivers = DriverProfile::query()
        ->with([
            'user:id,name',
            'pricing:driver_id,fixed_price,price_per_mile,price_per_hour,price_per_load',
            // load ONLY this service pivot (to ensure driver offers it + is_available)
            'services' => function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId)
                  ->select('services.id'); // keep light
            }
        ])
        ->where('is_online', 1)
        ->where('verification_status', 'verified')
        ->whereNotNull('current_latitude')
        ->whereNotNull('current_longitude')

        // ✅ IMPORTANT: filter drivers that offer this service AND are available
        ->whereHas('services', function ($q) use ($serviceId) {
            $q->where('services.id', $serviceId)
              ->wherePivot('is_available', 1);
        })

        ->selectRaw(
            "
            driver_profiles.*,
            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(current_latitude)) *
                    cos(radians(current_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(current_latitude))
                )
            ) AS distance_km
            ",
            [$lat, $lng, $lat]
        )
        ->having('distance_km', '<=', $radius)
        ->orderBy('distance_km')
        ->get();

    $data = $drivers->map(function ($dp) use ($routeMiles, $routeHours, $loads, $pricingType) {
        $distanceKm = (float) $dp->distance_km;
        $distanceMiles = $distanceKm * 0.621371;
        $etaMinutes = max(1, (int) ceil(($distanceKm / 30) * 60));

        $pricing = $dp->pricing;

        // ✅ driver’s own rate values
        $rate = 0;
        if ($pricing) {
            $rate = match ($pricingType) {
                'per_mile' => (float) $pricing->price_per_mile,
                'per_hour' => (float) $pricing->price_per_hour,
                'per_load', 'per_ton_kg' => (float) $pricing->price_per_load,
                default => (float) $pricing->fixed_price,
            };
        }

        // ✅ estimated price
        $estimatedPrice = match ($pricingType) {
            'per_mile' => $rate * $routeMiles,
            'per_hour' => $rate * $routeHours,
            'per_load', 'per_ton_kg' => $rate * $loads,
            default => $rate,
        };

        return [
            'driver_profile_id' => $dp->id,
            'driver_user_id'    => $dp->user_id,
            'name'              => $dp->user->name ?? 'Driver',
            'rating'            => (float) ($dp->rating ?? 0),
            'vehicle'           => trim(($dp->vehicle_type ?? '') . ' ' . ($dp->vehicle_make_model ?? '')),
            'lat'               => (float) $dp->current_latitude,
            'lng'               => (float) $dp->current_longitude,
            'distance_miles'    => round($distanceMiles, 2),
            'eta_minutes'       => $etaMinutes,

            // pricing
            'pricing_type'      => $pricingType,
            'rate'              => round($rate, 2),              // ✅ driver’s own rate
            'estimated_price'   => round($estimatedPrice, 2),    // ✅ calculated

            // helpful debug fields
            'route_miles'       => round($routeMiles, 2),
            'route_hours'       => round($routeHours, 2),
            'loads'             => $loads,
        ];
    });

    return response()->json([
        'success' => true,
        'booking_id' => $bookingId,
        'route' => [
            'distance_meters' => $routeDistanceMeters,
            'duration_seconds' => $routeDurationSeconds,
        ],
        'service' => [
            'id' => $service->id,
            'pricing_type' => $pricingType,
        ],
        'count' => $data->count(),
        'data' => $data,
    ]);
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
