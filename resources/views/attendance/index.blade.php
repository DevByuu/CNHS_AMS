@extends('layouts.app')

@section('content')
<div class="attendance-monitor-container">
    <!-- Header Section -->
    <div class="monitor-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="bi bi-radar"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">Live Attendance Monitor</h2>
                        <p class="text-white-50 mb-0">Real-time RFID check-in tracking</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="live-indicator">
                    <span class="pulse-dot"></span>
                    <span class="live-text">LIVE</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Date & Time -->
    <div class="datetime-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="datetime-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <div class="current-date" id="currentDate">{{ now()->format('l, F j, Y') }}</div>
                        <div class="current-time" id="currentTime">00:00:00</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-outline-primary" id="toggleSound">
                    <i class="bi bi-volume-up" id="soundIcon"></i>
                    <span id="soundText">Sound ON</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value" id="totalStudents">{{ $totalStudents ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Present Today</div>
                    <div class="stat-value" id="presentCount">{{ $presentToday ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-red">
                <div class="stat-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Absent</div>
                    <div class="stat-value" id="absentCount">{{ $absentToday ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-label">Last Check-in</div>
                    <div class="stat-value-small" id="lastCheckIn">--:--</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RFID Scanner Area -->
    <div class="scanner-area mb-4">
        <div class="scanner-status" id="scannerStatus">
            <div class="scanner-animation">
                <div class="scan-line"></div>
                <i class="bi bi-upc-scan"></i>
            </div>
            <h3 class="mt-4">Waiting for RFID Tap...</h3>
            <p class="text-muted">Students can tap their RFID cards now</p>
            <div class="scanner-pulse"></div>
        </div>
    </div>

    <!-- Recent Check-ins -->
    <div class="row">
        <!-- Recent Activity List -->
        <div class="col-lg-8">
            <div class="activity-card">
                <div class="activity-header">
                    <h5><i class="bi bi-clock-history me-2"></i>Today's Check-ins ({{ $presentToday ?? 0 }})</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshList">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="activity-body" id="activityList">
                    <!-- Activities will be loaded here -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h5 class="summary-title">
                    <i class="bi bi-bar-chart-fill me-2"></i>
                    Attendance Rate
                </h5>
                <div class="attendance-rate">
                    <div class="rate-circle">
                        <svg class="progress-ring" width="150" height="150">
                            <circle class="progress-ring-circle-bg" cx="75" cy="75" r="65"></circle>
                            <circle class="progress-ring-circle" cx="75" cy="75" r="65" id="progressCircle"></circle>
                        </svg>
                        <div class="rate-percentage" id="attendancePercentage">0%</div>
                    </div>
                </div>
                <div class="summary-details mt-4">
                    <div class="summary-item">
                        <span class="summary-dot bg-success"></span>
                        <span>Present: <strong id="summaryPresent">{{ $presentToday }}</strong></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-dot bg-danger"></span>
                        <span>Absent: <strong id="summaryAbsent">{{ $absentToday }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="toastMessage">Check-in successful!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Student Check-in Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content checkin-modal">
            <div class="modal-body text-center p-5">
                <div class="success-checkmark">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h3 class="mt-4 mb-2" id="checkinStudentName">Student Name</h3>
                <p class="text-muted mb-1" id="checkinGrade">Grade Level</p>
                <p class="text-muted" id="checkinTime">Check-in Time</p>
                <div class="status-badge mt-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    CHECKED IN
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .attendance-monitor-container {
        padding: 2rem 0;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .monitor-header {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
        padding: 2rem;
        border-radius: 16px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .monitor-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
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

    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(239, 68, 68, 0.2);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        border: 2px solid rgba(239, 68, 68, 0.5);
    }

    .pulse-dot {
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    .live-text {
        font-weight: 700;
        color: white;
        font-size: 0.875rem;
        letter-spacing: 0.1em;
    }

    /* DateTime Card */
    .datetime-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .datetime-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .current-date {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
    }

    .current-time {
        font-size: 2rem;
        font-weight: 700;
        color: #3b82f6;
        font-family: 'Courier New', monospace;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        display: flex;
        gap: 1rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .stat-blue { border-color: #3b82f6; }
    .stat-green { border-color: #10b981; }
    .stat-red { border-color: #ef4444; }
    .stat-purple { border-color: #8b5cf6; }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-blue .stat-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .stat-green .stat-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stat-red .stat-icon {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .stat-purple .stat-icon {
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }

    .stat-label {
        font-size: 0.8125rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
    }

    .stat-value-small {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    /* Scanner Area */
    .scanner-area {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 3px dashed #3b82f6;
        border-radius: 16px;
        padding: 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scanner-status {
        position: relative;
        z-index: 2;
    }

    .scanner-animation {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .scanner-animation i {
        font-size: 4rem;
        color: #3b82f6;
        animation: scanPulse 2s infinite;
    }

    @keyframes scanPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
    }

    .scan-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        animation: scanLine 2s infinite;
    }

    @keyframes scanLine {
        0% { top: 0%; }
        100% { top: 100%; }
    }

    .scanner-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 3px solid #3b82f6;
        border-radius: 50%;
        opacity: 0;
        animation: scannerPulse 2s infinite;
    }

    @keyframes scannerPulse {
        0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0.5; }
        100% { transform: translate(-50%, -50%) scale(2); opacity: 0; }
    }

    .scanner-status.scanning {
        animation: scanningEffect 0.5s ease;
    }

    @keyframes scanningEffect {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Activity Card */
    .activity-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        height: 600px;
        display: flex;
        flex-direction: column;
    }

    .activity-header {
        padding: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-header h5 {
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .activity-body {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .activity-body::-webkit-scrollbar {
        width: 8px;
    }

    .activity-body::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 4px;
    }

    .activity-body::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .activity-body::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    .activity-item {
        background: #f9fafb;
        padding: 1.25rem;
        border-radius: 10px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: slideIn 0.3s ease;
        border-left: 4px solid #10b981;
        transition: all 0.2s ease;
    }

    .activity-item:hover {
        background: #f3f4f6;
        transform: translateX(4px);
    }

    .activity-item.new-checkin {
        animation: newCheckinHighlight 2s ease;
    }

    @keyframes newCheckinHighlight {
        0% { background: #d1fae5; }
        100% { background: #f9fafb; }
    }

    @keyframes slideIn {
        from { 
            opacity: 0; 
            transform: translateX(-20px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }

    .activity-avatar {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .activity-info {
        flex: 1;
    }

    .activity-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .activity-details {
        font-size: 0.8125rem;
        color: #6b7280;
    }

    .activity-time {
        font-size: 0.875rem;
        color: #10b981;
        font-weight: 600;
        white-space: nowrap;
    }

    .empty-activity {
        text-align: center;
        color: #9ca3af;
        padding: 3rem 1rem;
    }

    .empty-activity i {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    /* Summary Card */
    .summary-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        height: 600px;
    }

    .summary-title {
        font-weight: 700;
        color: #111827;
        margin-bottom: 2rem;
    }

    .attendance-rate {
        display: flex;
        justify-content: center;
        margin: 2rem 0;
    }

    .rate-circle {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle-bg {
        stroke: #f3f4f6;
        stroke-width: 10;
        fill: none;
    }

    .progress-ring-circle {
        stroke: #10b981;
        stroke-width: 10;
        fill: none;
        stroke-linecap: round;
        stroke-dasharray: 408;
        stroke-dashoffset: 408;
        transition: stroke-dashoffset 1s ease;
    }

    .rate-percentage {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        font-weight: 700;
        color: #10b981;
    }

    .summary-details {
        border-top: 2px solid #f3f4f6;
        padding-top: 1.5rem;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
        color: #6b7280;
    }

    .summary-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    /* Check-in Modal */
    .checkin-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .success-checkmark {
        font-size: 5rem;
        color: #10b981;
        animation: checkmarkPop 0.5s ease;
    }

    @keyframes checkmarkPop {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .status-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        display: inline-block;
        font-size: 1.125rem;
    }
</style>

<script>
    let soundEnabled = true;
    let rfidBuffer = '';
    let scanTimeout = null;
    let lastFetchedCount = 0;

    // Update time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        document.getElementById('currentTime').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Fetch today's check-ins from database
    async function fetchTodayCheckIns() {
        try {
            const response = await fetch('/api/attendance/today', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch check-ins');
            }

            const data = await response.json();
            
            if (data.success) {
                displayCheckIns(data.checkIns);
                updateStats(data.stats);
                
                // Update header count
                document.querySelector('.activity-header h5').innerHTML = 
                    `<i class="bi bi-clock-history me-2"></i>Today's Check-ins (${data.checkIns.length})`;
            }

        } catch (error) {
            console.error('Error fetching check-ins:', error);
        }
    }

    // Display check-ins in the list
    function displayCheckIns(checkIns) {
        const activityList = document.getElementById('activityList');
        
        if (checkIns.length === 0) {
            activityList.innerHTML = `
                <div class="empty-activity">
                    <i class="bi bi-inbox"></i>
                    <p>No check-ins yet today</p>
                </div>
            `;
            return;
        }

        activityList.innerHTML = '';
        
        checkIns.forEach(checkIn => {
            const initials = checkIn.student_name.split(' ').map(n => n[0]).join('').substring(0, 2);
            
            const item = document.createElement('div');
            item.className = 'activity-item';
            item.innerHTML = `
                <div class="activity-avatar">${initials}</div>
                <div class="activity-info">
                    <div class="activity-name">${checkIn.student_name}</div>
                    <div class="activity-details">${checkIn.grade} • LRN: ${checkIn.lrn}</div>
                </div>
                <div class="activity-time">${checkIn.time_in}</div>
            `;
            
            activityList.appendChild(item);
        });

        // Update last check-in time
        if (checkIns.length > 0) {
            document.getElementById('lastCheckIn').textContent = checkIns[0].time_in;
        }
    }

    // Toggle sound
    document.getElementById('toggleSound').addEventListener('click', function() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        const text = document.getElementById('soundText');
        
        if (soundEnabled) {
            icon.className = 'bi bi-volume-up';
            text.textContent = 'Sound ON';
        } else {
            icon.className = 'bi bi-volume-mute';
            text.textContent = 'Sound OFF';
        }
    });

    // Manual refresh button
    document.getElementById('refreshList').addEventListener('click', function() {
        const btn = this;
        const originalHTML = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Refreshing...';
        
        fetchTodayCheckIns();
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }, 1000);
    });

    // Listen for RFID scans
    document.addEventListener('keypress', function(e) {
        // Clear previous timeout
        if (scanTimeout) {
            clearTimeout(scanTimeout);
        }

        // RFID scanners send Enter after the card number
        if (e.key === 'Enter') {
            if (rfidBuffer.length > 0) {
                processRfidScan(rfidBuffer.trim());
                rfidBuffer = '';
            }
        } else {
            rfidBuffer += e.key;
            
            // Auto-submit after 100ms of no input
            scanTimeout = setTimeout(function() {
                if (rfidBuffer.length > 0) {
                    processRfidScan(rfidBuffer.trim());
                    rfidBuffer = '';
                }
            }, 100);
        }
    });

    // Process RFID scan
    function processRfidScan(rfidNumber) {
        if (rfidNumber.length < 4) return;

        // Add scanning effect
        document.getElementById('scannerStatus').classList.add('scanning');
        setTimeout(() => {
            document.getElementById('scannerStatus').classList.remove('scanning');
        }, 500);

        // Send AJAX request to check in student
        fetch('/attendance/checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ rfid: rfidNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCheckIn(data.student);
                playSuccessSound();
                
                // Refresh the list after a short delay
                setTimeout(() => {
                    fetchTodayCheckIns();
                }, 500);
            } else {
                showError(data.message || 'Student not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to process check-in');
        });
    }

    // Show check-in modal
    function showCheckIn(student) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        // Update modal
        document.getElementById('checkinStudentName').textContent = student.name;
        document.getElementById('checkinGrade').textContent = student.grade;
        document.getElementById('checkinTime').textContent = timeString;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('checkinModal'));
        modal.show();

        // Auto hide after 3 seconds
        setTimeout(() => {
            modal.hide();
        }, 3000);

        // Show toast
        showToast(`${student.name} checked in successfully!`);
    }

    // Update stats with animation
    function updateStats(stats) {
        // Animate present count
        animateValue('presentCount', parseInt(document.getElementById('presentCount').textContent), stats.present, 500);
        
        // Animate absent count
        animateValue('absentCount', parseInt(document.getElementById('absentCount').textContent), stats.absent, 500);
        
        // Update summary
        animateValue('summaryPresent', parseInt(document.getElementById('summaryPresent').textContent || 0), stats.present, 500);
        animateValue('summaryAbsent', parseInt(document.getElementById('summaryAbsent').textContent || 0), stats.absent, 500);

        // Calculate and update attendance percentage
        const total = stats.total;
        const present = stats.present;
        const percentage = total > 0 ? Math.round((present / total) * 100) : 0;
        
        const currentPercentage = parseInt(document.getElementById('attendancePercentage').textContent);
        animatePercentage(currentPercentage, percentage);
        
        // Update progress circle
        updateProgressCircle(percentage);
    }

    // Animate number changes
    function animateValue(elementId, start, end, duration) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.round(current);
        }, 16);
    }

    // Animate percentage
    function animatePercentage(start, end) {
        const element = document.getElementById('attendancePercentage');
        if (!element) return;
        
        const duration = 1000;
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.round(current) + '%';
        }, 16);
    }

    // Update progress circle
    function updateProgressCircle(percentage) {
        const circle = document.getElementById('progressCircle');
        if (!circle) return;
        
        const radius = 65;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference - (percentage / 100) * circumference;
        
        circle.style.strokeDasharray = circumference;
        circle.style.strokeDashoffset = offset;
        
        // Change color based on percentage
        if (percentage >= 80) {
            circle.style.stroke = '#10b981';
        } else if (percentage >= 50) {
            circle.style.stroke = '#f59e0b';
        } else {
            circle.style.stroke = '#ef4444';
        }
    }

    // Show toast
    function showToast(message) {
        document.getElementById('toastMessage').textContent = message;
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
    }

    // Show error
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Check-in Failed',
            text: message,
            confirmButtonColor: '#3b82f6'
        });
    }

    // Play success sound
    function playSuccessSound() {
        if (!soundEnabled) return;

        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        const oscillator1 = audioContext.createOscillator();
        const gainNode1 = audioContext.createGain();
        oscillator1.connect(gainNode1);
        gainNode1.connect(audioContext.destination);
        oscillator1.frequency.value = 800;
        oscillator1.type = 'sine';
        gainNode1.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode1.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        oscillator1.start(audioContext.currentTime);
        oscillator1.stop(audioContext.currentTime + 0.1);

        const oscillator2 = audioContext.createOscillator();
        const gainNode2 = audioContext.createGain();
        oscillator2.connect(gainNode2);
        gainNode2.connect(audioContext.destination);
        oscillator2.frequency.value = 1000;
        oscillator2.type = 'sine';
        gainNode2.gain.setValueAtTime(0.3, audioContext.currentTime + 0.15);
        gainNode2.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.25);
        oscillator2.start(audioContext.currentTime + 0.15);
        oscillator2.stop(audioContext.currentTime + 0.25);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initial stats update
        const totalStudents = {{ $totalStudents }};
        const presentToday = {{ $presentToday }};
        const absentToday = {{ $absentToday }};
        
        updateStats({
            total: totalStudents,
            present: presentToday,
            absent: absentToday
        });

        // Load today's check-ins
        fetchTodayCheckIns();

        // Auto-refresh every 10 seconds
        setInterval(fetchTodayCheckIns, 10000);
    });
</script>
@endsection