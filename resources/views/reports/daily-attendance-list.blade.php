<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $school_name }} - Daily Attendance List</title>
    <style>
        @page {
            margin: 15mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        /* Header with Logo */
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px auto;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
            border: 3px solid #1e40af;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin: 8px 0 4px 0;
            text-transform: uppercase;
        }

        .school-address {
            font-size: 9pt;
            color: #666;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2563eb;
            margin-top: 8px;
            padding: 8px;
            background: #eff6ff;
            border-radius: 4px;
        }

        /* Report Info Bar */
        .report-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #2563eb;
        }

        .report-info table {
            width: 100%;
        }

        .report-info td {
            padding: 4px;
            font-size: 9pt;
        }

        .report-info td:first-child {
            font-weight: bold;
            color: #1e40af;
            width: 140px;
        }

        .report-info .highlight {
            background: #fef3c7;
            padding: 8px;
            border-radius: 4px;
            margin-top: 8px;
            text-align: center;
            font-weight: bold;
            color: #92400e;
        }

        /* Summary Stats Bar */
        .summary-bar {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            background: #f9fafb;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            border-left: 3px solid #2563eb;
        }

        .summary-item.success {
            border-left-color: #10b981;
        }

        .summary-item.warning {
            border-left-color: #f59e0b;
        }

        .summary-item.danger {
            border-left-color: #ef4444;
        }

        .summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
        }

        /* Grade Section */
        .grade-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .grade-header {
            background: linear-gradient(to right, #2563eb, #3b82f6);
            color: white;
            padding: 10px 12px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }

        .grade-title {
            font-size: 12pt;
            font-weight: bold;
            display: inline-block;
        }

        .grade-stats {
            float: right;
            font-size: 9pt;
        }

        .grade-stats span {
            margin-left: 15px;
            padding: 3px 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        /* Student Lists */
        .students-container {
            border: 2px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 6px 6px;
        }

        .status-section {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .status-section:last-child {
            border-bottom: none;
        }

        .status-header {
            font-weight: bold;
            margin-bottom: 8px;
            padding: 6px;
            border-radius: 4px;
            font-size: 10pt;
        }

        .status-present {
            background: #d1fae5;
            color: #065f46;
        }

        .status-late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .student-list {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .student-row {
            display: table-row;
        }

        .student-row > div {
            display: table-cell;
            padding: 6px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 9pt;
        }

        .student-row:last-child > div {
            border-bottom: none;
        }

        .student-number {
            width: 35px;
            color: #9ca3af;
            font-weight: bold;
        }

        .student-name {
            width: 40%;
            font-weight: 600;
            color: #111827;
        }

        .student-lrn {
            width: 30%;
            font-family: monospace;
            color: #6b7280;
        }

        .student-time {
            width: 20%;
            text-align: center;
            color: #374151;
        }

        .student-remarks {
            text-align: right;
            font-size: 8pt;
            color: #9ca3af;
            font-style: italic;
        }

        /* No students message */
        .no-students {
            padding: 15px;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            background: #f9fafb;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 2px solid #2563eb;
            padding: 8px 10mm;
            font-size: 7pt;
            color: #666;
            background: white;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-item {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 2px solid #333;
            padding-top: 5px;
            margin: 0 30px;
            font-weight: bold;
        }

        .signature-label {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }

        /* Page Break Control */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-count {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Empty State */
        .empty-day {
            text-align: center;
            padding: 40px;
            background: #fef3c7;
            border: 2px dashed #f59e0b;
            border-radius: 8px;
            margin: 20px 0;
        }

        .empty-day i {
            font-size: 40pt;
            color: #f59e0b;
            margin-bottom: 15px;
        }

        .empty-day h3 {
            color: #92400e;
            margin-bottom: 8px;
        }

        .empty-day p {
            color: #78350f;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">CNHS</div>
        <div class="school-name">{{ $school_name }}</div>
        <div class="school-address">{{ $school_address }}</div>
        <div class="report-title">{{ $report_title }}</div>
    </div>

    <!-- Report Info -->
    <div class="report-info no-break">
        <table>
            <tr>
                <td>Report Date:</td>
                <td><strong>{{ $report_date }}</strong></td>
            </tr>
            <tr>
                <td>Grade Level:</td>
                <td><strong>{{ $grade_filter }}</strong></td>
            </tr>
            <tr>
                <td>Generated On:</td>
                <td>{{ $generated_date }}</td>
            </tr>
        </table>
        <div class="highlight">
            📋 This report shows all students present, late, and absent for the selected date
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-bar no-break">
        <div class="summary-item">
            <div class="summary-label">Total Students</div>
            <div class="summary-value">{{ $total_students }}</div>
        </div>
        <div class="summary-item success">
            <div class="summary-label">Present</div>
            <div class="summary-value" style="color: #10b981;">{{ $total_present }}</div>
        </div>
        <div class="summary-item warning">
            <div class="summary-label">Late</div>
            <div class="summary-value" style="color: #f59e0b;">{{ $total_late }}</div>
        </div>
        <div class="summary-item danger">
            <div class="summary-label">Absent</div>
            <div class="summary-value" style="color: #ef4444;">{{ $total_absent }}</div>
        </div>
    </div>

    @if($total_students == 0)
        <!-- No Students Message -->
        <div class="empty-day">
            <h3>No Students Found</h3>
            <p>There are no students registered for the selected grade level(s).</p>
        </div>
    @else
        <!-- By Grade Level -->
        @foreach($grades_data as $grade => $data)
        <div class="grade-section">
            <div class="grade-header">
                <span class="grade-title">{{ $grade }}</span>
                <span class="grade-stats">
                    <span>Total: {{ $data['total'] }}</span>
                    <span>Present: {{ $data['present'] }}</span>
                    <span>Late: {{ $data['late'] }}</span>
                    <span>Absent: {{ $data['absent'] }}</span>
                    <span>Rate: {{ $data['attendance_rate'] }}%</span>
                </span>
            </div>

            <div class="students-container">
                <!-- Present Students -->
                @if($data['present_students']->count() > 0)
                <div class="status-section">
                    <div class="status-header status-present">
                        ✓ PRESENT STUDENTS <span class="badge badge-count">{{ $data['present_students']->count() }}</span>
                    </div>
                    <div class="student-list">
                        @foreach($data['present_students'] as $index => $student)
                        <div class="student-row">
                            <div class="student-number">{{ $index + 1 }}.</div>
                            <div class="student-name">{{ $student->name }}</div>
                            <div class="student-lrn">LRN: {{ $student->lrn }}</div>
                            <div class="student-time">
                                @if($student->attendances->first() && $student->attendances->first()->time_in)
                                    {{ \Carbon\Carbon::parse($student->attendances->first()->time_in)->format('h:i A') }}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="student-remarks">
                                @if($student->attendances->first() && $student->attendances->first()->remarks)
                                    {{ $student->attendances->first()->remarks }}
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Late Students -->
                @if($data['late_students']->count() > 0)
                <div class="status-section">
                    <div class="status-header status-late">
                        ⚠ LATE ARRIVALS <span class="badge badge-count">{{ $data['late_students']->count() }}</span>
                    </div>
                    <div class="student-list">
                        @foreach($data['late_students'] as $index => $student)
                        <div class="student-row">
                            <div class="student-number">{{ $index + 1 }}.</div>
                            <div class="student-name">{{ $student->name }}</div>
                            <div class="student-lrn">LRN: {{ $student->lrn }}</div>
                            <div class="student-time">
                                @if($student->attendances->first() && $student->attendances->first()->time_in)
                                    {{ \Carbon\Carbon::parse($student->attendances->first()->time_in)->format('h:i A') }}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="student-remarks">
                                @if($student->attendances->first() && $student->attendances->first()->remarks)
                                    {{ $student->attendances->first()->remarks }}
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Absent Students -->
                @if($data['absent_students']->count() > 0)
                <div class="status-section">
                    <div class="status-header status-absent">
                        ✗ ABSENT STUDENTS <span class="badge badge-count">{{ $data['absent_students']->count() }}</span>
                    </div>
                    <div class="student-list">
                        @foreach($data['absent_students'] as $index => $student)
                        <div class="student-row">
                            <div class="student-number">{{ $index + 1 }}.</div>
                            <div class="student-name">{{ $student->name }}</div>
                            <div class="student-lrn">LRN: {{ $student->lrn }}</div>
                            <div class="student-time">No Check-in</div>
                            <div class="student-remarks">
                                @if($student->attendances->first() && $student->attendances->first()->remarks)
                                    {{ $student->attendances->first()->remarks }}
                                @else
                                    Not present
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- If all sections are empty -->
                @if($data['total'] > 0 && $data['present_students']->count() == 0 && $data['late_students']->count() == 0 && $data['absent_students']->count() == 0)
                <div class="no-students">
                    No attendance data recorded for this grade level on {{ $report_date }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-item">
                <div class="signature-line">Prepared By</div>
                <div class="signature-label">Attendance Officer / Teacher</div>
            </div>
            <div class="signature-item">
                <div class="signature-line">Noted By</div>
                <div class="signature-label">School Principal</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            © {{ date('Y') }} {{ $school_name }} • Confidential Document
        </div>
        <div class="footer-right">
            Generated: {{ $generated_date }}
        </div>
    </div>
</body>
</html>
