<?php
// app/Http/Controllers/Admin/BankDetailController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverBankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    /**
     * Display list of bank details
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // ← DEFAULT 'all'
        
        $query = DriverBankDetail::with('driver');
        
        if ($status === 'pending') {
            $query->whereNull('is_approved');
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'rejected') {
            $query->where('is_approved', false);
        }
        // If 'all', no filter applied
        
        $bankDetails = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.bank-details.index', compact('bankDetails', 'status'));
    }
    
    /**
     * Show single bank detail
     */
    public function show($id)
    {
        $bankDetail = DriverBankDetail::with(['driver', 'approvedBy'])->findOrFail($id);
        
        return view('admin.bank-details.show', compact('bankDetail'));
    }
    
    /**
     * Approve bank details
     */
    public function approve($id)
    {
        $bankDetail = DriverBankDetail::findOrFail($id);
        
        if ($bankDetail->is_approved !== null) {
            return redirect()->back()->with('error', 'This bank detail has already been processed.');
        }
        
        $bankDetail->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null
        ]);
        
        return redirect()->route('admin.bank-details.index')
            ->with('success', 'Bank details approved successfully.');
    }
    
    /**
     * Reject bank details
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $bankDetail = DriverBankDetail::findOrFail($id);
        
        if ($bankDetail->is_approved !== null) {
            return redirect()->back()->with('error', 'This bank detail has already been processed.');
        }
        
        $bankDetail->update([
            'is_approved' => false,
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        return redirect()->route('admin.bank-details.index')
            ->with('success', 'Bank details rejected.');
    }
}