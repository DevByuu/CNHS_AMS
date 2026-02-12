<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $school_name }} - Present Students Report</title>
    <style>
        @page {
            margin: 20mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000000;
            line-height: 1.4;
            background-color: #ffffff;
        }

        /* Header Section - Three Column Layout */
        .header {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 5px;
            margin-top: 10px
        }

        .header-content {
            display: inline-block;
            text-align: center;
            position: relative;
        }

        .header-left,
        .header-right {
            display: inline-block;
            vertical-align: middle;
        }

        .header-center {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
            padding: 0 25px;
        }

        .logo {
            width: 85px;
            height: 85px;
            object-fit: contain;
            vertical-align: middle;
        }

        .header-text {
            line-height: 1.4;
        }

        .header-text .republic {
            font-size: 9pt;
            color: #000000;
        }

        .header-text .deped {
            font-weight: 700;
            font-size: 11pt;
            color: #000000;
        }

        .header-text .region {
            font-size: 9.5pt;
            color: #000000;
        }

        .header-text .school-name {
            font-weight: 700;
            font-size: 11pt;
            color: #000000;
            margin: 3px 0;
        }

        .header-text .address {
            font-size: 8.5pt;
            color: #000000;
            margin-top: 2px;
        }

        .header-divider {
            border-bottom: 1px solid #000000;
            margin: 8px 0 20px 0;
        }

        /* Title Section */
        .report-title {
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            color: #000000;
        }

        .report-subtitle {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 25px;
            color: #000000;
        }

        /* Table Styles */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9pt;
        }

        table.data-table th {
            background-color: #e8e8e8;
            border: 1px solid #000000;
            padding: 10px 8px;
            text-align: center;
            font-weight: 700;
            font-size: 9pt;
            color: #000000;
        }

        table.data-table td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
            color: #000000;
        }

        table.data-table td.center {
            text-align: center;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        /* Summary Stats */
        .summary-section {
            margin: 25px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border: 1px solid #cccccc;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .summary-label {
            display: table-cell;
            width: 200px;
            font-weight: 600;
            color: #000000;
        }

        .summary-value {
            display: table-cell;
            color: #000000;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            font-size: 8pt;
            text-align: center;
            color: #666666;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 60px;
            page-break-inside: avoid;
        }

        .signature-container {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 30px;
        }

        .signature-line {
            border-top: 1px solid #000000;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 9pt;
        }

        .signature-name {
            font-weight: 700;
            margin-top: 5px;
            color: #000000;
        }

        .signature-title {
            font-size: 8pt;
            margin-top: 3px;
            color: #000000;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @if($cnhs_logo_base64)
            <img src="{{ $cnhs_logo_base64 }}" alt="School Logo" class="logo">
            @endif
        </div><!--
        --><div class="header-center">
            <div class="header-text">
                <div class="republic">Republic of the Philippines</div>
                <div class="deped">Department of Education</div>
                <div class="region">Region VII - Central Visayas</div>
                <div class="school-name">{{ strtoupper($school_name) }}</div>
                <div class="address">{{ $school_address }}</div>
            </div>
        </div><!--
        --><div class="header-right">
            @if($deped_logo_base64)
            <img src="{{ $deped_logo_base64 }}" alt="DepEd Logo" class="logo">
            @endif
        </div>
    </div>

    <div class="header-divider"></div>

    <!-- Report Title -->
    <div class="report-title">List of Present Students</div>
    <div class="report-subtitle">{{ $report_date }}</div>

    <!-- Summary Statistics -->
    <div class="summary-section no-break">
        <div class="summary-row">
            <div class="summary-label">Date:</div>
            <div class="summary-value">{{ $report_date }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Grade Level:</div>
            <div class="summary-value">{{ $grade_filter ?? 'All Grades' }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Present:</div>
            <div class="summary-value">{{ number_format($stats['totalPresent']) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Enrolled:</div>
            <div class="summary-value">{{ number_format($stats['totalEnrolled']) }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Attendance Rate:</div>
            <div class="summary-value">{{ $stats['attendanceRate'] }}%</div>
        </div>
    </div>

    <!-- Students Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th style="width: 100px;">LRN</th>
                <th>Full Name</th>
                <th style="width: 100px;">Grade</th>
                <th style="width: 80px;">Time In</th>
                <th style="width: 80px;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presentStudents as $index => $student)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $student['lrn'] }}</td>
                <td>{{ $student['name'] }}</td>
                <td class="center">{{ $student['grade'] }}</td>
                <td class="center">{{ $student['time_in'] }}</td>
                <td class="center">Present</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary by Grade Level -->
    @if(isset($stats['gradeBreakdown']) && count($stats['gradeBreakdown']) > 1)
    <div class="page-break"></div>
    
    <div class="report-title" style="margin-top: 20px;">Summary by Grade Level</div>
    
    <table class="data-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Grade Level</th>
                <th>Total Present</th>
                <th>Total Enrolled</th>
                <th>Attendance Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['gradeBreakdown'] as $grade)
            <tr>
                <td class="center">{{ $grade['grade'] }}</td>
                <td class="center">{{ $grade['present'] }}</td>
                <td class="center">{{ $grade['total'] }}</td>
                <td class="center">{{ $grade['rate'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-container">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">_________________________</div>
                    <div class="signature-title">Prepared By</div>
                    <div class="signature-title">Attendance Officer</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">_________________________</div>
                    <div class="signature-title">Noted By</div>
                    <div class="signature-title">School Principal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated on {{ $generated_date }}
    </div>
</body>
</html>