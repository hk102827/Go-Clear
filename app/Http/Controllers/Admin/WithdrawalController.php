<?php
// app/Http/Controllers/Admin/WithdrawalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\DriverEarning;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Display list of withdrawals
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // ← DEFAULT 'all'
        
        $query = Withdrawal::with(['driver', 'bankDetails', 'admin']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.withdrawals.index', compact('withdrawals', 'status'));
    }
    
    /**
     * Show single withdrawal
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::with(['driver', 'bankDetails', 'admin'])->findOrFail($id);
        
        // Get driver's current balance
        $earning = DriverEarning::where('driver_id', $withdrawal->driver_id)->first();
        $currentBalance = $earning ? $earning->available_balance : 0;
        
        return view('admin.withdrawals.show', compact('withdrawal', 'currentBalance'));
    }
    
    /**
     * Approve withdrawal
     */
    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'This withdrawal has already been processed.');
        }
        
        DB::beginTransaction();
        
        try {
            // Check driver balance
            $earning = DriverEarning::where('driver_id', $withdrawal->driver_id)->first();
            
            if (!$earning || $withdrawal->amount > $earning->available_balance) {
                return redirect()->back()->with('error', 'Insufficient balance in driver account.');
            }
            
            // Update withdrawal
            $withdrawal->update([
                'status' => 'approved',
                'admin_id' => auth()->id(),
                'processed_at' => now()
            ]);
            
            // Update driver earnings
            $earning->available_balance -= $withdrawal->amount;
            $earning->withdrawn_amount += $withdrawal->amount;
            $earning->save();
            
            // Create transaction record
            Transaction::create([
                'transaction_id' => 'WTH-' . $withdrawal->id . '-' . time(),
                'job_id' => null,
                'customer_id' => null,
                'driver_id' => $withdrawal->driver_id,
                'amount' => $withdrawal->amount,
                'admin_commission' => 0,
                'driver_earning' => -$withdrawal->amount,
                'payment_method' => 'bank_transfer',
                'gateway_ref' => null,
                'status' => 'completed',
                'withdrawal_id' => $withdrawal->id
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Withdrawal approved successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject withdrawal
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', 'This withdrawal has already been processed.');
        }
        
        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'admin_id' => auth()->id(),
            'processed_at' => now()
        ]);
        
        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Withdrawal rejected.');
    }
}