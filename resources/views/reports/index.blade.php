@extends('layouts.app')

@section('content')
    <div class="reports-container">
        <!-- Header Section -->
        <div class="reports-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        </div>
                        <div>
                            <h2 class="mb-1">Attendance Reports</h2>
                            <p class="text-white-50 mb-0">Generate and analyze attendance data</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <div class="realtime-indicator">
                            <span class="pulse-dot"></span>
                            <span class="realtime-text">LIVE</span>
                            <span class="update-time" id="lastUpdate">Updated just now</span>
                        </div>
                        <button class="btn btn-export" id="exportBtn">
                            <i class="bi bi-download me-2"></i>
                            Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section mb-4">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="filter-label">Report Type</label>
                    <select class="form-select filter-select" id="reportType">
                        <option value="daily" selected>Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly">Monthly Report</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="filter-label">Select Date</label>
                    <input type="date" class="form-control filter-select" id="startDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-lg-3">
                    <label class="filter-label">Grade Level</label>
                    <select class="form-select filter-select" id="gradeFilter" name="grade_filter">
                        <option value="">All Grades</option>
                        <option value="7">Grade 7</option>
                        <option value="8">Grade 8</option>
                        <option value="9">Grade 9</option>
                        <option value="10">Grade 10</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <button class="btn btn-primary" id="generateReport">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Generate Report
                        </button>
                        <button class="btn btn-outline-secondary" id="resetFilters">
                            <i class="bi bi-x-circle me-2"></i>
                            Reset
                        </button>
                        <div class="vr" style="height: 38px;"></div>
                        <div class="form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" id="autoRefreshToggle" checked>
                            <label class="form-check-label" for="autoRefreshToggle">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                <strong>Auto-refresh</strong> <span class="text-muted">(every 30s)</span>
                            </label>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" id="refreshNow">
                            <i class="bi bi-arrow-clockwise"></i> Refresh Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="summary-stat-card card-blue">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Days</div>
                        <div class="stat-number" id="totalDays">0</div>
                        <div class="stat-trend">
                            <i class="bi bi-calendar3"></i>
                            This period
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat-card card-green">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Average Attendance</div>
                        <div class="stat-number" id="avgAttendance">0%</div>
                        <div class="stat-trend positive">
                            <i class="bi bi-arrow-up"></i>
                            <span id="attendanceTrend">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat-card card-orange">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Absences</div>
                        <div class="stat-number" id="totalAbsences">0</div>
                        <div class="stat-trend negative">
                            <i class="bi bi-arrow-down"></i>
                            <span id="absenceRate">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-stat-card card-purple">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Perfect Attendance</div>
                        <div class="stat-number" id="perfectAttendance">0</div>
                        <div class="stat-trend">
                            <i class="bi bi-people"></i>
                            Students
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <!-- Attendance by Grade Level Line Chart -->
            <div class="col-lg-9">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h5 class="chart-title">
                                <i class="bi bi-graph-up me-2"></i>
                                Attendance by Grade Level
                            </h5>
                            <p class="chart-subtitle">Daily attendance rate per grade over selected period</p>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="trendChart" height="87"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution -->
            <div class="col-lg-3">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h5 class="chart-title">
                                <i class="bi bi-pie-chart-fill me-2"></i>
                                By Grade Level
                            </h5>
                            <p class="chart-subtitle">Student distribution</p>
                        </div>
                    </div>
                    <div class="chart-body text-center">
                        <canvas id="gradeChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content export-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="bi bi-download me-2"></i>
                        Export Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="export-options">
                        <button class="export-option-btn" id="exportPresentStudentsBtn">
                            <i class="bi bi-file-pdf"></i>
                            <div>
                                <span>Present Students Report (PDF)</span>
                                <small>List of present students for selected date</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .reports-container {
            padding: 2rem 0;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reports-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
            padding: 2rem;
            border-radius: 16px;
            color: white;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .btn-export {
            background: white;
            color: #667eea;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            color: #667eea;
        }

        .realtime-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 2px solid rgba(239, 68, 68, 0.3);
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .realtime-text {
            font-weight: 700;
            color: #ef4444;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .update-time {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

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
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes dataUpdate {
            0% { background: rgba(16, 185, 129, 0.2); }
            100% { background: transparent; }
        }

        .data-updated {
            animation: dataUpdate 1s ease;
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .filter-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.625rem 1rem;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .summary-stat-card {
            background: white;
            padding: 1.75rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 1.25rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .summary-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .card-blue  { border-color: #3b82f6; }
        .card-green { border-color: #10b981; }
        .card-orange{ border-color: #f59e0b; }
        .card-purple{ border-color: #8b5cf6; }

        .stat-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .card-blue .stat-icon-wrapper   { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .card-green .stat-icon-wrapper  { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .card-orange .stat-icon-wrapper { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .card-purple .stat-icon-wrapper { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        .stat-content { flex: 1; }

        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .stat-trend {
            font-size: 0.8125rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-trend.positive { color: #10b981; }
        .stat-trend.negative { color: #ef4444; }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f3f4f6;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .chart-title i { color: #3b82f6; }

        .chart-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0.25rem 0 0 0;
        }

        .export-modal .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .export-options { display: grid; gap: 1rem; }

        .export-option-btn {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .export-option-btn:hover {
            background: white;
            border-color: #3b82f6;
            transform: translateX(4px);
        }

        .export-option-btn i { font-size: 2.5rem; color: #3b82f6; }
        .export-option-btn span { font-weight: 700; color: #111827; display: block; }
        .export-option-btn small { color: #6b7280; display: block; margin-top: 0.25rem; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        let autoRefreshEnabled = true;
        let refreshInterval = null;
        let lastUpdateTime = new Date();
        const REFRESH_INTERVAL = 30000;

        let trendChart;
        let gradeChart;

        // Colors per grade level
        const gradeColors = {
            'Grade 7':  { border: '#3b82f6', bg: 'rgba(59,130,246,0.1)'  },
            'Grade 8':  { border: '#10b981', bg: 'rgba(16,185,129,0.1)'  },
            'Grade 9':  { border: '#f59e0b', bg: 'rgba(245,158,11,0.1)'  },
            'Grade 10': { border: '#8b5cf6', bg: 'rgba(139,92,246,0.1)'  },
            'Grade 11': { border: '#ef4444', bg: 'rgba(239,68,68,0.1)'   },
            'Grade 12': { border: '#06b6d4', bg: 'rgba(6,182,212,0.1)'   },
        };

        // Multi-line chart: one line per grade
        function createTrendChart(labels, datasets) {
            const ctx = document.getElementById('trendChart').getContext('2d');

            if (trendChart) trendChart.destroy();

            const chartDatasets = (datasets || []).map(ds => ({
                label: ds.label,
                data: ds.values,
                borderColor: gradeColors[ds.label]?.border || '#3b82f6',
                backgroundColor: gradeColors[ds.label]?.bg || 'rgba(59,130,246,0.1)',
                tension: 0.4,
                fill: false,
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5
            }));

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels || [],
                    datasets: chartDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + '%'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: { callback: value => value + '%' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        }
                    }
                }
            });
        }

        function createGradeChart(labels, values) {
            const ctx = document.getElementById('gradeChart').getContext('2d');

            if (gradeChart) gradeChart.destroy();

            gradeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels || ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
                    datasets: [{
                        data: values || [0, 0, 0, 0, 0, 0],
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b',
                            '#8b5cf6', '#ef4444', '#06b6d4'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 15, usePointStyle: true }
                        }
                    }
                }
            });
        }

        // Initialize with empty data
        createTrendChart([], []);
        createGradeChart();

        async function fetchLatestData() {
            try {
                const startDate = document.getElementById('startDate').value;
                const grade = document.getElementById('gradeFilter').value;
                const reportType = document.getElementById('reportType').value;
                let endDate = startDate;

                if (reportType === 'weekly') {
                    const date = new Date(startDate);
                    date.setDate(date.getDate() + 6);
                    endDate = date.toISOString().split('T')[0];
                } else if (reportType === 'monthly') {
                    const date = new Date(startDate);
                    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                    endDate = lastDay.toISOString().split('T')[0];
                }

                const params = new URLSearchParams({ start_date: startDate, end_date: endDate });
                if (grade) params.append('grade', grade);

                const response = await fetch(`/reports/realtime-data?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch data');

                const data = await response.json();
                updateDashboard(data);
                updateLastUpdateTime();

            } catch (error) {
                console.error('Error fetching real-time data:', error);
                showErrorNotification('Failed to fetch latest data');
            }
        }

        function updateDashboard(data) {
            const summaryCards = document.querySelectorAll('.summary-stat-card');
            summaryCards.forEach(card => card.classList.add('updating'));

            setTimeout(() => {
                if (data.totalDays !== undefined) animateValue('totalDays', data.totalDays);
                if (data.avgAttendance !== undefined) animateValue('avgAttendance', data.avgAttendance, '%', 1);
                if (data.totalAbsences !== undefined) animateValue('totalAbsences', data.totalAbsences);
                if (data.perfectAttendance !== undefined) animateValue('perfectAttendance', data.perfectAttendance);

                if (data.totalStudents && data.totalAbsences) {
                    const absenceRate = ((data.totalAbsences / (data.totalStudents * data.totalDays)) * 100).toFixed(1);
                    document.getElementById('absenceRate').textContent = absenceRate + '% of total';
                }

                // Update grade-by-day multi-line chart
                if (data.gradeByDayData) {
                    createTrendChart(data.gradeByDayData.labels, data.gradeByDayData.datasets);
                }

                // Update doughnut chart
                if (data.gradeData && data.gradeData.labels && data.gradeData.values) {
                    createGradeChart(data.gradeData.labels, data.gradeData.values);
                }

                summaryCards.forEach(card => {
                    card.classList.remove('updating');
                    card.classList.add('data-updated');
                    setTimeout(() => card.classList.remove('data-updated'), 1000);
                });

                showUpdateNotification();
            }, 500);
        }

        function animateValue(elementId, endValue, suffix = '', decimals = 0) {
            const element = document.getElementById(elementId);
            if (!element) return;

            const startValue = parseFloat(element.textContent.replace(/[^\d.-]/g, '')) || 0;
            const duration = 500;
            const steps = 20;
            const increment = (endValue - startValue) / steps;
            let current = startValue;
            let step = 0;

            const timer = setInterval(() => {
                step++;
                current += increment;
                if (step >= steps) { current = endValue; clearInterval(timer); }
                element.textContent = current.toFixed(decimals) + suffix;
            }, duration / steps);
        }

        function updateLastUpdateTime() {
            lastUpdateTime = new Date();
            const element = document.getElementById('lastUpdate');
            if (element) element.textContent = 'Updated just now';
        }

        function updateRelativeTime() {
            const element = document.getElementById('lastUpdate');
            if (!element) return;

            const diffSecs = Math.floor((new Date() - lastUpdateTime) / 1000);
            const diffMins = Math.floor(diffSecs / 60);

            if (diffSecs < 10) element.textContent = 'Updated just now';
            else if (diffSecs < 60) element.textContent = `Updated ${diffSecs}s ago`;
            else if (diffMins < 60) element.textContent = `Updated ${diffMins}m ago`;
            else element.textContent = `Updated ${Math.floor(diffMins / 60)}h ago`;
        }

        function showUpdateNotification() {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-check-circle me-2"></i>Data updated successfully</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                    </div>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function showErrorNotification(message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show align-items-center text-white bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-exclamation-circle me-2"></i>${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                    </div>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function startAutoRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(() => {
                if (autoRefreshEnabled && !document.hidden) fetchLatestData();
            }, REFRESH_INTERVAL);
            setInterval(updateRelativeTime, 5000);
        }

        document.getElementById('autoRefreshToggle').addEventListener('change', function() {
            autoRefreshEnabled = this.checked;
            if (autoRefreshEnabled) {
                startAutoRefresh();
                Swal.fire({ icon: 'success', title: 'Auto-refresh Enabled', text: 'Reports will update automatically every 30 seconds', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            } else {
                if (refreshInterval) clearInterval(refreshInterval);
                Swal.fire({ icon: 'info', title: 'Auto-refresh Disabled', text: 'You can manually refresh using the button', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
            }
        });

        document.getElementById('refreshNow').addEventListener('click', function() {
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...';
            fetchLatestData();
            setTimeout(() => { btn.disabled = false; btn.innerHTML = originalHTML; }, 1000);
        });

        document.getElementById('exportBtn').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('exportModal'));
            modal.show();
        });

        document.getElementById('exportPresentStudentsBtn').addEventListener('click', function() {
            const selectedDate = document.getElementById('startDate').value;
            const gradeFilter = document.getElementById('gradeFilter').value;

            const params = new URLSearchParams({ date: selectedDate });
            if (gradeFilter) params.append('grade_filter', 'Grade ' + gradeFilter);

            Swal.fire({
                title: 'Generating Present Students Report...',
                html: `Preparing PDF for ${selectedDate}${gradeFilter ? ' (Grade ' + gradeFilter + ')' : ' (All Grades)'}`,
                timerProgressBar: true,
                didOpen: () => Swal.showLoading(),
                timer: 1500
            }).then(() => {
                window.location.href = `/reports/export-present?${params.toString()}`;
                Swal.fire({ icon: 'success', title: 'Report Generated!', text: 'Your Present Students PDF is ready for download', confirmButtonColor: '#3b82f6', timer: 2000 });
            });

            const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
            if (modal) modal.hide();
        });

        document.getElementById('generateReport').addEventListener('click', function() {
            Swal.fire({
                title: 'Generating Report...',
                html: 'Please wait while we compile your data',
                timerProgressBar: true,
                didOpen: () => Swal.showLoading(),
                timer: 1000
            }).then(() => {
                fetchLatestData();
                Swal.fire({ icon: 'success', title: 'Report Generated!', text: 'Your report has been updated with the selected filters', confirmButtonColor: '#3b82f6', timer: 2000 });
            });
        });

        document.getElementById('resetFilters').addEventListener('click', function() {
            document.getElementById('reportType').value = 'daily';
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('gradeFilter').value = '';
            fetchLatestData();
        });

        document.getElementById('reportType').addEventListener('change', function() {
            const today = new Date();
            const startDateInput = document.getElementById('startDate');
            if (this.value === 'daily') {
                startDateInput.value = today.toISOString().split('T')[0];
            } else if (this.value === 'weekly') {
                const weekAgo = new Date(today.getTime() - 6 * 24 * 60 * 60 * 1000);
                startDateInput.value = weekAgo.toISOString().split('T')[0];
            } else if (this.value === 'monthly') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                startDateInput.value = firstDay.toISOString().split('T')[0];
            }
            fetchLatestData();
        });

        document.getElementById('gradeFilter').addEventListener('change', fetchLatestData);
        document.getElementById('startDate').addEventListener('change', fetchLatestData);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && autoRefreshEnabled) fetchLatestData();
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            startAutoRefresh();
            setTimeout(fetchLatestData, 500);
        });
    </script>
@endsection