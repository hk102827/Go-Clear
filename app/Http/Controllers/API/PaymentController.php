<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log; // <-- Add this line

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Payment Intent
     * POST /api/v1/jobs/{job}/create-payment-intent
     */
    public function createPaymentIntent(Request $request, Job $job)
{
    // ✅ Only customer can create payment
    if ($job->customer_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    // ✅ Job must be completed
    if ($job->status !== 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'Job is not completed yet'
        ], 400);
    }

    // ✅ Check if already paid
    $existingPayment = Transaction::where('job_id', $job->id)
        ->where('status', 'completed')
        ->exists();

    if ($existingPayment) {
        return response()->json([
            'success' => false,
            'message' => 'Payment already completed'
        ], 400);
    }

    // ✅ Get final price (not estimated)
    if (!$job->final_price || $job->final_price <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Job price not set'
        ], 400);
    }

    try {
        $paymentIntent = PaymentIntent::create([
            'amount' => round($job->final_price * 100), // ✅ Use final_price
            'currency' => 'gbp',
            'description' => "Payment for Job #{$job->job_number}",
            'metadata' => [
                'job_id' => $job->id,
                'job_number' => $job->job_number,
                'customer_id' => $job->customer_id,
                'driver_id' => $job->driver_id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // ✅ Update transaction with payment_intent_id
        Transaction::where('job_id', $job->id)
            ->where('status', 'pending')
            ->update(['gateway_ref' => $paymentIntent->id]);

        return response()->json([
            'success' => true,
            'data' => [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $job->final_price,
                'currency' => 'gbp',
            ]
        ]);

    } catch (ApiErrorException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Payment creation failed: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Check Payment Status
     * GET /api/v1/payment-intent/{paymentIntentId}/status
     */
    public function checkPaymentStatus(Request $request, $paymentIntentId)
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $intent->status, // succeeded, processing, requires_payment_method, etc.
                    'amount' => $intent->amount / 100,
                    'currency' => $intent->currency,
                    'payment_method' => $intent->payment_method,
                ]
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment intent not found'
            ], 404);
        }
    }

    /**
     * Cancel Payment Intent
     * POST /api/v1/payment-intent/{paymentIntentId}/cancel
     */
    public function cancelPayment(Request $request, $paymentIntentId)
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            // Only cancel if not already succeeded
            if ($intent->status === 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel completed payment'
                ], 400);
            }

            $intent->cancel();

            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully'
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stripe Webhook Handler (Optional but recommended)
     * POST /api/v1/stripe/webhook
     */
public function handleWebhook(Request $request)
{
    $endpoint_secret = config('services.stripe.webhook_secret');
    
    \Log::info('=== WEBHOOK RECEIVED ===');
    \Log::info('Environment: ' . app()->environment());
    
    // ✅ For Stripe CLI, verify signature properly
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');

    try {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } catch (\UnexpectedValueException $e) {
        \Log::error('Invalid payload: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid payload'], 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        \Log::error('Invalid signature: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    \Log::info('Event Type: ' . $event->type);
    \Log::info('Event Data: ', (array) $event->data->object);

    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            
            \Log::info('Processing payment success', [
                'payment_intent_id' => $paymentIntent->id,
                'metadata' => (array) $paymentIntent->metadata,
                'job_id' => $paymentIntent->metadata->job_id ?? 'N/A'
            ]);
            
            // ✅ Find transaction by gateway_ref first
            $transaction = Transaction::where('gateway_ref', $paymentIntent->id)->first();
            
            if (!$transaction) {
                // ✅ Fallback: find by job_id from metadata
                $jobId = isset($paymentIntent->metadata->job_id) 
                    ? $paymentIntent->metadata->job_id 
                    : null;
                
                if ($jobId) {
                    $transaction = Transaction::where('job_id', $jobId)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                }
            }

            if ($transaction) {
                \Log::info('Transaction found: ' . $transaction->transaction_id);
                
                $transaction->update([
                    'gateway_ref' => $paymentIntent->id,
                    'status' => 'completed'
                ]);
                
                // ✅ Update driver earnings
                $earnings = \App\Models\DriverEarning::firstOrCreate(
                    ['driver_id' => $transaction->driver_id],
                    ['total_earnings' => 0, 'available_balance' => 0, 'withdrawn_amount' => 0]
                );

                $earnings->increment('total_earnings', $transaction->driver_earning);
                $earnings->increment('available_balance', $transaction->driver_earning);
                
                \Log::info('✅ Payment completed successfully', [
                    'transaction_id' => $transaction->transaction_id,
                    'driver_earning' => $transaction->driver_earning
                ]);
            } else {
                \Log::error('❌ Transaction not found', [
                    'payment_intent_id' => $paymentIntent->id,
                    'job_id' => $paymentIntent->metadata->job_id ?? 'N/A'
                ]);
            }
            break;

        case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;
            
            $transaction = Transaction::where('gateway_ref', $paymentIntent->id)->first();
            
            if (!$transaction) {
                $jobId = isset($paymentIntent->metadata->job_id) 
                    ? $paymentIntent->metadata->job_id 
                    : null;
                
                if ($jobId) {
                    $transaction = Transaction::where('job_id', $jobId)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                }
            }

            if ($transaction) {
                $transaction->update([
                    'gateway_ref' => $paymentIntent->id,
                    'status' => 'failed'
                ]);
                \Log::warning('Payment failed for Transaction: ' . $transaction->transaction_id);
            }
            break;

        case 'payment_intent.created':
        case 'charge.succeeded':
        case 'charge.updated':
            // ✅ Ignore these events
            \Log::info('Ignoring event type: ' . $event->type);
            break;

        default:
            \Log::info('Unhandled event type: ' . $event->type);
    }

    return response()->json(['status' => 'success'], 200);
}

}