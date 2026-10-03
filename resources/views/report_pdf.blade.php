<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle ?? 'Library Report' }}</title>

    <style>
        @page {
            size: landscape;
            margin: 24px;
        }

        body {
            margin: 0;
            color: #333333;
            font-family: DejaVu Sans, sans-serif;
            font-size: {{ count($columns ?? []) > 8 ? '7px' : '9px' }};
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
            padding: 8px 10px;
            border: 1px solid #ead5df;
            background: #fff6fa;
            vertical-align: top;
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

        .filter-details {
            margin-bottom: 12px;
            padding: 8px 10px;
            border: 1px solid #ead5df;
            background: #fffafb;
            color: #666666;
        }

        .filter-details strong {
            color: #444444;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .records-table th {
            padding: 7px 5px;
            border: 1px solid #c94d85;
            background: #d55b91;
            color: #ffffff;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
            overflow-wrap: anywhere;
        }

        .records-table td {
            padding: 7px 5px;
            border: 1px solid #dddddd;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .records-table tbody tr:nth-child(even) {
            background: #fff7fa;
        }

        .status-success {
            color: #218143;
            font-weight: bold;
        }

        .status-primary {
            color: #315caa;
            font-weight: bold;
        }

        .status-warning {
            color: #a76d00;
            font-weight: bold;
        }

        .status-danger {
            color: #bd3434;
            font-weight: bold;
        }

        .status-neutral {
            color: #555555;
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
    @php
        $reportTitle = $reportTitle ?? 'Library Report';
        $summary = $summary ?? collect();
        $columns = $columns ?? [];
        $rows = $rows ?? collect();

        $filters = $filters ?? [];
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $course = $filters['course'] ?? null;
        $userType = $filters['user_type'] ?? null;
        $bookStatus = $filters['book_status'] ?? null;
    @endphp

    <div class="report-header">
        <h1>RFID-BARM SYSTEM</h1>
        <p>{{ $reportTitle }}</p>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    @if(count($summary))
        <table class="report-summary">
            <tr>
                @foreach($summary as $item)
                    <td style="width: {{ 100 / max(1, count($summary)) }}%;">
                        <div class="summary-label">
                            {{ $item['label'] ?? 'Summary' }}
                        </div>

                        <div class="summary-value">
                            {{ $item['value'] ?? 0 }}
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @else
        <table class="report-summary">
            <tr>
                <td>
                    <div class="summary-label">Total Records</div>
                    <div class="summary-value">{{ count($rows) }}</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="filter-details">
        <strong>Period:</strong>
        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('M d, Y') : 'Beginning' }}
        –
        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('M d, Y') : 'Present' }}

        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Course:</strong> {{ $course ?: 'All Programs' }}

        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>User Type:</strong>
        {{ $userType ? ucfirst($userType) : 'All Users' }}

        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Status:</strong>
        {{ $bookStatus ? ucwords(str_replace('_', ' ', $bookStatus)) : 'All Statuses' }}
    </div>

    <table class="records-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>

                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    @foreach($columns as $column)
                        @php
                            $value = $row[$column] ?? '-';
                            $normalized = strtolower(trim((string) $value));
                            $statusClass = '';

                            if (in_array($normalized, [
                                'available',
                                'returned',
                                'paid',
                                'returned on time',
                                'success'
                            ], true)) {
                                $statusClass = 'status-success';
                            } elseif (in_array($normalized, [
                                'borrowed',
                                'reserved',
                                'pending',
                                'student',
                                'personnel'
                            ], true)) {
                                $statusClass = 'status-primary';
                            } elseif (in_array($normalized, [
                                'damaged',
                                'partially paid',
                                'returned late'
                            ], true)) {
                                $statusClass = 'status-warning';
                            } elseif (in_array($normalized, [
                                'overdue',
                                'lost',
                                'unpaid',
                                'failed'
                            ], true)) {
                                $statusClass = 'status-danger';
                            } elseif (in_array($column, [
                                'Status',
                                'Return Status',
                                'Payment Status',
                                'User Type'
                            ], true)) {
                                $statusClass = 'status-neutral';
                            }
                        @endphp

                        <td class="{{ $statusClass }}">{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="empty-records">
                        No records matched the selected report filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        RFID-BARM SYSTEM — {{ $reportTitle }} — {{ date('Y') }}
    </div>
</body>
</html>
