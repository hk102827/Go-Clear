<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    /**
     * Show all notifications (admin view)
     */
    public function index()
    {
        $notifications = Notification::with('user')
            ->latest()
            ->paginate(50);

        return view('admin.notification.index', compact('notifications'));
    }

    /**
     * Show form to send notification
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $drivers = User::where('role', 'driver')->get();

        return view('admin.notification.create', compact('customers', 'drivers'));
    }

    /**
     * Send single notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:GENERAL,PROMOTION,SYSTEM,JOB_UPDATE',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($validated['user_id']);

        NotificationService::custom(
            $user,
            $validated['type'],
            $validated['title'],
            $validated['message']
        );

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification sent successfully');
    }

    /**
     * Send bulk notification
     */
    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,customers,drivers,active_customers,verified_drivers',
            'type' => 'required|in:GENERAL,PROMOTION,SYSTEM',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        // Build user query based on target
        $query = User::query();

        switch ($validated['target']) {
            case 'customers':
                $query->where('role', 'customer');
                break;
            case 'drivers':
                $query->where('role', 'driver');
                break;
            case 'active_customers':
                $query->where('role', 'customer')
                    ->whereHas('customerJobs', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                break;
            case 'verified_drivers':
                $query->where('role', 'driver')
                    ->whereHas('driverProfile', function ($q) {
                        $q->where('verification_status', 'verified');
                    });
                break;
        }

        $userIds = $query->pluck('id')->toArray();

        NotificationService::sendToMultiple(
            $userIds,
            $validated['type'],
            $validated['title'],
            $validated['message']
        );

        return back()->with('success', "Notification sent to " . count($userIds) . " users");
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted successfully');
    }

        public function bell()
        {
            $notifications = Notification::where('user_id', auth()->id())
                ->where('type', 'ADMIN')
                ->where('is_read', false)
                ->latest()
                ->get();

            return response()->json([
                'notifications' => $notifications,
                'unread' => $notifications->count()
            ]);
        }

    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }


}
