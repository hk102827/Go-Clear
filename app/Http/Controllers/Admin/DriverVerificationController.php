<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;

class DriverVerificationController extends Controller
{
    public function index()
    {
        $verifications = DriverProfile::with('user')->paginate(15);
        return view('admin.verifications.index', compact('verifications'));
    }

    public function show(DriverProfile $driverProfile)
    {
        $driverProfile->load(['user', 'user.driverServices']);
        return view('admin.verifications.show', compact('driverProfile'));
    }

    public function approve(DriverProfile $driverProfile)
    {
        $driverProfile->update(['verification_status' => 'verified']);
        return redirect()->back()->with('success', 'Driver approved successfully.');
    }

    public function reject(DriverProfile $driverProfile, Request $request)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $driverProfile->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);
        return redirect()->back()->with('success', 'Driver rejected successfully.');
    }

    public function markInProgress(DriverProfile $driverProfile)
    {
        $driverProfile->update(['verification_status' => 'in_progress']);
        return redirect()->back()->with('success', 'Status updated to in progress.');
    }
}
