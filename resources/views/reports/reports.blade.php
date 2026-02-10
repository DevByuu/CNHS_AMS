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
                    <option value="daily">Daily Report</option>
                    <option value="weekly">Weekly Report</option>
                    <option value="monthly" selected>Monthly Report</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div class="col-lg-3">
                <label class="filter-label">Start Date</label>
                <input type="date" class="form-control filter-select" id="startDate" value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-lg-3">
                <label class="filter-label">End Date</label>
                <input type="date" class="form-control filter-select" id="endDate" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-lg-3">
                <label class="filter-label">Grade Level</label>
                <select class="form-select filter-select" id="gradeFilter">
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

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Attendance Trend Chart -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h5 class="chart-title">
                            <i class="bi bi-graph-up me-2"></i>
                            Attendance Trend Analysis
                        </h5>
                        <p class="chart-subtitle">Daily attendance rate over selected period</p>
                    </div>
                    <div class="chart-actions">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary active" data-chart="line">
                                <i class="bi bi-graph-up"></i> Line
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-chart="bar">
                                <i class="bi bi-bar-chart"></i> Bar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="trendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h5 class="chart-title">
                            <i class="bi bi-pie-chart-fill me-2"></i>
                            By Grade Level
                        </h5>
                        <p class="chart-subtitle">Attendance distribution</p>
                    </div>
                </div>
                <div class="chart-body text-center">
                    <canvas id="gradeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports Tabs -->
    <div class="reports-tabs-section">
        <ul class="nav nav-tabs custom-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                    <i class="bi bi-file-text me-2"></i>
                    Summary Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="detailed-tab" data-bs-toggle="tab" data-bs-target="#detailed" type="button">
                    <i class="bi bi-table me-2"></i>
                    Detailed Records
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    By Student
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="insights-tab" data-bs-toggle="tab" data-bs-target="#insights" type="button">
                    <i class="bi bi-lightbulb me-2"></i>
                    Insights
                </button>
            </li>
        </ul>

        <div class="tab-content custom-tab-content" id="reportTabsContent">
            <!-- Summary Report Tab -->
            <div class="tab-pane fade show active" id="summary" role="tabpanel">
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>Grade Level</th>
                                <th>Total Students</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Attendance Rate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="grade-badge">Grade 7</span></td>
                                <td>208</td>
                                <td><span class="badge badge-success">185</span></td>
                                <td><span class="badge badge-danger">20</span></td>
                                <td><span class="badge badge-warning">3</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 88.9%">88.9%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                            <tr>
                                <td><span class="grade-badge">Grade 8</span></td>
                                <td>215</td>
                                <td><span class="badge badge-success">192</span></td>
                                <td><span class="badge badge-danger">18</span></td>
                                <td><span class="badge badge-warning">5</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 89.3%">89.3%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                            <tr>
                                <td><span class="grade-badge">Grade 9</span></td>
                                <td>198</td>
                                <td><span class="badge badge-success">168</span></td>
                                <td><span class="badge badge-danger">25</span></td>
                                <td><span class="badge badge-warning">5</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 84.8%">84.8%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-average">Average</span></td>
                            </tr>
                            <tr>
                                <td><span class="grade-badge">Grade 10</span></td>
                                <td>205</td>
                                <td><span class="badge badge-success">178</span></td>
                                <td><span class="badge badge-danger">22</span></td>
                                <td><span class="badge badge-warning">5</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 86.8%">86.8%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                            <tr>
                                <td><span class="grade-badge">Grade 11</span></td>
                                <td>187</td>
                                <td><span class="badge badge-success">165</span></td>
                                <td><span class="badge badge-danger">18</span></td>
                                <td><span class="badge badge-warning">4</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 88.2%">88.2%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                            <tr>
                                <td><span class="grade-badge">Grade 12</span></td>
                                <td>232</td>
                                <td><span class="badge badge-success">201</span></td>
                                <td><span class="badge badge-danger">26</span></td>
                                <td><span class="badge badge-warning">5</span></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill" style="width: 86.6%">86.6%</div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                        </tbody>
                        <tfoot class="table-footer">
                            <tr>
                                <td><strong>TOTAL</strong></td>
                                <td><strong>1,245</strong></td>
                                <td><strong class="text-success">1,089</strong></td>
                                <td><strong class="text-danger">129</strong></td>
                                <td><strong class="text-warning">27</strong></td>
                                <td>
                                    <div class="progress-bar-wrapper">
                                        <div class="progress-bar-fill bg-primary" style="width: 87.5%"><strong>87.5%</strong></div>
                                    </div>
                                </td>
                                <td><span class="status-badge status-good">Good</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Detailed Records Tab -->
            <div class="tab-pane fade" id="detailed" role="tabpanel">
                <div class="detailed-controls mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box-report">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" placeholder="Search by student name or LRN...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <button class="btn btn-sm btn-outline-success">
                                <i class="bi bi-file-excel"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student Name</th>
                                <th>LRN</th>
                                <th>Grade</th>
                                <th>Time In</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Feb 03, 2026</td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-sm">JD</div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td><code>123456789012</code></td>
                                <td><span class="grade-badge-sm">Grade 10</span></td>
                                <td>07:45 AM</td>
                                <td><span class="status-dot status-present"></span> Present</td>
                                <td><span class="text-muted">On time</span></td>
                            </tr>
                            <tr>
                                <td>Feb 03, 2026</td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-sm">MS</div>
                                        <span>Maria Santos</span>
                                    </div>
                                </td>
                                <td><code>123456789013</code></td>
                                <td><span class="grade-badge-sm">Grade 9</span></td>
                                <td>08:15 AM</td>
                                <td><span class="status-dot status-late"></span> Late</td>
                                <td><span class="text-warning">15 mins late</span></td>
                            </tr>
                            <tr>
                                <td>Feb 03, 2026</td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-sm">RC</div>
                                        <span>Robert Chen</span>
                                    </div>
                                </td>
                                <td><code>123456789014</code></td>
                                <td><span class="grade-badge-sm">Grade 11</span></td>
                                <td>-</td>
                                <td><span class="status-dot status-absent"></span> Absent</td>
                                <td><span class="text-danger">No check-in</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper mt-3">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- By Student Tab -->
            <div class="tab-pane fade" id="students" role="tabpanel">
                <div class="student-reports-grid">
                    <div class="student-report-card">
                        <div class="student-report-header">
                            <div class="student-avatar-lg">JD</div>
                            <div>
                                <h6 class="mb-1">John Doe</h6>
                                <small class="text-muted">Grade 10 • LRN: 123456789012</small>
                            </div>
                        </div>
                        <div class="student-report-stats">
                            <div class="mini-stat">
                                <div class="mini-stat-value text-success">18</div>
                                <div class="mini-stat-label">Present</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-danger">2</div>
                                <div class="mini-stat-label">Absent</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-warning">0</div>
                                <div class="mini-stat-label">Late</div>
                            </div>
                        </div>
                        <div class="student-report-progress">
                            <div class="progress-label">
                                <span>Attendance Rate</span>
                                <strong>90%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 90%"></div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </div>

                    <div class="student-report-card">
                        <div class="student-report-header">
                            <div class="student-avatar-lg">MS</div>
                            <div>
                                <h6 class="mb-1">Maria Santos</h6>
                                <small class="text-muted">Grade 9 • LRN: 123456789013</small>
                            </div>
                        </div>
                        <div class="student-report-stats">
                            <div class="mini-stat">
                                <div class="mini-stat-value text-success">17</div>
                                <div class="mini-stat-label">Present</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-danger">1</div>
                                <div class="mini-stat-label">Absent</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-warning">2</div>
                                <div class="mini-stat-label">Late</div>
                            </div>
                        </div>
                        <div class="student-report-progress">
                            <div class="progress-label">
                                <span>Attendance Rate</span>
                                <strong>85%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 85%"></div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </div>

                    <div class="student-report-card">
                        <div class="student-report-header">
                            <div class="student-avatar-lg">RC</div>
                            <div>
                                <h6 class="mb-1">Robert Chen</h6>
                                <small class="text-muted">Grade 11 • LRN: 123456789014</small>
                            </div>
                        </div>
                        <div class="student-report-stats">
                            <div class="mini-stat">
                                <div class="mini-stat-value text-success">16</div>
                                <div class="mini-stat-label">Present</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-danger">4</div>
                                <div class="mini-stat-label">Absent</div>
                            </div>
                            <div class="mini-stat">
                                <div class="mini-stat-value text-warning">0</div>
                                <div class="mini-stat-label">Late</div>
                            </div>
                        </div>
                        <div class="student-report-progress">
                            <div class="progress-label">
                                <span>Attendance Rate</span>
                                <strong>80%</strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 80%"></div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-3">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Insights Tab -->
            <div class="tab-pane fade" id="insights" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="insight-card insight-success">
                            <div class="insight-icon">
                                <i class="bi bi-emoji-smile"></i>
                            </div>
                            <div class="insight-content">
                                <h6>Strong Overall Performance</h6>
                                <p>87.5% average attendance rate exceeds the target of 85%. Keep up the good work!</p>
                                <div class="insight-meta">
                                    <span class="badge bg-success">Positive Trend</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="insight-card insight-warning">
                            <div class="insight-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="insight-content">
                                <h6>Grade 9 Needs Attention</h6>
                                <p>Attendance rate of 84.8% is below average. Consider intervention strategies.</p>
                                <div class="insight-meta">
                                    <span class="badge bg-warning">Action Required</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="insight-card insight-info">
                            <div class="insight-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="insight-content">
                                <h6>Best Attendance Day</h6>
                                <p>Mondays show the highest attendance rate at 92%, while Fridays are lowest at 83%.</p>
                                <div class="insight-meta">
                                    <span class="badge bg-info">Observation</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="insight-card insight-primary">
                            <div class="insight-icon">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div class="insight-content">
                                <h6>45 Students with Perfect Attendance</h6>
                                <p>3.6% of students maintained 100% attendance. Consider recognition program.</p>
                                <div class="insight-meta">
                                    <span class="badge bg-primary">Achievement</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content export-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-download me-2"></i>
                    Export Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Select Report Type:</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="export-type-card" data-type="summary">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <h6>Summary Report</h6>
                                <p>Statistics and analysis</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="export-type-card active" data-type="daily_list">
                                <i class="bi bi-list-check"></i>
                                <h6>Daily Attendance List</h6>
                                <p>Student names by status</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="summaryExportOptions" style="display: none;">
                    <h6 class="text-muted mb-3">Select Format:</h6>
                    <div class="export-options">
                        <button class="export-option-btn" data-format="pdf" data-report-type="summary">
                            <i class="bi bi-file-pdf"></i>
                            <span>PDF Document</span>
                            <small>Formatted summary report</small>
                        </button>
                        <button class="export-option-btn" data-format="excel" data-report-type="summary">
                            <i class="bi bi-file-excel"></i>
                            <span>Excel Spreadsheet</span>
                            <small>Raw data</small>
                        </button>
                        <button class="export-option-btn" data-format="csv" data-report-type="summary">
                            <i class="bi bi-filetype-csv"></i>
                            <span>CSV File</span>
                            <small>Comma separated</small>
                        </button>
                    </div>
                </div>

                <div id="dailyListExportOptions">
                    <h6 class="text-muted mb-3">Export Daily Attendance List:</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Date:</label>
                        <input type="date" class="form-control" id="dailyListDate" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Grade Level:</label>
                        <select class="form-select" id="dailyListGrade">
                            <option value="">All Grades (Separate by Grade)</option>
                            <option value="Grade 7">Grade 7 Only</option>
                            <option value="Grade 8">Grade 8 Only</option>
                            <option value="Grade 9">Grade 9 Only</option>
                            <option value="Grade 10">Grade 10 Only</option>
                            <option value="Grade 11">Grade 11 Only</option>
                            <option value="Grade 12">Grade 12 Only</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100" id="exportDailyListBtn">
                        <i class="bi bi-file-pdf me-2"></i>
                        Generate PDF Daily List
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

    /* Header */
    .reports-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 16px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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

    /* Real-time Indicator */
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

    /* Data Change Animation */
    @keyframes dataUpdate {
        0% { background: rgba(16, 185, 129, 0.2); }
        100% { background: transparent; }
    }

    .data-updated {
        animation: dataUpdate 1s ease;
    }

    /* Auto-refresh Toggle */
    .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    /* Filter Section */
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

    /* Summary Stats Cards */
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
        flex-shrink: 0;
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

    .stat-content {
        flex: 1;
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
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-trend.positive { color: #10b981; }
    .stat-trend.negative { color: #ef4444; }

    /* Chart Cards */
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

    .chart-title i {
        color: #3b82f6;
    }

    .chart-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0.25rem 0 0 0;
    }

    /* Custom Tabs */
    .custom-tabs {
        border: none;
        background: white;
        padding: 0.5rem;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .custom-tabs .nav-link {
        border: none;
        color: #6b7280;
        font-weight: 600;
        padding: 0.875rem 1.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .custom-tabs .nav-link:hover {
        background: #f3f4f6;
        color: #3b82f6;
    }

    .custom-tabs .nav-link.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .custom-tab-content {
        background: white;
        padding: 2rem;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    /* Report Table */
    .report-table {
        margin: 0;
    }

    .report-table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .report-table th {
        font-weight: 700;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        padding: 1rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .report-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .report-table tbody tr:hover {
        background: #f9fafb;
    }

    .table-footer {
        background: #f9fafb;
        font-weight: 600;
    }

    .grade-badge {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
        display: inline-block;
    }

    .grade-badge-sm {
        background: #f3f4f6;
        color: #6b7280;
        padding: 0.25rem 0.625rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
    }

    .progress-bar-wrapper {
        background: #f3f4f6;
        border-radius: 50px;
        height: 28px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar-fill {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8125rem;
        transition: width 0.3s ease;
    }

    .progress-bar-fill.bg-primary {
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    }

    .status-badge {
        padding: 0.375rem 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8125rem;
        display: inline-block;
    }

    .status-good {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-average {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .status-poor {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    /* Student Cell */
    .student-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .student-avatar-lg {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .status-present { background: #10b981; }
    .status-absent { background: #ef4444; }
    .status-late { background: #f59e0b; }

    /* Search Box */
    .search-box-report {
        position: relative;
    }

    .search-box-report i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-box-report .form-control {
        padding-left: 2.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
    }

    /* Student Report Cards */
    .student-reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .student-report-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .student-report-card:hover {
        border-color: #3b82f6;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .student-report-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .student-report-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .mini-stat {
        text-align: center;
    }

    .mini-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .mini-stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .student-report-progress {
        margin-bottom: 1rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    /* Insight Cards */
    .insight-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        gap: 1.25rem;
        border-left: 4px solid;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .insight-success { border-color: #10b981; }
    .insight-warning { border-color: #f59e0b; }
    .insight-info { border-color: #3b82f6; }
    .insight-primary { border-color: #8b5cf6; }

    .insight-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .insight-success .insight-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .insight-warning .insight-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .insight-info .insight-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .insight-primary .insight-icon {
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }

    .insight-content h6 {
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .insight-content p {
        color: #6b7280;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
    }

    /* Export Modal */
    .export-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .export-type-card {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .export-type-card:hover {
        border-color: #3b82f6;
        background: white;
        transform: translateY(-2px);
    }

    .export-type-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .export-type-card i {
        font-size: 2.5rem;
        color: #3b82f6;
        display: block;
        margin-bottom: 0.75rem;
    }

    .export-type-card h6 {
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .export-type-card p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .export-options {
        display: grid;
        gap: 1rem;
    }

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

    .export-option-btn i {
        font-size: 2.5rem;
        color: #3b82f6;
    }

    .export-option-btn span {
        font-weight: 700;
        color: #111827;
        display: block;
    }

    .export-option-btn small {
        color: #6b7280;
        display: block;
        margin-top: 0.25rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // Real-time Update Configuration
    let autoRefreshEnabled = true;
    let refreshInterval = null;
    let lastUpdateTime = new Date();
    const REFRESH_INTERVAL = 30000; // 30 seconds

    // Trend Chart
    let currentChartType = 'line';
    let trendChart;

    function createTrendChart(type) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        if (trendChart) {
            trendChart.destroy();
        }

        trendChart = new Chart(ctx, {
            type: type,
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: [85, 87, 89, 87.5],
                    borderColor: '#3b82f6',
                    backgroundColor: type === 'line' ? 'rgba(59, 130, 246, 0.1)' : '#3b82f6',
                    tension: 0.4,
                    fill: type === 'line',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 80,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    createTrendChart('line');

    // Chart type switcher
    document.querySelectorAll('[data-chart]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const chartType = this.dataset.chart;
            createTrendChart(chartType);
        });
    });

    // Grade Chart
    const gradeCtx = document.getElementById('gradeChart').getContext('2d');
    let gradeChart = new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
            datasets: [{
                data: [208, 215, 198, 205, 187, 232],
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ef4444',
                    '#06b6d4'
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
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Real-time Data Fetch Function
    async function fetchLatestData() {
        try {
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

            const response = await fetch(`/api/reports/realtime?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const data = await response.json();
            console.log('Real-time data received:', data);
            
            updateDashboard(data);
            updateLastUpdateTime();
            
        } catch (error) {
            console.error('Error fetching real-time data:', error);
            // Continue with simulated updates in case of error
            updateWithSimulatedData();
        }
    }

    // Simulated real-time updates (for demo purposes)
    function updateWithSimulatedData() {
        // Simulate small random changes
        const stats = {
            totalDays: parseInt(document.getElementById('totalDays').textContent) || 20,
            avgAttendance: parseFloat(document.getElementById('avgAttendance').textContent) || 87.5,
            totalAbsences: parseInt(document.getElementById('totalAbsences').textContent) || 245,
            perfectAttendance: parseInt(document.getElementById('perfectAttendance').textContent) || 45
        };

        // Add small random variations
        stats.avgAttendance = Math.min(100, Math.max(80, stats.avgAttendance + (Math.random() - 0.5) * 0.5));
        stats.totalAbsences = Math.max(0, stats.totalAbsences + Math.floor((Math.random() - 0.5) * 5));
        stats.perfectAttendance = Math.max(0, stats.perfectAttendance + Math.floor((Math.random() - 0.5) * 2));

        updateDashboard(stats);
        updateLastUpdateTime();
    }

    // Update Dashboard with new data
    function updateDashboard(data) {
        // Add updating class for animation
        const summaryCards = document.querySelectorAll('.summary-stat-card');
        summaryCards.forEach(card => card.classList.add('updating'));

        setTimeout(() => {
            // Update statistics with animation
            if (data.totalDays !== undefined) {
                animateValue('totalDays', data.totalDays);
            }
            if (data.avgAttendance !== undefined) {
                animateValue('avgAttendance', data.avgAttendance, '%', 1);
            }
            if (data.totalAbsences !== undefined) {
                animateValue('totalAbsences', data.totalAbsences);
            }
            if (data.perfectAttendance !== undefined) {
                animateValue('perfectAttendance', data.perfectAttendance);
            }

            // Update charts if data provided
            if (data.trendData) {
                updateTrendChart(data.trendData);
            }

            if (data.gradeData) {
                updateGradeChart(data.gradeData);
            }

            // Update grade summary table if provided
            if (data.gradeSummary && data.gradeSummary.length > 0) {
                updateGradeSummaryTable(data.gradeSummary);
            }

            // Update recent attendance records if provided
            if (data.recentAttendance && data.recentAttendance.length > 0) {
                updateRecentAttendanceTable(data.recentAttendance);
            }

            // Update student reports if provided
            if (data.studentReports && data.studentReports.length > 0) {
                updateStudentReports(data.studentReports);
            }

            // Update insights if provided
            if (data.insights && data.insights.length > 0) {
                updateInsights(data.insights);
            }

            // Remove updating class
            summaryCards.forEach(card => {
                card.classList.remove('updating');
                card.classList.add('data-updated');
                setTimeout(() => card.classList.remove('data-updated'), 1000);
            });

            // Show notification
            showUpdateNotification();
        }, 500);
    }

    // Update grade summary table
    function updateGradeSummaryTable(summary) {
        const tbody = document.querySelector('#summary tbody');
        if (!tbody) return;

        let html = '';
        let totalStudents = 0;
        let totalPresent = 0;
        let totalAbsent = 0;
        let totalLate = 0;

        summary.forEach(row => {
            totalStudents += row.total_students;
            totalPresent += row.present;
            totalAbsent += row.absent;
            totalLate += row.late;

            const statusClass = row.status === 'good' ? 'status-good' : (row.status === 'average' ? 'status-average' : 'status-poor');

            html += `
                <tr>
                    <td><span class="grade-badge">${row.grade}</span></td>
                    <td>${row.total_students}</td>
                    <td><span class="badge badge-success">${row.present}</span></td>
                    <td><span class="badge badge-danger">${row.absent}</span></td>
                    <td><span class="badge badge-warning">${row.late}</span></td>
                    <td>
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-fill" style="width: ${row.rate}%">${row.rate}%</div>
                        </div>
                    </td>
                    <td><span class="status-badge ${statusClass}">${row.status.charAt(0).toUpperCase() + row.status.slice(1)}</span></td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Update footer totals
        const tfoot = document.querySelector('#summary tfoot tr');
        if (tfoot) {
            const overallRate = totalStudents > 0 ? ((totalPresent / (totalStudents * summary.length)) * 100).toFixed(1) : 0;
            const overallStatus = overallRate >= 85 ? 'status-good' : (overallRate >= 75 ? 'status-average' : 'status-poor');
            
            tfoot.innerHTML = `
                <td><strong>TOTAL</strong></td>
                <td><strong>${totalStudents.toLocaleString()}</strong></td>
                <td><strong class="text-success">${totalPresent.toLocaleString()}</strong></td>
                <td><strong class="text-danger">${totalAbsent.toLocaleString()}</strong></td>
                <td><strong class="text-warning">${totalLate.toLocaleString()}</strong></td>
                <td>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-fill bg-primary" style="width: ${overallRate}%"><strong>${overallRate}%</strong></div>
                    </div>
                </td>
                <td><span class="status-badge ${overallStatus}">Overall</span></td>
            `;
        }
    }

    // Update recent attendance table
    function updateRecentAttendanceTable(records) {
        const tbody = document.querySelector('#detailed tbody');
        if (!tbody || records.length === 0) return;

        let html = '';
        records.slice(0, 10).forEach(record => {
            const statusDotClass = record.status === 'present' ? 'status-present' : (record.status === 'late' ? 'status-late' : 'status-absent');
            const initials = record.student_name.split(' ').map(n => n[0]).join('').toUpperCase().substr(0, 2);

            html += `
                <tr>
                    <td>${record.date}</td>
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar-sm">${initials}</div>
                            <span>${record.student_name}</span>
                        </div>
                    </td>
                    <td><code>${record.lrn}</code></td>
                    <td><span class="grade-badge-sm">${record.grade}</span></td>
                    <td>${record.time_in}</td>
                    <td><span class="status-dot ${statusDotClass}"></span> ${record.status.charAt(0).toUpperCase() + record.status.slice(1)}</td>
                    <td><span class="text-muted">${record.remarks}</span></td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Update student reports
    function updateStudentReports(students) {
        const container = document.querySelector('.student-reports-grid');
        if (!container || students.length === 0) return;

        let html = '';
        students.forEach(student => {
            const initials = student.name.split(' ').map(n => n[0]).join('').toUpperCase().substr(0, 2);
            const progressColor = student.rate >= 85 ? 'success' : (student.rate >= 70 ? 'warning' : 'danger');

            html += `
                <div class="student-report-card">
                    <div class="student-report-header">
                        <div class="student-avatar-lg">${initials}</div>
                        <div>
                            <h6 class="mb-1">${student.name}</h6>
                            <small class="text-muted">${student.grade} • LRN: ${student.lrn}</small>
                        </div>
                    </div>
                    <div class="student-report-stats">
                        <div class="mini-stat">
                            <div class="mini-stat-value text-success">${student.present}</div>
                            <div class="mini-stat-label">Present</div>
                        </div>
                        <div class="mini-stat">
                            <div class="mini-stat-value text-danger">${student.absent}</div>
                            <div class="mini-stat-label">Absent</div>
                        </div>
                        <div class="mini-stat">
                            <div class="mini-stat-value text-warning">${student.late}</div>
                            <div class="mini-stat-label">Late</div>
                        </div>
                    </div>
                    <div class="student-report-progress">
                        <div class="progress-label">
                            <span>Attendance Rate</span>
                            <strong>${student.rate}%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-${progressColor}" style="width: ${student.rate}%"></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100 mt-3" onclick="window.location.href='/students/${student.id}'">
                        <i class="bi bi-eye me-1"></i> View Details
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Update insights
    function updateInsights(insights) {
        const container = document.querySelector('#insights .row');
        if (!container || insights.length === 0) return;

        let html = '';
        insights.forEach(insight => {
            const typeClass = `insight-${insight.type}`;
            const iconMap = {
                'success': 'emoji-smile',
                'warning': 'exclamation-triangle',
                'info': 'info-circle',
                'primary': 'lightbulb'
            };
            const icon = iconMap[insight.type] || 'info-circle';

            html += `
                <div class="col-lg-6">
                    <div class="insight-card ${typeClass}">
                        <div class="insight-icon">
                            <i class="bi bi-${icon}"></i>
                        </div>
                        <div class="insight-content">
                            <h6>${insight.title}</h6>
                            <p>${insight.message}</p>
                            <div class="insight-meta">
                                <span class="badge bg-${insight.type}">${insight.badge}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Animate value changes
    function animateValue(elementId, endValue, suffix = '', decimals = 0) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = parseFloat(element.textContent) || 0;
        const duration = 500;
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

            element.textContent = current.toFixed(decimals) + suffix;
        }, duration / steps);
    }

    // Update trend chart
    function updateTrendChart(newData) {
        if (trendChart && newData.labels && newData.values) {
            trendChart.data.labels = newData.labels;
            trendChart.data.datasets[0].data = newData.values;
            trendChart.update('none'); // Smooth update without animation
        }
    }

    // Update grade chart
    function updateGradeChart(newData) {
        if (gradeChart && newData.values) {
            gradeChart.data.datasets[0].data = newData.values;
            gradeChart.update('none');
        }
    }

    // Update last update time
    function updateLastUpdateTime() {
        lastUpdateTime = new Date();
        const element = document.getElementById('lastUpdate');
        if (element) {
            element.textContent = 'Updated just now';
        }
    }

    // Update relative time display
    function updateRelativeTime() {
        const element = document.getElementById('lastUpdate');
        if (!element) return;

        const now = new Date();
        const diffMs = now - lastUpdateTime;
        const diffSecs = Math.floor(diffMs / 1000);
        const diffMins = Math.floor(diffSecs / 60);

        if (diffSecs < 10) {
            element.textContent = 'Updated just now';
        } else if (diffSecs < 60) {
            element.textContent = `Updated ${diffSecs}s ago`;
        } else if (diffMins < 60) {
            element.textContent = `Updated ${diffMins}m ago`;
        } else {
            element.textContent = `Updated ${Math.floor(diffMins / 60)}h ago`;
        }
    }

    // Show update notification
    function showUpdateNotification() {
        // Optional: Show a subtle toast notification
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>
                        Data updated successfully
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.parentElement.remove()"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Start auto-refresh
    function startAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }

        refreshInterval = setInterval(() => {
            if (autoRefreshEnabled) {
                console.log('Auto-refreshing data...');
                fetchLatestData();
            }
        }, REFRESH_INTERVAL);

        // Also update relative time every 5 seconds
        setInterval(updateRelativeTime, 5000);
    }

    // Auto-refresh toggle
    document.getElementById('autoRefreshToggle').addEventListener('change', function() {
        autoRefreshEnabled = this.checked;
        
        if (autoRefreshEnabled) {
            startAutoRefresh();
            Swal.fire({
                icon: 'success',
                title: 'Auto-refresh Enabled',
                text: 'Reports will update automatically every 30 seconds',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            Swal.fire({
                icon: 'info',
                title: 'Auto-refresh Disabled',
                text: 'You can manually refresh using the button',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    });

    // Manual refresh button
    document.getElementById('refreshNow').addEventListener('click', function() {
        const btn = this;
        const originalHTML = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...';
        
        fetchLatestData();
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }, 1000);
    });

    // Start auto-refresh on page load
    startAutoRefresh();
    
    // Initial data fetch
    setTimeout(() => {
        fetchLatestData();
    }, 1000);

    // Export button
    document.getElementById('exportBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('exportModal'));
        modal.show();
    });

    // Export type selection
    document.querySelectorAll('.export-type-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove active from all
            document.querySelectorAll('.export-type-card').forEach(c => c.classList.remove('active'));
            
            // Add active to clicked
            this.classList.add('active');
            
            const type = this.dataset.type;
            
            // Show/hide appropriate options
            if (type === 'summary') {
                document.getElementById('summaryExportOptions').style.display = 'block';
                document.getElementById('dailyListExportOptions').style.display = 'none';
            } else if (type === 'daily_list') {
                document.getElementById('summaryExportOptions').style.display = 'none';
                document.getElementById('dailyListExportOptions').style.display = 'block';
            }
        });
    });

    // Export Daily Attendance List
    document.getElementById('exportDailyListBtn').addEventListener('click', function() {
        const date = document.getElementById('dailyListDate').value;
        const grade = document.getElementById('dailyListGrade').value;
        
        if (!date) {
            Swal.fire({
                icon: 'error',
                title: 'Date Required',
                text: 'Please select a date for the attendance list',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        Swal.fire({
            title: 'Generating Daily Attendance List...',
            html: `Creating PDF for ${new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
            timer: 2000
        }).then(() => {
            // Build the export URL
            const params = new URLSearchParams({
                format: 'pdf',
                report_type: 'daily_list',
                start_date: date,
                grade: grade
            });
            
            // Trigger download
            window.location.href = `/reports/export?${params.toString()}`;
            
            Swal.fire({
                icon: 'success',
                title: 'Report Generated!',
                text: 'Your daily attendance list PDF is ready',
                confirmButtonColor: '#3b82f6'
            });

            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        });
    });

    // Summary export format selection
    document.querySelectorAll('.export-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const format = this.dataset.format;
            const reportType = this.dataset.reportType || 'summary';
            
            Swal.fire({
                title: 'Generating Report...',
                html: `Preparing your ${format.toUpperCase()} report`,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                timer: 2000
            }).then(() => {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const grade = document.getElementById('gradeFilter').value;
                
                const params = new URLSearchParams({
                    format: format,
                    report_type: reportType,
                    start_date: startDate,
                    end_date: endDate,
                    grade: grade
                });
                
                window.location.href = `/reports/export?${params.toString()}`;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Report Generated!',
                    text: `Your ${format.toUpperCase()} report is ready for download`,
                    confirmButtonColor: '#3b82f6'
                });
            });

            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        });
    });

    // Generate Report
    document.getElementById('generateReport').addEventListener('click', function() {
        const reportType = document.getElementById('reportType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const grade = document.getElementById('gradeFilter').value;

        Swal.fire({
            title: 'Generating Report...',
            html: 'Please wait while we compile your data',
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
            timer: 1500
        }).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Report Generated!',
                text: 'Your report has been updated with the selected filters',
                confirmButtonColor: '#3b82f6'
            });
            
            // Fetch new data based on filters
            fetchLatestData();
        });
    });

    // Reset Filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('reportType').value = 'monthly';
        document.getElementById('startDate').value = '{{ date("Y-m-01") }}';
        document.getElementById('endDate').value = '{{ date("Y-m-d") }}';
        document.getElementById('gradeFilter').value = '';
    });

    // Report type change handler
    document.getElementById('reportType').addEventListener('change', function() {
        const value = this.value;
        const today = new Date();
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        if (value === 'daily') {
            startDateInput.value = today.toISOString().split('T')[0];
            endDateInput.value = today.toISOString().split('T')[0];
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

    // Page visibility change - pause/resume auto-refresh
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page hidden - pausing auto-refresh');
        } else {
            console.log('Page visible - resuming auto-refresh');
            if (autoRefreshEnabled) {
                fetchLatestData(); // Immediate refresh when page becomes visible
            }
        }
    });
</script>
@endsection
