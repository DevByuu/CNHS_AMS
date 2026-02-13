@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-white-50 mb-0">Here's what's happening with your attendance system today.</p>
            </div>
            <div class="text-end">
                <div class="text-light small">{{ now()->format('l, F j, Y') }}</div>
                <div class="text-warning fw-semibold" id="currentTime"></div>
            </div>
        </div>
    </div>


    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-card-blue">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value" id="totalStudents">{{ $totalStudents ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-card-green">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Present Today</div>
                    <div class="stat-value" id="presentToday">{{ $presentToday ?? 0 }}</div>
                    <div class="stat-change positive" id="presentPercentage">
                        {{ $presentPercentage ?? 0 }}% attendance
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-card-red">
                <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Absent Today</div>
                    <div class="stat-value" id="absentToday">{{ $absentToday ?? 0 }}</div>
                    <div class="stat-change negative" id="absentPercentage">
                        {{ $absentPercentage ?? 0 }}% of total
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card stat-card-orange">
                <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Late Arrivals</div>
                    <div class="stat-value" id="lateToday">{{ $lateToday ?? 0 }}</div>
                    <div class="stat-change" id="latePercentage">
                        {{ $latePercentage ?? 0 }}% of present
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="bi bi-graph-up me-2"></i>Attendance Trend (Last 7 Days)
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshDashboard">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="chart-body">
                    <canvas id="attendanceTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card text-center">
                <h5 class="chart-title"><i class="bi bi-pie-chart-fill me-2"></i>Today's Distribution</h5>
                <canvas id="attendanceDonutChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- --- CSS --- -->
<style>
    /* Fade In Animation */
    .dashboard-container {
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
        padding: 2rem;
        border-radius: 16px;
        color: white;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        transition: width 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card:hover::before {
        width: 100%;
        opacity: 0.05;
    }

    .stat-card-blue::before {
        background: #3b82f6;
    }

    .stat-card-green::before {
        background: #10b981;
    }

    .stat-card-red::before {
        background: #ef4444;
    }

    .stat-card-orange::before {
        background: #f59e0b;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .stat-card-blue .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .stat-card-green .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .stat-card-red .stat-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .stat-card-orange .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .stat-details {
        flex: 1;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .stat-change {
        font-size: 0.8125rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-change.positive {
        color: #10b981;
    }

    .stat-change.negative {
        color: #ef4444;
    }

    /* Charts */
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Loading State */
    .updating {
        position: relative;
        opacity: 0.6;
        pointer-events: none;
    }

    .updating::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        border: 3px solid #f3f4f6;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    @media (max-width:768px) {
        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>

<!-- --- JS Charts --- -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Update Time
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Initialize Charts
    let trendChart;
    let donutChart;

    // Create Attendance Trend Chart
    function createTrendChart(labels, presentData, absentData) {
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');

        if (trendChart) {
            trendChart.destroy();
        }

        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Present',
                        data: presentData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Absent',
                        data: absentData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Create Donut Chart
    function createDonutChart(present, absent, late) {
        const donutCtx = document.getElementById('attendanceDonutChart').getContext('2d');

        if (donutChart) {
            donutChart.destroy();
        }

        donutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [present, absent, late],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                }
            }
        });
    }

    // Fetch Dashboard Data
    async function fetchDashboardData() {
        try {
            const response = await fetch('/api/dashboard/stats', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch dashboard data');
            }

            const data = await response.json();
            updateDashboard(data);

        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }

    // Update Dashboard
    function updateDashboard(data) {
        // Update stat cards with animation
        if (data.totalStudents !== undefined) {
            animateValue('totalStudents', data.totalStudents);
        }
        if (data.presentToday !== undefined) {
            animateValue('presentToday', data.presentToday);
        }
        if (data.absentToday !== undefined) {
            animateValue('absentToday', data.absentToday);
        }
        if (data.lateToday !== undefined) {
            animateValue('lateToday', data.lateToday);
        }

        // Update percentages
        if (data.presentPercentage !== undefined) {
            document.getElementById('presentPercentage').textContent = data.presentPercentage + '% attendance';
        }
        if (data.absentPercentage !== undefined) {
            document.getElementById('absentPercentage').textContent = data.absentPercentage + '% of total';
        }
        if (data.latePercentage !== undefined) {
            document.getElementById('latePercentage').textContent = data.latePercentage + '% of present';
        }

        // Update charts
        if (data.trendLabels && data.trendPresent && data.trendAbsent) {
            createTrendChart(data.trendLabels, data.trendPresent, data.trendAbsent);
        }

        if (data.presentToday !== undefined) {
            createDonutChart(
                data.presentToday || 0,
                data.absentToday || 0,
                data.lateToday || 0
            );
        }
    }

    // Animate value changes
    function animateValue(elementId, endValue, duration = 500) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = parseInt(element.textContent) || 0;
        const steps = 20;
        const increment = (endValue - startValue) / steps;
        let current = startValue;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += increment;

            if (step >= steps) {
                current = endValue;
                clearInterval(timer);
            }

            element.textContent = Math.round(current);
        }, duration / steps);
    }

    // Refresh button
    document.getElementById('refreshDashboard')?.addEventListener('click', function() {
        const btn = this;
        const originalHTML = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...';

        fetchDashboardData();

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }, 1000);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // ✅ IMMEDIATELY fetch fresh data on page load
        fetchDashboardData();

        // ✅ Auto-refresh every 30 seconds
        setInterval(fetchDashboardData, 30000);
    });
</script>
@endsection
