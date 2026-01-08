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
use App\Models\DriverEarning;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Models\User;




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

        public function getJobs(Request $request)
        {
            $query = Job::where('driver_id', auth()->id());

            if ($request->status === 'active') {
                $query->whereIn('status',['accepted','in_progress']);
            }

            if ($request->status === 'completed') {
                $query->where('status','completed');
            }

            return response()->json($query->latest()->get());
        }
    public function getJobDetail(Job $job)
    {
        abort_if($job->driver_id !== auth()->id(), 403);
        return response()->json($job->load('customer','transaction'));
    }

    public function wallet()
    {
        $earning = DriverEarning::firstOrCreate(
            ['driver_id' => auth()->id()],
            [
                'total_earnings' => 0,
                'available_balance' => 0,
                'withdrawn_amount' => 0
            ]
        );

        return response()->json([
            'total_earnings' => $earning->total_earnings,
            'available_balance' => $earning->available_balance,
            'withdrawn_amount' => $earning->withdrawn_amount
        ]);
    }


    public function transactions()
    {
        return response()->json(
            Transaction::where('driver_id',auth()->id())->latest()->get()
        );
    }

    public function withdrawRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $driverId = auth()->id();

        $earning = DriverEarning::where('driver_id', $driverId)->first();

        if (!$earning) {
            return response()->json([
                'message' => 'Earning record not found'
            ], 404);
        }

        if ($request->amount > $earning->available_balance) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 422);
        }

        // ✅ Update wallet
        $earning->available_balance -= $request->amount;
        $earning->withdrawn_amount += $request->amount;
        $earning->save();

        return response()->json([
            'message' => 'Withdraw request submitted successfully',
            'available_balance' => $earning->available_balance,
            'withdrawn_amount' => $earning->withdrawn_amount
        ]);
    }
    public function rejectJob(Job $job)
    {
        $job->update(['status'=>'rejected']);
        return response()->json(['message'=>'Job rejected']);
    }

    public function uploadVehicleDetails(Request $request)
    {
        $profile = $request->user()->driverProfile;

        if (!$profile) {
            return response()->json([

                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $validated = $request->validate([
            'vehicle_make_model'  => 'nullable|string|max:150', // Ford Transit Custom
            'vehicle_type'        => 'nullable|string|max:100', // Van
            'registration_number' => 'nullable|string|max:50',  // LD20 FJK
            'manufacture_year'    => 'nullable|digits:4',       // 2020
            'fuel_type'           => 'nullable|string|max:50',  // Diesel
        ]);

        $profile->update($validated);

        // verification dobara pending
        $profile->verification_status = 'pending';
        $profile->rejection_reason = null;
        $profile->verified_at = null;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle details updated successfully.',
            'data' => [
                'vehicle_make_model'  => $profile->vehicle_make_model,
                'vehicle_type'        => $profile->vehicle_type,
                'registration_number' => $profile->registration_number,
                'manufacture_year'    => $profile->manufacture_year,
                'fuel_type'           => $profile->fuel_type,
            ]
        ]);
    }
    // Get Vehicle Details
    public function getVehicleDetails(Request $request)
    {
        $profile = $request->user()->driverProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'vehicle_make_model'  => $profile->vehicle_make_model,
                'vehicle_type'        => $profile->vehicle_type,
                'registration_number' => $profile->registration_number,
                'manufacture_year'    => $profile->manufacture_year,
                'fuel_type'           => $profile->fuel_type,
                'verification_status' => $profile->verification_status,
                'rejection_reason'    => $profile->rejection_reason,
            ]
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

        // 🔔 Notify driver about verification approval
        NotificationService::custom(
            $profile->user,
            'GENERAL',
            '',
            'Your documents have been submitted successfully and are currently under review.',
            [
                'verification_status' => 'pending'
            ]
        );
        // 🔔 Notify ADMIN about new driver documents
 
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                NotificationService::custom(
                    $admin,
                    'ADMIN',
                    'New Verification Request',
                    'Driver "' . $profile->user->name . '" has submitted documents for verification.',
                    [
                        'driver_profile_id' => $profile->id, // 🔥 IMPORTANT
                        'verification_status' => 'pending'
                    ]
                );
            }




        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded successfully! Your profile is under review.',
            'data' => $profile
        ]);
    }


    public function getDocumentStatus(Request $request)
    {
        $profile = $request->user()->driverProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $baseUrl = asset('storage/driver_documents');

        $docStatus = function ($file) use ($profile) {
            if (!$file) return 'missing';

            return match ($profile->verification_status) {
                'verified' => 'verified',
                'rejected' => 'rejected',
                default => 'pending_review',
            };
        };

        $documents = [
            'driving_licence' => [
                'status' => ($profile->driving_license_front && $profile->driving_license_back)
                    ? $docStatus(true)
                    : 'missing',
                'front_image' => $profile->driving_license_front
                    ? $baseUrl.'/'.$profile->driving_license_front
                    : null,
                'back_image' => $profile->driving_license_back
                    ? $baseUrl.'/'.$profile->driving_license_back
                    : null,
            ],

            'vehicle_insurance' => [
                'status' => $docStatus($profile->insurance_proof),
                'file' => $profile->insurance_proof
                    ? $baseUrl.'/'.$profile->insurance_proof
                    : null,
            ],

            'waste_carrier_licence' => [
                'status' => $docStatus($profile->waste_carrier_license),
                'file' => $profile->waste_carrier_license
                    ? $baseUrl.'/'.$profile->waste_carrier_license
                    : null,
                'required' => true
            ],

            'vehicle_photos' => [
                'status' => ($profile->vehicle_photo && $profile->number_plate)
                    ? $docStatus(true)
                    : 'missing',
                'vehicle_image' => $profile->vehicle_photo
                    ? $baseUrl.'/'.$profile->vehicle_photo
                    : null,
                'number_plate_image' => $profile->number_plate
                    ? $baseUrl.'/'.$profile->number_plate
                    : null,
            ],
        ];

        return response()->json([
            'success' => true,
            'documents' => $documents,
            'overall_status' => $profile->verification_status,
            'verified_at' => $profile->verified_at,
            'rejection_reason' => $profile->rejection_reason,
        ]);
    }




    public function reuploadDocuments(Request $request)
    {
        $profile = $request->user()->driverProfile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }

        $validated = $request->validate([
            'driving_license_front' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'driving_license_back'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'vehicle_photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'number_plate'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'insurance_proof'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'waste_carrier_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $updatedFields = [];

        foreach ($validated as $field => $file) {
            if ($request->hasFile($field)) {

                // 🔥 Delete old file
                if ($profile->$field && Storage::disk('public')->exists('driver_documents/'.$profile->$field)) {
                    Storage::disk('public')->delete('driver_documents/'.$profile->$field);
                }

                // 🔥 Save new file
                $path = $request->file($field)->store('driver_documents', 'public');
                $profile->$field = basename($path);
                $updatedFields[] = $field;
            }
        }

        // 🔁 Reset verification
        $profile->verification_status = 'pending';
        $profile->rejection_reason = null;
        $profile->verified_at = null;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Document(s) updated successfully. Verification is pending.',
            'updated_fields' => $updatedFields
        ]);
    }









    // Get Verification Status
    // public function getDocumentStatus(Request $request)
    // {
    //     $profile = $request->user()->driverProfile;

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'verification_status' => $profile->verification_status ?? 'pending',
    //             'rejection_reason' => $profile->rejection_reason
    //         ]
    //     ]);
    // }

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



        public function updateService(Request $request)
        {
            $validated = $request->validate([
                'services' => 'required|array',
                'services.*.id' => 'required|exists:services,id',
                'services.*.is_available' => 'required|boolean',
            ]);

            $user = $request->user();
            $updatedCount = 0;

            foreach ($validated['services'] as $service) {
                $record = DriverService::updateOrCreate(
                    [
                        'driver_id' => $user->id,
                        'service_id' => $service['id'],
                    ],
                    [
                        'is_available' => $service['is_available'],
                        'price' => 0, // agar aap default price bhi set karna chahen
                    ]
                );

                // Agar record pehle exist karta tha aur update hua
                if ($record->wasRecentlyCreated === false) {
                    $updatedCount++;
                } else {
                    // Nayi service add hui
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Services updated or added successfully.',
                'processed_rows' => $updatedCount
            ]);
        }



   public function getServices(Request $request)
    {
        $user = $request->user();

        $services = DriverService::with('service:id,name')
            ->where('driver_id', $user->id)
            ->get();

        if ($services->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No services found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'services' => $services->map(function ($item) {
                return [
                    'id' => $item->service_id,
                    'name' => $item->service->name ?? null,
                    'is_available' => $item->is_available,
                ];
            })
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
    public function getAvailability(Request $request)
    {
        $user = $request->user();

        $availability = DriverAvailability::where('driver_id', $user->id)->first();

        if (!$availability) {
            return response()->json([
                'success' => false,
                'message' => 'No availability found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'days' => json_decode($availability->days),
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
                'service_radius' => $availability->service_radius,
            ]
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
    public function toggleOnlineStatus(Request $request)
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


    // for Driver location tracking
}
