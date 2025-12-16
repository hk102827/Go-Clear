<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['customer', 'driver', 'service'])->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $job->load(['customer', 'driver', 'service', 'transaction', 'rating']);
        return view('admin.jobs.show', compact('job'));
    }

    public function updateStatus(Job $job, Request $request)
    {
        $request->validate(['status' => 'required|string']);
        $job->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Job status updated successfully.');
    }
}
