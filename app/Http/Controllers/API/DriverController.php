<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Models\DriverService;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Service;
use App\Models\DriverPricing;
use App\Models\DriverAvailability;


class DriverController extends Controller
{
    // Dashboard
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $profile = $user->driverProfile;

        $stats = [
            'total_jobs' => $user->driverJobs()->count(),
            'pending_jobs' => $user->driverJobs()->where('status', 'pending')->count(),
            'completed_jobs' => $user->driverJobs()->where('status', 'completed')->count(),
            'earnings' => $user->earnings->available_balance ?? 0,
            'rating' => $profile->rating ?? 0,
            'is_online' => $profile->is_online ?? false,
            'verification_status' => $profile->verification_status ?? 'pending',
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // Submit Verification Documents
 public function uploadDocuments(Request $request)
{
    $profile = $request->user()->driverProfile ?? DriverProfile::create(['user_id' => $request->user()->id]);

    $validated = $request->validate([
        'driving_license_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'driving_license_back' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'vehicle_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'number_plate' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Increased to 2048
        'insurance_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'waste_carrier_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'vehicle_type' => 'nullable|string|max:100',
    ]);

    // Sab image fields ko handle karo (including number_plate)
    foreach (['driving_license_front', 'driving_license_back', 'vehicle_photo', 'number_plate', 'insurance_proof', 'waste_carrier_license'] as $field) {
        if ($request->hasFile($field)) {
            // Delete old file if exists
            if ($profile->$field && Storage::exists($profile->$field)) {
                Storage::delete($profile->$field);
            }
            $path = $request->file($field)->store('driver_documents', 'public');
            $profile->$field = basename($path); // ONLY FILE NAME SAVED

        }
    }

    // Vehicle type sirf text hai
    if ($request->has('vehicle_type')) {
        $profile->vehicle_type = $request->vehicle_type;
    }
    
    $profile->verification_status = 'pending';
    $profile->save();

    return response()->json([
        'success' => true,
        'message' => 'Documents uploaded successfully! Your profile is under review.',
        'data' => $profile
    ]);
}

    // Get Verification Status
    public function getDocumentStatus(Request $request)
    {
        $profile = $request->user()->driverProfile;

        return response()->json([
            'success' => true,
            'data' => [
                'verification_status' => $profile->verification_status ?? 'pending',
                'rejection_reason' => $profile->rejection_reason
            ]
        ]);
    }

    // Add / Update Services
public function saveServices(Request $request)
{
    $validated = $request->validate([
        'services' => 'required|array',
        'services.*.id' => 'required|exists:services,id',
        // Remove pricing_type and price here
    ]);

    $user = $request->user();

    // Delete old selected services
    DriverService::where('driver_id', $user->id)->delete();

    // Save new selections WITHOUT pricing
foreach ($validated['services'] as $service) {
    DriverService::create([
        'driver_id' => $user->id,
        'service_id' => $service['id'],
        'price' => 0,    // default value
        'is_available' => true,
    ]);
}


    return response()->json([
        'success' => true,
        'message' => 'Services saved successfully.'
    ]);
}


public function savePricing(Request $request)
{
    $validated = $request->validate([
        'fixed_price' => 'nullable|numeric|min:0',
        'price_per_mile' => 'nullable|numeric|min:0',
        'price_per_hour' => 'nullable|numeric|min:0',
        'price_per_load' => 'nullable|numeric|min:0',
    ]);

    $user = $request->user();

    DriverPricing::updateOrCreate(
        ['driver_id' => $user->id],
        $validated
    );

    return response()->json([
        'success' => true,
        'message' => 'Pricing saved successfully.'
    ]);
}



public function saveAvailability(Request $request)
{
    $validated = $request->validate([
        'days' => 'required|array',
        'days.*' => 'string',
        'start_time' => 'required',
        'end_time' => 'required',
        'service_radius' => 'required|integer|min:1',
    ]);

    $user = $request->user();

    DriverAvailability::updateOrCreate(
        ['driver_id' => $user->id],
        [
            'days' => json_encode($validated['days']),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'service_radius' => $validated['service_radius']
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Availability saved successfully.'
    ]);
}



    // Get Driver Services
 public function getMyServices()
{
    $services = Service::where('is_active', 1)->get();
    return response()->json([
        'success' => true,
        'data' => $services
    ]);
}


    // Toggle Online Status
    public function toggleOnline(Request $request)
    {
        $profile = $request->user()->driverProfile;
        $profile->is_online = !$profile->is_online;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => ['is_online' => $profile->is_online]
        ]);
    }

    // Update Driver Location
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $profile = $request->user()->driverProfile;
        $profile->current_latitude = $validated['latitude'];
        $profile->current_longitude = $validated['longitude'];
        $profile->last_location_update = now();
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
            'data' => [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude']
            ]
        ]);
    }
}
