<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        Audit Log Report
    </title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            margin: 0;
            color: #333333;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        .report-header {
            padding-bottom: 14px;
            border-bottom: 3px solid #d55b91;
            text-align: center;
        }

        .report-header h1 {
            margin: 0 0 5px;
            color: #d55b91;
            font-size: 22px;
        }

        .report-header p {
            margin: 3px 0;
            color: #666666;
            font-size: 10px;
        }

        .report-information {
            width: 100%;
            margin: 14px 0;
            border-collapse: collapse;
        }

        .report-information td {
            width: 33.33%;
            padding: 8px 10px;
            border: 1px solid #ead5df;
            background: #fff6fa;
        }

        .information-label {
            display: block;
            margin-bottom: 3px;
            color: #888888;
            font-size: 8px;
            text-transform: uppercase;
        }

        .information-value {
            color: #333333;
            font-size: 11px;
            font-weight: bold;
        }

        .filter-box {
            margin-bottom: 14px;
            padding: 9px 12px;
            border: 1px solid #eeeeee;
            background: #fafafa;
        }

        .filter-box strong {
            color: #d55b91;
        }

        table.audit-table {
            width: 100%;
            border-collapse: collapse;
        }

        .audit-table th {
            padding: 8px 5px;
            border: 1px solid #c94d85;
            background: #d55b91;
            color: #ffffff;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .audit-table td {
            padding: 7px 5px;
            border: 1px solid #dddddd;
            font-size: 8px;
            vertical-align: top;
        }

        .audit-table tbody tr:nth-child(even) {
            background: #fff7fa;
        }

        .role,
        .result {
            font-weight: bold;
            text-transform: capitalize;
        }

        .success {
            color: #218143;
        }

        .failed {
            color: #bd3434;
        }

        .empty-record {
            padding: 30px !important;
            color: #777777;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -15px;
            left: 0;
            color: #777777;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="report-header">
        <h1>
            RFID-BARM SYSTEM
        </h1>

        <p>
            Audit Log Report
        </p>

        <p>
            Generated:
            {{ now()->format('F d, Y h:i A') }}
        </p>
    </div>

    <table class="report-information">
        <tr>
            <td>
                <span class="information-label">
                    Total Records
                </span>

                <span class="information-value">
                    {{ $auditLogs->count() }}
                </span>
            </td>

            <td>
                <span class="information-label">
                    Successful
                </span>

                <span class="information-value">
                    {{
                        $auditLogs
                            ->where(
                                'result',
                                'success'
                            )
                            ->count()
                    }}
                </span>
            </td>

            <td>
                <span class="information-label">
                    Failed
                </span>

                <span class="information-value">
                    {{
                        $auditLogs
                            ->where(
                                'result',
                                'failed'
                            )
                            ->count()
                    }}
                </span>
            </td>
        </tr>
    </table>

    @if(
        collect($filters)
            ->filter()
            ->isNotEmpty()
    )
        <div class="filter-box">
            <strong>
                Applied filters:
            </strong>

            Search:
            {{ $filters['search'] ?: 'All' }}

            &nbsp; | &nbsp;

            Role:
            {{ ucfirst($filters['role'] ?: 'All') }}

            &nbsp; | &nbsp;

            Module:
            {{ $filters['module'] ?: 'All' }}

            &nbsp; | &nbsp;

            Result:
            {{ ucfirst($filters['result'] ?: 'All') }}

            &nbsp; | &nbsp;

            Date:
            {{
                $filters['date']
                    ? \Carbon\Carbon::parse(
                        $filters['date']
                    )->format('F d, Y')
                    : 'All'
            }}
        </div>
    @endif

    <table class="audit-table">
        <thead>
            <tr>
                <th style="width: 13%;">
                    Date and Time
                </th>

                <th style="width: 13%;">
                    User
                </th>

                <th style="width: 8%;">
                    Role
                </th>

                <th style="width: 12%;">
                    Action
                </th>

                <th style="width: 12%;">
                    Module
                </th>

                <th style="width: 22%;">
                    Affected Record
                </th>

                <th style="width: 11%;">
                    IP Address
                </th>

                <th style="width: 9%;">
                    Result
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($auditLogs as $log)
                <tr>
                    <td>
                        {{
                            $log->created_at
                                ->format(
                                    'M d, Y h:i:s A'
                                )
                        }}
                    </td>

                    <td>
                        {{
                            $log->actor_name
                            ?? 'System'
                        }}
                    </td>

                    <td class="role">
                        {{
                            ucfirst(
                                $log->user_role
                            )
                        }}
                    </td>

                    <td>
                        {{ $log->action }}
                    </td>

                    <td>
                        {{ $log->module }}
                    </td>

                    <td>
                        {{
                            $log->affected_record
                            ?? '-'
                        }}

                        @if($log->description)
                            <br>

                            <small>
                                {{ $log->description }}
                            </small>
                        @endif
                    </td>

                    <td>
                        {{
                            $log->ip_address
                            ?? '-'
                        }}
                    </td>

                    <td
                        class="result
                        {{
                            strtolower(
                                $log->result
                            )
                        }}"
                    >
                        {{
                            ucfirst(
                                $log->result
                            )
                        }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td
                        colspan="8"
                        class="empty-record"
                    >
                        No audit logs matched the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        RFID-BARM SYSTEM — Audit Log Report —
        {{ date('Y') }}
    </div>

</body>
</html>