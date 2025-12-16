<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Transaction;
use App\Models\Service;

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

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalDrivers',
            'totalCustomers',
            'activeJobs',
            'completedJobs',
            'totalRevenue',
            'totalServices',
            'recentJobs',
            'recentTransactions'
        ));
    }
}
