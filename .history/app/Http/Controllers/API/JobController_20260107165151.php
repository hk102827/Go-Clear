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




class JobController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'pickup_address' => 'required|string',
            'pickup_postcode' => 'required|string',
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

            $job->update([
                'status' => 'in_progress'
            ]);
                    // 🔔 Notify customer that job has started
            NotificationService::jobStarted(
                $job->customer,
                $job->id,
                auth()->user()->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Job started',
                'data' => $job
            ]);
        }


    public function completeJob(Request $request, Job $job)
    {
        if ($job->driver_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($job->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Job is not in progress'
            ], 400);
        }

        $validated = $request->validate([
            'final_price' => 'required|numeric|min:0',
            'distance' => 'nullable|numeric',
            'duration' => 'nullable|integer',
        ]);

        $job->update([
            'status' => 'completed',
            'final_price' => $validated['final_price'],
            'distance' => $validated['distance'] ?? null,
            'duration' => $validated['duration'] ?? null,
        ]);

        // Calculate commission
        $commission_rate = Setting::where('key', 'admin_commission')->value('value') ?? 15;
$commission_rate = (float) $commission_rate; // ensure it's numeric

        $admin_commission = ($validated['final_price'] * $commission_rate) / 100;
        $driver_earning = $validated['final_price'] - $admin_commission;

        // Create transaction
        $transaction = Transaction::create([
            'transaction_id' => 'TXN-' . now()->timestamp . '-' . rand(1000, 9999),
            'job_id' => $job->id,
            'customer_id' => $job->customer_id,
            'driver_id' => $job->driver_id,
            'amount' => $validated['final_price'],
            'admin_commission' => $admin_commission,
            'driver_earning' => $driver_earning,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);


        // Update driver earnings
        $earnings = DriverEarning::firstOrCreate(
            ['driver_id' => $job->driver_id],
            ['total_earnings' => 0, 'available_balance' => 0, 'withdrawn_amount' => 0]
        );

        $earnings->increment('total_earnings', $driver_earning);
        $earnings->increment('available_balance', $driver_earning);

        // Update driver stats
        $job->driver->driverProfile->increment('total_jobs');
         // 🔔 Notify customer about job completion
        NotificationService::jobCompleted(
            $job->customer,
            $job->id
        );

        // 🔔 Notify customer about payment
        NotificationService::paymentReceived(
            $job->customer,
            $job->id,
            $validated['final_price']
        );

        return response()->json([
            'success' => true,
            'message' => 'Job completed successfully',
            'data' => [
                'job' => $job,
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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($job->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Job cannot be confirmed'], 400);
        }

        // Expect driver_ids from frontend (selected nearby)
        $validated = $request->validate([
            'driver_ids' => 'required|array|min:1',
            'driver_ids.*' => 'integer|exists:users,id'
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
                'JOB_REQUEST',
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