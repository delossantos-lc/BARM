<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        Personnel Monitoring Report
    </title>

    <style>
        @page {
            margin: 24px;
        }

        body {
            margin: 0;
            color: #333333;
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
            color: #666666;
        }

        .report-summary {
            width: 100%;
            margin: 13px 0;
            border-collapse: collapse;
        }

        .report-summary td {
            width: 33.33%;
            padding: 8px 10px;
            border: 1px solid #ead5df;
            background: #fff6fa;
        }

        .summary-label {
            color: #777777;
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
            color: #ffffff;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .records-table td {
            padding: 7px 5px;
            border: 1px solid #dddddd;
            vertical-align: top;
        }

        .records-table tbody tr:nth-child(even) {
            background: #fff7fa;
        }

        .personnel-name {
            font-weight: bold;
        }

        .status-inside {
            color: #218143;
            font-weight: bold;
        }

        .status-out {
            color: #555555;
            font-weight: bold;
        }

        .status-warning {
            color: #b57600;
            font-weight: bold;
        }

        .status-none {
            color: #888888;
            font-weight: bold;
        }

        .empty-records {
            padding: 30px !important;
            color: #777777;
            text-align: center;
        }

        .report-footer {
            position: fixed;
            right: 0;
            bottom: -14px;
            left: 0;
            color: #777777;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- REPORT HEADER --}}
    <div class="report-header">
        <h1>
            RFID-BARM SYSTEM
        </h1>

        <p>
            Personnel Attendance Monitoring Report
        </p>

        <p>
            Generated:
            {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>

    {{-- REPORT SUMMARY --}}
    <table class="report-summary">
        <tr>
            <td>
                <div class="summary-label">
                    Total Records
                </div>

                <div class="summary-value">
                    {{ $personnel->count() }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Date Filter
                </div>

                <div class="summary-value">
                    {{
                        !empty($filters['date'])
                            ? \Carbon\Carbon::parse(
                                $filters['date']
                            )->format('F d, Y')
                            : 'All Dates'
                    }}
                </div>
            </td>

            <td>
                <div class="summary-label">
                    Search
                </div>

                <div class="summary-value">
                    {{
                        !empty($filters['search'])
                            ? $filters['search']
                            : 'None'
                    }}
                </div>
            </td>
        </tr>
    </table>

    {{-- PERSONNEL RECORDS --}}
    <table class="records-table">
        <thead>
            <tr>
                <th style="width: 3%;">
                    #
                </th>

                <th style="width: 18%;">
                    Personnel
                </th>

                <th style="width: 13%;">
                    Employee Number
                </th>

                <th style="width: 17%;">
                    Department
                </th>

                <th style="width: 12%;">
                    Date
                </th>

                <th style="width: 11%;">
                    Time In
                </th>

                <th style="width: 11%;">
                    Time Out
                </th>

                <th style="width: 12%;">
                    Status
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($personnel as $person)
                @php
                    $attendance =
                        $person->latestAttendance;

                    $isInside =
                        $attendance
                        && $attendance->date
                        && $attendance->date->isToday()
                        && $attendance->time_in
                        && !$attendance->time_out;

                    if ($isInside) {
                        $status = 'Inside';
                        $statusClass = 'status-inside';
                    } elseif (
                        $attendance
                        && $attendance->time_out
                    ) {
                        $status = 'Time Out';
                        $statusClass = 'status-out';
                    } elseif (
                        $attendance
                        && $attendance->time_in
                    ) {
                        $status = 'No Time Out';
                        $statusClass = 'status-warning';
                    } else {
                        $status = 'No Attendance';
                        $statusClass = 'status-none';
                    }
                @endphp

                <tr>
                    {{-- NUMBER --}}
                    <td>
                        {{ $loop->iteration }}
                    </td>

                    {{-- PERSONNEL NAME --}}
                    <td>
                        <span class="personnel-name">
                            {{ $person->firstname }}
                            {{ $person->lastname }}
                        </span>
                    </td>

                    {{-- EMPLOYEE NUMBER --}}
                    <td>
                        {{
                            $person->employee_number
                            ?? '-'
                        }}
                    </td>

                    {{-- DEPARTMENT --}}
                    <td>
                        {{
                            $person->department
                            ?? '-'
                        }}
                    </td>

                    {{-- ATTENDANCE DATE --}}
                    <td>
                        {{
                            $attendance
                            && $attendance->date
                                ? $attendance->date
                                    ->format('M d, Y')
                                : '-'
                        }}
                    </td>

                    {{-- TIME IN --}}
                    <td>
                        {{
                            $attendance
                            && $attendance->time_in
                                ? $attendance->time_in
                                    ->format('h:i A')
                                : '-'
                        }}
                    </td>

                    {{-- TIME OUT --}}
                    <td>
                        {{
                            $attendance
                            && $attendance->time_out
                                ? $attendance->time_out
                                    ->format('h:i A')
                                : (
                                    $isInside
                                        ? 'Still Inside'
                                        : '-'
                                )
                        }}
                    </td>

                    {{-- STATUS --}}
                    <td class="{{ $statusClass }}">
                        {{ $status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="8"
                        class="empty-records"
                    >
                        No personnel records matched the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- REPORT FOOTER --}}
    <div class="report-footer">
        RFID-BARM SYSTEM —
        Personnel Monitoring Report —
        {{ date('Y') }}
    </div>

</body>

</html>