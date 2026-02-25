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
                        <div class="current-date">{{ now()->format('l, F j, Y') }}</div>
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
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value" id="totalStudents">{{ $totalStudents ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Present Today</div>
                    <div class="stat-value" id="presentCount">{{ $presentToday ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-red">
                <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Absent</div>
                    <div class="stat-value" id="absentCount">{{ $absentToday ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-details">
                    <div class="stat-label">Last Scan</div>
                    <div class="stat-value-small" id="lastCheckIn">--:--</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RFID Scanner Area -->
    <div class="scanner-area mb-4" id="scannerArea">
        <div class="scanner-status">
            <div class="scanner-animation">
                <div class="scan-line"></div>
                <i class="bi bi-upc-scan"></i>
            </div>
            <h3 class="mt-4">Waiting for RFID Tap...</h3>
            <p class="text-muted">Students can tap their RFID cards now</p>
            <div class="scanner-pulse"></div>
        </div>
    </div>

    <!-- Recent Check-ins + Summary -->
    <div class="row">
        <div class="col-lg-8">
            <div class="activity-card">
                <div class="activity-header">
                    <h5><i class="bi bi-clock-history me-2"></i>Today's Check-ins (<span id="checkinCount">{{ $presentToday ?? 0 }}</span>)</h5>
                    <button class="btn btn-sm btn-outline-primary" id="refreshList">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="activity-body" id="activityList">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h5 class="summary-title">
                    <i class="bi bi-bar-chart-fill me-2"></i>Attendance Rate
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

<style>
    .attendance-monitor-container { padding: 2rem 0; animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

    .monitor-header {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 10px 30px rgba(30,64,175,0.3);
        padding: 2rem; border-radius: 16px; color: white;
        position: relative; overflow: hidden;
    }
    .monitor-header::before {
        content:''; position:absolute; top:0; left:0; right:0; height:100%;
        background: radial-gradient(circle at 20% 50%, rgba(59,130,246,0.3) 0%, transparent 50%);
    }
    .header-icon { width:64px; height:64px; background:rgba(255,255,255,0.2); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:2rem; }
    .live-indicator { display:inline-flex; align-items:center; gap:0.5rem; background:rgba(239,68,68,0.2); padding:0.5rem 1.25rem; border-radius:50px; border:2px solid rgba(239,68,68,0.5); }
    .pulse-dot { width:12px; height:12px; background:#ef4444; border-radius:50%; animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.5; transform:scale(1.2); } }
    .live-text { font-weight:700; color:white; font-size:0.875rem; letter-spacing:0.1em; }

    .datetime-card { background:white; padding:1.5rem; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
    .datetime-icon { width:56px; height:56px; background:linear-gradient(135deg,#3b82f6,#2563eb); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:1.5rem; }
    .current-date { font-size:1.125rem; font-weight:600; color:#111827; }
    .current-time { font-size:2rem; font-weight:700; color:#3b82f6; font-family:'Courier New',monospace; }

    .stat-card { background:white; padding:1.5rem; border-radius:12px; display:flex; gap:1rem; box-shadow:0 2px 12px rgba(0,0,0,0.08); border-left:4px solid; transition:all 0.3s ease; }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 4px 16px rgba(0,0,0,0.12); }
    .stat-blue   { border-color:#3b82f6; }
    .stat-green  { border-color:#10b981; }
    .stat-red    { border-color:#ef4444; }
    .stat-purple { border-color:#8b5cf6; }
    .stat-icon { width:56px; height:56px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
    .stat-blue   .stat-icon { background:rgba(59,130,246,0.1);  color:#3b82f6; }
    .stat-green  .stat-icon { background:rgba(16,185,129,0.1);  color:#10b981; }
    .stat-red    .stat-icon { background:rgba(239,68,68,0.1);   color:#ef4444; }
    .stat-purple .stat-icon { background:rgba(139,92,246,0.1);  color:#8b5cf6; }
    .stat-label       { font-size:0.8125rem; color:#6b7280; font-weight:500; margin-bottom:0.25rem; }
    .stat-value       { font-size:2rem; font-weight:700; color:#111827; }
    .stat-value-small { font-size:1.25rem; font-weight:700; color:#111827; }

    .scanner-area {
        background:linear-gradient(135deg,#f0f9ff,#e0f2fe);
        border:3px dashed #3b82f6; border-radius:16px; padding:3rem;
        text-align:center; position:relative; overflow:hidden;
        min-height:250px; display:flex; align-items:center; justify-content:center;
        transition:all 0.4s ease;
    }
    .scanner-area.scan-success  { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-color:#10b981; }
    .scanner-area.scan-checkout { background:linear-gradient(135deg,#fffbeb,#fef3c7); border-color:#f59e0b; }
    .scanner-area.scan-error    { background:linear-gradient(135deg,#fff1f2,#ffe4e6); border-color:#ef4444; }

    .scanner-status { position:relative; z-index:2; }
    .scanner-animation { position:relative; width:120px; height:120px; margin:0 auto; display:flex; align-items:center; justify-content:center; }
    .scanner-animation i { font-size:4rem; color:#3b82f6; animation:scanPulse 2s infinite; }
    @keyframes scanPulse { 0%,100% { transform:scale(1); opacity:1; } 50% { transform:scale(1.1); opacity:0.7; } }
    .scan-line { position:absolute; width:100%; height:3px; background:linear-gradient(90deg,transparent,#3b82f6,transparent); animation:scanLine 2s infinite; }
    @keyframes scanLine { 0% { top:0%; } 100% { top:100%; } }
    .scanner-pulse { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:200px; height:200px; border:3px solid #3b82f6; border-radius:50%; opacity:0; animation:scannerPulse 2s infinite; }
    @keyframes scannerPulse { 0% { transform:translate(-50%,-50%) scale(0.5); opacity:0.5; } 100% { transform:translate(-50%,-50%) scale(2); opacity:0; } }

    .activity-card { background:white; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); height:550px; display:flex; flex-direction:column; }
    .activity-header { padding:1.5rem; border-bottom:2px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
    .activity-header h5 { font-weight:700; color:#111827; margin:0; }
    .activity-body { flex:1; overflow-y:auto; padding:1rem; }
    .activity-body::-webkit-scrollbar { width:8px; }
    .activity-body::-webkit-scrollbar-track { background:#f3f4f6; border-radius:4px; }
    .activity-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }

    .activity-item { background:#f9fafb; padding:1.25rem; border-radius:10px; margin-bottom:0.75rem; display:flex; align-items:center; gap:1rem; border-left:4px solid #10b981; transition:all 0.2s ease; }
    .activity-item:hover { background:#f3f4f6; transform:translateX(4px); }
    .activity-item.new-item { border-left-color:#3b82f6; animation:newItemSlide 0.5s ease; }
    @keyframes newItemSlide { from { opacity:0; transform:translateX(-20px); background:#dbeafe; } to { opacity:1; transform:translateX(0); } }

    .activity-avatar { width:48px; height:48px; border-radius:10px; background:linear-gradient(135deg,#10b981,#059669); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; flex-shrink:0; }
    .activity-info { flex:1; }
    .activity-name    { font-weight:600; color:#111827; margin-bottom:0.25rem; }
    .activity-details { font-size:0.8125rem; color:#6b7280; margin-bottom:0.5rem; }
    .activity-times   { display:flex; gap:1rem; font-size:0.8125rem; }
    .time-in  { color:#10b981; font-weight:600; }
    .time-out { color:#f59e0b; font-weight:600; }
    .empty-activity   { text-align:center; color:#9ca3af; padding:3rem 1rem; }
    .empty-activity i { font-size:4rem; margin-bottom:1rem; display:block; }

    .summary-card { background:white; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.08); padding:1.5rem; height:550px; }
    .summary-title { font-weight:700; color:#111827; margin-bottom:2rem; }
    .attendance-rate { display:flex; justify-content:center; margin:2rem 0; }
    .rate-circle { position:relative; width:150px; height:150px; }
    .progress-ring { transform:rotate(-90deg); }
    .progress-ring-circle-bg { stroke:#f3f4f6; stroke-width:10; fill:none; }
    .progress-ring-circle { stroke:#10b981; stroke-width:10; fill:none; stroke-linecap:round; stroke-dasharray:408; stroke-dashoffset:408; transition:stroke-dashoffset 1s ease; }
    .rate-percentage { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:2rem; font-weight:700; color:#10b981; }
    .summary-details { border-top:2px solid #f3f4f6; padding-top:1.5rem; }
    .summary-item { display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem; font-size:0.9375rem; color:#6b7280; }
    .summary-dot  { width:12px; height:12px; border-radius:50%; flex-shrink:0; }
</style>

<script>
    let soundEnabled       = true;
    let lastKnownId        = null;
    let lastKnownUpdatedAt = null;
    let isFirstLoad        = true;

    // ── Clock with Philippine Time ─────────────────────────────────────────────
    function updateTime() {
        const now = new Date();
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
            timeZone: 'Asia/Manila'
        };
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', options);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // ── Sound toggle ───────────────────────────────────────────────────────────
    document.getElementById('toggleSound').addEventListener('click', function() {
        soundEnabled = !soundEnabled;
        document.getElementById('soundIcon').className    = soundEnabled ? 'bi bi-volume-up' : 'bi bi-volume-mute';
        document.getElementById('soundText').textContent  = soundEnabled ? 'Sound ON' : 'Sound OFF';
        console.log('🔊 Sound:', soundEnabled ? 'ON' : 'OFF');
    });

    // ── Auto-refresh every 1 second with DEBUG LOGGING ─────────────────────────
    async function pollAttendance() {
        try {
            const res = await fetch('/api/attendance/today', {
                headers: { 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                }
            });

            if (!res.ok) {
                console.error('❌ Poll failed:', res.status);
                return;
            }

            const data = await res.json();
            if (!data.success) {
                console.warn('⚠️ API returned success: false');
                return;
            }

            const latestId        = data.latest_id;
            const latestUpdatedAt = data.latest_updated_at;

            // Debug logging
            if (!isFirstLoad) {
                console.log('📊 Poll:', {
                    newId: latestId,
                    oldId: lastKnownId,
                    newUpdate: latestUpdatedAt,
                    oldUpdate: lastKnownUpdatedAt,
                    hasChange: latestId !== lastKnownId || latestUpdatedAt !== lastKnownUpdatedAt
                });
            }

            // Detect new scan or update (only after first load)
            if (!isFirstLoad && latestId !== null) {
                const isNewRecord = latestId !== lastKnownId;
                const isUpdated   = latestUpdatedAt !== lastKnownUpdatedAt;

                if (isNewRecord || isUpdated) {
                    const latest = data.checkIns[0];
                    if (latest) {
                        console.log('🎉 CHANGE DETECTED!', latest);
                        
                        // Check if it's a new check-in or check-out
                        const hasTimeOut = latest.time_out && latest.time_out !== '--';
                        
                        if (hasTimeOut && isUpdated && !isNewRecord) {
                            // This is a check-out (time_out was just added to existing record)
                            console.log('🚪 CHECKOUT - Showing popup');
                            showScanPopup('checkout', latest);
                            flashScanner('scan-checkout');
                            playCheckoutSound();
                        } else if (isNewRecord) {
                            // This is a new check-in
                            console.log('✅ CHECK-IN - Showing popup');
                            showScanPopup('checkin', latest);
                            flashScanner('scan-success');
                            playSuccessSound();
                        }
                    }
                }
            }

            lastKnownId        = latestId;
            lastKnownUpdatedAt = latestUpdatedAt;
            
            if (isFirstLoad) {
                console.log('🎬 Monitor initialized. Watching for changes...');
            }
            isFirstLoad        = false;

            // Update UI
            displayCheckIns(data.checkIns);
            updateStats(data.stats);
            document.getElementById('checkinCount').textContent = data.checkIns.length;

        } catch (err) {
            console.error('❌ Poll error:', err);
        }
    }

    // ── Scan popup (SweetAlert2) ───────────────────────────────────────────────
    function showScanPopup(type, student) {
        console.log('🎭 Showing popup:', type, student);
        
        if (type === 'checkin') {
            Swal.fire({
                html: `
                    <div style="text-align:center;">
                        <div style="width:90px;height:90px;border-radius:50%;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="bi bi-check-circle-fill" style="font-size:3.5rem;color:#10b981;"></i>
                        </div>
                        <span style="display:inline-block;background:#d1fae5;color:#065f46;padding:0.4rem 1.25rem;border-radius:50px;font-weight:700;font-size:0.85rem;letter-spacing:0.05em;margin-bottom:1rem;">
                            ✅ CHECKED IN
                        </span>
                        <h3 style="margin:0.5rem 0 0.25rem;color:#111827;font-size:1.5rem;">${student.student_name}</h3>
                        <p style="color:#6b7280;margin:0 0 0.25rem;">${student.grade}</p>
                        <p style="color:#6b7280;margin:0 0 1rem;font-size:0.875rem;">LRN: ${student.lrn}</p>
                        <div style="background:#f3f4f6;padding:0.6rem 1.5rem;border-radius:10px;display:inline-block;font-weight:600;color:#374151;">
                            <i class="bi bi-clock me-2"></i>${student.time_in}
                        </div>
                        <p style="color:#3b82f6;font-size:1.1rem;font-weight:600;margin-top:1rem;">Good morning! 👋</p>
                    </div>`,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                width: 420,
                padding: '2rem',
            });
        } else if (type === 'checkout') {
            Swal.fire({
                html: `
                    <div style="text-align:center;">
                        <div style="width:90px;height:90px;border-radius:50%;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="bi bi-box-arrow-right" style="font-size:3.5rem;color:#f59e0b;"></i>
                        </div>
                        <span style="display:inline-block;background:#fef3c7;color:#92400e;padding:0.4rem 1.25rem;border-radius:50px;font-weight:700;font-size:0.85rem;letter-spacing:0.05em;margin-bottom:1rem;">
                            🚪 CHECKED OUT
                        </span>
                        <h3 style="margin:0.5rem 0 0.25rem;color:#111827;font-size:1.5rem;">${student.student_name}</h3>
                        <p style="color:#6b7280;margin:0 0 1rem;">${student.grade}</p>
                        <div style="background:#f3f4f6;padding:0.6rem 1.5rem;border-radius:10px;display:inline-block;font-weight:600;color:#374151;font-size:0.875rem;">
                            <i class="bi bi-clock me-2"></i>In: ${student.time_in} &nbsp;|&nbsp; Out: ${student.time_out}
                        </div>
                        ${student.duration ? `<p style="color:#6b7280;margin-top:0.75rem;font-size:0.9rem;"><i class="bi bi-hourglass-split me-1"></i>Duration: <strong>${student.duration}</strong></p>` : ''}
                        <p style="color:#f59e0b;font-size:1.1rem;font-weight:600;margin-top:0.5rem;">See you tomorrow! 👋</p>
                    </div>`,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                width: 420,
                padding: '2rem',
            });
        }
    }

    // ── Flash scanner area ─────────────────────────────────────────────────────
    function flashScanner(cls) {
        const area = document.getElementById('scannerArea');
        area.classList.add(cls);
        setTimeout(() => area.classList.remove(cls), 3000);
    }

    // ── Display check-in list with Time In and Time Out ───────────────────────
    function displayCheckIns(checkIns) {
        const list = document.getElementById('activityList');

        if (!checkIns || checkIns.length === 0) {
            list.innerHTML = `<div class="empty-activity"><i class="bi bi-inbox"></i><p>No check-ins yet today</p></div>`;
            document.getElementById('lastCheckIn').textContent = '--';
            return;
        }

        const prevCount = list.querySelectorAll('.activity-item').length;
        const hasNewData = prevCount !== checkIns.length;

        // Force update every time to show time_out changes
        list.innerHTML = '';
        checkIns.forEach((c, i) => {
            const initials = (c.student_name || 'U')
                .split(' ').filter(n => n).map(n => n[0]).join('').substring(0, 2).toUpperCase();

            const hasTimeOut = c.time_out && c.time_out !== '--';
            const borderColor = hasTimeOut ? '#f59e0b' : '#10b981';

            const item = document.createElement('div');
            item.className = 'activity-item' + (i === 0 && hasNewData ? ' new-item' : '');
            item.style.borderLeftColor = borderColor;
            
            item.innerHTML = `
                <div class="activity-avatar">${initials}</div>
                <div class="activity-info">
                    <div class="activity-name">${c.student_name}</div>
                    <div class="activity-details">${c.grade}${c.lrn ? ' • LRN: ' + c.lrn : ''}</div>
                    <div class="activity-times">
                        <div class="time-in">
                            <i class="bi bi-box-arrow-in-right"></i> IN: ${c.time_in || '--'}
                        </div>
                        <div class="time-out" style="color: ${hasTimeOut ? '#f59e0b' : '#9ca3af'};">
                            <i class="bi bi-box-arrow-right"></i> OUT: ${c.time_out || '--'}
                        </div>
                        
                    </div>
                </div>
            `;
            list.appendChild(item);
        });

        document.getElementById('lastCheckIn').textContent = checkIns[0]?.time_in || '--';
    }

    // ── Update stats ───────────────────────────────────────────────────────────
    function updateStats(stats) {
        animateValue('presentCount',   stats.present);
        animateValue('absentCount',    stats.absent);
        animateValue('summaryPresent', stats.present);
        animateValue('summaryAbsent',  stats.absent);
        
        const pct = stats.total > 0 ? Math.round((stats.present / stats.total) * 100) : 0;
        document.getElementById('attendancePercentage').textContent = pct + '%';
        updateProgressCircle(pct);
    }

    function animateValue(id, end) {
        const el = document.getElementById(id);
        if (!el) return;
        const start = parseInt(el.textContent) || 0;
        if (start === end) return;
        el.textContent = end;
    }

    function updateProgressCircle(pct) {
        const circle = document.getElementById('progressCircle');
        const text   = document.getElementById('attendancePercentage');
        if (!circle) return;
        
        const circ = 2 * Math.PI * 65;
        circle.style.strokeDasharray  = circ;
        circle.style.strokeDashoffset = circ - (pct / 100) * circ;
        
        const color = pct >= 80 ? '#10b981' : pct >= 50 ? '#f59e0b' : '#ef4444';
        circle.style.stroke = color;
        if (text) text.style.color = color;
    }

    // ── Sounds ─────────────────────────────────────────────────────────────────
    function playSuccessSound() {
        if (!soundEnabled) return;
        console.log('🔊 Playing success sound');
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [[800,0,0.1],[1000,0.15,0.25],[1200,0.3,0.45]].forEach(([freq,s,e]) => {
            const osc = ctx.createOscillator(), g = ctx.createGain();
            osc.connect(g); g.connect(ctx.destination);
            osc.frequency.value = freq; osc.type = 'sine';
            g.gain.setValueAtTime(0.3, ctx.currentTime+s);
            g.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime+e);
            osc.start(ctx.currentTime+s); osc.stop(ctx.currentTime+e);
        });
    }

    function playCheckoutSound() {
        if (!soundEnabled) return;
        console.log('🔊 Playing checkout sound');
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [[1000,0,0.15],[800,0.2,0.35]].forEach(([freq,s,e]) => {
            const osc = ctx.createOscillator(), g = ctx.createGain();
            osc.connect(g); g.connect(ctx.destination);
            osc.frequency.value = freq; osc.type = 'sine';
            g.gain.setValueAtTime(0.3, ctx.currentTime+s);
            g.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime+e);
            osc.start(ctx.currentTime+s); osc.stop(ctx.currentTime+e);
        });
    }

    // ── Refresh button ─────────────────────────────────────────────────────────
    document.getElementById('refreshList').addEventListener('click', function() {
        const btn = this, orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Refreshing...';
        console.log('🔄 Manual refresh triggered');
        pollAttendance();
        setTimeout(() => { 
            btn.disabled = false; 
            btn.innerHTML = orig; 
        }, 1000);
    });

    // ── Initialize ─────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Live Monitor Started');
        console.log('⏱️ Auto-refresh: Every 1 second');
        console.log('🕐 Timezone: Philippine Time (Asia/Manila)');
        console.log('🎭 Popups: Enabled for check-in/check-out');
        console.log('👀 Watch the console for real-time debugging');
        
        // Initial stats
        updateStats({
            total:   {{ $totalStudents }},
            present: {{ $presentToday }},
            absent:  {{ $absentToday }}
        });

        // Start polling immediately
        pollAttendance();
        
        // Then poll every 1 second
        setInterval(pollAttendance, 1000);
    });
</script>
@endsection