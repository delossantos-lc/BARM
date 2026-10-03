@include('layouts.header')
@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>
    /* ============================================================
       DASHBOARD – MODERN & RESPONSIVE
       ============================================================ */

    :root {
        --primary: #7571f9;
        --primary-light: rgba(117, 113, 249, 0.12);
        --primary-soft: rgba(117, 113, 249, 0.18);
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.07);
        --shadow-lg: 0 12px 30px rgba(0, 0, 0, 0.09);
        --radius: 14px;
        --radius-sm: 10px;
        --transition: 0.25s ease;
        --text-dark: #2d2d3a;
        --text-muted: #7a7a8c;
        --bg-soft: #f8f9fd;
    }

    /* ---------- GLOBAL ---------- */
    body {
        background: var(--bg-soft);
    }

    .content-body {
        padding: 1.2rem 1.5rem 2rem;
    }

    @media (max-width: 767px) {
        .content-body {
            padding: 0.8rem 0.8rem 1.5rem;
        }
    }

    /* ---------- PAGE TITLE ---------- */
    .page-titles {
        background: transparent;
        padding: 0 0 0.5rem 0;
        margin-bottom: 0.5rem;
    }

    .page-titles .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 0.9rem;
    }

    .page-titles .breadcrumb-item a {
        color: var(--text-muted);
        font-weight: 500;
        transition: color var(--transition);
    }

    .page-titles .breadcrumb-item a:hover {
        color: var(--primary);
        text-decoration: none;
    }

    /* ---------- SUMMARY CARDS ---------- */
    .summary-card {
        min-height: 150px;
        border: none;
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform var(--transition), box-shadow var(--transition);
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .summary-card .card-body {
        position: relative;
        padding: 22px 24px;
        z-index: 1;
    }

    .summary-card .card-title {
        font-size: 0.95rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        opacity: 0.9;
        margin-bottom: 6px;
    }

    .summary-card h2 {
        font-size: 2.4rem;
        font-weight: 700;
        margin-bottom: 2px;
        line-height: 1.1;
    }

    .summary-card p {
        font-size: 0.82rem;
        opacity: 0.8;
        margin-bottom: 0;
    }

    .summary-icon {
        position: absolute;
        right: 18px;
        bottom: 18px;
        font-size: 48px;
        opacity: 0.22;
        transition: opacity var(--transition), transform var(--transition);
        pointer-events: none;
    }

    .summary-card:hover .summary-icon {
        opacity: 0.35;
        transform: scale(1.08);
    }

    /* Gradient backgrounds (kept, but softer) */
    .gradient-1 {
        background: linear-gradient(135deg, #6a5af9 0%, #8b7bfa 100%);
    }

    .gradient-2 {
        background: linear-gradient(135deg, #f97b8b 0%, #fa9a9a 100%);
    }

    .gradient-3 {
        background: linear-gradient(135deg, #4bc9c9 0%, #6ed6d6 100%);
    }

    .gradient-4 {
        background: linear-gradient(135deg, #f9a44a 0%, #fbbf6e 100%);
    }

    /* ---------- ANALYTICS CARDS ---------- */
    .analytics-card {
        border: none;
        border-radius: var(--radius);
        min-height: 130px;
        background: #ffffff;
        transition: transform var(--transition), box-shadow var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .analytics-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .analytics-card .card-body {
        padding: 20px 22px;
    }

    .analytics-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .analytics-label {
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 500;
        letter-spacing: 0.2px;
        margin-top: 2px;
    }

    .analytics-card small.text-muted {
        font-size: 0.75rem;
    }

    .analytics-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 22px;
        background: var(--primary-light);
        color: var(--primary);
        flex-shrink: 0;
        transition: background var(--transition);
    }

    .analytics-card:hover .analytics-icon {
        background: var(--primary-soft);
    }

    /* ---------- SECTION TITLES ---------- */
    .section-title {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 1.15rem;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary);
        font-size: 1.1rem;
        width: 22px;
        text-align: center;
    }

    .section-description {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    /* ---------- PANELS ---------- */
    .dashboard-panel {
        height: 100%;
        border: none;
        border-radius: var(--radius);
        background: #ffffff;
        box-shadow: var(--shadow-sm);
        transition: box-shadow var(--transition);
    }

    .dashboard-panel:hover {
        box-shadow: var(--shadow-md);
    }

    .dashboard-panel .card-body {
        padding: 24px 26px;
    }

    @media (max-width: 767px) {
        .dashboard-panel .card-body {
            padding: 18px 16px;
        }
    }

    /* ---------- TOP BORROWER ---------- */
    .top-borrower-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 18px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 38px;
        transition: transform var(--transition);
    }

    .top-borrower-avatar:hover {
        transform: scale(1.04);
    }

    .borrow-count {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1.1;
    }

    .top-borrower-info .badge {
        font-size: 0.8rem;
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 500;
    }

    /* ---------- BOOK LIST ---------- */
    .popular-book {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f5;
        transition: background var(--transition);
        border-radius: 6px;
    }

    .popular-book:last-child {
        border-bottom: none;
    }

    .popular-book:hover {
        background: #fafaff;
        padding-left: 6px;
        padding-right: 6px;
    }

    .book-rank {
        width: 34px;
        height: 34px;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        font-size: 0.85rem;
        margin-right: 12px;
        flex-shrink: 0;
        transition: background var(--transition);
    }

    .popular-book:hover .book-rank {
        background: var(--primary-soft);
    }

    /* ---------- LEADERBOARD TABLE ---------- */
    .borrower-table {
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .borrower-table th {
        border-top: none;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        padding: 12px 10px;
        border-bottom: 2px solid #f0f0f5;
    }

    .borrower-table td {
        vertical-align: middle;
        padding: 12px 10px;
        border-top: 1px solid #f5f5fa;
    }

    .borrower-table tbody tr {
        transition: background var(--transition);
    }

    .borrower-table tbody tr:hover {
        background: #fafaff;
    }

    .rank-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        font-size: 0.82rem;
        background: #f1f1f6;
        color: var(--text-dark);
        transition: transform var(--transition);
    }

    .rank-number.rank-one {
        background: #ffc107;
        color: #fff;
        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.35);
    }

    .rank-number.rank-two {
        background: #8e9aab;
        color: #fff;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.25);
    }

    .rank-number.rank-three {
        background: #cd7f32;
        color: #fff;
        box-shadow: 0 2px 8px rgba(205, 127, 50, 0.25);
    }

    .borrower-table .badge {
        font-size: 0.72rem;
        padding: 5px 12px;
        border-radius: 30px;
        font-weight: 500;
        letter-spacing: 0.2px;
    }

    /* ---------- CHART CONTAINERS ---------- */
    .chart-container {
        position: relative;
        height: 340px;
        width: 100%;
    }

    .trend-chart-container {
        position: relative;
        height: 290px;
        width: 100%;
    }

    /* ---------- RESPONSIVE ADJUSTMENTS ---------- */
    @media (max-width: 991px) {
        .chart-container {
            height: 280px;
        }

        .trend-chart-container {
            height: 250px;
        }

        .summary-card {
            min-height: 130px;
        }

        .summary-card h2 {
            font-size: 2rem;
        }

        .summary-icon {
            font-size: 38px;
            right: 14px;
            bottom: 14px;
        }

        .analytics-number {
            font-size: 1.7rem;
        }

        .analytics-icon {
            width: 46px;
            height: 46px;
            font-size: 19px;
        }

        .borrow-count {
            font-size: 2.3rem;
        }

        .top-borrower-avatar {
            width: 76px;
            height: 76px;
            font-size: 32px;
        }
    }

    @media (max-width: 575px) {
        .content-body {
            padding: 0.6rem 0.6rem 1.2rem;
        }

        .summary-card {
            min-height: 110px;
        }

        .summary-card .card-body {
            padding: 16px 18px;
        }

        .summary-card h2 {
            font-size: 1.7rem;
        }

        .summary-card .card-title {
            font-size: 0.82rem;
        }

        .summary-card p {
            font-size: 0.72rem;
        }

        .summary-icon {
            font-size: 30px;
            right: 12px;
            bottom: 12px;
        }

        .analytics-card .card-body {
            padding: 16px;
        }

        .analytics-number {
            font-size: 1.4rem;
        }

        .analytics-label {
            font-size: 0.72rem;
        }

        .analytics-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
            border-radius: 10px;
        }

        .dashboard-panel .card-body {
            padding: 16px 14px;
        }

        .section-title {
            font-size: 1rem;
        }

        .section-description {
            font-size: 0.78rem;
        }

        .chart-container {
            height: 220px;
        }

        .trend-chart-container {
            height: 200px;
        }

        .borrow-count {
            font-size: 1.9rem;
        }

        .top-borrower-avatar {
            width: 64px;
            height: 64px;
            font-size: 26px;
            margin-bottom: 12px;
        }

        .borrower-table {
            font-size: 0.78rem;
        }

        .borrower-table th,
        .borrower-table td {
            padding: 8px 6px;
        }

        .rank-number {
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }

        .book-rank {
            width: 28px;
            height: 28px;
            font-size: 0.72rem;
            margin-right: 10px;
        }

        .popular-book strong {
            font-size: 0.82rem;
        }

        .popular-book .text-muted {
            font-size: 0.72rem;
        }
    }

    /* ---------- UTILITY ---------- */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
        border-radius: var(--radius-sm);
    }

    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #d0d0dd;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f6;
        border-radius: 10px;
    }

    /* Fix badge colors to match theme */
    .badge-primary {
        background: var(--primary);
        color: #fff;
    }

    .badge-success {
        background: #2ecc71;
        color: #fff;
    }

    .badge-secondary {
        background: #a0a0b8;
        color: #fff;
    }

    .alert {
        border-radius: var(--radius-sm);
        border: none;
        font-size: 0.9rem;
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')


<!--**********************************
    Content body start
***********************************-->

<div class="content-body">

    <div class="container-fluid">

        <!-- PAGE TITLE -->

        <div class="row page-titles mx-0">

            <div class="col p-md-0">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)">
                            Dashboard
                        </a>
                    </li>

                </ol>

            </div>

        </div>


        <!-- SESSION MESSAGE -->

        @if(Session::has('success'))

            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>

        @endif


        @if(Session::has('fail'))

            <div class="alert alert-danger">
                {{ Session::get('fail') }}
            </div>

        @endif


        <!-- ==================================================
             SUMMARY CARDS
        =================================================== -->

        <div class="row">

            <!-- BORROWED BOOKS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">

                <div class="card gradient-1 summary-card">

                    <div class="card-body">

                        <h3 class="card-title text-white">
                            Borrowed Books
                        </h3>

                        <div class="d-inline-block">

                            <h2 class="text-white">
                                {{ $borrowedToday ?? 0 }}
                            </h2>

                            <p class="text-white mb-0">
                                {{ date('F j, Y') }}
                            </p>

                        </div>

                        <span class="summary-icon text-white">
                            <i class="fa fa-book"></i>
                        </span>

                    </div>

                </div>

            </div>


            <!-- RETURNED BOOKS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">

                <div class="card gradient-2 summary-card">

                    <div class="card-body">

                        <h3 class="card-title text-white">
                            Returned Books
                        </h3>

                        <div class="d-inline-block">

                            <h2 class="text-white">
                                {{ $returnedToday ?? 0 }}
                            </h2>

                            <p class="text-white mb-0">
                                {{ date('F j, Y') }}
                            </p>

                        </div>

                        <span class="summary-icon text-white">
                            <i class="fa fa-undo"></i>
                        </span>

                    </div>

                </div>

            </div>


            <!-- CURRENTLY BORROWED -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">

                <div class="card gradient-3 summary-card">

                    <div class="card-body">

                        <h3 class="card-title text-white">
                            Currently Borrowed
                        </h3>

                        <div class="d-inline-block">

                            <h2 class="text-white">
                                {{ $currentlyBorrowed ?? 0 }}
                            </h2>

                            <p class="text-white mb-0">
                                Active Borrowings
                            </p>

                        </div>

                        <span class="summary-icon text-white">
                            <i class="fa fa-bookmark"></i>
                        </span>

                    </div>

                </div>

            </div>


            <!-- OVERDUE BOOKS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">

                <div class="card gradient-4 summary-card">

                    <div class="card-body">

                        <h3 class="card-title text-white">
                            Overdue Books
                        </h3>

                        <div class="d-inline-block">

                            <h2 class="text-white">
                                {{ $overdueBooks ?? 0 }}
                            </h2>

                            <p class="text-white mb-0">
                                Need Attention
                            </p>

                        </div>

                        <span class="summary-icon text-white">
                            <i class="fa fa-exclamation-triangle"></i>
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             BORROWING ANALYTICS TITLE
        =================================================== -->

        <div class="row mt-4 mb-3">

            <div class="col-12">

                <h3 class="section-title">

                    <i class="fa fa-line-chart"></i>
                    Borrowing Analytics

                </h3>

                <p class="section-description">
                    Monitor students and personnel who frequently borrow books.
                </p>

            </div>

        </div>


        <!-- ==================================================
             ANALYTICS CARDS
        =================================================== -->

        <div class="row">

            <!-- TOTAL BORROWINGS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">

                <div class="card analytics-card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="analytics-number">
                                    {{ $totalBorrowings ?? 0 }}
                                </div>

                                <div class="analytics-label">
                                    Total Borrowings
                                </div>

                            </div>

                            <div class="analytics-icon">
                                <i class="fa fa-book"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- UNIQUE BORROWERS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">

                <div class="card analytics-card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="analytics-number">
                                    {{ $uniqueBorrowers ?? 0 }}
                                </div>

                                <div class="analytics-label">
                                    Unique Borrowers
                                </div>

                            </div>

                            <div class="analytics-icon">
                                <i class="fa fa-users"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- STUDENT BORROWINGS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">

                <div class="card analytics-card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="analytics-number">
                                    {{ $studentBorrowings ?? 0 }}
                                </div>

                                <div class="analytics-label">
                                    Student Borrowings
                                </div>

                                <small class="text-muted">
                                    {{ $uniqueStudents ?? 0 }} student(s)
                                </small>

                            </div>

                            <div class="analytics-icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PERSONNEL BORROWINGS -->

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">

                <div class="card analytics-card shadow-sm">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <div class="analytics-number">
                                    {{ $personnelBorrowings ?? 0 }}
                                </div>

                                <div class="analytics-label">
                                    Personnel Borrowings
                                </div>

                                <small class="text-muted">
                                    {{ $uniquePersonnel ?? 0 }} personnel
                                </small>

                            </div>

                            <div class="analytics-icon">
                                <i class="fa fa-briefcase"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             TOP BORROWER ANALYTICS
        =================================================== -->

        <div class="row">

            <!-- MOST FREQUENT BORROWERS -->

            <div class="col-xl-8 col-lg-7 mb-4">

                <div class="card dashboard-panel shadow-sm">

                    <div class="card-body">

                        <h4 class="section-title">
                            <i class="fa fa-bar-chart"></i>
                            Most Frequent Borrowers
                        </h4>

                        <p class="section-description mb-4">
                            Top 10 students or personnel based on total borrowing transactions.
                        </p>

                        @if(isset($topBorrowers) && $topBorrowers->count() > 0)

                            <div class="chart-container">
                                <canvas id="borrowerChart"></canvas>
                            </div>

                        @else

                            <div class="text-center text-muted py-5">

                                <i
                                    class="fa fa-bar-chart"
                                    style="font-size:45px; opacity:0.4;"
                                ></i>

                                <p class="mt-3 mb-0">
                                    No borrowing records yet.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            <!-- TOP BORROWER -->

            <div class="col-xl-4 col-lg-5 mb-4">

                <div class="card dashboard-panel shadow-sm">

                    <div class="card-body">

                        <h4 class="section-title">
                            <i class="fa fa-trophy"></i>
                            Top Borrower
                        </h4>

                        <p class="section-description mb-4">
                            Most active library borrower.
                        </p>

                        @if(isset($topBorrower) && $topBorrower)

                            <div class="text-center top-borrower-info">

                                <div class="top-borrower-avatar">
                                    <i class="fa fa-user"></i>
                                </div>

                                <h3 class="mb-2">
                                    {{ $topBorrower->borrower_name }}
                                </h3>

                                @if($topBorrower->borrower_type === 'student')

                                    <span class="badge badge-primary p-2">
                                        Student
                                    </span>

                                @else

                                    <span class="badge badge-success p-2">
                                        Personnel
                                    </span>

                                @endif

                                <hr>

                                <div class="borrow-count">
                                    {{ $topBorrower->total_borrowed }}
                                </div>

                                <div class="text-muted mt-2">
                                    Total Books Borrowed
                                </div>

                            </div>

                        @else

                            <div class="text-center text-muted py-5">

                                <i
                                    class="fa fa-user"
                                    style="font-size:45px; opacity:0.4;"
                                ></i>

                                <p class="mt-3 mb-0">
                                    No borrower data yet.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             7 DAY CHART + MOST BORROWED BOOK
        =================================================== -->

        <div class="row">

            <!-- 7 DAY BORROWING ACTIVITY -->

            <div class="col-xl-8 col-lg-7 mb-4">

                <div class="card dashboard-panel shadow-sm">

                    <div class="card-body">

                        <h4 class="section-title">
                            <i class="fa fa-line-chart"></i>
                            7-Day Borrowing Activity
                        </h4>

                        <p class="section-description mb-4">
                            Number of books borrowed during the last seven days.
                        </p>

                        <div class="trend-chart-container">
                            <canvas id="dailyBorrowChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>


            <!-- MOST BORROWED BOOKS -->

            <div class="col-xl-4 col-lg-5 mb-4">

                <div class="card dashboard-panel shadow-sm">

                    <div class="card-body">

                        <h4 class="section-title">
                            <i class="fa fa-book"></i>
                            Most Borrowed Books
                        </h4>

                        <p class="section-description mb-3">
                            Top books based on borrowing transactions.
                        </p>

                        @if(isset($mostBorrowedBooks))

                            @forelse($mostBorrowedBooks as $index => $book)

                                <div class="popular-book d-flex align-items-center">

                                    <div class="book-rank">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="flex-grow-1">

                                        <strong>
                                            {{ $book->book_title ?? 'Unknown Book' }}
                                        </strong>

                                        <div class="text-muted">
                                            {{ $book->total_borrowed }} borrowing(s)
                                        </div>

                                    </div>

                                </div>

                            @empty

                                <div class="text-center text-muted py-4">
                                    No book records yet.
                                </div>

                            @endforelse

                        @else

                            <div class="text-center text-muted py-4">
                                No book records yet.
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             BORROWING LEADERBOARD
        =================================================== -->

        <div class="row">

            <div class="col-12 mb-4">

                <div class="card dashboard-panel shadow-sm">

                    <div class="card-body">

                        <h4 class="section-title">
                            <i class="fa fa-users"></i>
                            Borrowing Leaderboard
                        </h4>

                        <p class="section-description mb-4">
                            Students and personnel with the highest borrowing activity.
                        </p>


                        <div class="table-responsive">

                            <table class="table table-hover borrower-table">

                                <thead>

                                    <tr>

                                        <th>Rank</th>

                                        <th>Borrower</th>

                                        <th>Type</th>

                                        <th>Total Borrowed</th>

                                        <th>Activity</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @if(isset($topBorrowers))

                                        @forelse($topBorrowers as $index => $borrower)

                                            <tr>

                                                <!-- RANK -->

                                                <td>

                                                    @if($index === 0)

                                                        <span class="rank-number rank-one">
                                                            1
                                                        </span>

                                                    @elseif($index === 1)

                                                        <span class="rank-number rank-two">
                                                            2
                                                        </span>

                                                    @elseif($index === 2)

                                                        <span class="rank-number rank-three">
                                                            3
                                                        </span>

                                                    @else

                                                        <span class="rank-number">
                                                            {{ $index + 1 }}
                                                        </span>

                                                    @endif

                                                </td>


                                                <!-- BORROWER -->

                                                <td>

                                                    <strong>
                                                        {{ $borrower->borrower_name }}
                                                    </strong>

                                                </td>


                                                <!-- TYPE -->

                                                <td>

                                                    @if($borrower->borrower_type === 'student')

                                                        <span class="badge badge-primary">
                                                            Student
                                                        </span>

                                                    @else

                                                        <span class="badge badge-success">
                                                            Personnel
                                                        </span>

                                                    @endif

                                                </td>


                                                <!-- TOTAL BORROWED -->

                                                <td>

                                                    <strong>
                                                        {{ $borrower->total_borrowed }}
                                                    </strong>

                                                </td>


                                                <!-- ACTIVITY -->

                                                <td>

                                                    @if($borrower->total_borrowed >= 10)

                                                        <span class="badge badge-success">
                                                            Very Active
                                                        </span>

                                                    @elseif($borrower->total_borrowed >= 5)

                                                        <span class="badge badge-primary">
                                                            Active
                                                        </span>

                                                    @else

                                                        <span class="badge badge-secondary">
                                                            Regular
                                                        </span>

                                                    @endif

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td
                                                    colspan="5"
                                                    class="text-center text-muted py-5"
                                                >

                                                    <i
                                                        class="fa fa-users"
                                                        style="font-size:40px; opacity:0.4;"
                                                    ></i>

                                                    <p class="mt-3 mb-0">
                                                        No borrowing records found.
                                                    </p>

                                                </td>

                                            </tr>

                                        @endforelse

                                    @else

                                        <tr>

                                            <td
                                                colspan="5"
                                                class="text-center text-muted py-5"
                                            >
                                                No borrowing records found.
                                            </td>

                                        </tr>

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!--**********************************
    Content body end
