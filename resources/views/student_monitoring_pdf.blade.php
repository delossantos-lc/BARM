<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Monitoring Report</title>

    <style>
        @page {
            margin: 24px;
        }

        body {
            margin: 0;
            color: #333;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        .report-header {
            padding-bottom: 12px;
            border-bottom: 3px solid #d55b91;
            text-align: center;
        }

        .report-header h1 {
            margin: 0 0 5px;
            color: #d55b91;
            font-size: 21px;
        }

        .report-header p {
            margin: 2px 0;
            color: #666;
        }

        .report-summary {
            width: 100%;
            margin: 13px 0;
            border-collapse: collapse;
        }

        .report-summary td {
            padding: 8px 10px;
            border: 1px solid #ead5df;
            background: #fff6fa;
        }

        .summary-label {
            color: #777;
            font-size: 8px;
            text-transform: uppercase;
        }

        .summary-value {
            margin-top: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
        }

        .records-table th {
            padding: 7px 5px;
            border: 1px solid #c94d85;
            background: #d55b91;
            color: #fff;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .records-table td {
            padding: 7px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .records-table tbody tr:nth-child(even) {
            background: #fff7fa;
        }

        .status-inside {
            color: #218143;
            font-weight: bold;
        }

        .status-out {
            color: #555;
            font-weight: bold;
        }

        .status-warning {
            color: #b57600;
            font-weight: bold;
        }

        .empty-records {
            padding: 30px !important;
            color: #777;
            text-align: center;
        }

        .report-footer {
            position: fixed;
            right: 0;
            bottom: -14px;
            left: 0;
            color: #777;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <h1>RFID-BARM SYSTEM</h1>
        <p>Student Attendance Monitoring Report</p>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <table class="report-summary">
        <tr>
            <td>
                <div class="summary-label">Total Records</div>
                <div class="summary-value">{{ $students->count() }}</div>
            </td>

            <td>
                <div class="summary-label">Course Filter</div>
                <div class="summary-value">
                    {{ $filters['course'] ?: 'All Courses' }}
                </div>
            </td>

            <td>
                <div class="summary-label">Date Filter</div>
                <div class="summary-value">
                    {{
                        $filters['date']
                            ? \Carbon\Carbon::parse($filters['date'])->format('F d, Y')
                            : 'All Dates'
                    }}
                </div>
            </td>

            <td>
                <div class="summary-label">Search</div>
                <div class="summary-value">
                    {{ $filters['search'] ?: 'None' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="records-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 16%;">Student</th>
                <th style="width: 11%;">Student Number</th>
                <th style="width: 7%;">Year</th>
                <th style="width: 16%;">Course / Program</th>
                <th style="width: 11%;">Date</th>
                <th style="width: 10%;">Time In</th>
                <th style="width: 10%;">Time Out</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse($students as $student)
                @php
                    $attendance = $student->latestAttendance;

                    $isInside =
                        $attendance
                        && $attendance->date
                        && $attendance->date->isToday()
                        && $attendance->time_in
                        && !$attendance->time_out;

                    if ($isInside) {
                        $status = 'Inside';
                        $statusClass = 'status-inside';
                    } elseif ($attendance && $attendance->time_out) {
                        $status = 'Time Out';
                        $statusClass = 'status-out';
                    } elseif ($attendance && $attendance->time_in) {
                        $status = 'No Time Out';
                        $statusClass = 'status-warning';
                    } else {
                        $status = 'No Attendance';
                        $statusClass = 'status-out';
                    }
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $student->firstname }}
                        {{ $student->lastname }}
                    </td>

                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->year_level ?? '-' }}</td>
                    <td>{{ $student->course_program ?? '-' }}</td>

                    <td>
                        {{
                            $attendance && $attendance->date
                                ? $attendance->date->format('M d, Y')
                                : '-'
                        }}
                    </td>

                    <td>
                        {{
                            $attendance && $attendance->time_in
                                ? $attendance->time_in->format('h:i A')
                                : '-'
                        }}
                    </td>

                    <td>
                        {{
                            $attendance && $attendance->time_out
                                ? $attendance->time_out->format('h:i A')
                                : ($isInside ? 'Still Inside' : '-')
                        }}
                    </td>

                    <td class="{{ $statusClass }}">
                        {{ $status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-records">
                        No student records matched the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        RFID-BARM SYSTEM — Student Monitoring Report — {{ date('Y') }}
    </div>
</body>
</html>
