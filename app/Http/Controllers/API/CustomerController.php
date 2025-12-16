<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
