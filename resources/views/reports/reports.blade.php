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
        <form id="reportForm">
            @csrf
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="filter-label">Report Type</label>
                    <select class="form-select filter-select" id="reportType" name="report_type">
                        <option value="daily">Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly" selected>Monthly Report</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="filter-label">Start Date</label>
                    <input type="date" class="form-control filter-select" id="startDate" name="start_date" value="{{ date('Y-m-01') }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="filter-label">End Date</label>
                    <input type="date" class="form-control filter-select" id="endDate" name="end_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="filter-label">Grade Level</label>
                    <select class="form-select filter-select" id="gradeFilter" name="grade">
                        <option value="">All Grades</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <button type="button" class="btn btn-primary" id="generatePdfBtn">
                            <i class="bi bi-file-pdf me-2"></i>
                            Generate PDF Report
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetFilters">
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
                        <button type="button" class="btn btn-outline-primary btn-sm" id="refreshNow">
                            <i class="bi bi-arrow-clockwise"></i> Refresh Now
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
                    <div class="stat-number" id="totalDays">{{ $totalDays ?? 20 }}</div>
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
                    <div class="stat-number" id="avgAttendance">{{ $avgAttendance ?? '87.5' }}%</div>
                    <div class="stat-trend positive">
                        <i class="bi bi-arrow-up"></i>
                        +2.3% from last period
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
                    <div class="stat-number" id="totalAbsences">{{ $totalAbsences ?? 245 }}</div>
                    <div class="stat-trend negative">
                        <i class="bi bi-arrow-down"></i>
                        12.5% of total
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
                    <div class="stat-number" id="perfectAttendance">{{ $perfectAttendance ?? 45 }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-people"></i>
                        Students
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rest of your existing content (charts, tabs, etc.) -->
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Generate PDF Report Button
    document.getElementById('generatePdfBtn').addEventListener('click', function() {
        const reportType = document.getElementById('reportType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const grade = document.getElementById('gradeFilter').value;

        // Validate dates
        if (new Date(endDate) < new Date(startDate)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'End date must be after or equal to start date',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Generating PDF Report...',
            html: `
                <div class="text-start">
                    <p><strong>Report Type:</strong> ${reportType.charAt(0).toUpperCase() + reportType.slice(1)}</p>
                    <p><strong>Date Range:</strong> ${startDate} to ${endDate}</p>
                    <p><strong>Grade Level:</strong> ${grade || 'All Grades'}</p>
                </div>
                <div class="mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        // Build URL with parameters
        const params = new URLSearchParams({
            format: 'pdf',
            report_type: reportType,
            start_date: startDate,
            end_date: endDate
        });

        if (grade) {
            params.append('grade', grade);
        }

        // Trigger download
        window.location.href = `/reports/export?${params.toString()}`;

        // Close loading after delay
        setTimeout(() => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'PDF Generated!',
                text: 'Your report is being downloaded',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1500);
    });

    // Export Button (shows format options)
    document.getElementById('exportBtn').addEventListener('click', function() {
        const reportType = document.getElementById('reportType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const grade = document.getElementById('gradeFilter').value;

        Swal.fire({
            title: 'Choose Export Format',
            html: `
                <div class="export-options-modal">
                    <button class="export-option-btn-modal" data-format="pdf">
                        <i class="bi bi-file-pdf" style="font-size: 2rem; color: #dc2626;"></i>
                        <div>
                            <strong>PDF Document</strong>
                            <small>Formatted attendance report</small>
                        </div>
                    </button>
                    <button class="export-option-btn-modal" data-format="csv">
                        <i class="bi bi-filetype-csv" style="font-size: 2rem; color: #3b82f6;"></i>
                        <div>
                            <strong>CSV File</strong>
                            <small>Spreadsheet format</small>
                        </div>
                    </button>
                    <button class="export-option-btn-modal" data-format="excel">
                        <i class="bi bi-file-excel" style="font-size: 2rem; color: #059669;"></i>
                        <div>
                            <strong>Excel Spreadsheet</strong>
                            <small>XLS format</small>
                        </div>
                    </button>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Close',
            width: '500px',
            didOpen: () => {
                document.querySelectorAll('.export-option-btn-modal').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const format = this.dataset.format;
                        Swal.close();

                        // Build URL
                        const params = new URLSearchParams({
                            format: format,
                            report_type: reportType,
                            start_date: startDate,
                            end_date: endDate
                        });

                        if (grade) {
                            params.append('grade', grade);
                        }

                        // Trigger download
                        window.location.href = `/reports/export?${params.toString()}`;

                        Swal.fire({
                            icon: 'success',
                            title: 'Generating Report...',
                            text: `Your ${format.toUpperCase()} file is being downloaded`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                });
            }
        });
    });

    // Report type change handler
    document.getElementById('reportType').addEventListener('change', function() {
        const value = this.value;
        const today = new Date();
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (value === 'daily') {
            const todayStr = today.toISOString().split('T')[0];
            startDateInput.value = todayStr;
            endDateInput.value = todayStr;
        } else if (value === 'weekly') {
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            startDateInput.value = weekAgo.toISOString().split('T')[0];
            endDateInput.value = today.toISOString().split('T')[0];
        } else if (value === 'monthly') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            startDateInput.value = firstDay.toISOString().split('T')[0];
            endDateInput.value = today.toISOString().split('T')[0];
        }
    });

    // Reset Filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('reportType').value = 'monthly';
        document.getElementById('startDate').value = '{{ date("Y-m-01") }}';
        document.getElementById('endDate').value = '{{ date("Y-m-d") }}';
        document.getElementById('gradeFilter').value = '';
    });

    // Auto-refresh functionality
    let autoRefreshEnabled = true;
    let refreshInterval = null;

    function fetchRealtimeData() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const grade = document.getElementById('gradeFilter').value;

        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate
        });

        if (grade) {
            params.append('grade', grade);
        }

        fetch(`/api/reports/realtime?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboard(data);
                    document.getElementById('lastUpdate').textContent = 'Updated just now';
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function updateDashboard(data) {
        if (data.totalDays !== undefined) {
            document.getElementById('totalDays').textContent = data.totalDays;
        }
        if (data.avgAttendance !== undefined) {
            document.getElementById('avgAttendance').textContent = data.avgAttendance + '%';
        }
        if (data.totalAbsences !== undefined) {
            document.getElementById('totalAbsences').textContent = data.totalAbsences;
        }
        if (data.perfectAttendance !== undefined) {
            document.getElementById('perfectAttendance').textContent = data.perfectAttendance;
        }
    }

    function startAutoRefresh() {
        if (refreshInterval) clearInterval(refreshInterval);
        refreshInterval = setInterval(() => {
            if (autoRefreshEnabled) {
                fetchRealtimeData();
            }
        }, 30000); // 30 seconds
    }

    document.getElementById('autoRefreshToggle').addEventListener('change', function() {
        autoRefreshEnabled = this.checked;
        if (autoRefreshEnabled) {
            startAutoRefresh();
            Swal.fire({
                icon: 'success',
                title: 'Auto-refresh Enabled',
                text: 'Reports will update every 30 seconds',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            if (refreshInterval) clearInterval(refreshInterval);
            Swal.fire({
                icon: 'info',
                title: 'Auto-refresh Disabled',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });

    document.getElementById('refreshNow').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...';
        
        fetchRealtimeData();
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh Now';
        }, 1000);
    });

    // Start auto-refresh
    startAutoRefresh();
</script>

<style>
.export-options-modal {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.export-option-btn-modal {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-align: left;
}

.export-option-btn-modal:hover {
    background: white;
    border-color: #3b82f6;
    transform: translateX(4px);
}

.export-option-btn-modal strong {
    display: block;
    color: #111827;
}

.export-option-btn-modal small {
    display: block;
    color: #6b7280;
    font-size: 0.875rem;
}

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

.card-blue { border-color: #3b82f6; }
.card-green { border-color: #10b981; }
.card-orange { border-color: #f59e0b; }
.card-purple { border-color: #8b5cf6; }

.stat-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.card-blue .stat-icon-wrapper {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.card-green .stat-icon-wrapper {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.card-orange .stat-icon-wrapper {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.card-purple .stat-icon-wrapper {
    background: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
}

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
}

.stat-trend.positive { color: #10b981; }
.stat-trend.negative { color: #ef4444; }

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
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.realtime-text {
    font-weight: 700;
    color: #ef4444;
    font-size: 0.75rem;
}

.update-time {
    font-size: 0.75rem;
    color: #6b7280;
}
</style>
@endsection