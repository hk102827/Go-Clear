<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Transaction;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $activeJobs = Job::where('status', 'assigned')->orWhere('status', 'in_progress')->count();
        $completedJobs = Job::where('status', 'completed')->count();
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $totalServices = Service::count();
        
        $recentJobs = Job::latest()->take(10)->get();
        $recentTransactions = Transaction::latest()->take(10)->get();

        // Chart Data - Last 7 days revenue
        $revenueChart = Transaction::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => date('M d', strtotime($item->date)),
                    'total' => (float) $item->total
                ];
            });

        // Jobs Status Distribution
        $jobsStatusChart = Job::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'count' => $item->count
                ];
            });

        // Monthly Revenue Trend (Last 6 months)
        $monthlyRevenue = Transaction::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => date('M Y', strtotime($item->month . '-01')),
                    'total' => (float) $item->total
                ];
            });

        // User Growth (Last 30 days)
        $userGrowth = User::where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => date('M d', strtotime($item->date)),
                    'count' => $item->count
                ];
            });

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalDrivers',
            'totalCustomers',
            'activeJobs',
            'completedJobs',
            'totalRevenue',
            'totalServices',
            'recentJobs',
            'recentTransactions',
            'revenueChart',
            'jobsStatusChart',
            'monthlyRevenue',
            'userGrowth'
        ));
    }

    /**
     * Send promotion to customers
     */
    public function sendPromotion(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'promo_code' => 'required|string|max:50',
            'expiry_date' => 'required|date|after:today',
            'target' => 'required|in:all,active,inactive'
        ]);

        // Get target customers
        $query = User::where('role', 'customer');

        if ($validated['target'] === 'active') {
            $query->whereHas('customerJobs', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            });
        } elseif ($validated['target'] === 'inactive') {
            $query->whereDoesntHave('customerJobs', function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            });
        }

        $customers = $query->get();
        $customerIds = $customers->pluck('id')->toArray();

        if (empty($customerIds)) {
            return back()->with('error', 'No customers found matching the selected criteria');
        }

        // Send bulk promotion notification
        NotificationService::sendToMultiple(
            $customerIds,
            'PROMOTION',
            $validated['title'],
            "Use code: {$validated['promo_code']} (valid till " . date('d M', strtotime($validated['expiry_date'])) . ").",
            [
                'promo_code' => $validated['promo_code'],
                'expiry_date' => $validated['expiry_date'],
            ]
        );

        return back()->with('success', "Promotion sent to {$customers->count()} customers successfully");
    }
}