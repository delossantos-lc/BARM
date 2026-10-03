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

    .mgmt-page { color: var(--mgmt-text); }

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
    .mgmt-page .welcome-text span { color: var(--mgmt-muted); font-size: 13px; }
    .mgmt-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255,255,255,.78);
    }
    .mgmt-page .breadcrumb-item a { color: var(--mgmt-primary); font-weight: 600; }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .summary-card, .filter-card, .overdue-card {
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
        top: 20px; right: 22px;
        width: 40px; height: 40px;
        border-radius: 12px;
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
        font-family: FontAwesome;
        font-size: 16px;
        line-height: 40px;
        text-align: center;
        content: "\f071";
    }
    .summary-card.summary-unpaid .card-body::after {
        content: "\f017";
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }
    .summary-card.summary-paid .card-body::after {
        content: "\f00c";
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }
    .summary-card.summary-fine .card-body::after {
        content: "\f155";
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
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
    .summary-card .text-muted { font-size: 11px; }

    /* ═══════════════════════════════════════
       FILTER CARD
       ═══════════════════════════════════════ */
    .filter-card { margin-bottom: 24px; }
    .filter-card .card-body { padding: 22px 24px; }
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
    .filter-label i { color: var(--mgmt-primary); font-size: 11px; opacity: .85; }
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
    .filter-control:hover { border-color: #c9c2ce; }
    .filter-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213,91,145,.12);
        outline: none;
    }
    .search-wrapper { position: relative; }
    .search-wrapper > i {
        position: absolute;
        top: 50%; left: 14px;
        z-index: 2;
        color: var(--mgmt-primary);
        opacity: .7;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .search-wrapper input { padding-left: 40px; }

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
    .clear-filter-button i { font-size: 12px; transition: transform .3s ease; }
    .clear-filter-button:hover, .clear-filter-button:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        box-shadow: 0 4px 12px rgba(213,91,145,.12);
        transform: translateY(-1px);
        outline: none;
    }
    .clear-filter-button:hover i { transform: rotate(90deg); }
    .clear-filter-button:active { transform: translateY(0); }

    /* ═══════════════════════════════════════
       OVERDUE CARD HEADER
       ═══════════════════════════════════════ */
    .overdue-card .card-header {
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
    .overdue-card .card-title {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
    }
    .overdue-card .card-title i {
        color: var(--mgmt-danger);
    }
    .overdue-card .card-body { padding: 22px; }

    /* ═══════════════════════════════════════
       AUTO-REFRESH / STATUS PILL
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

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td, .table th { vertical-align: middle !important; }
    #overdueTable { width: 100% !important; margin-bottom: 0; }

    #overdueTable thead th {
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
    #overdueTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }
    #overdueTable.table-hover tbody tr:hover td { background: #fff7fa; }

    #overdueTable tbody tr.overdue-row td {
        background: var(--mgmt-danger-soft) !important;
    }
    #overdueTable.table-hover tbody tr.overdue-row:hover td {
        background: #ffe2e2 !important;
    }
    #overdueTable tbody tr.overdue-row td:first-child {
        position: relative;
    }
    #overdueTable tbody tr.overdue-row td:first-child::before {
        position: absolute;
        top: 50%; left: 4px;
        width: 3px; height: 24px;
        border-radius: 3px;
        background: var(--mgmt-danger);
        transform: translateY(-50%);
        content: "";
        animation: overduePulse 1.6s infinite ease-in-out;
    }
    @keyframes overduePulse {
        0%, 100% { opacity: .55; }
        50%      { opacity: 1; }
    }

    /* ═══════════════════════════════════════
       TABLE CELLS
       ═══════════════════════════════════════ */
    .borrower-name { color: var(--mgmt-text); font-weight: 700; font-size: 13px; }
    .borrower-details { display: block; margin-top: 3px; color: var(--mgmt-muted); font-size: 12px; }

    .book-title { color: var(--mgmt-text); font-weight: 700; font-size: 13px; }
    .book-details { display: block; color: var(--mgmt-muted); font-size: 12px; margin-top: 2px; }

    .fine-amount {
        color: var(--mgmt-danger);
        font-size: 14px;
        font-weight: 800;
    }
    .fine-per-day {
        color: #555;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12px;
    }
    .days-overdue {
        color: var(--mgmt-danger);
        font-weight: 800;
        font-size: 13px;
    }

    .remarks-text {
        display: block;
        max-width: 220px;
        color: var(--mgmt-text);
        font-size: 12px;
        line-height: 1.45;
        white-space: normal;
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
        font-weight: 700;
        white-space: nowrap;
        letter-spacing: .2px;
    }
    .badge-success.status-badge { background: var(--mgmt-success-soft); color: var(--mgmt-success); }
    .badge-warning.status-badge { background: var(--mgmt-warning-soft); color: var(--mgmt-warning); }
    .badge-info.status-badge { background: var(--mgmt-info-soft); color: var(--mgmt-info); }

    /* ═══════════════════════════════════════
       ALERTS
       ═══════════════════════════════════════ */
    .alert { border-radius: 14px; border: 1px solid var(--mgmt-border); font-size: 13px; }
    .alert-success { border-color: #c9e9d4; background: var(--mgmt-success-soft); color: var(--mgmt-success); }
    .alert-danger { border-color: #f3c9c9; background: var(--mgmt-danger-soft); color: var(--mgmt-danger); }

    /* ═══════════════════════════════════════
       DATATABLES
       ═══════════════════════════════════════ */
    .dataTables_wrapper { padding: 6px 0 4px; }
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
    .dataTables_wrapper .dataTables_paginate { padding-top: 12px; }
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
    .page-link { color: var(--mgmt-primary); }

    /* ═══════════════════════════════════════
       EMPTY STATE
       ═══════════════════════════════════════ */
    #overdueTable tbody td[colspan="10"] {
        padding: 55px 20px !important;
        text-align: center;
    }
    #overdueTable tbody td[colspan="10"] i {
        font-size: 45px;
    }
    #overdueTable tbody td[colspan="10"] h5 {
        margin-top: 16px;
        color: var(--mgmt-text);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .mgmt-page .page-titles { padding: 18px; }
        .filter-column { margin-bottom: 15px; }
        .filter-card .card-body { padding: 16px; }
        .overdue-card .card-header { align-items: stretch; flex-direction: column; }
        .overdue-card .card-header .text-md-right { text-align: left !important; }
        .overdue-card .card-body { padding: 16px; }
        .summary-number { font-size: 26px; }
        .auto-refresh-status { margin-left: 0; margin-top: 8px; }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    $overdueRecords = $overdues ?? collect();
    $defaultFinePerDay = 5;

    $totalUnpaid = $overdueRecords
        ->filter(function ($borrow) {
            return strtolower($borrow->payment_status ?? 'unpaid') !== 'paid';
        })
        ->count();

    $totalPaid = $overdueRecords
        ->filter(function ($borrow) {
            return strtolower($borrow->payment_status ?? 'unpaid') === 'paid';
        })
        ->count();

    $totalOutstandingFine = $overdueRecords
        ->filter(function ($borrow) {
            return strtolower($borrow->payment_status ?? 'unpaid') !== 'paid';
        })
        ->sum(function ($borrow) use ($defaultFinePerDay) {
            $dueDate = \Carbon\Carbon::parse($borrow->due_date)->startOfDay();
            $daysOverdue = $dueDate->isPast()
                ? $dueDate->diffInDays(\Carbon\Carbon::today())
                : 0;
            $finePerDay = $borrow->fine_per_day ?? $defaultFinePerDay;
            return $daysOverdue * $finePerDay;
        });
@endphp

<div class="content-body mgmt-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Overdue Books</h4>
                    <span>Monitor overdue books, fines and payment status</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Overdues</li>
                </ol>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- SUMMARY CARDS --}}
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Total Overdue</h5>
                        <p id="totalOverdueCount" class="summary-number text-danger">
                            {{ $overdueRecords->count() }}
                        </p>
                        <span class="text-muted">Books past their due date</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-unpaid">
                    <div class="card-body">
                        <h5>Unpaid</h5>
                        <p id="totalUnpaidCount" class="summary-number text-warning">
                            {{ $totalUnpaid }}
                        </p>
                        <span class="text-muted">Fines awaiting payment</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-paid">
                    <div class="card-body">
                        <h5>Paid</h5>
                        <p id="totalPaidCount" class="summary-number text-success">
                            {{ $totalPaid }}
                        </p>
                        <span class="text-muted">Settled overdue fines</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-fine">
                    <div class="card-body">
                        <h5>Outstanding Fine</h5>
                        <p id="outstandingFine" class="summary-number text-danger">
                            ₱{{ number_format($totalOutstandingFine, 2) }}
                        </p>
                        <span class="text-muted">Total unpaid fine amount</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-xl-4 col-lg-6 col-md-6 filter-column">
                        <label class="filter-label" for="overdueSearch">
                            <i class="fa fa-search"></i>
                            Search Borrower or Book
                        </label>
                        <div class="search-wrapper">
                            <i class="fa fa-search"></i>
                            <input
                                type="text"
                                id="overdueSearch"
                                class="form-control filter-control"
                                placeholder="Search name, ID or book"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                        <label class="filter-label" for="borrowerTypeFilter">
                            <i class="fa fa-user"></i>
                            Borrower Type
                        </label>
                        <select id="borrowerTypeFilter" class="form-control filter-control">
                            <option value="">All Borrowers</option>
                            <option value="student">Student</option>
                            <option value="personnel">Personnel</option>
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                        <label class="filter-label" for="paymentStatusFilter">
                            <i class="fa fa-credit-card"></i>
                            Payment Status
                        </label>
                        <select id="paymentStatusFilter" class="form-control filter-control">
                            <option value="">All Status</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="waived">Waived</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                        <label class="filter-label" for="dueDateFilter">
                            <i class="fa fa-calendar"></i>
                            Due Date
                        </label>
                        <input type="date" id="dueDateFilter" class="form-control filter-control">
                    </div>

                    <div class="col-xl-1 col-lg-6 col-md-6 filter-column">
                        <label class="filter-label">Clear</label>
                        <button
                            type="button"
                            id="clearFiltersButton"
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

        {{-- OVERDUE TABLE --}}
        <div class="card overdue-card">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-1">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        Overdue Book Records
                    </h4>
                    <small class="text-muted">
                        Fine calculations update based on days overdue
                    </small>
                </div>

                <div class="d-flex align-items-center flex-wrap">
                    <small class="text-muted mr-2">{{ now()->format('F d, Y') }}</small>
                    <span class="auto-refresh-status">
                        <span class="auto-refresh-dot"></span>
                        Admin Monitoring
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="overdueTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Borrower</th>
                                <th>Book</th>
                                <th>Borrowed Date</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Fine Per Day</th>
                                <th>Total Fine</th>
                                <th>Payment Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($overdueRecords as $borrow)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($borrow->due_date)->startOfDay();
                                    $borrowedDate = \Carbon\Carbon::parse($borrow->borrowed_at);
                                    $daysOverdue = $dueDate->isPast()
                                        ? $dueDate->diffInDays(\Carbon\Carbon::today())
                                        : 0;
                                    $finePerDay = $borrow->fine_per_day ?? $defaultFinePerDay;
                                    $totalFine = $daysOverdue * $finePerDay;
                                    $paymentStatus = strtolower($borrow->payment_status ?? 'unpaid');
                                    $borrower = $borrow->borrower_record;
                                    $borrowerName = $borrower
                                        ? trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''))
                                        : 'Unknown Borrower';

                                    if ($borrow->borrower_type === 'student') {
                                        $borrowerNumber = $borrower->student_number ?? '-';
                                    } else {
                                        $borrowerNumber = $borrower->employee_number ?? '-';
                                    }
                                @endphp

                                <tr
                                    class="overdue-row"
                                    data-borrower-type="{{ strtolower($borrow->borrower_type) }}"
                                    data-payment-status="{{ $paymentStatus }}"
                                    data-due-date="{{ $dueDate->format('Y-m-d') }}"
                                >
                                    <td class="row-index"></td>

                                    <td style="min-width: 190px;">
                                        <strong class="borrower-name">
                                            {{ $borrowerName }}
                                        </strong>
                                        <span class="borrower-details">
                                            {{ ucfirst($borrow->borrower_type) }} - {{ $borrowerNumber }}
                                        </span>
                                    </td>

                                    <td style="min-width: 220px;">
                                        <strong class="book-title">
                                            {{ $borrow->book->title ?? 'Unknown Book' }}
                                        </strong>
                                        <span class="book-details">
                                            {{ $borrow->book->author ?? 'Unknown Author' }}
                                        </span>
                                        <span class="book-details">
                                            Call No: {{ $borrow->book->call_number ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $borrowedDate->format('M d, Y') }}
                                    </td>

                                    <td>
                                        <strong class="text-danger">
                                            {{ $dueDate->format('M d, Y') }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="days-overdue">
                                            {{ $daysOverdue }} day(s)
                                        </span>
                                    </td>

                                    <td>
                                        <span class="fine-per-day">
                                            ₱{{ number_format($finePerDay, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="fine-amount">
                                            ₱{{ number_format($totalFine, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($paymentStatus === 'paid')
                                            <span class="badge badge-success status-badge">
                                                <i class="fa fa-check-circle mr-1"></i>
                                                Paid
                                            </span>
                                        @elseif($paymentStatus === 'waived')
                                            <span class="badge badge-info status-badge">
                                                <i class="fa fa-minus-circle mr-1"></i>
                                                Waived
                                            </span>
                                        @else
                                            <span class="badge badge-warning status-badge">
                                                <i class="fa fa-clock-o mr-1"></i>
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($borrow->remarks)
                                            <span class="remarks-text">
                                                {{ $borrow->remarks }}
                                            </span>
                                        @else
                                            <span class="text-muted">No remarks</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fa fa-check-circle" style="color: #28a745;"></i>
                                        <h5>No overdue books</h5>
                                        <p class="text-muted mb-0">
                                            All borrowed books are within their due dates.
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

<script src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function () {

    /* ============================================================
       CUSTOM FILTER (Borrower Type + Payment Status + Due Date)
    ============================================================ */
    $.fn.dataTable.ext.search.push(function (settings, rowData, rowIndex) {
        if (settings.nTable.id !== 'overdueTable') return true;

        const rowNode = settings.aoData[rowIndex] ? settings.aoData[rowIndex].nTr : null;
        if (!rowNode) return true;

        const row = $(rowNode);

        const selectedBorrowerType = $('#borrowerTypeFilter').val();
        const selectedPaymentStatus = $('#paymentStatusFilter').val();
        const selectedDueDate = $('#dueDateFilter').val();

        const rowBorrowerType = row.attr('data-borrower-type') ?? '';
        const rowPaymentStatus = row.attr('data-payment-status') ?? '';
        const rowDueDate = row.attr('data-due-date') ?? '';

        const matchesBorrowerType = selectedBorrowerType === '' || rowBorrowerType === selectedBorrowerType;
        const matchesPaymentStatus = selectedPaymentStatus === '' || rowPaymentStatus === selectedPaymentStatus;
        const matchesDueDate = selectedDueDate === '' || rowDueDate === selectedDueDate;

        return matchesBorrowerType && matchesPaymentStatus && matchesDueDate;
    });

    /* ============================================================
       DATATABLE INIT
    ============================================================ */
    const overdueTable = $('#overdueTable').DataTable({
        pageLength: 10,
        order: [[5, 'desc']],
        dom: 'lrtip',
        columnDefs: [
            { orderable: false, targets: [0] }
        ],
        language: {
            emptyTable: 'No overdue book records found.',
            zeroRecords: 'No overdue records match the selected filters.',
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
        drawCallback: function () {
            const api = this.api();
            const startIndex = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    /* ============================================================
       SEARCH + FILTERS
    ============================================================ */
    $('#overdueSearch').on('input', function () {
        overdueTable.search($(this).val()).page('first').draw();
    });

    $('#borrowerTypeFilter, #paymentStatusFilter, #dueDateFilter').on('change', function () {
        overdueTable.page('first').draw();
    });

    $('#clearFiltersButton').on('click', function () {
        $('#overdueSearch').val('');
        $('#borrowerTypeFilter').val('');
        $('#paymentStatusFilter').val('');
        $('#dueDateFilter').val('');

        overdueTable.search('').page('first').draw();
        $('#overdueSearch').focus();
    });

});
</script>