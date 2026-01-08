<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use App\Services\NotificationService;

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
        // 🔔 Notify driver about verification approval
        NotificationService::custom(
            $driverProfile->user,
            'GENERAL',
            'Verification Approved',
            'Congratulations! Your driver account has been verified. You can now start accepting jobs.',
            ['verification_status' => 'verified']
        );
        return redirect()->back()->with('success', 'Driver approved successfully.');
    }

    public function reject(DriverProfile $driverProfile, Request $request)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $driverProfile->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
            
        ]);
        // 🔔 Notify driver about rejection
        NotificationService::custom(
            $driverProfile->user,
            'GENERAL',
            'Verification Rejected',
            "Your verification was rejected. Reason: {$request->rejection_reason}. Please update your documents and resubmit.",
            [
                'verification_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]
        );
        return redirect()->back()->with('success', 'Driver rejected successfully.');
    }

    public function markInProgress(DriverProfile $driverProfile)
    {
        $driverProfile->update(['verification_status' => 'in_progress']);
                // 🔔 Notify driver
        NotificationService::custom(
            $driverProfile->user,
            'GENERAL',
            'Verification In Progress',
            'Your documents are currently being reviewed. We will notify you once the verification is complete.',
            ['verification_status' => 'in_progress']
        );
        return redirect()->back()->with('success', 'Status updated to in progress.');
    }
}
