@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Drivers</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalDrivers }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Active Jobs</p>
                <p class="text-3xl font-bold text-gray-900">{{ $activeJobs }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8.16 2.75a.75.75 0 00-1.32 0l-3.5 9.5A.75.75 0 004.5 13h11a.75.75 0 00.66-1.25l-3.5-9.5z"></path></svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart (Last 7 Days) -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue (Last 7 Days)</h3>
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Jobs Status Distribution -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jobs Status Distribution</h3>
        <div style="height: 300px;">
            <canvas id="jobsStatusChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Monthly Revenue Trend -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue (Last 6 Months)</h3>
        <div style="height: 300px;">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>

    <!-- User Growth -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth (Last 30 Days)</h3>
        <div style="height: 300px;">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Jobs</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentJobs as $job)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">#{{ $job->job_number }}</p>
                            <p class="text-sm text-gray-600">{{ $job->service->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full @if($job->status === 'completed') bg-green-100 text-green-800 @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($job->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $job->created_at->format('M d, Y H:i') }}</p>
                </div>
            @empty
                <p class="px-6 py-4 text-gray-500">No jobs yet</p>
            @endforelse
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Jobs →</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentTransactions as $transaction)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</p>
                            <p class="text-sm text-gray-600">{{ $transaction->customer->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full @if($transaction->status === 'completed') bg-green-100 text-green-800 @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
            @empty
                <p class="px-6 py-4 text-gray-500">No transactions yet</p>
            @endforelse
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Transactions →</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Customers</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $totalCustomers }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Completed Jobs</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $completedJobs }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Services</h3>
        <p class="text-4xl font-bold text-gray-900">{{ $totalServices }}</p>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart (Last 7 Days)
    const revenueData = @json($revenueChart ?? []);
    if (revenueData.length > 0) {
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map(d => d.date),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: revenueData.map(d => d.total),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Jobs Status Chart
    const jobsStatusData = @json($jobsStatusChart ?? []);
    if (jobsStatusData.length > 0) {
        const jobsStatusCtx = document.getElementById('jobsStatusChart');
        if (jobsStatusCtx) {
            new Chart(jobsStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: jobsStatusData.map(d => d.status),
                    datasets: [{
                        data: jobsStatusData.map(d => d.count),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Monthly Revenue Chart
    const monthlyRevenueData = @json($monthlyRevenue ?? []);
    if (monthlyRevenueData.length > 0) {
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
        if (monthlyRevenueCtx) {
            new Chart(monthlyRevenueCtx, {
                type: 'bar',
                data: {
                    labels: monthlyRevenueData.map(d => d.month),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: monthlyRevenueData.map(d => d.total),
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // User Growth Chart
    const userGrowthData = @json($userGrowth ?? []);
    if (userGrowthData.length > 0) {
        const userGrowthCtx = document.getElementById('userGrowthChart');
        if (userGrowthCtx) {
            new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: userGrowthData.map(d => d.date),
                    datasets: [{
                        label: 'New Users',
                        data: userGrowthData.map(d => d.count),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endsection