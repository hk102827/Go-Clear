<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\PaymentIntent as PaymentIntentModel;
use Exception;


class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Payment Intent
     */
    public function createPaymentIntent($jobId, $customerId, $amount, $currency = 'usd')
    {
        try {
            // Stripe payment intent create
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // cents me convert
                'currency' => $currency,
                'metadata' => [
                    'job_id' => $jobId,
                    'customer_id' => $customerId,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Database me save
            $dbIntent = PaymentIntentModel::create([
                'job_id' => $jobId,
                'customer_id' => $customerId,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
                'metadata' => [
                    'job_id' => $jobId,
                    'customer_id' => $customerId,
                ],
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $amount,
                'currency' => $currency,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve Payment Intent Status
     */
    public function getPaymentIntentStatus($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // Database update
            PaymentIntentModel::where('stripe_payment_intent_id', $paymentIntentId)
                ->update(['status' => $paymentIntent->status]);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel Payment Intent
     */
    public function cancelPaymentIntent($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::cancel($paymentIntentId);

            PaymentIntentModel::where('stripe_payment_intent_id', $paymentIntentId)
                ->update(['status' => 'canceled']);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}   

