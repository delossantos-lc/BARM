@include('layouts.header')
@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>
    :root {
        --audit-primary: #d6538c;
        --audit-primary-dark: #bd3f77;
        --audit-primary-soft: #fff2f7;
        --audit-primary-softer: #fff8fb;
        --audit-page: #f7f7fb;
        --audit-surface: #ffffff;
        --audit-border: #ebe7ed;
        --audit-border-soft: #f2eff3;
        --audit-text: #292631;
        --audit-muted: #797482;
        --audit-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --audit-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .audit-page {
        color: var(--audit-text);
    }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .audit-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--audit-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--audit-shadow);
    }

    .audit-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--audit-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }

    .audit-page .welcome-text span {
        color: var(--audit-muted);
        font-size: 13px;
    }

    .audit-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .78);
    }

    .audit-page .breadcrumb-item a {
        color: var(--audit-primary);
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .summary-card,
    .filter-card,
    .audit-card {
        overflow: hidden;
        border: 1px solid var(--audit-border);
        border-radius: 18px;
        background: var(--audit-surface);
        box-shadow: var(--audit-shadow);
    }

    .summary-card {
        height: calc(100% - 30px);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--audit-shadow-hover);
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
        background: var(--audit-primary-soft);
        color: var(--audit-primary);
        font-family: FontAwesome;
        font-size: 16px;
        line-height: 40px;
        text-align: center;
        content: "\f080";
    }

    .summary-card h5 {
        max-width: calc(100% - 55px);
        margin-bottom: 12px;
        color: var(--audit-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .35px;
        text-transform: uppercase;
    }

    .summary-number {
        margin-bottom: 6px;
        color: var(--audit-text);
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
        padding: 0;
    }

    .filter-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 0;
        padding: 22px 26px;
        border-bottom: 1px solid var(--audit-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .filter-heading-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .filter-heading-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--audit-primary) 0%, var(--audit-primary-dark) 100%);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .filter-heading h5 {
        margin: 0 0 4px;
        color: var(--audit-text);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .filter-heading small {
        display: block;
        color: var(--audit-muted);
        font-size: 12px;
        font-weight: 400;
    }

    /* ═══════════════════════════════════════
       FILTER FIELDS
       ═══════════════════════════════════════ */
    .filter-fields {
        padding: 26px 26px 9px;
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
        color: var(--audit-primary);
        font-size: 11px;
        opacity: .85;
    }

    .filter-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--audit-text);
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .filter-control:hover {
        border-color: #c9c2ce;
    }

    .filter-control:focus {
        border-color: var(--audit-primary);
        background-color: var(--audit-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: var(--audit-primary);
        opacity: .7;
        transform: translateY(-50%);
    }

    .search-wrapper input {
        padding-left: 40px;
    }

    .search-wrapper input:focus + i,
    .search-wrapper:focus-within i {
        opacity: 1;
    }

    /* ═══════════════════════════════════════
       FILTER ACTIONS
       ═══════════════════════════════════════ */
    .filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 14px;
        margin: 10px -26px 0;
        padding: 18px 26px;
        border-top: 1px solid var(--audit-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .clear-filter-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 160px;
        min-height: 43px;
        padding: 10px 18px;
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
        background: var(--audit-primary-soft);
        color: var(--audit-primary-dark);
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
       DOWNLOAD / EXPORT BUTTONS
       ═══════════════════════════════════════ */
    .download-pdf-button,
    .download-csv-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border: 0;
        border-radius: 11px;
        background: var(--audit-primary);
        color: #fff !important;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .download-pdf-button i,
    .download-csv-button i {
        font-size: 13px;
    }

    .download-pdf-button:hover,
    .download-pdf-button:focus,
    .download-csv-button:hover,
    .download-csv-button:focus {
        background: var(--audit-primary-dark);
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
        transform: translateY(-1px);
    }

    .download-pdf-button:disabled,
    .download-csv-button:disabled {
        cursor: not-allowed;
        opacity: .65;
        transform: none;
    }

    /* ═══════════════════════════════════════
       AUDIT CARD
       ═══════════════════════════════════════ */
    .audit-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        min-height: 78px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--audit-border);
        background: #fff;
    }

    .audit-card .card-title {
        color: var(--audit-text);
        font-size: 17px;
        font-weight: 700;
    }

    .audit-card .card-title i {
        color: var(--audit-primary);
    }

    .audit-card .card-body {
        padding: 0;
    }

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    #auditTable thead th {
        padding: 15px 14px;
        border: 0;
        border-bottom: 1px solid var(--audit-border);
        background: #f8f7fa;
        color: #625c68;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #auditTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    #auditTable.table-striped tbody tr:nth-of-type(odd) td {
        background: #fdfcfd;
    }

    #auditTable.table-hover tbody tr:hover td {
        background: #fff7fa;
    }

    /* ═══════════════════════════════════════
       AUDIT CELLS / BADGES
       ═══════════════════════════════════════ */
    .audit-actor {
        color: var(--audit-text);
        font-weight: 700;
    }

    .audit-subtext {
        display: block;
        margin-top: 3px;
        color: var(--audit-muted);
        font-size: 12px;
    }

    .audit-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .role-admin {
        background: #fff0f3;
        color: #c4365f;
    }

    .role-staff {
        background: #eef3ff;
        color: #315caa;
    }

    .role-system {
        background: #f1f1f1;
        color: #555;
    }

    .action-badge {
        background: #eef3ff;
        color: #315caa;
    }

    .module-badge {
        background: #fff0f6;
        color: var(--audit-primary-dark);
    }

    .success-badge {
        background: #eaf8ef;
        color: #218143;
    }

    .failed-badge {
        background: #ffeded;
        color: #bd3434;
    }

    .record-text {
        display: block;
        min-width: 150px;
        max-width: 250px;
        overflow-wrap: anywhere;
        color: #504a56;
        font-size: 12px;
        line-height: 1.45;
    }

    .ip-address {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 8px;
        background: #f4f5f7;
        color: #555;
        font-size: 11px;
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       EMPTY STATE
       ═══════════════════════════════════════ */
    .empty-audit {
        padding: 55px 20px !important;
        text-align: center;
    }

    .empty-audit i {
        color: #ccc;
        font-size: 45px;
    }

    /* ═══════════════════════════════════════
       DATATABLES
       ═══════════════════════════════════════ */
    .dataTables_wrapper {
        padding: 20px 22px 16px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info {
        padding-bottom: 12px;
        color: var(--audit-muted);
        font-size: 12px;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
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
        border-color: var(--audit-primary) !important;
        background: var(--audit-primary) !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: var(--audit-primary-dark) !important;
        background: var(--audit-primary-dark) !important;
        color: #fff !important;
    }

    /* ═══════════════════════════════════════
       ACTIVE FILTER TAGS
       ═══════════════════════════════════════ */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 26px;
        margin-top: -4px;
        min-height: 0;
    }

    .active-filters:not(:empty) {
        padding-top: 14px;
        padding-bottom: 16px;
        border-top: 1px solid var(--audit-border);
        background: var(--audit-primary-softer);
    }

    .active-filters:not(:empty)::before {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-right: 4px;
        color: var(--audit-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
        content: "Active:";
    }

    .active-filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid #f5d6e1;
        border-radius: 20px;
        background: #fff;
        color: var(--audit-primary-dark);
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(213, 91, 145, .08);
        transition: all .2s ease;
    }

    .active-filter-tag:hover {
        border-color: var(--audit-primary);
        box-shadow: 0 3px 10px rgba(213, 91, 145, .16);
    }

    .active-filter-tag strong {
        color: var(--audit-muted);
        font-weight: 700;
    }

    .active-filter-tag .remove-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        margin-left: 2px;
        border-radius: 50%;
        background: #f7e6ee;
        cursor: pointer;
        color: var(--audit-primary-dark);
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        opacity: .75;
        transition: all .2s ease;
    }

    .active-filter-tag .remove-tag:hover {
        background: var(--audit-primary);
        color: #fff;
        opacity: 1;
        transform: scale(1.1);
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .audit-page .page-titles {
            padding: 18px;
        }

        .filter-column {
            margin-bottom: 15px;
        }

        .filter-heading,
        .filter-fields {
            padding-right: 18px;
            padding-left: 18px;
        }

        .filter-actions {
            align-items: stretch;
            flex-direction: column;
            margin-right: -18px;
            margin-left: -18px;
            padding-right: 18px;
            padding-left: 18px;
        }

        .audit-card .card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .clear-filter-button,
        .download-pdf-button,
        .download-csv-button {
            width: 100%;
        }

        .active-filters {
            padding-left: 18px;
            padding-right: 18px;
        }

        .active-filters:not(:empty)::before {
            width: 100%;
            margin-bottom: 2px;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    $todayLogs = $auditLogs
        ->filter(function ($log) {
            return $log->created_at->isToday();
        })
        ->count();

    $successfulLogs = $auditLogs
        ->where('result', 'success')
        ->count();

    $failedLogs = $auditLogs
        ->where('result', 'failed')
        ->count();

    $modules = $auditLogs
        ->pluck('module')
        ->filter()
        ->unique()
        ->sort()
        ->values();
@endphp

<div class="content-body audit-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Audit Logs</h4>
                    <span>Review administrative and staff activities</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Audit Logs</li>
                </ol>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row">
            {{-- TOTAL LOGS --}}
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Total Logs</h5>
                        <p class="summary-number">{{ $auditLogs->count() }}</p>
                        <span class="text-muted">All recorded activities</span>
                    </div>
                </div>
            </div>

            {{-- TODAY --}}
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Today</h5>
                        <p class="summary-number text-primary">{{ $todayLogs }}</p>
                        <span class="text-muted">Activities recorded today</span>
                    </div>
                </div>
            </div>

            {{-- SUCCESSFUL --}}
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Successful</h5>
                        <p class="summary-number text-success">{{ $successfulLogs }}</p>
                        <span class="text-muted">Successful operations</span>
                    </div>
                </div>
            </div>

            {{-- FAILED --}}
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Failed</h5>
                        <p class="summary-number text-danger">{{ $failedLogs }}</p>
                        <span class="text-muted">Failed operations</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="card filter-card">
            <div class="card-body">

                <div class="filter-heading">
                    <div class="filter-heading-title">
                        <span class="filter-heading-icon">
                            <i class="fa fa-sliders"></i>
                        </span>

                        <div>
                            <h5>Filter Logs</h5>
                            <small>
                                Refine the audit trail using the filters below.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="filter-fields">
                    <div class="row align-items-end">
                        {{-- SEARCH --}}
                        <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                            <label for="auditSearch" class="filter-label">
                                <i class="fa fa-search"></i>
                                Search Logs
                            </label>
                            <div class="search-wrapper">
                                <i class="fa fa-search"></i>
                                <input
                                    type="text"
                                    id="auditSearch"
                                    class="form-control filter-control"
                                    placeholder="Search user, action or record"
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        {{-- ROLE --}}
                        <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                            <label for="roleFilter" class="filter-label">
                                <i class="fa fa-user-circle-o"></i>
                                User Role
                            </label>
                            <select id="roleFilter" class="form-control filter-control">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="system">System</option>
                            </select>
                        </div>

                        {{-- MODULE --}}
                        <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                            <label for="moduleFilter" class="filter-label">
                                <i class="fa fa-cube"></i>
                                Module
                            </label>
                            <select id="moduleFilter" class="form-control filter-control">
                                <option value="">All Modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ strtolower($module) }}">
                                        {{ $module }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- RESULT --}}
                        <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                            <label for="resultFilter" class="filter-label">
                                <i class="fa fa-check-circle-o"></i>
                                Result
                            </label>
                            <select id="resultFilter" class="form-control filter-control">
                                <option value="">All Results</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        {{-- DATE --}}
                        <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                            <label for="dateFilter" class="filter-label">
                                <i class="fa fa-calendar-o"></i>
                                Date
                            </label>
                            <input
                                type="date"
                                id="dateFilter"
                                class="form-control filter-control"
                            >
                        </div>
                    </div>

                    {{-- ACTIVE FILTERS DISPLAY --}}
                    <div id="activeFilters" class="active-filters"></div>

                    <div class="filter-actions">
                        <button
                            type="button"
                            id="clearFilters"
                            class="btn clear-filter-button"
                            title="Clear filters"
                        >
                            <i class="fa fa-refresh"></i>
                            Clear Filters
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- AUDIT TABLE --}}
        <div class="card audit-card">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-1">
                        <i class="fa fa-history mr-2"></i>
                        System Activity
                    </h4>
                    <small class="text-muted">
                        Audit logs are read-only and cannot be edited.
                    </small>
                </div>

                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <button
                        type="button"
                        id="downloadCsv"
                        class="btn download-csv-button"
                        {{ $auditLogs->isEmpty() ? 'disabled' : '' }}
                    >
                        <i class="fa fa-file-excel-o"></i>
                        Download CSV
                    </button>

                    <button
                        type="button"
                        id="downloadPdf"
                        class="btn download-pdf-button"
                        {{ $auditLogs->isEmpty() ? 'disabled' : '' }}
                    >
                        <i class="fa fa-file-pdf-o"></i>
                        Download PDF
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table
                        id="auditTable"
                        class="table table-striped table-hover"
                        style="width: 100%;"
                    >
                        <thead>
                            <tr>
                                <th>Date and Time</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Affected Record</th>
                                <th>IP Address</th>
                                <th>Result</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($auditLogs as $log)
                                <tr
                                    data-role="{{ strtolower($log->user_role) }}"
                                    data-module="{{ strtolower($log->module) }}"
                                    data-result="{{ strtolower($log->result) }}"
                                    data-date="{{ $log->created_at->format('Y-m-d') }}"
                                >
                                    {{-- DATE AND TIME --}}
                                    <td style="min-width:145px;"
                                        data-order="{{ $log->created_at->format('Y-m-d H:i:s') }}-{{ str_pad((string) $log->id, 12, '0', STR_PAD_LEFT) }}">
                                        <strong>
                                            {{ $log->created_at->format('M d, Y') }}
                                        </strong>
                                        <br>
                                        <span class="audit-subtext">
                                            {{ $log->created_at->format('h:i:s A') }}
                                        </span>
                                    </td>

                                    {{-- USER --}}
                                    <td style="min-width:140px;">
                                        <span class="audit-actor">
                                            {{ $log->actor_name ?? 'System' }}
                                        </span>
                                    </td>

                                    {{-- ROLE --}}
                                    <td>
                                        @if(strtolower($log->user_role) === 'admin')
                                            <span class="audit-badge role-admin">Admin</span>
                                        @elseif(strtolower($log->user_role) === 'staff')
                                            <span class="audit-badge role-staff">Staff</span>
                                        @else
                                            <span class="audit-badge role-system">System</span>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td>
                                        <span class="audit-badge action-badge">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    {{-- MODULE --}}
                                    <td>
                                        <span class="audit-badge module-badge">
                                            {{ $log->module }}
                                        </span>
                                    </td>

                                    {{-- AFFECTED RECORD --}}
                                    <td>
                                        <span class="record-text">
                                            {{ $log->affected_record ?? '-' }}
                                        </span>
                                        @if($log->description)
                                            <span class="audit-subtext">
                                                {{ $log->description }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- IP ADDRESS --}}
                                    <td>
                                        <span class="ip-address">
                                            {{ $log->ip_address ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- RESULT --}}
                                    <td>
                                        @if(strtolower($log->result) === 'success')
                                            <span class="audit-badge success-badge">
                                                <i class="fa fa-check-circle mr-1"></i>
                                                Success
                                            </span>
                                        @else
                                            <span class="audit-badge failed-badge">
                                                <i class="fa fa-times-circle mr-1"></i>
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-audit">
                                        <i class="fa fa-history"></i>
                                        <h5 class="mt-3">No audit logs found</h5>
                                        <p class="text-muted">
                                            Recorded system activities will appear here.
                                        </p>
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

