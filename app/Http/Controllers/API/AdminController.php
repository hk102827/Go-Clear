<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DriverBankDetail;
use App\Models\DriverEarning;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // ==========================================
    // BANK DETAILS MANAGEMENT
    // ==========================================

    /**
     * Get All Pending Bank Details
     * GET /api/admin/pending-bank-details
     */
    public function getPendingBankDetails()
    {
        $pendingDetails = DriverBankDetail::with('driver')
            ->whereNull('is_approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_bank_details' => $pendingDetails,
                'total_count' => $pendingDetails->count()
            ]
        ]);
    }

    /**
     * Get All Bank Details (with filters)
     * GET /api/admin/bank-details
     */
    public function getAllBankDetails(Request $request)
    {
        $query = DriverBankDetail::with('driver');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('is_approved');
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'rejected') {
                $query->where('is_approved', false);
            }
        }

        $bankDetails = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'bank_details' => $bankDetails,
                'total_count' => $bankDetails->count()
            ]
        ]);
    }

    /**
     * Approve/Reject Bank Details
     * POST /api/admin/bank-details/{id}/approve
     */
    public function approveBankDetails(Request $request, $bankDetailId)
    {
        $validator = Validator::make($request->all(), [
            'is_approved' => 'required|boolean',
            'rejection_reason' => 'required_if:is_approved,false|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $bankDetails = DriverBankDetail::find($bankDetailId);

        if (!$bankDetails) {
            return response()->json([
                'success' => false,
                'message' => 'Bank details not found'
            ], 404);
        }

        if ($bankDetails->is_approved !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This bank details has already been processed'
            ], 422);
        }

        $bankDetails->update([
            'is_approved' => $request->is_approved,
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        $status = $request->is_approved ? 'approved' : 'rejected';

        // ✅ SEND NOTIFICATION TO DRIVER
        if ($request->is_approved) {
            Notification::create([
                'user_id' => $bankDetails->driver_id,
                'type' => 'BANK_ACCOUNT_APPROVED',
                'title' => 'Bank Account Approved',
                'message' => 'Great news! Your bank account has been approved. You can now request withdrawals.',
                'data' => json_encode([
                    'bank_name' => $bankDetails->bank_name,
                    'account_number' => substr($bankDetails->account_number, -4),
                    'approved_at' => now()->toDateTimeString()
                ]),
                'is_read' => false
            ]);
        } else {
            Notification::create([
                'user_id' => $bankDetails->driver_id,
                'type' => 'BANK_ACCOUNT_REJECTED',
                'title' => 'Bank Account Rejected',
                'message' => 'Your bank account verification was rejected. Reason: ' . $request->rejection_reason,
                'data' => json_encode([
                    'bank_name' => $bankDetails->bank_name,
                    'rejection_reason' => $request->rejection_reason,
                    'rejected_at' => now()->toDateTimeString()
                ]),
                'is_read' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Bank details {$status} successfully",
            'data' => [
                'bank_details' => $bankDetails->load(['driver', 'approvedBy'])
            ]
        ]);
    }
    // ==========================================
    // WITHDRAWAL MANAGEMENT
    // ==========================================

    /**
     * Get All Pending Withdrawals
     * GET /api/admin/pending-withdrawals
     */
    public function getPendingWithdrawals()
    {
        $pendingWithdrawals = Withdrawal::with(['driver', 'bankDetails'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add driver balance info
        $pendingWithdrawals->each(function ($withdrawal) {
            $earning = DriverEarning::where('driver_id', $withdrawal->driver_id)->first();
            $withdrawal->driver_balance = $earning ? (float) $earning->available_balance : 0;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'pending_withdrawals' => $pendingWithdrawals,
                'total_count' => $pendingWithdrawals->count()
            ]
        ]);
    }

    /**
     * Get All Withdrawals (with filters)
     * GET /api/admin/withdrawals
     */
    public function getAllWithdrawals(Request $request)
    {
        $query = Withdrawal::with(['driver', 'bankDetails', 'admin']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by driver
        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $withdrawals = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $withdrawals
        ]);
    }

    /**
     * Approve/Reject Withdrawal
     * POST /api/admin/withdrawals/{id}/approve
     */
     public function approveWithdrawal(Request $request, $withdrawalId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $withdrawal = Withdrawal::find($withdrawalId);

        if (!$withdrawal) {
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request not found'
            ], 404);
        }

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This withdrawal request has already been processed'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Update withdrawal status
            $withdrawal->update([
                'status' => $request->status,
                'admin_id' => auth()->id(),
                'rejection_reason' => $request->rejection_reason,
                'processed_at' => now()
            ]);

            if ($request->status === 'approved') {
                // Update driver earnings
                $earning = DriverEarning::where('driver_id', $withdrawal->driver_id)->first();

                if (!$earning || $withdrawal->amount > $earning->available_balance) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance in driver account'
                    ], 422);
                }

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

                // ✅ SEND NOTIFICATION TO DRIVER - APPROVED
                Notification::create([
                    'user_id' => $withdrawal->driver_id,
                    'type' => 'WITHDRAWAL_APPROVED',
                    'title' => 'Withdrawal Approved',
                    'message' => 'Your withdrawal request of £' . number_format($withdrawal->amount, 2) . ' has been approved. The amount will be transferred to your bank account within 2-3 business days.',
                    'data' => json_encode([
                        'withdrawal_id' => $withdrawal->id,
                        'amount' => $withdrawal->amount,
                        'bank_name' => $withdrawal->bankDetails->bank_name,
                        'approved_at' => now()->toDateTimeString()
                    ]),
                    'is_read' => false
                ]);
            } else {
                // ✅ SEND NOTIFICATION TO DRIVER - REJECTED
                Notification::create([
                    'user_id' => $withdrawal->driver_id,
                    'type' => 'WITHDRAWAL_REJECTED',
                    'title' => 'Withdrawal Rejected',
                    'message' => 'Your withdrawal request of £' . number_format($withdrawal->amount, 2) . ' has been rejected. Reason: ' . $request->rejection_reason,
                    'data' => json_encode([
                        'withdrawal_id' => $withdrawal->id,
                        'amount' => $withdrawal->amount,
                        'rejection_reason' => $request->rejection_reason,
                        'rejected_at' => now()->toDateTimeString()
                    ]),
                    'is_read' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Withdrawal request {$request->status} successfully",
                'data' => [
                    'withdrawal' => $withdrawal->load(['driver', 'bankDetails', 'admin'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get Withdrawal Statistics
     * GET /api/admin/withdrawal-stats
     */
    public function getWithdrawalStats()
    {
        $stats = [
            'total_withdrawals' => Withdrawal::count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'approved_withdrawals' => Withdrawal::where('status', 'approved')->count(),
            'rejected_withdrawals' => Withdrawal::where('status', 'rejected')->count(),
            'total_amount_withdrawn' => Withdrawal::where('status', 'approved')->sum('amount'),
            'pending_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}