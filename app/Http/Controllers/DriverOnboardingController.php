<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DriverProfile;
use App\Models\DriverService;
use App\Models\Service;

class DriverOnboardingController extends Controller
{
    /**
     * Step 1: Show document upload form
     */
    // public function showUploadForm()
    // {
    //     if (Auth::user()->role !== 'driver') {
    //         return redirect()->route('login');
    //     }

    //     return view('driver.upload-documents');
    // }

    /**
     * Step 1: Handle document upload
     */
    // public function uploadDocuments(Request $request)
    // {
    //     $request->validate([
    //         'driving_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    //         'vehicle_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
    //         'number_plate' => 'required|file|mimes:jpg,jpeg,png|max:5120',
    //         'insurance_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    //         'waste_carrier_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    //     ]);

    //     $userId = Auth::id();

    //     // Store documents
    //     $drivingLicensePath = $request->file('driving_license')->store('documents/driving_licenses', 'public');
    //     $vehiclePhotoPath = $request->file('vehicle_photo')->store('documents/vehicle_photos', 'public');
    //     $numberPlatePath = $request->file('number_plate')->store('documents/number_plates', 'public');
        
    //     // Create driver profile
    //     DriverProfile::updateOrCreate(
    //         ['user_id' => $userId],
    //         [
    //             'driving_license' => $drivingLicensePath,
    //             'vehicle_photo' => $vehiclePhotoPath,
    //             'number_plate' => $numberPlatePath,
    //             'verification_status' => 'in_progress',
    //         ]
    //     );

    //     // Redirect to service selection
    //     return redirect()->route('admin.driver.services.select');
    // }

    /**
     * Step 2: Show service selection form
     */
    // public function showServicesForm()
    // {
    //     if (Auth::user()->role !== 'driver') {
    //         return redirect()->route('login');
    //     }

    //     $services = Service::all(); // Assuming you have a services table
    //     return view('driver.select-services', compact('services'));
    // }

    /**
     * Step 2: Save selected services
     */
    // public function saveServices(Request $request)
    // {
    //     $request->validate([
    //         'services' => 'required|array|min:1',
    //         'services.*' => 'exists:services,id',
    //     ]);

    //     $userId = Auth::id();

    //     // Store selected services in session for next step
    //     session(['selected_services' => $request->services]);

    //     // Redirect to pricing setup
    //     return redirect()->route('admin.driver.pricing.setup');
    // }

    /**
     * Step 3: Show pricing setup form
     */
    // public function showPricingForm()
    // {
    //     if (Auth::user()->role !== 'driver') {
    //         return redirect()->route('login');
    //     }

    //     $selectedServiceIds = session('selected_services', []);
        
    //     if (empty($selectedServiceIds)) {
    //         return redirect()->route('admin.driver.services.select');
    //     }

    //     $selectedServices = Service::whereIn('id', $selectedServiceIds)->get();
        
    //     return view('driver.set-pricing', compact('selectedServices'));
    // }

    /**
     * Step 3: Save pricing
     */
    // public function savePricing(Request $request)
    // {
    //     $request->validate([
    //         'pricing' => 'required|array',
    //     ]);

    //     $userId = Auth::id();
    //     $selectedServiceIds = session('selected_services', []);

    //     foreach ($request->pricing as $serviceId => $prices) {
    //         if (in_array($serviceId, $selectedServiceIds)) {
    //             // Determine pricing type and price
    //             $pricingType = 'fixed';
    //             $price = 0;

    //             if (!empty($prices['fixed']) && $prices['fixed'] > 0) {
    //                 $pricingType = 'fixed';
    //                 $price = $prices['fixed'];
    //             } elseif (!empty($prices['per_mile']) && $prices['per_mile'] > 0) {
    //                 $pricingType = 'per_mile';
    //                 $price = $prices['per_mile'];
    //             } elseif (!empty($prices['per_hour']) && $prices['per_hour'] > 0) {
    //                 $pricingType = 'per_hour';
    //                 $price = $prices['per_hour'];
    //             } elseif (!empty($prices['per_load']) && $prices['per_load'] > 0) {
    //                 $pricingType = 'per_load';
    //                 $price = $prices['per_load'];
    //             }

    //             if ($price > 0) {
    //                 DriverService::updateOrCreate(
    //                     [
    //                         'driver_id' => $userId,
    //                         'service_id' => $serviceId,
    //                     ],
    //                     [
    //                         'pricing_type' => $pricingType,
    //                         'price' => $price,
    //                         'is_available' => true,
    //                     ]
    //                 );
    //             }
    //         }
    //     }

    //     // Redirect to availability setup
    //     return redirect()->route('admin.driver.availability.setup');
    // }

    /**
     * Step 4: Show availability setup form
    //  */
    // public function showAvailabilityForm()
    // {
    //     if (Auth::user()->role !== 'driver') {
    //         return redirect()->route('login');
    //     }

    //     return view('driver.set-availability');
    // }

    /**
     * Step 4: Save availability and complete onboarding
     */
    // public function saveAvailability(Request $request)
    // {
    //     $request->validate([
    //         'working_days' => 'required|string|max:50',
    //         'start_time' => 'required',
    //         'end_time' => 'required',
    //         'service_radius' => 'required|integer|min:1',
    //     ]);

    //     $userId = Auth::id();

    //     // Update driver profile with availability
    //     $profile = DriverProfile::where('user_id', $userId)->first();
        
    //     if ($profile) {
    //         // You can add working_hours and service_radius columns to driver_profiles table
    //         // or create a separate driver_availability table
    //         $profile->update([
    //             'is_online' => true,
    //         ]);
    //     }

    //     // Clear session
    //     session()->forget('selected_services');

    //     // Redirect to verification status
    //     return redirect()->route('admin.driver.verification.status');
    // }

    /**
     * Show verification status
     */
    // public function showVerificationStatus()
    // {
    //     $userId = Auth::id();
    //     $profile = DriverProfile::where('user_id', $userId)->first();

    //     if (!$profile) {
    //         return redirect()->route('admin.driver.documents.upload');
    //     }

    //     return view('driver.verification-status', [
    //         'status' => $profile->verification_status,
    //         'rejectionReason' => $profile->rejection_reason ?? null,
    //     ]);
    // }

    /**
     * Admin: Approve driver
     */
    // public function approveDriver(Request $request, $userId)
    // {
    //     $profile = DriverProfile::where('user_id', $userId)->first();

    //     if ($profile) {
    //         $profile->verification_status = 'verified';
    //         $profile->verified_at = now();
    //         $profile->rejection_reason = null;
    //         $profile->save();

    //         return response()->json(['message' => 'Driver verified successfully']);
    //     }

    //     return response()->json(['message' => 'Driver profile not found'], 404);
    // }

    /**
     * Admin: Reject driver
     */
    // public function rejectDriver(Request $request, $userId)
    // {
    //     $request->validate([
    //         'reason' => 'required|string|max:500',
    //     ]);

    //     $profile = DriverProfile::where('user_id', $userId)->first();

    //     if ($profile) {
    //         $profile->verification_status = 'rejected';
    //         $profile->rejection_reason = $request->reason;
    //         $profile->save();

    //         return response()->json(['message' => 'Driver rejected']);
    //     }

    //     return response()->json(['message' => 'Driver profile not found'], 404);
    // }
}