<script
    src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"
></script>
<script
    src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"
></script>

<script>
$(document).ready(function () {
    let auditTable;

    // Custom search function for filters
    $.fn.dataTable.ext.search.push(
        function (settings, searchData, index, rowData) {
            // Only apply to our audit table
            if (settings.nTable.id !== 'auditTable') {
                return true;
            }

            const row = $(settings.aoData[index].nTr);
            const role = $('#roleFilter').val();
            const module = $('#moduleFilter').val();
            const result = $('#resultFilter').val();
            const date = $('#dateFilter').val();

            const matchesRole = role === '' || row.attr('data-role') === role;
            const matchesModule = module === '' || row.attr('data-module') === module;
            const matchesResult = result === '' || row.attr('data-result') === result;
            const matchesDate = date === '' || row.attr('data-date') === date;

            return matchesRole && matchesModule && matchesResult && matchesDate;
        }
    );

    // Initialize DataTable
    auditTable = $('#auditTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']],
        dom: 'lrtip',
        language: {
            emptyTable: 'No audit logs found.',
            zeroRecords: 'No logs match the selected filters.',
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
        }
    });

    // Search input handler
    $('#auditSearch').on('input', function () {
        auditTable.search($(this).val()).draw();
        updateActiveFilters();
    });

    // Filter change handler
    $('#roleFilter, #moduleFilter, #resultFilter, #dateFilter').on('change', function () {
        auditTable.draw();
        updateActiveFilters();
    });

    // Clear filters
    $('#clearFilters').on('click', function () {
        $('#auditSearch').val('');
        $('#roleFilter').val('');
        $('#moduleFilter').val('');
        $('#resultFilter').val('');
        $('#dateFilter').val('');
        auditTable.search('').draw();
        updateActiveFilters();
    });

    // Update active filter tags
    function updateActiveFilters() {
        const container = $('#activeFilters');
        container.empty();

        const search = $('#auditSearch').val().trim();
        const role = $('#roleFilter').val();
        const module = $('#moduleFilter').val();
        const result = $('#resultFilter').val();
        const date = $('#dateFilter').val();

        if (search) {
            container.append(createFilterTag('Search', search, 'search'));
        }
        if (role) {
            container.append(createFilterTag('Role', role.charAt(0).toUpperCase() + role.slice(1), 'role'));
        }
        if (module) {
            const moduleText = $('#moduleFilter option[value="' + module + '"]').text().trim();
            container.append(createFilterTag('Module', moduleText, 'module'));
        }
        if (result) {
            container.append(createFilterTag('Result', result.charAt(0).toUpperCase() + result.slice(1), 'result'));
        }
        if (date) {
            container.append(createFilterTag('Date', date, 'date'));
        }
    }

    // Create a filter tag element
    function createFilterTag(label, value, type) {
        const tag = $('<span class="active-filter-tag"></span>');

        tag.html(
            '<strong>' + label + ':</strong> ' +
            $('<div>').text(value).html() +
            '<span class="remove-tag" data-type="' + type + '" title="Remove filter">&times;</span>'
        );

        tag.find('.remove-tag').on('click', function () {
            const filterType = $(this).data('type');

            if (filterType === 'search') {
                $('#auditSearch').val('');
                auditTable.search('').draw();
            } else if (filterType === 'role') {
                $('#roleFilter').val('');
                auditTable.draw();
            } else if (filterType === 'module') {
                $('#moduleFilter').val('');
                auditTable.draw();
            } else if (filterType === 'result') {
                $('#resultFilter').val('');
                auditTable.draw();
            } else if (filterType === 'date') {
                $('#dateFilter').val('');
                auditTable.draw();
            }

            updateActiveFilters();
        });

        return tag;
    }

    // Download CSV
    $('#downloadCsv').on('click', function () {
        if (!auditTable) {
            return;
        }

        function csvCell(value) {
            let text = $('<div>').html(value ?? '').text().trim();

            // Prevent formula injection
            if (/^[=+\-@]/.test(text)) {
                text = "'" + text;
            }

            return '"' + text.replace(/"/g, '""') + '"';
        }

        const csvRows = [];

        // Add header row
        csvRows.push(
            auditTable.columns().header().toArray().map(function (header) {
                return csvCell(header.textContent);
            }).join(',')
        );

        // Add data rows (only visible/filtered rows)
        auditTable.rows({ search: 'applied' }).every(function () {
            const rowData = this.data();
            const cells = Array.isArray(rowData)
                ? rowData
                : Object.values(rowData);
            csvRows.push(cells.map(csvCell).join(','));
        });

        const blob = new Blob(
            ['\uFEFF' + csvRows.join('\r\n')],
            { type: 'text/csv;charset=utf-8;' }
        );

        const downloadUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = 'audit-logs-' + @json(now()->format('Y-m-d-His')) + '.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(downloadUrl);
    });

    // Download PDF
    $('#downloadPdf').on('click', function () {
        const downloadButton = $(this);
        const parameters = new URLSearchParams();

        const search = $('#auditSearch').val().trim();
        const role = $('#roleFilter').val();
        const module = $('#moduleFilter').val();
        const result = $('#resultFilter').val();
        const date = $('#dateFilter').val();

        if (search !== '') {
            parameters.set('search', search);
        }
        if (role !== '') {
            parameters.set('role', role);
        }
        if (module !== '') {
            parameters.set('module', module);
        }
        if (result !== '') {
            parameters.set('result', result);
        }
        if (date !== '') {
            parameters.set('date', date);
        }

        const queryString = parameters.toString();
        let pdfUrl = @json(route('audit.log.pdf'));
        if (queryString !== '') {
            pdfUrl += '?' + queryString;
        }

        downloadButton
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin mr-1"></i> Preparing PDF...');

        window.location.href = pdfUrl;

        window.setTimeout(function () {
            downloadButton
                .prop('disabled', false)
                .html('<i class="fa fa-file-pdf-o mr-1"></i> Download PDF');
        }, 2500);
    });

    // Initialize active filters display
    updateActiveFilters();
});
</script>