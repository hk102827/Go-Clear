<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class NotificationController extends Controller
{

    public function index(Request $request)
    {
        try {
            $customer = Auth::user();
            
            $notifications = Notification::where('user_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Group by type
            $grouped = [
                'jobUpdates' => $notifications->where('type', 'JOB_UPDATE')->values(),
                'promotions' => $notifications->where('type', 'PROMOTION')->values(),
            ];

            $unreadCount = $notifications->where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'all' => $notifications,
                    'grouped' => $grouped,
                    'unreadCount' => $unreadCount,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single notification
     * GET /api/customer/notifications/{notification}
     */
    public function show(Notification $notification)
    {
        try {
            $customer = Auth::user();

            // Check if notification belongs to customer
            if ($notification->user_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $notification,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark notification as read
     * PATCH /api/customer/notifications/{notification}/read
     */
    public function markAsRead(Notification $notification)
    {
        try {
            $customer = Auth::user();

            if ($notification->user_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     * PATCH /api/customer/notifications/read-all
     */
    public function markAllAsRead()
    {
        try {
            $customer = Auth::user();

            Notification::where('user_id', $customer->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete notification
     * DELETE /api/customer/notifications/{notification}
     */
    public function destroy(Notification $notification)
    {
        try {
            $customer = Auth::user();

            if ($notification->user_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     * GET /api/customer/notifications/unread-count
     */
    public function unreadCount()
    {
        try {
            $customer = Auth::user();

            $unreadCount = Notification::where('user_id', $customer->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unreadCount' => $unreadCount,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching unread count',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}