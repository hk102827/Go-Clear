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
use App\Models\DriverLocation;
use App\Models\DriverRating;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\DriverDocument;
use App\Models\JobRequest;
use Illuminate\Support\Facades\Log;
use App\Models\DriverBankDetail;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Validator;





class DriverController extends Controller
{
    // Dashboard
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $profile = $user->driverProfile;

        $stats = [
            'total_jobs' => $user->driverJobs()->count(),
            // 'pending_jobs' => $user->driverJobs()->where('status', 'pending')->count(),
            'pending_requests' => JobRequest::where('driver_id',$user->id)->where('status','sent')->count(),
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

public function driverJobs(Request $request)
{
    $driverId = auth()->id();

    // Fetch jobs with relations
    $jobs = Job::where('driver_id', $driverId)
        ->with(['customer', 'rating', 'service', 'transaction'])
        ->latest()
        ->get();

    // Active Jobs
    $activeJobs = $jobs
        ->whereIn('status', ['accepted', 'in_progress'])
        ->map(function ($job) {
            $transaction = $job->transaction;

            // Mile price & formula
            $milePrice = $job->distance && $transaction ? round($transaction->amount / max($job->distance, 1), 2) : 0;
            $mileFormula = $job->distance && $milePrice ? $job->distance.' × $'.$milePrice.' = $'.round($milePrice*$job->distance, 2) : null;

            return [
                'id' => $job->id,
                'job_number' => '#'.$job->job_number,
                'customer_name' => $job->customer->name ?? null,
                'service_type' => optional($job->service)->name,
                'pickup_address' => $job->pickup_address ?? null,
                'delivery_address' => $job->delivery_address ?? null,
                'distance' => $job->distance ? $job->distance.' miles' : null,
                'eta' => $job->eta ?? null,
                'price' => $transaction->amount ?? 0,
                'status' => $job->status === 'in_progress' ? 'Working' : 'Accepted',
                'pricing_breakdown' => [
                    'mile_price' => $milePrice,
                    'mile_formula' => $mileFormula,
                    'duration_charge' => $job->duration && $transaction ? round($transaction->amount / max($job->duration, 1), 2) : 0,
                    'platform_fee' => (float) ($transaction->admin_commission ?? 0),
                    'driver_earnings' => (float) ($transaction->driver_earning ?? 0)
                ],
                'rating' => $job->rating->rating ?? null,
                'review' => $job->rating->review ?? null,
                'payment_method' => $transaction->payment_method ?? null,
                'transaction_id' => $transaction->transaction_id ?? null,
                'paid_at' => $transaction && $transaction->created_at
                    ? $transaction->created_at->format('d M Y – h:i A')
                    : null
            ];
        });

    // Completed Jobs
    $completedJobs = $jobs
        ->where('status', 'completed')
        ->groupBy(function ($job) {
            if (!$job->completed_at) return 'older';
            return $job->completed_at->isToday()
                ? 'today'
                : ($job->completed_at->isYesterday() ? 'yesterday' : 'older');
        })
        ->map(function ($group) {
            return $group->map(function ($job) {
                $transaction = $job->transaction;

                $milePrice = $job->distance && $transaction ? round($transaction->amount / max($job->distance, 1), 2) : 0;
                $mileFormula = $job->distance && $milePrice ? $job->distance.' × $'.$milePrice.' = $'.round($milePrice*$job->distance, 2) : null;

                return [
                    'id' => $job->id,
                    'job_number' => '#'.$job->job_number,
                    'customer_name' => $job->customer->name ?? null,
                    'service_type' => optional($job->service)->name,
                    'pickup_address' => $job->pickup_address ?? null,
                    'delivery_address' => $job->delivery_address ?? null,
                    'completed_at' => $job->completed_at ? $job->completed_at->format('h:i A') : null,
                    'price' => $transaction->amount ?? 0,
                    'rating' => $job->rating->rating ?? null,
                    'review' => $job->rating->review ?? null,
                    'payment_method' => $transaction->payment_method ?? null,
                    'transaction_id' => $transaction->transaction_id ?? null,
                    'paid_at' => $transaction && $transaction->created_at
                        ? $transaction->created_at->format('d M Y – h:i A')
                        : null,
                    'pricing_breakdown' => [
                        'mile_price' => $milePrice,
                        'mile_formula' => $mileFormula,
                        'duration_charge' => $job->duration && $transaction ? round($transaction->amount / max($job->duration, 1), 2) : 0,
                        'platform_fee' => (float) ($transaction->admin_commission ?? 0),
                        'driver_earnings' => (float) ($transaction->driver_earning ?? 0)
                    ]
                ];
            });
        });

    // Job Detail
    $jobDetail = null;
    if ($request->job_id) {
        $job = Job::where('driver_id', $driverId)
            ->with(['customer', 'transaction', 'rating', 'service'])
            ->findOrFail($request->job_id);

        $transaction = $job->transaction;

        $milePrice = $job->distance && $transaction ? round($transaction->amount / max($job->distance, 1), 2) : 0;
        $mileFormula = $job->distance && $milePrice ? $job->distance.' × $'.$milePrice.' = $'.round($milePrice*$job->distance, 2) : null;

        $jobDetail = [
            'job_number' => '#WC-'.$job->job_number,
            'status' => ucfirst($job->status),
            'date' => $job->created_at ? $job->created_at->format('d M Y') : null,
            'customer_name' => $job->customer->name ?? null,
            'pickup_address' => $job->pickup_address ?? null,
            'dropoff_address' => $job->delivery_address ?? null,
            'timeline' => [
                'assigned' => $job->assigned_at ? $job->assigned_at->format('h:i A') : null,
                'completed' => $job->completed_at ? $job->completed_at->format('h:i A') : null,
            ],
            'pricing' => [
                'distance' => $job->distance ? $job->distance.' miles' : null,
                'duration' => $job->duration ? $job->duration.' mins' : null,
                'base_price' => (float) ($transaction->amount ?? 0),
                'platform_fee' => (float) ($transaction->admin_commission ?? 0),
                'driver_earnings' => (float) ($transaction->driver_earning ?? 0)
            ],
            'rating' => $job->rating->rating ?? null,
            'review' => $job->rating->review ?? null,
            'payment' => [
                'method' => $transaction->payment_method ?? null,
                'transaction_id' => $transaction->transaction_id ?? null,
                'paid_at' => $transaction && $transaction->created_at
                    ? $transaction->created_at->format('d M Y – h:i A')
                    : null
            ],
            'pricing_breakdown' => [
                'mile_price' => $milePrice,
                'mile_formula' => $mileFormula,
                'duration_charge' => $job->duration && $transaction ? round($transaction->amount / max($job->duration, 1), 2) : 0,
                'platform_fee' => (float) ($transaction->admin_commission ?? 0),
                'driver_earnings' => (float) ($transaction->driver_earning ?? 0)
            ]
        ];
    }

    return response()->json([
        'success' => true,
        'data' => [
            'jobs' => [
                'active' => $activeJobs,
                'completed' => $completedJobs
            ],
            'job_detail' => $jobDetail
        ]
    ]);
}


public function transactions()
{
    $user = auth()->user();

    if ($user->role === 'driver') {
        $transaction = Transaction::where('driver_id', $user->id)
            ->latest()
            ->first();
    } 
    elseif ($user->role === 'customer') {
        $transaction = Transaction::where('customer_id', $user->id)
            ->latest()
            ->first();
    } 
    else {
        return response()->json([
            'success' => false,
            'message' => 'Invalid user role'
        ], 403);
    }

    return response()->json([
        'success' => true,
        'data' => $transaction
    ]);
}



    // public function withdrawRequest(Request $request)
    // {
    //     $request->validate([
    //         'amount' => 'required|numeric|min:1'
    //     ]);

    //     $driverId = auth()->id();

    //     $earning = DriverEarning::where('driver_id', $driverId)->first();

    //     if (!$earning) {
    //         return response()->json([
    //             'message' => 'Earning record not found'
    //         ], 404);
    //     }

    //     if ($request->amount > $earning->available_balance) {
    //         return response()->json([
    //             'message' => 'Insufficient balance'
    //         ], 422);
    //     }

    //     // ✅ Update wallet
    //     $earning->available_balance -= $request->amount;
    //     $earning->withdrawn_amount += $request->amount;
    //     $earning->save();

    //     return response()->json([
    //         'message' => 'Withdraw request submitted successfully',
    //         'available_balance' => $earning->available_balance,
    //         'withdrawn_amount' => $earning->withdrawn_amount
    //     ]);
    // }
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
    $profile = $request->user()->driverProfile
        ?? DriverProfile::create(['user_id' => $request->user()->id]);

    $request->validate([
        'driving_license_front' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'driving_license_back'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'vehicle_photo'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'number_plate'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'insurance_proof'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'waste_carrier_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'vehicle_type'          => 'nullable|string|max:100',
    ]);

    // Ensure directory exists
    $driverDocsPath = public_path('storage/driver_documents');
    if (!file_exists($driverDocsPath)) {
        mkdir($driverDocsPath, 0755, true);
    }

    foreach ([
        'driving_license_front',
        'driving_license_back',
        'vehicle_photo',
        'number_plate',
        'insurance_proof',
        'waste_carrier_license'
    ] as $field) {

        if ($request->hasFile($field)) {

            // delete old file
            if ($profile->$field) {
                $oldPath = public_path('storage/' . $profile->$field);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file($field);

            $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();

            // 🔥 THIS WAS MISSING
            $file->move($driverDocsPath, $filename);

            // save relative path in DB
            $profile->$field = 'driver_documents/' . $filename;
        }
    }

    if ($request->filled('vehicle_type')) {
        $profile->vehicle_type = $request->vehicle_type;
    }

    $profile->verification_status = 'pending';
    $profile->rejection_reason = null;
    $profile->save();

    // User notification
    NotificationService::custom(
        $profile->user,
        'GENERAL',
        '',
        'Your documents have been submitted successfully and are currently under review.',
        ['verification_status' => 'pending']
    );

    // Admin notification
    foreach (User::where('role', 'admin')->get() as $admin) {
        NotificationService::custom(
            $admin,
            'ADMIN',
            'New Verification Request',
            'Driver "' . $profile->user->name . '" has submitted documents for verification.',
            [
                'driver_profile_id' => $profile->id,
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

    // ✅ Correct base URL
    $baseUrl = asset('storage');

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
        'insurance_proof'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'waste_carrier_license' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // ✅ Create directories (same as uploadDocuments)
    $storagePath = public_path('storage');
    $driverDocsPath = public_path('storage/driver_documents');
    
    if (!file_exists($storagePath)) {
        mkdir($storagePath, 0777, true);
    }
    
    if (!file_exists($driverDocsPath)) {
        mkdir($driverDocsPath, 0777, true);
    }

    $updatedFields = [];

    // ✅ Same logic as uploadDocuments
    foreach ([
        'driving_license_front',
        'driving_license_back',
        'vehicle_photo',
        'number_plate',
        'insurance_proof',
        'waste_carrier_license'
    ] as $field) {

        if ($request->hasFile($field)) {
            
            // 🔥 Delete old file (same path structure)
            if ($profile->$field) {
                $oldFilePath = public_path('storage/' . $profile->$field);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // 🔥 Save new file (same as uploadDocuments)
            $file = $request->file($field);
            $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();

            // Move file to public/storage/driver_documents
            $file->move($driverDocsPath, $filename);

            // Save relative path in database (same format)
            $profile->$field = 'driver_documents/' . $filename;
            
            $updatedFields[] = $field;
        }
    }

    // 🔁 Reset verification
    $profile->verification_status = 'pending';
    $profile->rejection_reason = null;
    $profile->verified_at = null;
    $profile->save();

    // 🔔 Notify driver
    NotificationService::custom(
        $profile->user,
        'GENERAL',
        '',
        'Your updated documents have been submitted successfully and are currently under review.',
        [
            'verification_status' => 'pending'
        ]
    );

    // 🔔 Notify admins
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        NotificationService::custom(
            $admin,
            'ADMIN',
            'Document Reupload Request',
            'Driver "' . $profile->user->name . '" has resubmitted documents for verification.',
            [
                'driver_profile_id' => $profile->id,
                'verification_status' => 'pending',
                'updated_fields' => $updatedFields
            ]
        );
    }

    return response()->json([
        'success' => true,
        'message' => 'Document(s) updated successfully. Verification is pending.',
        'updated_fields' => $updatedFields,
        'data' => $profile
    ]);
}

public function saveServices(Request $request)
{
    $validated = $request->validate([
        'services' => 'required|array',
        'services.*.id' => 'required|exists:services,id',

        // ✅ make optional for step-1
        'services.*.price' => 'nullable|numeric|min:0',
        'services.*.is_available' => 'nullable|boolean',
    ]);

    $user = $request->user();

    $serviceIds = collect($validated['services'])->pluck('id')->unique();
    $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

    DriverService::where('driver_id', $user->id)->delete();

    foreach ($validated['services'] as $item) {
        $service = $services[$item['id']];

        DriverService::create([
            'driver_id'    => $user->id,
            'service_id'   => $item['id'],
            'pricing_type' => $service->pricing_type,
            'price'        => $item['price'] ?? 0,            // ✅ default
            'is_available' => $item['is_available'] ?? true,  // ✅ default
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
        'services.*.price' => 'required|numeric|min:0',
        'services.*.is_available' => 'required|boolean',
    ]);

    $user = $request->user();

    foreach ($validated['services'] as $item) {
        DriverService::where('driver_id', $user->id)
            ->where('service_id', $item['id'])
            ->update([
                'price' => $item['price'],
                'is_available' => $item['is_available'],
            ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Services updated successfully.'
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
    public function getOnlineStatus(Request $request)
{
    $user = $request->user();

    $profile = $user->driverProfile
        ?? \App\Models\DriverProfile::where('user_id', $user->id)->first();

    if (!$profile) {
        return response()->json([
            'success' => false,
            'message' => 'Driver profile not found',
            'debug' => [
                'user_id' => $user->id,
                'role' => $user->role ?? null,
            ]
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'is_online' => (bool) $profile->is_online
        ]
    ]);
}




    // for Driver location tracking
public function updateLocation(Request $request)
{
    $user = $request->user();
    
    Log::info('Location update request from user:', ['user_id' => $user->id]);

    // Get driver profile using Eloquent
    $profile = DriverProfile::where('user_id', $user->id)->first();

    if (!$profile) {
        Log::error('Driver profile not found', ['user_id' => $user->id]);
        return response()->json([
            'status' => false,
            'message' => 'Driver profile not found'
        ], 404);
    }

    Log::info('Driver profile found', ['profile_id' => $profile->id]);

    $data = $request->validate([
        'lat' => 'required|numeric|between:-90,90',
        'lng' => 'required|numeric|between:-180,180',
        'heading' => 'nullable|integer|between:0,360',
        'speed' => 'nullable|integer|min:0|max:300',
        'recorded_at' => 'nullable|date',
        'is_online' => 'nullable|boolean',
    ]);

    Log::info('Validated data:', $data);

    try {
        // Upsert location
        $location = DriverLocation::updateOrCreate(
            ['driver_profile_id' => $profile->id],
            [
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'heading' => $data['heading'] ?? null,
                'speed' => $data['speed'] ?? null,
                'recorded_at' => $data['recorded_at'] ?? now(),
            ]
        );

        Log::info('Location saved successfully', [
            'location_id' => $location->id,
            'driver_profile_id' => $profile->id,
            'lat' => $location->lat,
            'lng' => $location->lng
        ]);

        // Update driver profile
        $profile->current_latitude = $data['lat'];
        $profile->current_longitude = $data['lng'];
        $profile->last_location_update = now();
        
        if (array_key_exists('is_online', $data)) {
            $profile->is_online = (bool)$data['is_online'];
            Log::info('Online status updated', ['is_online' => $profile->is_online]);
        }
        
        $profile->save();

        Log::info('Driver profile updated successfully');

        // ✅ CHECK FOR ACTIVE JOBS AND SEND PROXIMITY NOTIFICATIONS
        $activeJob = \App\Models\Job::where('driver_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeJob) {
            // Calculate distance to pickup location
            $distance = $this->calculateDistance(
                $data['lat'],
                $data['lng'],
                $activeJob->pickup_latitude,
                $activeJob->pickup_longitude
            );

            Log::info('Distance to customer:', ['distance_km' => $distance]);

            // 🔔 SEND "DRIVER NEARBY" NOTIFICATION (within 500 meters / 0.5 km)
            if ($distance <= 0.5 && !$activeJob->nearby_notification_sent) {
                \App\Models\Notification::create([
                    'user_id' => $activeJob->customer_id,
                    'type' => 'DRIVER_NEARBY',
                    'title' => 'Driver is Nearby',
                    'message' => 'Your driver ' . $user->name . ' is nearby and will arrive soon!',
                    'data' => json_encode([
                        'job_id' => $activeJob->id,
                        'driver_name' => $user->name,
                        'driver_phone' => $user->phone ?? 'N/A',
                        'distance' => round($distance * 1000) . 'm',
                        'estimated_arrival' => '2-3 minutes'
                    ]),
                    'is_read' => false
                ]);

                $activeJob->update(['nearby_notification_sent' => true]);
                Log::info('Nearby notification sent to customer');
            }

            // 🔔 SEND "DRIVER ARRIVED" NOTIFICATION (within 50 meters / 0.05 km)
            if ($distance <= 0.05 && !$activeJob->arrived_notification_sent) {
                \App\Models\Notification::create([
                    'user_id' => $activeJob->customer_id,
                    'type' => 'DRIVER_ARRIVED',
                    'title' => 'Driver Has Arrived',
                    'message' => 'Your driver ' . $user->name . ' is waiting for you at the pickup location.',
                    'data' => json_encode([
                        'job_id' => $activeJob->id,
                        'driver_name' => $user->name,
                        'driver_phone' => $user->phone ?? 'N/A',
                        'vehicle_number' => $profile->vehicle_number ?? 'N/A',
                        'arrived_at' => now()->toDateTimeString()
                    ]),
                    'is_read' => false
                ]);

                // 🔔 ALSO NOTIFY DRIVER THAT THEY'VE ARRIVED
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'ARRIVED_AT_PICKUP',
                    'title' => 'You Have Arrived',
                    'message' => 'You have arrived at the pickup location. Please contact the customer.',
                    'data' => json_encode([
                        'job_id' => $activeJob->id,
                        'customer_name' => $activeJob->customer->name ?? 'Customer',
                        'customer_phone' => $activeJob->customer->phone ?? 'N/A'
                    ]),
                    'is_read' => false
                ]);

                $activeJob->update([
                    'arrived_notification_sent' => true,
                    'arrived_at' => now()
                ]);

                Log::info('Arrived notification sent to customer and driver');
            }
        }

    } catch (\Exception $e) {
        Log::error('Error saving location', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => false,
            'message' => 'Failed to update location: ' . $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => true,
        'message' => 'Location updated successfully',
        'data' => [
            'driver_profile_id' => $profile->id,
            'lat' => (float)$location->lat,
            'lng' => (float)$location->lng,
            'heading' => $location->heading,
            'speed' => $location->speed,
            'is_online' => $profile->is_online,
            'recorded_at' => $location->recorded_at?->toISOString(),
        ]
    ]);
}

/**
 * Calculate distance between two coordinates (in kilometers)
 */
private function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;

    return $distance; // in kilometers
}

public function getLocation(Request $request)
{
    $user = $request->user();

    $profile = DB::table('driver_profiles')
        ->where('user_id', $user->id)
        ->first();

    if (!$profile) {
        return response()->json([
            'status' => false,
            'message' => 'Driver profile not found'
        ], 404);
    }

    $location = DriverLocation::where('driver_profile_id', $profile->id)->first();

    return response()->json([
        'status' => true,
        'data' => $location ? [
            'lat' => (float)$location->lat,
            'lng' => (float)$location->lng,
            'heading' => $location->heading,
            'speed' => $location->speed,
            'recorded_at' => optional($location->recorded_at)->toISOString(),
            'updated_at' => optional($location->updated_at)->toISOString(),
        ] : null
    ]);
}



    public function addBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driverId = auth()->id();

        // Check if already has pending/approved bank details
        $existing = DriverBankDetail::where('driver_id', $driverId)
            ->whereIn('is_approved', [null, true])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have bank details. Please wait for approval or update existing one.'
            ], 422);
        }

        $bankDetails = DriverBankDetail::create([
            'driver_id' => $driverId,
            'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'branch_name' => $request->branch_name,
            'is_approved' => null,
        ]);

        // ✅ SEND NOTIFICATION TO DRIVER
        Notification::create([
            'user_id' => $driverId,
            'type' => 'BANK_ACCOUNT_CREATED',
            'title' => 'Bank Account Added Successfully',
            'message' => 'Your bank account has been added successfully and is pending admin approval.',
            'data' => json_encode([
                'bank_name' => $request->bank_name,
                'account_number' => substr($request->account_number, -4), // Last 4 digits
                'status' => 'pending'
            ]),
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bank details submitted successfully. Waiting for admin approval.',
            'data' => [
                'bank_details' => $bankDetails,
                'approval_status' => 'pending'
            ]
        ], 201);
    }

    /**
     * Get Bank Details
     * GET /api/driver/bank-details
     */
    public function getBankDetails()
    {
        $bankDetails = DriverBankDetail::where('driver_id', auth()->id())->first();

        if (!$bankDetails) {
            return response()->json([
                'success' => true,
                'message' => 'No bank details found',
                'data' => [
                    'bank_details' => null,
                    'has_bank_details' => false
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bank_details' => $bankDetails,
                'approval_status' => $bankDetails->approval_status,
                'has_bank_details' => true
            ]
        ]);
    }

    /**
     * Update Bank Details
     * PUT /api/driver/bank-details
     */
    public function updateBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $bankDetails = DriverBankDetail::where('driver_id', auth()->id())->first();

        if (!$bankDetails) {
            return response()->json([
                'success' => false,
                'message' => 'No bank details found to update'
            ], 404);
        }

        // Reset approval status when updating
        $bankDetails->update([
            'account_holder_name' => $request->account_holder_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'branch_name' => $request->branch_name,
            'is_approved' => null, // Reset to pending
            'rejection_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully. Waiting for admin approval.',
            'data' => [
                'bank_details' => $bankDetails,
                'approval_status' => 'pending'
            ]
        ]);
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

    /**
     * Request Withdrawal
     * POST /api/driver/withdraw-request
     */
   public function withdrawRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $driverId = auth()->id();

        // Check if bank details exist and are approved
        $bankDetails = DriverBankDetail::where('driver_id', $driverId)
            ->where('is_approved', true)
            ->first();

        if (!$bankDetails) {
            return response()->json([
                'success' => false,
                'message' => 'Please add and verify bank details first',
                'error_code' => 'BANK_DETAILS_NOT_APPROVED'
            ], 422);
        }

        // Check balance
        $earning = DriverEarning::where('driver_id', $driverId)->first();

        if (!$earning || $request->amount > $earning->available_balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'error_code' => 'INSUFFICIENT_BALANCE',
                'data' => [
                    'available_balance' => $earning ? (float) $earning->available_balance : 0,
                    'requested_amount' => (float) $request->amount
                ]
            ], 422);
        }

        // Check if there's already a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('driver_id', $driverId)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending withdrawal request',
                'error_code' => 'PENDING_WITHDRAWAL_EXISTS',
                'data' => [
                    'pending_withdrawal' => $pendingWithdrawal
                ]
            ], 422);
        }

        // Create withdrawal request
        $withdrawal = Withdrawal::create([
            'driver_id' => $driverId,
            'amount' => $request->amount,
            'status' => 'pending',
            'bank_details_id' => $bankDetails->id
        ]);

        // ✅ SEND NOTIFICATION TO DRIVER
        Notification::create([
            'user_id' => $driverId,
            'type' => 'WITHDRAWAL_REQUESTED',
            'title' => 'Withdrawal Request Submitted',
            'message' => 'Your withdrawal request of £' . number_format($request->amount, 2) . ' has been submitted and is pending admin approval.',
            'data' => json_encode([
                'withdrawal_id' => $withdrawal->id,
                'amount' => $request->amount,
                'status' => 'pending'
            ]),
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully. Waiting for admin approval.',
            'data' => [
                'withdrawal' => $withdrawal->load('bankDetails'),
                'status' => 'pending'
            ]
        ], 201);
    }

    /**
     * Get Withdrawal History
     * GET /api/driver/withdrawal-history
     */
    public function withdrawalHistory()
    {
        $withdrawals = Withdrawal::where('driver_id', auth()->id())
            ->with(['bankDetails', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'withdrawals' => $withdrawals,
                'total_count' => $withdrawals->count()
            ]
        ]);
    }

    /**
     * Get Pending Withdrawal
     * GET /api/driver/pending-withdrawal
     */
    public function getPendingWithdrawal()
    {
        $pendingWithdrawal = Withdrawal::where('driver_id', auth()->id())
            ->where('status', 'pending')
            ->with('bankDetails')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_withdrawal' => $pendingWithdrawal,
                'has_pending' => $pendingWithdrawal ? true : false
            ]
        ]);
    }

    public function earningsSummary()
    {
        $driverId = auth()->id();
        
        // Get driver earning record
        $earning = DriverEarning::where('driver_id', $driverId)->first();
        
        if (!$earning) {
            return response()->json([
                'success' => true,
                'data' => [
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0
                ]
            ]);
        }
        
        // Today's earnings from transactions
        $today = Transaction::where('driver_id', $driverId)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('driver_earning');
        
        // This week's earnings (Monday to Sunday)
        $thisWeek = Transaction::where('driver_id', $driverId)
            ->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->where('status', 'completed')
            ->sum('driver_earning');
        
        // This month's earnings
        $thisMonth = Transaction::where('driver_id', $driverId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('driver_earning');
        
        return response()->json([
            'success' => true,
            'data' => [
                'today' => (float) $today,
                'this_week' => (float) $thisWeek,
                'this_month' => (float) $thisMonth,
                'currency' => '£'
            ]
        ]);
    }

        public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'confirmation_text' => 'required|string|in:DELETE',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.',
                'error_code' => 'INVALID_PASSWORD'
            ], 422);
        }

        // Check if driver has pending withdrawals
        $pendingWithdrawal = Withdrawal::where('driver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete account. You have a pending withdrawal request.',
                'error_code' => 'PENDING_WITHDRAWAL_EXISTS',
                'data' => [
                    'pending_amount' => (float) $pendingWithdrawal->amount
                ]
            ], 422);
        }

        // Check if driver has available balance
        $earning = DriverEarning::where('driver_id', $user->id)->first();
        if ($earning && $earning->available_balance > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete account. Please withdraw your remaining balance first.',
                'error_code' => 'BALANCE_REMAINING',
                'data' => [
                    'available_balance' => (float) $earning->available_balance
                ]
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Delete all related data
            // 1. Delete driver earnings
            DriverEarning::where('driver_id', $user->id)->delete();

            // 2. Delete bank details
            DriverBankDetail::where('driver_id', $user->id)->delete();

            // 3. Delete withdrawals
            Withdrawal::where('driver_id', $user->id)->delete();

            // 4. Delete transactions
            Transaction::where('driver_id', $user->id)->delete();

            // 6. Revoke all tokens (logout from all devices)
            $user->tokens()->delete();

            // 7. Delete user account
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your account has been permanently deleted. We\'re sad to see you go!',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting account: ' . $e->getMessage()
            ], 500);
        }
    }

public function deleteBankAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        // Verify password
        if (!\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.',
                'error_code' => 'INVALID_PASSWORD'
            ], 422);
        }

        // Check if driver has pending withdrawals
        $pendingWithdrawal = Withdrawal::where('driver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete bank account. You have a pending withdrawal request.',
                'error_code' => 'PENDING_WITHDRAWAL_EXISTS'
            ], 422);
        }

        // Delete bank details
        $deleted = DriverBankDetail::where('driver_id', $user->id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'No bank account found to delete'
            ], 404);
        }

        // ✅ SEND NOTIFICATION TO DRIVER
        Notification::create([
            'user_id' => $user->id,
            'type' => 'BANK_ACCOUNT_DELETED',
            'title' => 'Bank Account Deleted',
            'message' => 'Your bank account has been deleted successfully.',
            'data' => json_encode([
                'deleted_at' => now()->toDateTimeString()
            ]),
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bank account deleted successfully',
        ], 200);
    }



}

