<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send driver arriving notification
     */
    public static function driverArriving(User $customer, $driverName, $minutesAway)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Driver Arriving Soon',
            'message' => "{$driverName} is {$minutesAway} minutes away",
            'data' => [
                'driver_name' => $driverName,
                'minutes_away' => $minutesAway,
            ],
        ]);
    }

    /**
     * Send job completed notification
     */
    public static function jobCompleted(User $customer, $jobId)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Job Completed',
            'message' => 'Your waste collection service has been completed.',
            'data' => [
                'job_id' => $jobId,
            ],
        ]);
    }

    /**
     * Send job started notification
     */
    public static function jobStarted(User $customer, $jobId, $driverName)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Job Started',
            'message' => "{$driverName} has started your waste collection service.",
            'data' => [
                'job_id' => $jobId,
                'driver_name' => $driverName,
            ],
        ]);
    }

    /**
     * Send job cancelled notification
     */
    public static function jobCancelled(User $customer, $jobId, $reason = null)
    {
        $message = 'Your booking has been cancelled.';
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Job Cancelled',
            'message' => $message,
            'data' => [
                'job_id' => $jobId,
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Send promotion notification
     */
    public static function promotion(User $customer, $title, $promoCode, $expiryDate)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'PROMOTION',
            'title' => $title,
            'message' => "Use code: {$promoCode} (valid till {$expiryDate}).",
            'data' => [
                'promo_code' => $promoCode,
                'expiry_date' => $expiryDate,
            ],
        ]);
    }

    /**
     * Send driver assigned notification
     */
    public static function driverAssigned(User $customer, $jobId, $driverName)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Driver Assigned',
            'message' => "{$driverName} has been assigned to your booking.",
            'data' => [
                'job_id' => $jobId,
                'driver_name' => $driverName,
            ],
        ]);
    }

    /**
     * Send payment received notification
     */
    public static function paymentReceived(User $customer, $jobId, $amount)
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => 'JOB_UPDATE',
            'title' => 'Payment Received',
            'message' => "Payment of £{$amount} has been received successfully.",
            'data' => [
                'job_id' => $jobId,
                'amount' => $amount,
            ],
        ]);
    }

    /**
     * Send custom notification
     */
    public static function custom(User $customer, $type, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $customer->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToMultiple(array $userIds, $type, $title, $message, $data = [])
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return Notification::insert($notifications);
    }
}