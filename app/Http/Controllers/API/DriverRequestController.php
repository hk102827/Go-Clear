<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class DriverRequestController extends Controller
{
    // GET /driver/requests?status=sent
    public function index(Request $request)
    {
        $status = $request->get('status', 'sent');

        $requests = JobRequest::with(['job.customer', 'job.service'])
            ->where('driver_id', $request->user()->id)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // POST /driver/requests/{request}/accept
    public function accept(Request $request, JobRequest $requestModel)
    {
        $driverId = $request->user()->id;

        if ($requestModel->driver_id !== $driverId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($requestModel->status !== 'sent') {
            return response()->json(['success' => false, 'message' => 'Request not available'], 400);
        }

        // Driver verified check (same as your acceptJob)
        if (!$request->user()->driverProfile->isVerified()) {
            return response()->json(['success' => false, 'message' => 'Driver verification pending'], 403);
        }

        try {
            $result = DB::transaction(function () use ($requestModel, $driverId) {

                // Lock job row to prevent 2 drivers accepting same job
                $job = Job::where('id', $requestModel->job_id)->lockForUpdate()->first();

                if (!$job) {
                    return ['ok' => false, 'code' => 404, 'msg' => 'Job not found'];
                }

                if ($job->status !== 'pending') {
                    return ['ok' => false, 'code' => 400, 'msg' => 'Job not available'];
                }

                // Assign driver + accepted
                $job->update([
                    'driver_id' => $driverId,
                    'status' => 'accepted'
                ]);

                // Mark this request accepted
                $requestModel->update([
                    'status' => 'accepted',
                    'responded_at' => now()
                ]);

                // Expire other requests for same job
                JobRequest::where('job_id', $job->id)
                    ->where('id', '!=', $requestModel->id)
                    ->where('status', 'sent')
                    ->update([
                        'status' => 'expired',
                        'responded_at' => now()
                    ]);

                return ['ok' => true, 'job' => $job->load(['customer','service'])];
            });

            if (!$result['ok']) {
                return response()->json(['success' => false, 'message' => $result['msg']], $result['code']);
            }

            // 🔔 Notify customer
            NotificationService::driverAssigned(
                $result['job']->customer,
                $result['job']->id,
                $request->user()->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Job accepted successfully',
                'data' => $result['job']
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // POST /driver/requests/{request}/reject
    public function reject(Request $request, JobRequest $requestModel)
    {
        $driverId = $request->user()->id;

        if ($requestModel->driver_id !== $driverId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($requestModel->status !== 'sent') {
            return response()->json(['success' => false, 'message' => 'Request not available'], 400);
        }

        $requestModel->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected'
        ]);
    }
}
