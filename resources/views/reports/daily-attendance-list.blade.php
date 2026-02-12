<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $school_name }} - Daily Attendance List</title>
    <style>
        @page {
            margin: 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11pt;
            color: #2c3e50;
            line-height: 1.5;
        }

        /* Enhanced Header Design */
        .header {
            text-align: center;
            padding: 25px 0;
            margin-bottom: 30px;
            border-bottom: 5px double #1a5490;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1a5490 0%, #2874a6 50%, #1a5490 100%);
            border-radius: 8px 8px 0 0;
        }

        .logo-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .logo-left,
        .logo-right {
            display: table-cell;
            width: 20%;
            vertical-align: middle;
        }

        .logo-center {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            text-align: center;
        }

        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto;
            background: linear-gradient(135deg, #1a5490 0%, #2874a6 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            border: 4px solid #154360;
            box-shadow: 0 4px 15px rgba(26, 84, 144, 0.3);
        }

        .school-info {
            margin-top: 10px;
        }

        .republic {
            font-size: 9pt;
            color: #5d6d7e;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .deped {
            font-size: 10pt;
            color: #2874a6;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .school-name {
            font-size: 24pt;
            font-weight: 900;
            color: #1a5490;
            margin: 8px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .school-address {
            font-size: 10pt;
            color: #5d6d7e;
            margin-top: 5px;
            font-weight: 500;
        }

        .school-contact {
            font-size: 9pt;
            color: #7f8c8d;
            margin-top: 3px;
            font-style: italic;
        }

        .divider {
            width: 200px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #2874a6, transparent);
            margin: 15px auto;
        }

        .report-title-section {
            background: linear-gradient(135deg, #1a5490 0%, #2874a6 100%);
            color: white;
            padding: 15px 20px;
            margin: 0 -20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(26, 84, 144, 0.2);
        }

        .report-title {
            font-size: 18pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }

        .report-subtitle {
            font-size: 11pt;
            margin-top: 5px;
            opacity: 0.95;
            font-weight: 500;
        }

        /* Report Info Bar */
        .report-info-bar {
            display: table;
            width: 100%;
            background: #f8f9fa;
            border: 2px solid #d5dbdb;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
        }

        .info-item {
            display: table-cell;
            padding: 12px 15px;
            text-align: center;
            border-right: 2px solid #d5dbdb;
            width: 33.33%;
        }

        .info-item:last-child {
            border-right: none;
        }

        .info-label {
            font-size: 8pt;
            color: #5d6d7e;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 12pt;
            color: #1a5490;
            font-weight: 800;
        }

        /* Summary Stats */
        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            border: 2px solid;
        }

        .stat-box.total {
            background: #ebf5fb;
            border-color: #5dade2;
        }

        .stat-box.present {
            background: #d5f4e6;
            border-color: #52be80;
        }

        .stat-box.late {
            background: #fef5e7;
            border-color: #f39c12;
        }

        .stat-box.absent {
            background: #fadbd8;
            border-color: #ec7063;
        }

        .stat-label {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #34495e;
        }

        .stat-number {
            font-size: 20pt;
            font-weight: 900;
        }

        .stat-box.total .stat-number { color: #2874a6; }
        .stat-box.present .stat-number { color: #27ae60; }
        .stat-box.late .stat-number { color: #e67e22; }
        .stat-box.absent .stat-number { color: #c0392b; }

        /* Student List Table */
        .section-title {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 12px 15px;
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0;
            border-radius: 6px 6px 0 0;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .student-table thead {
            background: linear-gradient(135deg, #5d6d7e 0%, #34495e 100%);
            color: white;
        }

        .student-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 700;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #2c3e50;
        }

        .student-table td {
            padding: 10px;
            border: 1px solid #d5dbdb;
            font-size: 10pt;
        }

        .student-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .student-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .student-table tbody tr:hover {
            background: #ebf5fb;
        }

        /* Student Number Column */
        .student-number {
            text-align: center;
            font-weight: 700;
            color: #5d6d7e;
            width: 40px;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-present {
            background: #27ae60;
            color: white;
        }

        .status-late {
            background: #f39c12;
            color: white;
        }

        .status-absent {
            background: #e74c3c;
            color: white;
        }

        /* Grade Badge */
        .grade-badge {
            display: inline-block;
            background: #8e44ad;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 45px;
            border-top: 3px solid #1a5490;
            padding: 10px 15mm;
            font-size: 8pt;
            color: #5d6d7e;
            background: white;
        }

        .footer-left {
            float: left;
            font-weight: 600;
        }

        .footer-right {
            float: right;
            font-style: italic;
        }

        /* Page Numbers */
        .page-number {
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
            margin-top: 5px;
        }

        /* Signatures */
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }

        .signature-line {
            border-top: 2px solid #2c3e50;
            margin-top: 40px;
            padding-top: 8px;
            font-weight: 700;
            color: #2c3e50;
        }

        .signature-title {
            font-size: 9pt;
            color: #5d6d7e;
            margin-top: 5px;
            font-style: italic;
        }

        /* No Students Message */
        .no-students {
            text-align: center;
            padding: 40px;
            background: #fef5e7;
            border: 3px dashed #f39c12;
            border-radius: 8px;
            margin: 20px 0;
        }

        .no-students-icon {
            font-size: 48pt;
            color: #f39c12;
            margin-bottom: 15px;
        }

        .no-students h3 {
            color: #d68910;
            margin-bottom: 10px;
        }

        /* Page Break Control */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Enhanced Header -->
    <div class="header">
        <div class="logo-section">
            <div class="logo-left"></div>
            <div class="logo-center">
                <div class="logo">CNHS</div>
            </div>
            <div class="logo-right"></div>
        </div>
        
        <div class="school-info">
            <div class="republic">Republic of the Philippines</div>
            <div class="deped">Department of Education</div>
            <div class="divider"></div>
            <div class="school-name">Concepcion National High School</div>
            <div class="school-address">Concepcion, Mabini, Bohol</div>
            <div class="school-contact">Tel: (045) 123-4567 • Email: cnhs@deped.gov.ph</div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title-section">
        <div class="report-title">Daily Attendance Record</div>
        <div class="report-subtitle">Student Attendance List</div>
    </div>

    <!-- Report Information Bar -->
    <div class="report-info-bar no-break">
        <div class="info-item">
            <div class="info-label">Date</div>
            <div class="info-value">{{ $report_date }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Grade Level</div>
            <div class="info-value">{{ $grade_filter }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Generated</div>
            <div class="info-value">{{ Carbon\Carbon::now()->format('M d, Y') }}</div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats no-break">
        <div class="stat-box total">
            <div class="stat-label">Total Students</div>
            <div class="stat-number">{{ $total_students }}</div>
        </div>
        <div class="stat-box present">
            <div class="stat-label">Present</div>
            <div class="stat-number">{{ $total_present }}</div>
        </div>
        <div class="stat-box late">
            <div class="stat-label">Late</div>
            <div class="stat-number">{{ $total_late }}</div>
        </div>
        <div class="stat-box absent">
            <div class="stat-label">Absent</div>
            <div class="stat-number">{{ $total_absent }}</div>
        </div>
    </div>

    @if($total_students == 0)
        <!-- No Students Message -->
        <div class="no-students">
            <div class="no-students-icon">📋</div>
            <h3>No Students Found</h3>
            <p>There are no students registered for the selected grade level(s).</p>
        </div>
    @else
        <!-- Student List by Grade -->
        @foreach($grades_data as $grade => $data)
            <div class="no-break">
                <div class="section-title">
                    {{ $grade }} - {{ $data['total'] }} Students (Present: {{ $data['present'] }}, Late: {{ $data['late'] }}, Absent: {{ $data['absent'] }})
                </div>

                <table class="student-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 35%;">Student Name</th>
                            <th style="width: 20%;">LRN</th>
                            <th style="width: 15%; text-align: center;">Time In</th>
                            <th style="width: 15%; text-align: center;">Status</th>
                            <th style="width: 15%;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $studentCounter = 1;
                            $allStudents = $data['present_students']
                                ->concat($data['late_students'])
                                ->concat($data['absent_students']);
                        @endphp

                        @foreach($allStudents as $student)
                            @php
                                $attendance = $student->attendances->first();
                                $status = 'absent';
                                $timeIn = 'No Check-in';
                                $remarks = 'Not present';
                                
                                if ($attendance) {
                                    $status = $attendance->status;
                                    if ($attendance->time_in) {
                                        $timeIn = Carbon\Carbon::parse($attendance->time_in)->format('h:i A');
                                    }
                                    if ($attendance->remarks) {
                                        $remarks = $attendance->remarks;
                                    } elseif ($status === 'present') {
                                        $remarks = 'On time';
                                    } elseif ($status === 'late') {
                                        $timeInCarbon = Carbon\Carbon::parse($attendance->time_in);
                                        $cutoff = Carbon\Carbon::parse('08:00');
                                        $minutesLate = $timeInCarbon->diffInMinutes($cutoff);
                                        $remarks = $minutesLate . ' mins late';
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="student-number">{{ $studentCounter++ }}</td>
                                <td><strong>{{ $student->name }}</strong></td>
                                <td style="font-family: monospace; color: #5d6d7e;">{{ $student->lrn }}</td>
                                <td style="text-align: center;">{{ $timeIn }}</td>
                                <td style="text-align: center;">
                                    <span class="status-badge status-{{ $status }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>
                                <td style="font-size: 9pt; color: #7f8c8d;">{{ $remarks }}</td>
                            </tr>
                        @endforeach

                        @if($allStudents->count() == 0)
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #95a5a6; font-style: italic;">
                                    No students found for this grade level
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-col">
                <div class="signature-line">Prepared By</div>
                <div class="signature-title">Attendance Teacher/Officer</div>
            </div>
            <div class="signature-col">
                <div class="signature-line">Noted By</div>
                <div class="signature-title">School Principal</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            Concepcion National High School • Official Attendance Record
        </div>
        <div class="footer-right">
            Generated: {{ $generated_date }}
        </div>
    </div>
</body>
</html>