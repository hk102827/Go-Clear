<?php
// app/Http/Controllers/API/JobController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use App\Models\Transaction;
use App\Models\DriverEarning;
use App\Models\Rating;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\JobRequest;
use Illuminate\Support\Facades\DB;
use App\Services\StripeService;





class JobController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'pickup_address' => 'required|string',
            'pickup_postcode' => 'nullable|string',
            'pickup_latitude' => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'delivery_address' => 'nullable|string',
            'delivery_postcode' => 'nullable|string',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $validated['customer_id'] = $request->user()->id;
        $validated['status'] = 'pending';
        

        $job = Job::create($validated);
    // 🔔 Send notification to customer
        NotificationService::custom(
            $request->user(),
            'JOB_UPDATE',
            'Booking Created',
            'Your booking has been created successfully. We will assign a driver soon.',
            ['job_id' => $job->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Job created successfully',
            'data' => $job->load('service')
        ], 201);
    }

    public function customerJobs(Request $request)
    {
        $jobs = $request->user()
            ->customerJobs()
            ->with(['driver', 'service'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    public function driverJobs(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = $request->user()->driverJobs()->with(['customer', 'service']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $jobs = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    public function show(Job $job)
    {
        // Check if user has access to this job
        if ($job->customer_id !== auth()->id() && $job->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $job->load(['customer', 'driver', 'service', 'transaction', 'rating']);

        return response()->json([
            'success' => true,
            'data' => $job
        ]);
    }

    public function acceptJob(Request $request, Job $job)
    {
        if ($job->driver_id && $job->driver_id !== $request->user()->id) {
        return response()->json([
            'success' => false,
            'message' => 'Job is not available'
            ], 400);
        }

        $driver = $request->user();
        
        // Check if driver is verified
        if (!$driver->driverProfile->isVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Driver verification pending'
            ], 403);
        }

        $job->update([
            'driver_id' => $driver->id,
            'status' => 'accepted'
        ]);
     // 🔔 Notify customer that driver has been assigned
        NotificationService::driverAssigned(
            $job->customer,
            $job->id,
            $driver->name
        );

        return response()->json([
            'success' => true,
            'message' => 'Job accepted successfully',
            'data' => $job->load(['customer', 'service'])
        ]);
    }

public function startJob(Job $job)
{
    if ($job->driver_id !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    if ($job->status !== 'accepted') {
        return response()->json(['success' => false, 'message' => 'Job not accepted'], 400);
    }

    $driver = auth()->user();

    $job->update([
        'status' => 'in_progress',
        'started_at' => now()
    ]);

    // 🔔 SEND NOTIFICATION TO CUSTOMER - Job Started
    \App\Models\Notification::create([
        'user_id' => $job->customer_id,
        'type' => 'JOB_STARTED',
        'title' => 'Driver is On The Way',
        'message' => 'Your driver ' . $driver->name . ' has started heading to your location.',
        'data' => json_encode([
            'job_id' => $job->id,
            'driver_name' => $driver->name,
            'driver_phone' => $driver->phone ?? 'N/A',
            'vehicle_number' => $driver->driverProfile->vehicle_number ?? 'N/A',
            'started_at' => now()->toDateTimeString()
        ]),
        'is_read' => false
    ]);

    // 🔔 SEND NOTIFICATION TO DRIVER - Job Started Confirmation
    \App\Models\Notification::create([
        'user_id' => $driver->id,
        'type' => 'JOB_STARTED_DRIVER',
        'title' => 'Job Started',
        'message' => 'You have started the job. Navigate to the customer location.',
        'data' => json_encode([
            'job_id' => $job->id,
            'customer_name' => $job->customer->name ?? 'Customer',
            'pickup_address' => $job->pickup_address,
            'started_at' => now()->toDateTimeString()
        ]),
        'is_read' => false
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Job started',
        'data' => $job->fresh()
    ]);
}

public function completeJob(Request $request, Job $job)
{
    if ($job->driver_id !== auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    if ($job->status !== 'in_progress') {
        return response()->json(['success' => false, 'message' => 'Job is not in progress'], 400);
    }

    $validated = $request->validate([
        'final_price' => 'required|numeric|min:0',
        'distance'    => 'nullable|numeric',
        'duration'    => 'nullable|integer',
    ]);

    $commissionRate = (float) (\App\Models\Setting::where('key', 'admin_commission')->value('value') ?? 15);
    $adminCommission = round(($validated['final_price'] * $commissionRate) / 100, 2);
    $driverEarning = max(round($validated['final_price'] - $adminCommission, 2), 0);
    $txId = 'TXN-' . now()->timestamp . '-' . rand(1000, 9999);

    $driver = auth()->user();

    $transaction = DB::transaction(function () use ($job, $validated, $adminCommission, $driverEarning, $txId, $driver) {

        $job->update([
            'status'      => 'completed',
            'final_price' => $validated['final_price'],
            'distance'    => $validated['distance'],
            'duration'    => $validated['duration'],
            'completed_at' => now()
        ]);

        // ✅ Create transaction with 'pending' status (waiting for customer payment)
        $transaction = Transaction::create([
            'transaction_id'   => $txId,
            'job_id'           => $job->id,
            'customer_id'      => $job->customer_id,
            'driver_id'        => $job->driver_id,
            'amount'           => $validated['final_price'],
            'admin_commission' => $adminCommission,
            'driver_earning'   => $driverEarning,
            'payment_method'   => 'card',
            'gateway_ref'      => null, // Will be updated after payment
            'status'           => 'pending', // ✅ Waiting for payment
        ]);

        optional($job->driver?->driverProfile)->increment('total_jobs');

        // 🔔 SEND NOTIFICATION TO CUSTOMER - Job Completed, Payment Required
        \App\Models\Notification::create([
            'user_id' => $job->customer_id,
            'type' => 'JOB_COMPLETED',
            'title' => 'Job Completed - Payment Required',
            'message' => 'Your job has been completed by ' . $driver->name . '. Please proceed to payment.',
            'data' => json_encode([
                'job_id' => $job->id,
                'transaction_id' => $txId,
                'final_price' => $validated['final_price'],
                'driver_name' => $driver->name,
                'completed_at' => now()->toDateTimeString()
            ]),
            'is_read' => false
        ]);

        // 🔔 SEND NOTIFICATION TO DRIVER - Job Completed, Awaiting Payment
        \App\Models\Notification::create([
            'user_id' => $driver->id,
            'type' => 'JOB_COMPLETED_DRIVER',
            'title' => 'Job Completed',
            'message' => 'You have completed the job. Waiting for customer payment confirmation.',
            'data' => json_encode([
                'job_id' => $job->id,
                'transaction_id' => $txId,
                'final_price' => $validated['final_price'],
                'your_earning' => $driverEarning,
                'completed_at' => now()->toDateTimeString()
            ]),
            'is_read' => false
        ]);

        return $transaction;
    });

    return response()->json([
        'success' => true,
        'message' => 'Job completed. Waiting for customer payment.',
        'data' => [
            'job'         => $job->fresh(),
            'transaction' => $transaction
        ]
    ]);
}


    public function cancel(Request $request, Job $job)
    {
        if ($job->customer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($job->status === 'completed' || $job->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Job cannot be cancelled'
            ], 400);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $job->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason']
        ]);
        // 🔔 Notify driver if assigned
        if ($job->driver_id) {
            NotificationService::custom(
                $job->driver,
                'JOB_UPDATE',
                'Job Cancelled',
                "Customer has cancelled the booking. Reason: {$validated['cancellation_reason']}",
                ['job_id' => $job->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Job cancelled successfully'
        ]);
    }

    public function rate(Request $request, Job $job)
    {
        if ($job->customer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($job->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate completed jobs'
            ], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);
        if (!$job->driver_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot rate job without assigned driver'
            ], 400);
        }


        $rating = Rating::updateOrCreate(
            ['job_id' => $job->id],
            [
                'customer_id' => $job->customer_id,
                'driver_id' => $job->driver_id,
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null
            ]
        );

        // Update driver's average rating
        $this->updateDriverRating($job->driver_id);
    // 🔔 Notify driver if assigned
        if ($job->driver_id) {
            NotificationService::custom(
                $job->driver,
                'JOB_UPDATE',
                'New Rating Received',
                'You have received a new rating from the customer.',
                ['job_id' => $job->id]
            );

        }

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'data' => $rating
        ]);
    }

    private function updateDriverRating($driver_id)
    {
        $avgRating = Rating::where('driver_id', $driver_id)->avg('rating');
        
        $driver = User::find($driver_id);
        $driver->driverProfile->update([
            'rating' => round($avgRating, 2)
        ]);
    }

    public function confirmBooking(Request $request, Job $job)
    {
        // Only customer can confirm
    if ($job->customer_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'debug' => [
                'job_customer_id' => $job->customer_id,
                'auth_id' => auth()->id(),
            ]
        ], 403);
    }


        if ($job->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Job cannot be confirmed'], 400);
        }

        $validated = $request->validate([
        'driver_ids' => 'required|array|min:1',
        'driver_ids.*' => 'integer|exists:users,id',

        'route_miles' => 'nullable|numeric|min:0',
        'duration_seconds' => 'nullable|integer|min:0',
        'estimated_price' => 'nullable|numeric|min:0',
        ]);

        $job->update([
        'distance' => $validated['route_miles'] ?? $job->distance,
        'duration' => isset($validated['duration_seconds'])
            ? (int) ceil($validated['duration_seconds'] / 60)
            : $job->duration,
        'estimated_price' => $validated['estimated_price'] ?? $job->estimated_price,
        ]);


        // Prevent duplicate requests
        $already = JobRequest::where('job_id', $job->id)->whereIn('driver_id', $validated['driver_ids'])->count();

        // Create requests
        foreach ($validated['driver_ids'] as $driverId) {
            JobRequest::firstOrCreate(
                ['job_id' => $job->id, 'driver_id' => $driverId],
                ['status' => 'sent', 'sent_at' => now()]
            );

            // 🔔 notify each driver
            NotificationService::custom(
                \App\Models\User::find($driverId),
                'JOB_UPDATE',
                'New Job Request',
                'You have received a new job request.',
                ['job_id' => $job->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Request sent to drivers',
            'data' => [
                'job_id' => $job->id,
                'sent_to' => count($validated['driver_ids']),
                'duplicate_skipped' => $already
            ]
        ]);
    }
}