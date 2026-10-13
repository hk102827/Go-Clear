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
public function accept(Request $request, JobRequest $jobRequest)
{
    $driverId = $request->user()->id;

    if ($jobRequest->driver_id !== $driverId) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    if ($jobRequest->status !== 'sent') {
        return response()->json(['success' => false, 'message' => 'Request not available'], 400);
    }

    if (!$request->user()->driverProfile->isVerified()) {
        return response()->json(['success' => false, 'message' => 'Driver verification pending'], 403);
    }

    try {
        $result = DB::transaction(function () use ($jobRequest, $driverId) {

            $job = Job::where('id', $jobRequest->job_id)->lockForUpdate()->first();

            if (!$job) {
                return ['ok' => false, 'code' => 404, 'msg' => 'Job not found'];
            }

            if ($job->status !== 'pending') {
                return ['ok' => false, 'code' => 400, 'msg' => 'Job not available'];
            }

            $job->update([
                'driver_id' => $driverId,
                'status' => 'accepted'
            ]);

            $jobRequest->update([
                'status' => 'accepted',
                'responded_at' => now()
            ]);

            JobRequest::where('job_id', $job->id)
                ->where('id', '!=', $jobRequest->id)
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

    public function reject(Request $request, JobRequest $jobRequest)
    {
        $driverId = $request->user()->id;

        if ($jobRequest->driver_id !== $driverId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($jobRequest->status !== 'sent') {
            return response()->json(['success' => false, 'message' => 'Request not available'], 400);
        }

        $result = DB::transaction(function () use ($jobRequest) {

            $jobRequest->update([
                'status'       => 'rejected',
                'responded_at' => now(),
            ]);

            $job = Job::where('id', $jobRequest->job_id)->lockForUpdate()->first();

            if (!$job) {
                return ['ok' => false, 'code' => 404, 'msg' => 'Job not found'];
            }

            if ($job->status === 'pending') {

                $stillOpen = JobRequest::where('job_id', $job->id)
                    ->where('status', 'sent')
                    ->exists();

                if (!$stillOpen) {
                    $job->update(['status' => 'rejected']);
                }
            }

            return ['ok' => true, 'job' => $job->fresh()];
        });

        if (!$result['ok']) {
            return response()->json(['success' => false, 'message' => $result['msg']], $result['code']);
        }

        if ($result['job']->status === 'rejected') {
            NotificationService::custom(
                $result['job']->customer,
                'JOB_UPDATE',
                'No Driver Available',
                'Unfortunately no driver accepted your booking. Please create a new booking.',
                ['job_id' => $result['job']->id]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Request rejected',
        ]);
    }
}
