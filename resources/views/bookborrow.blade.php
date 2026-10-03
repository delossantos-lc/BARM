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
       SUMMARY CARDS
       ═══════════════════════════════════════ */
    .summary-card {
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--mgmt-shadow-hover);
    }

    .summary-card .card-body {
        padding: 22px 24px;
    }

    .summary-card h5 {
        color: var(--mgmt-muted);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .summary-number {
        margin-bottom: 5px;
        font-size: 32px;
        font-weight: 800;
        color: var(--mgmt-text);
    }

    .summary-card span.text-muted {
        font-size: 12px;
        color: var(--mgmt-muted) !important;
    }

    /* ═══════════════════════════════════════
       FILTER CARD
       ═══════════════════════════════════════ */
    .filter-card {
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
        margin-bottom: 24px;
    }

    .filter-card .card-body {
        padding: 20px 24px;
    }

    .filter-label {
        display: block;
        margin-bottom: 8px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
    }

    .filter-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
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

    .search-wrapper i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: var(--mgmt-muted);
        transform: translateY(-50%);
        font-size: 13px;
    }

    .search-wrapper input {
        padding-left: 40px;
    }

    .clear-filter-button {
        width: 100%;
        min-height: 44px;
        border-radius: 11px;
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
        font-weight: 700;
        transition: all .2s ease;
    }

    .clear-filter-button:hover {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
    }

    /* ═══════════════════════════════════════
       BORROW CARD & TABLE SWITCHER
       ═══════════════════════════════════════ */
    .borrow-card {
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
        overflow: hidden;
    }

    .borrow-card .card-header {
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

    .borrow-card .card-title {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .borrow-card .card-title i {
        color: var(--mgmt-primary);
    }

    .borrow-card .card-body {
        padding: 22px;
    }

    .table-switcher {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .table-switch-button {
        padding: 9px 16px;
        border: 1px solid #e2e2e2;
        border-radius: 10px;
        background: #fff;
        color: #555;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .table-switch-button:hover {
        border-color: var(--mgmt-primary);
        color: var(--mgmt-primary);
    }

    .table-switch-button.active {
        border-color: var(--mgmt-primary);
        background: var(--mgmt-primary);
        color: #fff;
        box-shadow: 0 4px 10px rgba(213, 91, 145, 0.25);
    }

    /* ═══════════════════════════════════════
       TABLE STYLING
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    #borrowTable {
        width: 100% !important;
        margin-bottom: 0;
    }

    #borrowTable thead th {
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

    #borrowTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
        font-size: 13px;
    }

    #borrowTable.table-hover tbody tr:hover td {
        background: #fff7fa;
    }

    /* Row highlights */
    .overdue-row td {
        background: #fff1f1 !important;
    }

    .returned-row td {
        background: #f2fff5 !important;
    }

    /* ═══════════════════════════════════════
       TABLE CELLS
       ═══════════════════════════════════════ */
    .borrower-details {
        color: var(--mgmt-muted);
        font-size: 12px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-success.status-badge {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .badge-danger.status-badge {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }

    .badge-warning.status-badge {
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }

    /* ═══════════════════════════════════════
       ACTION BUTTONS
       ═══════════════════════════════════════ */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        margin: 2px;
        padding: 0;
        border: 0;
        border-radius: 9px;
        font-size: 12px;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(42, 35, 48, .12);
    }

    .btn-success.action-btn {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .btn-success.action-btn:hover {
        background: var(--mgmt-success);
        color: #fff;
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
    #borrowTable tbody td[colspan="8"] {
        padding: 55px 20px !important;
        text-align: center;
    }

    #borrowTable tbody td[colspan="8"] i {
        color: #ccc;
        font-size: 45px;
    }

    #borrowTable tbody td[colspan="8"] h5 {
        margin-top: 16px;
        color: var(--mgmt-text);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 991px) {
        .table-switcher {
            width: 100%;
            margin-top: 15px;
        }

        .table-switch-button {
            flex: 1;
            min-width: 140px;
        }
    }

    @media (max-width: 767px) {
        .mgmt-page .page-titles {
            padding: 18px;
        }

        .borrow-card .card-body,
        .filter-card .card-body,
        .summary-card .card-body {
            padding: 16px;
        }

        .borrow-card .card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .filter-column {
            margin-bottom: 15px;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    /*
     * Active books include both borrowed
     * and overdue transaction statuses.
     */
    $activeBorrowCount = $borrows
        ->whereIn(
            'status',
            [
                'borrowed',
                'overdue',
            ]
        )
        ->count();

    /*
     * Calculate current overdue records using
     * each transaction's policy-generated due date.
     */
    $overdueCount = $borrows
        ->filter(function ($borrow) {
            if ($borrow->status === 'returned') {
                return false;
            }

            return \Carbon\Carbon::parse(
                $borrow->due_date
            )
                ->endOfDay()
                ->isPast();
        })
        ->count();

    $returnedCount = $borrows
        ->where('status', 'returned')
        ->count();
@endphp

<div class="content-body mgmt-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">

            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        Borrowed and Returned Books
                    </h4>

                    <span>
                        Monitor borrowed books, returned books and deadlines
                    </span>
                </div>
            </div>

            <div
                class="col-sm-6 p-md-0
                       justify-content-sm-end
                       mt-2 mt-sm-0 d-flex"
            >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Borrowed Books
                    </li>
                </ol>
            </div>

        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div
                class="alert alert-success
                       alert-dismissible fade show"
            >
                <i class="fa fa-check-circle mr-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- ERROR --}}
        @if(session('error'))
            <div
                class="alert alert-danger
                       alert-dismissible fade show"
            >
                <i class="fa fa-exclamation-circle mr-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- SUMMARY CARDS --}}
        <div class="row">

            {{-- ACTIVE BORROWED --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>
                            Currently Borrowed
                        </h5>

                        <h2
                            id="totalBorrowed"
                            class="summary-number"
                        >
                            {{ $activeBorrowCount }}
                        </h2>

                        <span class="text-muted">
                            Books not yet returned
                        </span>
                    </div>
                </div>
            </div>

            {{-- OVERDUE --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>
                            Overdue
                        </h5>

                        <h2
                            id="totalOverdue"
                            class="summary-number text-danger"
                        >
                            {{ $overdueCount }}
                        </h2>

                        <span class="text-muted">
                            Books past their policy deadline
                        </span>
                    </div>
                </div>
            </div>

            {{-- RETURNED --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>
                            Returned
                        </h5>

                        <h2
                            id="totalReturned"
                            class="summary-number text-success"
                        >
                            {{ $returnedCount }}
                        </h2>

                        <span class="text-muted">
                            Successfully returned books
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- SEARCH FILTER --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-end">

                    <div
                        class="col-xl-11 col-lg-10 col-md-9
                               filter-column"
                    >
                        <label
                            for="borrowSearch"
                            class="filter-label"
                        >
                            Search Borrower or Book
                        </label>

                        <div class="search-wrapper">
                            <i class="fa fa-search"></i>

                            <input
                                type="text"
                                id="borrowSearch"
                                class="form-control filter-control"
                                placeholder="Search"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div
                        class="col-xl-1 col-lg-2 col-md-3
                               filter-column"
                    >
                        <label class="filter-label">
                            Clear
                        </label>

                        <button
                            type="button"
                            id="clearBorrowSearch"
                            class="btn btn-secondary
                                   clear-filter-button"
                            title="Clear search"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card borrow-card">

            <div class="card-header">
                <div class="row w-100 align-items-center">

                    <div class="col-lg-5">
                        <h4 class="card-title">
                            <i
                                id="tableViewIcon"
                                class="fa fa-list mr-2"
                            ></i>

                            <span id="tableViewTitle">
                                All Book Records
                            </span>
                        </h4>
                    </div>

                    <div class="col-lg-7 text-lg-right">
                        <div
                            class="table-switcher
                                   justify-content-lg-end"
                        >
                            <button
                                type="button"
                                class="table-switch-button active"
                                data-filter="all"
                            >
                                <i class="fa fa-list mr-1"></i>
                                All Records
                            </button>

                            <button
                                type="button"
                                class="table-switch-button"
                                data-filter="borrowed"
                            >
                                <i class="fa fa-book mr-1"></i>
                                Borrowed Books
                            </button>

                            <button
                                type="button"
                                class="table-switch-button"
                                data-filter="returned"
                            >
                                <i class="fa fa-check-circle mr-1"></i>
                                Returned Books
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table
                        id="borrowTable"
                        class="table table-hover"
                    >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book</th>
                                <th>Borrower</th>
                                <th>Date Borrowed</th>
                                <th>Deadline</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($borrows as $borrow)

                                @php
                                    /*
                                     * due_date was generated from
                                     * LibraryPolicy during borrowing.
                                     */
                                    $today =
                                        \Carbon\Carbon::today();

                                    $deadline =
                                        \Carbon\Carbon::parse(
                                            $borrow->due_date
                                        )->startOfDay();

                                    /*
                                     * Do not mark the book overdue
                                     * until the due date fully ends.
                                     */
                                    $isOverdue =
                                        $borrow->status
                                            !== 'returned'
                                        &&
                                        $deadline
                                            ->copy()
                                            ->endOfDay()
                                            ->isPast();

                                    /*
                                     * Difference between today and
                                     * the saved policy deadline.
                                     */
                                    $daysRemaining =
                                        $today->diffInDays(
                                            $deadline,
                                            false
                                        );
                                @endphp

                                <tr
                                    class="
                                        {{
                                            $isOverdue
                                                ? 'overdue-row'
                                                : ''
                                        }}

                                        {{
                                            $borrow->status
                                                === 'returned'
                                                ? 'returned-row'
                                                : ''
                                        }}
                                    "
                                >
                                    {{-- NUMBER --}}
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- BOOK --}}
                                    <td style="min-width:230px;">
                                        <strong>
                                            {{
                                                $borrow->book->title
                                                ?? 'Unknown Book'
                                            }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{
                                                $borrow->book->author
                                                ?? 'Unknown Author'
                                            }}
                                        </small>

                                        <br>

                                        <small>
                                            Call No:

                                            {{
                                                $borrow
                                                    ->book
                                                    ->call_number
                                                ?? '-'
                                            }}
                                        </small>
                                    </td>

                                    {{-- BORROWER --}}
                                    <td style="min-width:180px;">
                                        @if($borrow->borrower_record)
                                            <strong>
                                                {{
                                                    trim(
                                                        (
                                                            $borrow
                                                                ->borrower_record
                                                                ->firstname
                                                            ?? ''
                                                        )
                                                        . ' ' .
                                                        (
                                                            $borrow
                                                                ->borrower_record
                                                                ->lastname
                                                            ?? ''
                                                        )
                                                    )
                                                }}
                                            </strong>

                                            <br>

                                            <span class="borrower-details">
                                                {{
                                                    ucfirst(
                                                        $borrow
                                                            ->borrower_type
                                                    )
                                                }}

                                                -

                                                @if(
                                                    $borrow->borrower_type
                                                    === 'student'
                                                )
                                                    {{
                                                        $borrow
                                                            ->borrower_record
                                                            ->student_number
                                                        ?? '-'
                                                    }}
                                                @else
                                                    {{
                                                        $borrow
                                                            ->borrower_record
                                                            ->employee_number
                                                        ?? '-'
                                                    }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                Unknown Borrower
                                            </span>
                                        @endif
                                    </td>

                                    {{-- BORROWED DATE --}}
                                    <td>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $borrow->borrowed_at
                                            )->format('M d, Y')
                                        }}
                                    </td>

                                    {{-- POLICY DEADLINE --}}
                                    <td>
                                        <strong
                                            class="{{
                                                $isOverdue
                                                    ? 'text-danger'
                                                    : ''
                                            }}"
                                        >
                                            {{
                                                $deadline->format(
                                                    'M d, Y'
                                                )
                                            }}
                                        </strong>
                                    </td>

                                    {{-- DAYS REMAINING --}}
                                    <td>
                                        @if(
                                            $borrow->status
                                            === 'returned'
                                        )
                                            <span class="text-muted">
                                                Completed
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="text-danger">
                                                <i
                                                    class="fa
                                                           fa-exclamation-circle
                                                           mr-1"
                                                ></i>

                                                {{
                                                    abs(
                                                        $daysRemaining
                                                    )
                                                }}

                                                day(s) overdue
                                            </span>
                                        @elseif(
                                            $daysRemaining === 0
                                        )
                                            <span class="text-warning">
                                                <i
                                                    class="fa
                                                           fa-clock-o mr-1"
                                                ></i>

                                                Due Today
                                            </span>
                                        @else
                                            <span class="text-success">
                                                <i
                                                    class="fa
                                                           fa-calendar-check-o
                                                           mr-1"
                                                ></i>

                                                {{ $daysRemaining }}
                                                day(s) remaining
                                            </span>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        @if(
                                            $borrow->status
                                            === 'returned'
                                        )
                                            <span
                                                class="badge
                                                       badge-success
                                                       status-badge"
                                            >
                                                Returned
                                            </span>
                                        @elseif($isOverdue)
                                            <span
                                                class="badge
                                                       badge-danger
                                                       status-badge"
                                            >
                                                Overdue
                                            </span>
                                        @else
                                            <span
                                                class="badge
                                                       badge-warning
                                                       status-badge"
                                            >
                                                Borrowed
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td>
                                        @if(
                                            $borrow->status
                                            !== 'returned'
                                        )
                                            <form
                                                action="{{
                                                    route(
                                                        'bookborrow.return',
                                                        $borrow->id
                                                    )
                                                }}"
                                                method="POST"
                                                class="return-book-form"
                                            >
                                                @csrf
                                                @method('PUT')

                                                <button
                                                    type="submit"
                                                    class="btn
                                                           btn-success
                                                           action-btn"
                                                    title="Return Book"
                                                >
                                                    <i
                                                        class="fa fa-check"
                                                    ></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success">
                                                <i
                                                    class="fa
                                                           fa-check-circle
                                                           mr-1"
                                                ></i>

                                                Returned
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td
                                        colspan="8"
                                        class="text-center py-5"
                                    >
                                        <i class="fa fa-book"></i>
                                        <h5>No borrowing records found</h5>
                                        <p class="text-muted mb-0">
                                            There are no books currently borrowed or returned.
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

    var csrfToken = "{{ csrf_token() }}";

    var returnRouteTemplate = "{{ route('bookborrow.return', ['id' => '__BORROW_ID__']) }}";

    var dataUrl = "{{ route('bookborrow.data') }}";

    var ajaxIsRunning = false;
    var formIsSubmitting = false;
    var currentRecordFilter = 'all';

    /*
     * Custom table filter.
     */
    $.fn.dataTable.ext.search.push(
        function (
            settings,
            rowData
        ) {
            if (
                settings.nTable.id
                !== 'borrowTable'
            ) {
                return true;
            }

            if (
                currentRecordFilter
                === 'all'
            ) {
                return true;
            }

            const statusText = $('<div>')
                .html(rowData[6] ?? '')
                .text()
                .trim()
                .toLowerCase();

            if (
                currentRecordFilter
                === 'returned'
            ) {
                return statusText.includes(
                    'returned'
                );
            }

            return (
                statusText.includes('borrowed')
                ||
                statusText.includes('overdue')
            );
        }
    );

    const table =
        $('#borrowTable').DataTable({
            pageLength: 10,

            order: [
                [4, 'asc']
            ],

            stateSave: true,

            dom: 'lrtip',

            language: {
                emptyTable:
                    'No borrowing records found.',

                zeroRecords:
                    'No matching book records found.'
            }
        });

    /*
     * Restore a search saved by DataTables.
     */
    $('#borrowSearch').val(
        table.search()
    );

    /*
     * Search borrower name, borrower ID,
     * book information, dates and status.
     */
    $('#borrowSearch').on(
        'input',
        function () {
            table
                .search(
                    $(this).val()
                )
                .page('first')
                .draw();
        }
    );

    /*
     * Clear only the search. Keep the selected
     * All/Borrowed/Returned table view.
     */
    $('#clearBorrowSearch').on(
        'click',
        function () {
            $('#borrowSearch').val('');

            table
                .search('')
                .page('first')
                .draw();

            $('#borrowSearch').focus();
        }
    );

    /*
     * Table switch buttons.
     */
    $('.table-switch-button').on(
        'click',
        function () {
            currentRecordFilter =
                $(this).data('filter');

            $('.table-switch-button')
                .removeClass('active');

            $(this).addClass('active');

            if (
                currentRecordFilter
                === 'borrowed'
            ) {
                $('#tableViewIcon').attr(
                    'class',
                    'fa fa-book mr-2'
                );

                $('#tableViewTitle').text(
                    'Currently Borrowed Books'
                );
            } else if (
                currentRecordFilter
                === 'returned'
            ) {
                $('#tableViewIcon').attr(
                    'class',
                    'fa fa-check-circle mr-2'
                );

                $('#tableViewTitle').text(
                    'Returned Books'
                );
            } else {
                $('#tableViewIcon').attr(
                    'class',
                    'fa fa-list mr-2'
                );

                $('#tableViewTitle').text(
                    'All Book Records'
                );
            }

            table
                .page('first')
                .draw(false);
        }
    );

    function escapeHtml(value) {
        return $('<div>')
            .text(value ?? '')
            .html();
    }

    function createBookColumn(record) {
        return '<strong>' +
            escapeHtml(record.book_title) +
            '</strong><br>' +
            '<small class="text-muted">' +
            escapeHtml(record.book_author) +
            '</small><br>' +
            '<small>Call No: ' +
            escapeHtml(record.call_number) +
            '</small>';
    }

    function createBorrowerColumn(record) {
        return '<strong>' +
            escapeHtml(record.borrower_name) +
            '</strong><br>' +
            '<span class="borrower-details">' +
            escapeHtml(record.borrower_type) +
            ' - ' +
            escapeHtml(record.borrower_number) +
            '</span>';
    }

    function createDeadlineColumn(record) {
        var deadlineClass =
            record.is_overdue
                ? 'text-danger'
                : '';

        return '<strong class="' + deadlineClass + '">' +
            escapeHtml(record.deadline) +
            '</strong>';
    }

    function createDaysColumn(record) {
        if (
            record.status
            === 'returned'
        ) {
            return '<span class="text-muted">Completed</span>';
        }

        if (record.is_overdue) {
            return '<span class="text-danger">' +
                '<i class="fa fa-exclamation-circle mr-1"></i>' +
                Math.abs(Number(record.days)) +
                ' day(s) overdue</span>';
        }

        if (
            Number(record.days)
            === 0
        ) {
            return '<span class="text-warning">' +
                '<i class="fa fa-clock-o mr-1"></i>' +
                'Due Today</span>';
        }

        return '<span class="text-success">' +
            '<i class="fa fa-calendar-check-o mr-1"></i>' +
            record.days +
            ' day(s) remaining</span>';
    }

    function createStatusColumn(record) {
        if (
            record.status
            === 'returned'
        ) {
            return '<span class="badge badge-success status-badge">Returned</span>';
        }

        if (record.is_overdue) {
            return '<span class="badge badge-danger status-badge">Overdue</span>';
        }

        return '<span class="badge badge-warning status-badge">Borrowed</span>';
    }

    function createActionColumn(record) {
        if (
            record.status
            === 'returned'
        ) {
            return '<span class="text-success">' +
                '<i class="fa fa-check-circle mr-1"></i>' +
                'Returned</span>';
        }

        var returnUrl =
            returnRouteTemplate.replace(
                '__BORROW_ID__',
                record.id
            );

        return '<form action="' + returnUrl + '" method="POST" class="return-book-form d-inline">' +
            '<input type="hidden" name="_token" value="' + csrfToken + '">' +
            '<input type="hidden" name="_method" value="PUT">' +
            '<button type="submit" class="btn btn-success action-btn" title="Return Book">' +
            '<i class="fa fa-check"></i></button></form>';
    }

    /*
     * Automatic refresh.
     */
    async function refreshBorrowTable() {
        if (
            ajaxIsRunning
            ||
            formIsSubmitting
        ) {
            return;
        }

        ajaxIsRunning = true;

        try {
            const response = await fetch(
                dataUrl,
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'
                    },

                    cache: 'no-store'
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Unable to retrieve borrowing records.'
                );
            }

            const data =
                await response.json();

            const currentPage =
                table.page();

            const currentSearch =
                table.search();

            const rows = data.records.map(
                function (
                    record,
                    index
                ) {
                    return [
                        index + 1,

                        createBookColumn(
                            record
                        ),

                        createBorrowerColumn(
                            record
                        ),

                        escapeHtml(
                            record.borrowed_at
                        ),

                        createDeadlineColumn(
                            record
                        ),

                        createDaysColumn(
                            record
                        ),

                        createStatusColumn(
                            record
                        ),

                        createActionColumn(
                            record
                        )
                    ];
                }
            );

            table.clear();
            table.rows.add(rows);

            table.search(
                currentSearch
            );

            table.draw(false);

            const pageInformation =
                table.page.info();

            if (
                currentPage
                < pageInformation.pages
            ) {
                table.page(
                    currentPage
                ).draw(false);
            }

            $('#totalBorrowed').text(
                data.summary.borrowed
            );

            $('#totalOverdue').text(
                data.summary.overdue
            );

            $('#totalReturned').text(
                data.summary.returned
            );

        } catch (error) {
            console.error(
                'Borrow table refresh error:',
                error
            );
        } finally {
            ajaxIsRunning = false;
        }
    }

    /*
     * Return confirmation.
     */
    $('#borrowTable tbody').on(
        'submit',
        '.return-book-form',
        function (event) {
            const confirmed =
                window.confirm(
                    'Mark this book as returned?'
                );

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            formIsSubmitting = true;

            $(this)
                .find(
                    'button[type="submit"]'
                )
                .prop('disabled', true)
                .html(
                    '<i class="fa fa-spinner fa-spin"></i>'
                );
        }
    );

    refreshBorrowTable();

    setInterval(
        refreshBorrowTable,
        3000
    );

});
</script>