***********************************-->


@include('layouts.footer')


<!-- Chart.js -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | LOAD DASHBOARD CHARTS
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {

        createBorrowerChart();

        createDailyBorrowChart();

    });


    /*
    |--------------------------------------------------------------------------
    | TOP BORROWERS CHART
    |--------------------------------------------------------------------------
    */

    function createBorrowerChart() {

        const canvas =
            document.getElementById('borrowerChart');

        if (!canvas) {
            return;
        }


        const names =
            @json($borrowerNames ?? []);

        const counts =
            @json($borrowerCounts ?? []);


        if (names.length === 0) {
            return;
        }


        new Chart(canvas, {

            type: 'bar',

            data: {

                labels: names,

                datasets: [

                    {

                        label:
                            'Books Borrowed',

                        data:
                            counts,

                        backgroundColor:
                            'rgba(117, 113, 249, 0.75)',

                        borderColor:
                            'rgba(117, 113, 249, 1)',

                        borderWidth:
                            1,

                        borderRadius:
                            6

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        title: {
                            display: true,
                            text: 'Total Borrowings'
                        }

                    },

                    x: {

                        title: {
                            display: true,
                            text: 'Borrower'
                        }

                    }

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | SEVEN DAY BORROWING CHART
    |--------------------------------------------------------------------------
    */

    function createDailyBorrowChart() {

        const canvas =
            document.getElementById('dailyBorrowChart');

        if (!canvas) {
            return;
        }


        const labels =
            @json($dailyLabels ?? []);

        const values =
            @json($dailyValues ?? []);


        new Chart(canvas, {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {

                        label:
                            'Books Borrowed',

                        data:
                            values,

                        backgroundColor:
                            'rgba(117, 113, 249, 0.12)',

                        borderColor:
                            'rgba(117, 113, 249, 1)',

                        borderWidth:
                            3,

                        fill:
                            true,

                        tension:
                            0.35,

                        pointRadius:
                            4,

                        pointHoverRadius:
                            6

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        title: {
                            display: true,
                            text: 'Books Borrowed'
                        }

                    },

                    x: {

                        title: {
                            display: true,
                            text: 'Date'
                        }

                    }

                }

            }

        });

    }

</script>