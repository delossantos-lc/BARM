@include('layouts.header')
@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>
    :root {
        --mgmt-primary: #d6538c;
        --mgmt-primary-dark: #bd3f77;
        --mgmt-primary-soft: #fff2f7;
        --mgmt-primary-softer: #fff8fb;
        --mgmt-page: #f7f7fb;
        --mgmt-surface: #ffffff;
        --mgmt-border: #ebe7ed;
        --mgmt-border-soft: #f2eff3;
        --mgmt-text: #292631;
        --mgmt-muted: #797482;
        --mgmt-success: #218143;
        --mgmt-success-soft: #eaf8ef;
        --mgmt-danger: #bd3434;
        --mgmt-danger-soft: #ffeded;
        --mgmt-info: #315caa;
        --mgmt-info-soft: #eef3ff;
        --mgmt-warning: #b57b16;
        --mgmt-warning-soft: #fff6e5;
        --mgmt-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --mgmt-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .mgmt-page {
        color: var(--mgmt-text);
    }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .mgmt-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--mgmt-shadow);
    }

    .mgmt-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--mgmt-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }

    .mgmt-page .welcome-text span {
        color: var(--mgmt-muted);
        font-size: 13px;
    }

    .mgmt-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .78);
    }

    .mgmt-page .breadcrumb-item a {
        color: var(--mgmt-primary);
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .summary-card,
    .filter-card,
    .management-card {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
    }

    .summary-card {
        height: calc(100% - 30px);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--mgmt-shadow-hover);
    }

    .summary-card .card-body {
        position: relative;
        min-height: 132px;
        padding: 22px 24px;
    }

    .summary-card .card-body::after {
        position: absolute;
        top: 20px;
        right: 22px;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary);
        font-family: FontAwesome;
        font-size: 16px;
        line-height: 40px;
        text-align: center;
        content: "\f0c0";
    }

    .summary-card.summary-visited .card-body::after {
        content: "\f0f4";
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .summary-card.summary-inside .card-body::after {
        content: "\f2c2";
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }

    .summary-card h5 {
        max-width: calc(100% - 55px);
        margin-bottom: 12px;
        color: var(--mgmt-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .35px;
        text-transform: uppercase;
    }

    .summary-number {
        margin-bottom: 6px;
        color: var(--mgmt-text);
        font-size: 31px;
        font-weight: 800;
        line-height: 1;
    }

    .summary-card .text-muted {
        font-size: 11px;
    }

    /* ═══════════════════════════════════════
       FILTER CARD
       ═══════════════════════════════════════ */
    .filter-card {
        margin-bottom: 24px;
    }

    .filter-card .card-body {
        padding: 22px 24px;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
    }

    .filter-label i {
        color: var(--mgmt-primary);
        font-size: 11px;
        opacity: .85;
    }

    .filter-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background: #fff;
        color: var(--mgmt-text);
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .filter-control:hover {
        border-color: #c9c2ce;
    }

    .filter-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
        outline: none;
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper > i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: var(--mgmt-primary);
        opacity: .7;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .search-wrapper input {
        padding-left: 40px;
    }

    .clear-filter-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        min-height: 44px;
        border: 1px solid #e5dfe8;
        border-radius: 11px;
        background: #fff;
        color: #68616e;
        font-size: 13px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .clear-filter-button i {
        font-size: 12px;
        transition: transform .3s ease;
    }

    .clear-filter-button:hover,
    .clear-filter-button:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .12);
        transform: translateY(-1px);
        outline: none;
    }

    .clear-filter-button:hover i {
        transform: rotate(90deg);
    }

    .clear-filter-button:active {
        transform: translateY(0);
    }

    /* ═══════════════════════════════════════
       PILL TAB (All Personnel)
       ═══════════════════════════════════════ */
    .pill-tab {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 8px 16px;
        border: 1px solid rgba(236, 111, 165, 0.22);
        border-radius: 999px;
        background: #ffffff;
        color: var(--mgmt-primary-dark);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .2px;
        box-shadow: 0 4px 14px rgba(213, 91, 145, .10);
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        cursor: default;
        white-space: nowrap;
        align-self: center;
    }

    .pill-tab:hover {
        background: var(--mgmt-primary-softer);
        box-shadow: 0 6px 18px rgba(213, 91, 145, .16);
        transform: translateY(-1px);
    }

    .pill-tab i {
        color: var(--mgmt-primary);
        font-size: 14px;
    }

    .pill-tab .pill-label {
        color: var(--mgmt-primary-dark);
    }

    .pill-tab .pill-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 24px;
        padding: 0 8px;
        border-radius: 12px;
        background: var(--mgmt-primary);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .2px;
    }

    /* ═══════════════════════════════════════
       MANAGEMENT CARD HEADER
       ═══════════════════════════════════════ */
    .management-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        min-height: 78px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--mgmt-border);
        background: #fff;
    }

    .management-card .card-title {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
        margin: 0;
    }

    .management-card .card-title i {
        color: var(--mgmt-primary);
    }

    .management-card .card-body {
        padding: 22px;
    }

    /* ═══════════════════════════════════════
       AUTO REFRESH INDICATOR + BUTTON
       ═══════════════════════════════════════ */
    .auto-refresh-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-left: 12px;
        padding: 5px 12px;
        border: 1px solid #f5d6e1;
        border-radius: 20px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .2px;
    }

    .auto-refresh-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #28a745;
    }

    .auto-refresh-dot.refreshing {
        animation: refreshPulse 0.8s infinite alternate;
    }

    @keyframes refreshPulse {
        from { opacity: 0.3; transform: scale(0.8); }
        to   { opacity: 1; transform: scale(1.2); }
    }

    .refresh-now-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-left: 6px;
        border: 1px solid #f5d6e1;
        border-radius: 50%;
        background: #fff;
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        cursor: pointer;
        transition: transform .25s ease, background .2s ease, color .2s ease;
    }

    .refresh-now-btn:hover {
        background: var(--mgmt-primary);
        color: #fff;
        transform: rotate(180deg);
    }

    .refresh-now-btn.spinning i {
        animation: spin .8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    #personnelTable {
        width: 100% !important;
        margin-bottom: 0;
    }

    #personnelTable thead th {
        padding: 15px 14px;
        border: 0;
        border-bottom: 1px solid var(--mgmt-border);
        background: #f8f7fa;
        color: #625c68;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #personnelTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    #personnelTable.table-hover tbody tr:hover td {
        background: #fff7fa;
    }

    #personnelTable tbody tr.inside-row td {
        background: var(--mgmt-success-soft) !important;
    }

    #personnelTable.table-hover tbody tr.inside-row:hover td {
        background: #dcf7e4 !important;
    }

    #personnelTable tbody tr.inside-row td:first-child {
        position: relative;
    }

    #personnelTable tbody tr.inside-row td:first-child::before {
        position: absolute;
        top: 50%;
        left: 4px;
        width: 3px;
        height: 24px;
        border-radius: 3px;
        background: var(--mgmt-success);
        transform: translateY(-50%);
        content: "";
        animation: insidePulse 1.6s infinite ease-in-out;
    }

    @keyframes insidePulse {
        0%, 100% { opacity: .55; }
        50%      { opacity: 1; }
    }

    /* ═══════════════════════════════════════
       TABLE CELLS
       ═══════════════════════════════════════ */
    .person-name {
        color: var(--mgmt-text);
        font-weight: 700;
    }

    .person-number {
        display: block;
        margin-top: 3px;
        color: var(--mgmt-muted);
        font-size: 12px;
    }

    .time-in {
        color: var(--mgmt-success);
        font-weight: 700;
    }

    .time-out {
        color: var(--mgmt-danger);
        font-weight: 700;
    }

    .still-inside {
        color: var(--mgmt-warning);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       STATUS BADGES
       ═══════════════════════════════════════ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        letter-spacing: .2px;
    }

    .badge-success.status-badge {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .badge-secondary.status-badge {
        background: #f1f1f1;
        color: #555;
    }

    .badge-warning.status-badge {
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }

    .badge-light.status-badge {
        background: #f4f5f7;
        color: #888;
    }

    /* ═══════════════════════════════════════
       DATATABLES
       ═══════════════════════════════════════ */
    .dataTables_wrapper {
        padding: 6px 0 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info {
        padding-bottom: 12px;
        color: var(--mgmt-muted);
        font-size: 12px;
    }

    .dataTables_wrapper .dataTables_length select {
        min-height: 38px;
        border: 1px solid #dfdbe2;
        border-radius: 9px;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 12px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover,
    .page-item.active .page-link {
        border-color: var(--mgmt-primary) !important;
        background: var(--mgmt-primary) !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: var(--mgmt-primary-dark) !important;
        background: var(--mgmt-primary-dark) !important;
        color: #fff !important;
    }

    .page-link {
        color: var(--mgmt-primary);
    }

    /* ═══════════════════════════════════════
       EMPTY STATE
       ═══════════════════════════════════════ */
    #personnelTable tbody td[colspan="8"] {
        padding: 55px 20px !important;
        text-align: center;
    }

    #personnelTable tbody td[colspan="8"] i {
        color: #ccc;
        font-size: 45px;
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .mgmt-page .page-titles {
            padding: 18px;
        }

        .filter-column {
            margin-bottom: 15px;
        }

        .filter-card .card-body {
            padding: 18px;
        }

        .management-card .card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .management-card .card-body {
            padding: 16px;
        }

        .auto-refresh-status {
            margin-left: 0;
            margin-top: 8px;
        }

        .summary-number {
            font-size: 26px;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

<div class="content-body mgmt-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Personnel Monitoring</h4>
                    <span>Monitor personnel information and library attendance</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Personnel</li>
                </ol>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row">
            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Total Personnel</h5>
                        <p class="summary-number" id="totalPersonnelCount">{{ $totalPersonnel }}</p>
                        <span class="text-muted">Registered personnel</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="card summary-card summary-visited">
                    <div class="card-body">
                        <h5>Visited Today</h5>
                        <p class="summary-number text-primary" id="visitedTodayCount">{{ $visitedToday }}</p>
                        <span class="text-muted">Personnel who entered today</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="card summary-card summary-inside">
                    <div class="card-body">
                        <h5>Currently Inside</h5>
                        <p class="summary-number text-success" id="insideTodayCount">{{ $insideToday }}</p>
                        <span class="text-muted">Personnel without time out</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Fixed Department List as provided
            $departments = [
                'Arts and Sciences Program',
                'Teacher Education Program',
                'Allied Health Program',
                'Business, Accountancy, and Information Technology Program',
                'Hospitality Management Program',
                'Social Work Program'
            ];

            // ---------------------------------------------------------
            // SERVER-SIDE FILTERING LOGIC
            // ---------------------------------------------------------
            // 1. Check if the user has explicitly selected a date from the calendar.
            $selectedDate = request('personnel_date_filter');

            // 2. If no date is selected by the user, default to TODAY.
            $filterDate = $selectedDate ? $selectedDate : now()->format('Y-m-d');

            // 3. Group personnel and aggregate their attendance based on the filter date.
            $uniquePersonnel = $personnel->groupBy('id')->map(function ($personGroup) use ($filterDate, $selectedDate) {
                $person = $personGroup->first();

                // Get all attendance records for this person
                $allAttendances = $personGroup->pluck('latestAttendance')->filter();

                // Filter attendances to only those that match the active filter date
                $attendances = $allAttendances->filter(function($att) use ($filterDate) {
                    return $att->date && $att->date->format('Y-m-d') === $filterDate;
                })->sortBy('time_in');

                // Get the latest attendance record for this specific date
                $latestAttendance = $attendances->last();

                // Aggregate data
                $firstTimeIn = $attendances->whereNotNull('time_in')->first()?->time_in;
                $lastTimeOut = $attendances->whereNotNull('time_out')->last()?->time_out;
                $visitCount = $attendances->whereNotNull('time_in')->count();

                // Determine if the personnel is currently inside
                $isInside = $filterDate === now()->format('Y-m-d')
                    && $latestAttendance
                    && $latestAttendance->time_in
                    && !$latestAttendance->time_out;

                // Check if the latest attendance was a cut-off (auto timed out at 11:59 PM)
                $isCutOff = false;
                if ($latestAttendance && $latestAttendance->time_out) {
                    $isCutOff = $latestAttendance->time_out->format('H:i') === '23:59';
                }

                // Determine the date to display in the row (always the filter date)
                $displayDate = \Carbon\Carbon::parse($filterDate);

                // IMPORTANT: ONLY show personnel who actually have a visit count greater than 0.
                // If they did not time in/out, they are excluded from the table entirely.
                if ($visitCount === 0) {
                    return null;
                }

                return (object) [
                    'person' => $person,
                    'first_time_in' => $firstTimeIn,
                    'last_time_out' => $lastTimeOut,
                    'visit_count' => $visitCount,
                    'is_inside' => $isInside,
                    'is_cut_off' => $isCutOff,
                    'display_date' => $displayDate,
                    'raw_date' => $displayDate->format('Y-m-d'),
                ];
            })->filter()->sortByDesc(function($item) {
                return $item->person->id;
            });
        @endphp

        {{-- FILTER CARD --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-xl-5 col-lg-4 col-md-6 filter-column">
                        <label class="filter-label" for="personnelSearch">
                            <i class="fa fa-search"></i>
                            Search
                        </label>

                        <div class="search-wrapper">
                            <i class="fa fa-search"></i>
                            <input
                                type="text"
                                id="personnelSearch"
                                class="form-control filter-control"
                                placeholder="Search"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-3 col-md-6 filter-column">
                        <label class="filter-label" for="departmentFilter">
                            <i class="fa fa-building"></i>
                            Department
                        </label>

                        <select id="departmentFilter" class="form-control filter-control">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-6 filter-column">
                        <label class="filter-label" for="personnelDateFilter">
                            <i class="fa fa-calendar-o"></i>
                            Attendance Date
                        </label>

                        <input
                            type="date"
                            id="personnelDateFilter"
                            class="form-control filter-control"
                            value="{{ request('personnel_date_filter') }}"
                        >
                    </div>

                    <div class="col-xl-1 col-lg-2 col-md-6 filter-column">
                        <label class="filter-label">Clear</label>
                        <button
                            type="button"
                            id="clearPersonnelFilters"
                            class="btn clear-filter-button"
                            title="Clear filters"
                            aria-label="Clear filters"
                        >
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MANAGEMENT CARD --}}
        <div class="card management-card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-users mr-2"></i>
                        Personnel Records
                    </h4>

                    {{-- PILL TAB: All Personnel --}}
                    <div class="pill-tab">
                        <i class="fa fa-users"></i>
                        <span class="pill-label">All Personnel</span>
                        <span class="pill-count">{{ $uniquePersonnel->count() }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap">
                    <span class="auto-refresh-status" title="Live data updates automatically">
                        <span class="auto-refresh-dot" id="autoRefreshDot"></span>
                        <span id="autoRefreshText">Auto Refresh</span>
                    </span>

                    <button
                        type="button"
                        class="refresh-now-btn"
                        id="refreshNowBtn"
                        title="Refresh now"
                        aria-label="Refresh now"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="personnelTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Personnel</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Visits</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody id="personnelTableBody">
                        @forelse($uniquePersonnel as $data)
                            @php
                                $person = $data->person;
                                $isInside = $data->is_inside;
                                $isCutOff = $data->is_cut_off;
                            @endphp

                            <tr
                                class="{{ $isInside ? 'inside-row' : '' }}"
                                data-department="{{ strtolower($person->department ?? '') }}"
                                data-date="{{ $data->raw_date }}"
                            >
                                <td class="row-index"></td>

                                <td>
                                    <strong class="person-name">
                                        {{ $person->firstname }} {{ $person->lastname }}
                                    </strong>
                                    @if($person->employee_number)
                                        <span class="person-number">
                                            Employee No: {{ $person->employee_number }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $person->department ?? '-' }}</td>

                                <td>
                                    {{ $data->display_date->format($systemSettings->date_format ?? 'F j, Y') }}
                                </td>

                                <td>
                                    @if($data->visit_count > 0)
                                        <span class="badge badge-info status-badge" style="background: #eef3ff; color: #315caa;">
                                            {{ $data->visit_count }} {{ Str::plural('Visit', $data->visit_count) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($data->first_time_in)
                                        <i class="fa fa-sign-in mr-1 text-success"></i>
                                        <span class="time-in">
                                            {{ $data->first_time_in->format($systemSettings->time_format ?? 'h:i:s A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($data->last_time_out)
                                        <i class="fa fa-sign-out mr-1 text-danger"></i>
                                        <span class="time-out">
                                            {{ $data->last_time_out->format($systemSettings->time_format ?? 'h:i:s A') }}
                                        </span>
                                    @elseif($isInside)
                                        <span class="still-inside">
                                            <i class="fa fa-clock-o mr-1"></i>
                                            Still Inside
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($isInside)
                                        <span class="badge badge-success status-badge">Inside</span>
                                    @elseif($isCutOff)
                                        <span class="badge badge-danger status-badge" style="background: #ffeded; color: #bd3434;">Cut Off</span>
                                    @elseif($data->last_time_out)
                                        <span class="badge badge-secondary status-badge">Time Out</span>
                                    @elseif($data->first_time_in)
                                        <span class="badge badge-warning status-badge">No Time Out</span>
                                    @else
                                        <span class="badge badge-light status-badge">No Attendance</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fa fa-users"></i>
                                    <h5 class="mt-3">No personnel records found</h5>
                                    <p class="text-muted mb-0">No personnel have recorded attendance for this date.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function () {

    /* ============================================================
       DEPARTMENT + DATE CUSTOM FILTER
    ============================================================ */
    $.fn.dataTable.ext.search.push(function (settings, searchData, index) {
        if (settings.nTable.id !== 'personnelTable') return true;

        const rowNode = settings.aoData[index] ? settings.aoData[index].nTr : null;
        if (!rowNode) return true;

        const $row = $(rowNode);
        const selectedDepartment = $('#departmentFilter').val();
        const selectedDate = $('#personnelDateFilter').val();
        const rowDepartment = $row.attr('data-department') || '';
        const rowDate = $row.attr('data-date') || '';

        return (selectedDepartment === '' || rowDepartment === selectedDepartment)
            && (selectedDate === '' || rowDate === selectedDate);
    });

    /* ============================================================
       DATATABLE INIT
    ============================================================ */
    let personnelTable = $('#personnelTable').DataTable({
        pageLength: {{ (int) ($systemSettings->records_per_page ?? 10) }},
        order: [],
        dom: 'lrtip',
        language: {
            emptyTable: 'No personnel records found.',
            zeroRecords: 'No personnel match the selected filters.',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        columnDefs: [
            { orderable: false, targets: [0] }
        ],
        drawCallback: function () {
            const api = this.api();
            const startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    /* ============================================================
       LIVE SEARCH (single filter card input drives the DataTable)
    ============================================================ */
    $('#personnelSearch').on('input', function () {
        const value = $(this).val();
        personnelTable.search(value).page('first').draw();
    });

    /* ============================================================
       FILTER HANDLERS
    ============================================================ */
    // Department filter is purely JS based
    $('#departmentFilter').on('change', function () {
        personnelTable.page('first').draw();
    });

    // Date filter requires a page reload because the aggregation (Visit Counts, First Time In)
    // is calculated server-side based on the selected date.
    $('#personnelDateFilter').on('change', function () {
        const date = $(this).val();
        const url = new URL(window.location.href);

        if (date) {
            url.searchParams.set('personnel_date_filter', date);
        } else {
            url.searchParams.delete('personnel_date_filter');
        }

        window.location.href = url.toString();
    });

    /* ============================================================
       CLEAR FILTERS BUTTON
    ============================================================ */
    $('#clearPersonnelFilters').on('click', function () {
        // 1. Clear all input fields visually
        $('#personnelSearch').val('');
        $('#departmentFilter').val('');
        $('#personnelDateFilter').val('');

        // 2. Reset the DataTable search and redraw
        personnelTable.search('').page('first').draw();

        // 3. Reload the page without the date URL parameter.
        // This resets the table to the default "Today" filter.
        const url = new URL(window.location.href);
        url.searchParams.delete('personnel_date_filter');
        window.location.href = url.toString();
    });

    /* ============================================================
       AUTO REFRESH
    ============================================================ */
    const refreshInterval = {{ max(1, (int) ($systemSettings->table_refresh_seconds ?? 3)) * 1000 }};
    let isRefreshing = false;
    let refreshTimer = null;

    const autoRefreshDot = document.getElementById('autoRefreshDot');
    const autoRefreshText = document.getElementById('autoRefreshText');
    const refreshNowBtn = document.getElementById('refreshNowBtn');

    async function refreshPersonnelMonitoring() {
        if (isRefreshing) return;
        isRefreshing = true;

        autoRefreshDot?.classList.add('refreshing');
        refreshNowBtn?.classList.add('spinning');
        if (autoRefreshText) autoRefreshText.textContent = 'Updating...';

        try {
            const currentSearch = personnelTable.search();
            const currentPage = personnelTable.page();
            const currentOrder = personnelTable.order();
            const currentLength = personnelTable.page.len();

            const response = await fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                },
                cache: 'no-store'
            });

            if (!response.ok) throw new Error('Unable to load the latest personnel monitoring data.');

            const html = await response.text();
            const newDocument = new DOMParser().parseFromString(html, 'text/html');

            // ---- Summary counts ----
            ['totalPersonnelCount', 'visitedTodayCount', 'insideTodayCount'].forEach(id => {
                const newEl = newDocument.getElementById(id);
                const oldEl = document.getElementById(id);
                if (newEl && oldEl) {
                    const newVal = newEl.textContent.trim();
                    if (oldEl.textContent.trim() !== newVal) oldEl.textContent = newVal;
                }
            });

            // ---- Table body ----
            const newTableBody = newDocument.getElementById('personnelTableBody');
            const currentTableBody = document.getElementById('personnelTableBody');

            if (newTableBody && currentTableBody) {
                const oldRows = currentTableBody.innerHTML.trim();
                const newRows = newTableBody.innerHTML.trim();

                if (oldRows !== newRows) {
                    personnelTable.destroy();
                    currentTableBody.innerHTML = newTableBody.innerHTML;

                    personnelTable = $('#personnelTable').DataTable({
                        pageLength: currentLength,
                        order: currentOrder,
                        dom: 'lrtip',
                        language: {
                            emptyTable: 'No personnel records found.',
                            zeroRecords: 'No personnel match the selected filters.',
                            lengthMenu: 'Show _MENU_ entries',
                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                            infoEmpty: 'Showing 0 to 0 of 0 entries',
                            infoFiltered: '(filtered from _MAX_ total entries)',
                            paginate: {
                                first: 'First',
                                last: 'Last',
                                next: 'Next',
                                previous: 'Previous'
                            }
                        },
                        columnDefs: [
                            { orderable: false, targets: [0] }
                        ],
                        drawCallback: function () {
                            const api = this.api();
                            const startIndex = api.page.info().start;
                            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                                cell.innerHTML = startIndex + i + 1;
                            });
                        }
                    });

                    personnelTable.search(currentSearch).draw();

                    const pageInfo = personnelTable.page.info();
                    if (currentPage < pageInfo.pages) {
                        personnelTable.page(currentPage).draw('page');
                    }

                    // Re-apply custom filters
                    personnelTable.draw(false);
                }
            }

            if (autoRefreshText) autoRefreshText.textContent = 'Auto Refresh';

        } catch (error) {
            console.error('PERSONNEL AUTO REFRESH ERROR:', error);
            if (autoRefreshText) autoRefreshText.textContent = 'Refresh Error';
        } finally {
            isRefreshing = false;
            autoRefreshDot?.classList.remove('refreshing');
            refreshNowBtn?.classList.remove('spinning');
        }
    }

    refreshNowBtn?.addEventListener('click', refreshPersonnelMonitoring);

    function startAutoRefresh() {
        stopAutoRefresh();
        refreshTimer = setInterval(refreshPersonnelMonitoring, refreshInterval);
    }
    function stopAutoRefresh() {
        if (refreshTimer) { clearInterval(refreshTimer); refreshTimer = null; }
    }

    startAutoRefresh();

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            refreshPersonnelMonitoring();
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });

    window.addEventListener('beforeunload', stopAutoRefresh);

});
</script>