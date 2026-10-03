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
    .form-card,
    .management-card {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
    }

    .form-card {
        margin-bottom: 24px;
    }

    .form-card .card-body {
        padding: 22px 24px;
    }

    .form-card h4 {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 700;
    }

    .form-card h4 i {
        color: var(--mgmt-primary);
    }

    .form-card p.text-muted {
        font-size: 13px;
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
    }

    .management-card .card-title i {
        color: var(--mgmt-primary);
    }

    .management-card .card-body {
        padding: 22px;
    }

    /* ═══════════════════════════════════════
       TOTAL BOOKS PILL
       ═══════════════════════════════════════ */
    .total-books-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: 1px solid #f5d6e1;
        border-radius: 20px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        font-weight: 700;
    }

    .total-books-pill i {
        color: var(--mgmt-primary);
        font-size: 12px;
    }

    /* ═══════════════════════════════════════
       FORM SECTION TITLES
       ═══════════════════════════════════════ */
    .form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--mgmt-border);
        color: var(--mgmt-text);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .form-section-title::before {
        content: "";
        display: inline-block;
        width: 4px;
        height: 16px;
        border-radius: 2px;
        background: var(--mgmt-primary);
    }

    /* ═══════════════════════════════════════
       FORM CONTROLS
       ═══════════════════════════════════════ */
    .form-card label,
    .management-card label,
    .modal label {
        display: block;
        margin-bottom: 8px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
    }

    .form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--mgmt-text);
        font-size: 13px;
        height: auto;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .form-control:hover {
        border-color: #c9c2ce;
    }

    .form-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
        outline: none;
    }

    textarea.form-control {
        min-height: 90px;
        resize: vertical;
    }

    .form-text {
        font-size: 11px;
        color: var(--mgmt-muted);
    }

    /* ═══════════════════════════════════════
       BUTTONS
       ═══════════════════════════════════════ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        border: 0;
        background: var(--mgmt-primary);
        color: #fff;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: var(--mgmt-primary-dark);
        color: #fff;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
    }

    .btn-secondary {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .btn-secondary:hover,
    .btn-secondary:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .12);
    }

    .btn-light {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .btn-light:hover,
    .btn-light:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
    }

    .btn-outline-primary {
        border: 1px solid var(--mgmt-primary);
        background: #fff;
        color: var(--mgmt-primary-dark);
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background: var(--mgmt-primary);
        color: #fff;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
    }

    .btn-add-book {
        min-width: 140px;
        min-height: 43px;
        font-weight: 700;
    }

    .book-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* Search Input styling */
    .book-search-wrapper {
        position: relative;
        min-width: 200px;
    }
    .book-search-wrapper .form-control {
        padding-left: 36px;
        min-height: 43px;
        border-radius: 11px;
    }
    .book-search-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--mgmt-muted);
        font-size: 13px;
    }

    /* ═══════════════════════════════════════
       CSV HELP
       ═══════════════════════════════════════ */
    .csv-help {
        padding: 12px 15px;
        border-radius: 12px;
        background: var(--mgmt-primary-softer);
        border: 1px solid #f5d6e1;
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.6;
    }

    /* ═══════════════════════════════════════
       TABLE (Normal scroll, no sticky header)
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    #booksTable {
        width: 100% !important;
        margin-bottom: 0;
    }

    #booksTable thead th {
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

    #booksTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    #booksTable.table-hover tbody tr:hover td {
        background: #fff7fa;
    }

    /* ═══════════════════════════════════════
       TABLE CELLS
       ═══════════════════════════════════════ */
    .book-title {
        color: var(--mgmt-text);
        font-weight: 700;
        font-size: 13px;
    }

    .book-author {
        color: var(--mgmt-muted);
        font-size: 12px;
        margin-top: 3px;
    }

    .book-author i {
        font-size: 10px;
    }

    .book-code {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 18px;
        background: #f4f5f7;
        color: #4a4652;
        font-size: 11px;
        font-weight: 700;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        white-space: nowrap;
    }

    .badge-book {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-success.badge-book {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .badge-secondary.badge-book {
        background: #f1f1f1;
        color: #666;
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

    .btn-info.action-btn {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
    }

    .btn-info.action-btn:hover {
        background: var(--mgmt-info);
        color: #fff;
    }

    .btn-warning.action-btn {
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }

    .btn-warning.action-btn:hover {
        background: var(--mgmt-warning);
        color: #fff;
    }

    .btn-danger.action-btn {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }

    .btn-danger.action-btn:hover {
        background: var(--mgmt-danger);
        color: #fff;
    }

    /* ═══════════════════════════════════════
       MODALS
       ═══════════════════════════════════════ */
    .modal-content {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        box-shadow: var(--mgmt-shadow-hover);
    }

    .modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .modal-title {
        color: var(--mgmt-text);
        font-size: 16px;
        font-weight: 700;
    }

    .modal-title i {
        color: var(--mgmt-primary);
    }

    .modal-body {
        padding: 22px;
        background: #fff;
    }

    .modal-footer {
        padding: 16px 22px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    /* Info Boxes - Used in View, Add, and Edit Modals */
    .info-box {
        background: #fcfbfe; /* Soft off-white */
        border: 1px solid #eae6f0; /* Light border */
        border-radius: 10px;
        padding: 14px 16px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .info-box:hover {
        background: var(--mgmt-primary-softer);
        border-color: #f3cbd7;
        box-shadow: 0 4px 12px rgba(213, 91, 145, 0.05);
    }
    .info-box strong {
        display: block;
        color: var(--mgmt-primary-dark); /* Primary dark for theme match */
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .info-box p {
        margin-bottom: 0;
        color: var(--mgmt-text);
        font-size: 13.5px;
        font-weight: 500;
        line-height: 1.5;
    }
    .info-box .book-code {
        margin-bottom: 0;
        font-size: 12px;
    }
    /* Adjustments for forms inside info-boxes */
    .info-box label {
        margin-bottom: 6px;
        font-size: 10px;
        color: var(--mgmt-primary-dark);
        font-weight: 800;
    }
    .info-box .form-control {
        background: #fff;
        border-color: #eae6f0;
        min-height: 40px;
        padding: 8px 12px;
        font-size: 13px;
    }
    .info-box .form-control:focus {
        border-color: var(--mgmt-primary);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
    }

    /* Edit modal layout */
    .edit-book-modal .modal-dialog {
        max-width: 980px;
        width: calc(100% - 32px);
        margin: 24px auto;
    }

    .edit-book-modal .modal-body .row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 24px;
        margin: 0;
    }

    .edit-book-modal .modal-body .row > div {
        width: 100%;
        max-width: none;
        padding: 0;
        margin-bottom: 0 !important;
    }

    .edit-book-modal .edit-book-rfid {
        grid-column: 1 / -1;
    }

    /* Add Book Modal Layout */
    .add-book-modal .modal-dialog {
        max-width: 900px;
        width: calc(100% - 32px);
        margin: 24px auto;
    }

    .add-book-modal .modal-body .row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 24px;
        margin: 0;
    }

    .add-book-modal .modal-body .row > div {
        width: 100%;
        max-width: none;
        padding: 0;
        margin-bottom: 0 !important;
    }

    .add-book-modal .add-book-rfid {
        grid-column: 1 / -1;
    }

    @media (max-width: 700px) {
        .edit-book-modal .modal-body .row,
        .add-book-modal .modal-body .row {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    /* ═══════════════════════════════════════
       ALERTS
       ═══════════════════════════════════════ */
    .alert {
        border-radius: 14px;
        border: 1px solid var(--mgmt-border);
        font-size: 13px;
    }

    .alert-success {
        border-color: #c9e9d4;
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .alert-danger {
        border-color: #f3c9c9;
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
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
    #booksTable tbody td[colspan="11"] {
        padding: 55px 20px !important;
        text-align: center;
    }

    #booksTable tbody td[colspan="11"] i {
        color: #ccc;
        font-size: 45px;
    }

    #booksTable tbody td[colspan="11"] h5 {
        margin-top: 16px;
        color: var(--mgmt-text);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .mgmt-page .page-titles {
            padding: 18px;
        }

        .form-card .card-body,
        .management-card .card-body {
            padding: 16px;
        }

        .management-card .card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .form-card .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .book-actions {
            width: 100%;
            flex-direction: column;
        }

        .book-actions .btn {
            width: 100%;
        }

        .book-search-wrapper {
            width: 100%;
        }

        .btn-add-book {
            flex: 1;
        }

        .action-btn {
            min-width: 40px;
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
                    <h4>Book Management</h4>
                    <span>Manage library book records</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Books</li>
                </ol>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle mr-2"></i>
                {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if(Session::has('fail'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>
                {{ Session::get('fail') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please check the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- ADD BOOK FORM CARD --}}
        <div class="card form-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="mb-0">
                            <i class="fa fa-book mr-2"></i>
                            Add New Book
                        </h4>
                    </div>
                    <div class="book-actions">
                        <div class="book-search-wrapper">
                            <i class="fa fa-search"></i>
                            <input type="text" id="bookSearchInput" class="form-control" placeholder="Search books...">
                        </div>
                        <button
                            class="btn btn-outline-primary btn-add-book"
                            type="button"
                            data-toggle="modal"
                            data-target="#csvImportModal"
                        >
                            <i class="fa fa-file-excel-o"></i>
                            Import CSV
                        </button>
                        <button
                            class="btn btn-primary btn-add-book"
                            type="button"
                            data-toggle="modal"
                            data-target="#addBookModal"
                        >
                            <i class="fa fa-plus"></i>
                            Add Book
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOOKS TABLE --}}
        <div class="card management-card">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fa fa-book mr-2"></i>
                        Books
                    </h4>
                </div>

                <span class="total-books-pill">
                    <i class="fa fa-database"></i>
                    Total Books: {{ isset($books) ? $books->count() : 0 }}
                </span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="booksTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book Code</th>
                                <th>Book</th>
                                <th>Call Number</th>
                                <th>Sublocation</th>
                                <th>Publisher</th>
                                <th>Year</th>
                                <th>ISBN</th>
                                <th>Subject</th>
                                <th>Reservable</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($books as $book)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <span class="book-code">
                                            {{ $book->unique_key ?? '-' }}
                                        </span>
                                    </td>

                                    <td style="min-width:250px;">
                                        <div class="book-title">
                                            {{ $book->title ?? 'No Title' }}
                                        </div>
                                        <div class="book-author">
                                            <i class="fa fa-user mr-1"></i>
                                            {{ $book->author ?? 'Unknown Author' }}
                                        </div>
                                    </td>

                                    <td>{{ $book->call_number ?? '-' }}</td>
                                    <td>{{ $book->sublocation ?? '-' }}</td>

                                    <td>
                                        {{ $book->publisher ? \Illuminate\Support\Str::limit($book->publisher, 30) : '-' }}
                                    </td>

                                    <td>{{ $book->year ?? '-' }}</td>

                                    <td>
                                        {{ $book->isbn ? \Illuminate\Support\Str::limit($book->isbn, 25) : '-' }}
                                    </td>

                                    <td>
                                        {{ $book->subjects ? \Illuminate\Support\Str::limit($book->subjects, 35) : '-' }}
                                    </td>

                                    <td>
                                        @if((bool) $book->is_reservable)
                                            <span class="badge badge-success badge-book">
                                                <i class="fa fa-check-circle mr-1"></i>Yes
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-book">
                                                <i class="fa fa-times-circle mr-1"></i>No
                                            </span>
                                        @endif
                                    </td>

                                    <td style="min-width:150px;">
                                        <button
                                            type="button"
                                            class="btn btn-info action-btn"
                                            title="View"
                                            data-toggle="modal"
                                            data-target="#viewBook{{ $book->id }}"
                                        >
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        @if(
                                            auth()->check() &&
                                            in_array(auth()->user()->access_level, ['admin', 'staff'], true)
                                        )
                                            <button
                                                type="button"
                                                class="btn btn-warning action-btn"
                                                title="Edit"
                                                data-toggle="modal"
                                                data-target="#editBook{{ $book->id }}"
                                            >
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            @if(auth()->user()->access_level === 'admin')
                                                <form
                                                    action="{{ route('books.destroy', $book->id) }}"
                                                    method="POST"
                                                    class="d-inline js-delete-form"
                                                    data-delete-title="Delete Book?"
                                                    data-delete-message="You are about to delete <strong>{{ $book->title ?? 'this book' }}</strong>."
                                                    data-delete-button="Delete Book"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger action-btn" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                {{-- VIEW BOOK MODAL --}}
                                <div class="modal fade" id="viewBook{{ $book->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-book mr-2"></i>
                                                    Book Information
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box">
                                                            <strong>Book Code</strong>
                                                            <p><span class="book-code">{{ $book->unique_key ?? '-' }}</span></p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box">
                                                            <strong>Reservation Availability</strong>
                                                            <p>
                                                                @if((bool) $book->is_reservable)
                                                                    <span class="badge badge-success badge-book">Reservable</span>
                                                                @else
                                                                    <span class="badge badge-secondary badge-book">Not Reservable</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box">
                                                            <strong>Title</strong>
                                                            <p style="font-size: 16px; font-weight: 700;">{{ $book->title ?? 'No Title' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box">
                                                            <strong>Author</strong>
                                                            <p>{{ $book->author ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <div class="info-box">
                                                            <strong>Call Number</strong>
                                                            <p>{{ $book->call_number ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <div class="info-box">
                                                            <strong>Sublocation</strong>
                                                            <p>{{ $book->sublocation ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="info-box">
                                                            <strong>Publisher</strong>
                                                            <p>{{ $book->publisher ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <div class="info-box">
                                                            <strong>Year</strong>
                                                            <p>{{ $book->year ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <div class="info-box">
                                                            <strong>Edition</strong>
                                                            <p>{{ $book->edition ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>Format</strong>
                                                            <p>{{ $book->format ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>Content Type</strong>
                                                            <p>{{ $book->content_type ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>Media Type</strong>
                                                            <p>{{ $book->media_type ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>Carrier Type</strong>
                                                            <p>{{ $book->carrier_type ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>ISBN</strong>
                                                            <p>{{ $book->isbn ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>ISSN</strong>
                                                            <p>{{ $book->issn ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <div class="info-box">
                                                            <strong>LCCN</strong>
                                                            <p>{{ $book->lccn ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <div class="info-box">
                                                            <strong>Subjects</strong>
                                                            <p>{{ $book->subjects ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="info-box">
                                                            <strong>Additional Details</strong>
                                                            <p>{!! nl2br(e($book->additional_details ?? '-')) !!}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- EDIT BOOK MODAL --}}
                                @if(
                                    auth()->check() &&
                                    in_array(auth()->user()->access_level, ['admin', 'staff'], true)
                                )
                                    <div class="modal fade edit-book-modal" id="editBook{{ $book->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('books.update', $book->id) }}"
                                                    method="POST"
                                                    class="edit-book-form"
                                                    data-book-id="{{ $book->id }}"
                                                >
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fa fa-pencil mr-2"></i>
                                                            Edit Book
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Title <span class="text-danger">*</span></label>
                                                                    <textarea name="title" class="form-control" required>{{ $book->title }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Author</label>
                                                                    <textarea name="author" class="form-control">{{ $book->author }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>Call Number</label>
                                                                    <input type="text" name="call_number" value="{{ $book->call_number }}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>Sublocation</label>
                                                                    <input type="text" name="sublocation" value="{{ $book->sublocation }}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>Year</label>
                                                                    <input type="text" name="year" value="{{ $book->year }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 mb-3 edit-book-rfid">
                                                                <div class="info-box">
                                                                    <label for="editBookRfid{{ $book->id }}">Book RFID (optional)</label>
                                                                    <input
                                                                        type="text"
                                                                        id="editBookRfid{{ $book->id }}"
                                                                        name="rfid_tag_uid"
                                                                        class="form-control rfid-input"
                                                                        value="{{ $book->copies->sortBy('id')->first()?->rfid_tag_uid }}"
                                                                        data-book-id="{{ $book->id }}"
                                                                        data-original-rfid="{{ $book->copies->sortBy('id')->first()?->rfid_tag_uid }}"
                                                                        maxlength="191"
                                                                        autocomplete="off"
                                                                        placeholder="Scan or enter the RFID tag"
                                                                    >
                                                                    <small class="form-text text-muted">
                                                                        Leave blank to remove the tag from the first copy.
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>Reservation Availability <span class="text-danger">*</span></label>
                                                                    <select name="is_reservable" class="form-control" required>
                                                                        <option value="1" {{ (bool) $book->is_reservable ? 'selected' : '' }}>Reservable</option>
                                                                        <option value="0" {{ !(bool) $book->is_reservable ? 'selected' : '' }}>Not Reservable</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Publisher</label>
                                                                    <textarea name="publisher" class="form-control">{{ $book->publisher }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Format</label>
                                                                    <textarea name="format" class="form-control">{{ $book->format }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 mb-3">
                                                                <div class="info-box">
                                                                    <label>Edition</label>
                                                                    <input type="text" name="edition" value="{{ $book->edition }}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="info-box">
                                                                    <label>Content Type</label>
                                                                    <input type="text" name="content_type" value="{{ $book->content_type }}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="info-box">
                                                                    <label>Media Type</label>
                                                                    <input type="text" name="media_type" value="{{ $book->media_type }}" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="info-box">
                                                                    <label>Carrier Type</label>
                                                                    <input type="text" name="carrier_type" value="{{ $book->carrier_type }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>ISBN</label>
                                                                    <textarea name="isbn" class="form-control">{{ $book->isbn }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>ISSN</label>
                                                                    <textarea name="issn" class="form-control">{{ $book->issn }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 mb-3">
                                                                <div class="info-box">
                                                                    <label>LCCN</label>
                                                                    <input type="text" name="lccn" value="{{ $book->lccn }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Subjects</label>
                                                                    <textarea name="subjects" rows="4" class="form-control">{{ $book->subjects }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 mb-3">
                                                                <div class="info-box">
                                                                    <label>Additional Details</label>
                                                                    <textarea name="additional_details" rows="4" class="form-control">{{ $book->additional_details }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-primary book-save-button">
                                                            <i class="fa fa-save"></i>
                                                            Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <i class="fa fa-book"></i>
                                        <h5>No books found</h5>
                                        <p class="text-muted mb-0">
                                            Add your first book to the library database.
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

{{-- ADD BOOK MODAL --}}
<div class="modal fade add-book-modal" id="addBookModal" tabindex="-1" role="dialog" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('books.store') }}" method="POST" id="addBookForm">
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="addBookModalLabel">
                            <i class="fa fa-book mr-2"></i>
                            Add New Book
                        </h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5 class="form-section-title">Basic Information</h5>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Book Title <span class="text-danger">*</span></label>
                                <textarea name="title" class="form-control" placeholder="Enter book title" required>{{ old('title') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Author</label>
                                <textarea name="author" class="form-control" placeholder="Enter author name">{{ old('author') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>Call Number</label>
                                <input type="text" name="call_number" value="{{ old('call_number') }}" class="form-control" placeholder="Example: QA76.73">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>Sublocation</label>
                                <input type="text" name="sublocation" value="{{ old('sublocation') }}" class="form-control" placeholder="Example: Filipiniana">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>Publication Year</label>
                                <input type="text" name="year" value="{{ old('year') }}" class="form-control" placeholder="Example: 2026">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3 add-book-rfid">
                            <div class="info-box">
                                <label for="bookRfid">Book RFID (optional)</label>
                                <input
                                    type="text"
                                    id="bookRfid"
                                    name="rfid_tag_uid"
                                    value="{{ old('rfid_tag_uid') }}"
                                    class="form-control rfid-input"
                                    maxlength="191"
                                    autocomplete="off"
                                    placeholder="Scan the book's RFID tag"
                                >
                                <small class="form-text text-muted">Leave blank if the copy has no tag yet.</small>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>Reservation Availability <span class="text-danger">*</span></label>
                                <select name="is_reservable" class="form-control" required>
                                    <option value="1" {{ old('is_reservable', '1') === '1' ? 'selected' : '' }}>Reservable</option>
                                    <option value="0" {{ old('is_reservable', '1') === '0' ? 'selected' : '' }}>Not Reservable</option>
                                </select>
                                <small class="form-text text-muted">
                                    Only reservable books appear on the reservation kiosk.
                                </small>
                            </div>
                        </div>
                    </div>

                    <h5 class="form-section-title mt-4">Publication Information</h5>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Publisher</label>
                                <textarea name="publisher" class="form-control" placeholder="Enter publisher">{{ old('publisher') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Format</label>
                                <textarea name="format" class="form-control" placeholder="Example: Print, Electronic Resource">{{ old('format') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="info-box">
                                <label>Edition</label>
                                <input type="text" name="edition" value="{{ old('edition') }}" class="form-control" placeholder="Example: 3rd Edition">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="info-box">
                                <label>Content Type</label>
                                <input type="text" name="content_type" value="{{ old('content_type') }}" class="form-control" placeholder="Example: Text">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="info-box">
                                <label>Media Type</label>
                                <input type="text" name="media_type" value="{{ old('media_type') }}" class="form-control" placeholder="Example: Unmediated">
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="info-box">
                                <label>Carrier Type</label>
                                <input type="text" name="carrier_type" value="{{ old('carrier_type') }}" class="form-control" placeholder="Example: Volume">
                            </div>
                        </div>
                    </div>

                    <h5 class="form-section-title mt-4">Book Identifiers</h5>
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>ISBN</label>
                                <textarea name="isbn" class="form-control" placeholder="Enter ISBN">{{ old('isbn') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>ISSN</label>
                                <textarea name="issn" class="form-control" placeholder="Enter ISSN">{{ old('issn') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="info-box">
                                <label>LCCN</label>
                                <input type="text" name="lccn" value="{{ old('lccn') }}" class="form-control" placeholder="Enter LCCN">
                            </div>
                        </div>
                    </div>

                    <h5 class="form-section-title mt-4">Subjects &amp; Additional Details</h5>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Subjects</label>
                                <textarea name="subjects" rows="4" class="form-control" placeholder="Example: Computer Science; Programming; Information Technology">{{ old('subjects') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="info-box">
                                <label>Additional Details</label>
                                <textarea name="additional_details" rows="4" class="form-control" placeholder="Enter other bibliographic details">{{ old('additional_details') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="reset" class="btn btn-light">
                        <i class="fa fa-refresh"></i>
                        Clear
                    </button>
                    <button type="submit" class="btn btn-primary book-save-button">
                        <i class="fa fa-save"></i>
                        Save Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSV IMPORT MODAL --}}
<div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="csvImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('books.import.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="csvImportModalLabel">
                            <i class="fa fa-file-excel-o mr-2"></i>
                            Import Books from CSV
                        </h5>
                        <small class="text-muted">Add several books from one CSV file. The RFID column is optional.</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="csv-help mb-3">
                        <strong>Import your books</strong>
                    </div>

                    <div class="form-group">
                        <label for="booksCsv">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" id="booksCsv" class="form-control-file" accept=".csv,text/csv" required>
                    </div>

                    <a href="{{ route('books.import.template') }}" class="btn btn-light">
                        <i class="fa fa-download"></i>
                        Download CSV Template
                    </a>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i>
                        Import Books
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CUSTOM DELETE CONFIRMATION MODAL --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-body text-center" style="padding: 32px 30px 24px;">
                <div style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 78px;
                    height: 78px;
                    margin: 0 auto 20px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #ffe3ec 0%, #ffd0de 100%);
                    color: var(--mgmt-danger);
                    font-size: 30px;
                    box-shadow: 0 10px 24px rgba(189, 52, 52, .18);
                ">
                    <i class="fa fa-trash-o"></i>
                </div>

                <h5 id="deleteConfirmTitle" style="color: var(--mgmt-text); font-size: 19px; font-weight: 800;">
                    Delete Book?
                </h5>

                <p id="deleteConfirmMessage" style="color: var(--mgmt-muted); font-size: 13.5px; line-height: 1.55; max-width: 320px; margin: 0 auto;">
                    This action cannot be undone.
                </p>

                <div class="csv-help mt-3" style="text-align: left;">
                    <i class="fa fa-exclamation-triangle mr-1"></i>
                    The book and its copies will be permanently removed from the system.
                </div>
            </div>

            <div class="modal-footer" style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary flex-fill" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-primary flex-fill" id="deleteConfirmButton" style="background: var(--mgmt-danger); border-color: var(--mgmt-danger);">
                    <i class="fa fa-trash"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
    /* ============================================================
       1. Prevent Enter key / RFID scanner auto-submitting
    ============================================================ */
    document.querySelectorAll('.book-save-button').forEach(button => {
        const form = button.closest('form');
        if (!form) return;

        form.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.keyCode === 13) {
                const target = event.target;
                if (target.matches('input') || target.matches('select')) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }
        }, true);

        form.addEventListener('submit', event => {
            if (event.submitter !== button) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }, true);
    });

    /* ============================================================
       2. RFID Enter guard
    ============================================================ */
    document.querySelectorAll('input.rfid-input').forEach(input => {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                e.stopPropagation();
                this.blur();
                setTimeout(() => this.focus(), 150);
                return false;
            }
        }, true);
    });

    /* ============================================================
       3. RFID uniqueness data
    ============================================================ */
    const existingRfids = @json(
        $books->flatMap(function ($book) {
            return $book->copies->pluck('rfid_tag_uid');
        })->filter()->values()
    );

    const rfidOwners = @json(
        $books->flatMap(function ($book) {
            return $book->copies
                ->filter(fn($c) => !empty($c->rfid_tag_uid))
                ->map(fn($c) => ['rfid' => $c->rfid_tag_uid, 'book_id' => $book->id]);
        })->values()
    );

    function checkRfidUniqueness(input) {
        const value = (input.value || '').trim();
        const currentBookId = input.dataset.bookId ? parseInt(input.dataset.bookId) : null;
        const originalRfid = (input.dataset.originalRfid || '').trim();

        const parent = input.parentElement;
        const existingError = parent.querySelector('.rfid-error');
        if (existingError) existingError.remove();
        input.classList.remove('is-invalid');

        if (!value) return true;

        if (currentBookId !== null && value === originalRfid) {
            return true;
        }

        const match = rfidOwners.find(entry =>
            entry.rfid && entry.rfid.toLowerCase() === value.toLowerCase()
        );

        if (match) {
            if (currentBookId !== null && match.book_id === currentBookId) {
                return true;
            }
            const error = document.createElement('small');
            error.className = 'text-danger rfid-error d-block mt-1';
            error.textContent = 'This RFID is already registered to another book.';
            parent.appendChild(error);
            input.classList.add('is-invalid');
            return false;
        }

        return true;
    }

    document.querySelectorAll('input.rfid-input').forEach(input => {
        input.addEventListener('blur', function () {
            checkRfidUniqueness(this);
        });
        input.addEventListener('input', function () {
            const err = this.parentElement.querySelector('.rfid-error');
            if (err) err.remove();
            this.classList.remove('is-invalid');
        });
    });

    /* ============================================================
       4. Block submit on duplicate RFID
    ============================================================ */
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const rfidInput = form.querySelector('input.rfid-input');
            if (rfidInput && rfidInput.value.trim()) {
                if (!checkRfidUniqueness(rfidInput)) {
                    e.preventDefault();
                    e.stopPropagation();
                    rfidInput.focus();
                    rfidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            }
        }, true);
    });

    /* ============================================================
       5. DataTable init & Search
    ============================================================ */
    $(document).ready(function () {
        var table = $('#booksTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            order: [],
            dom: 'lrtip', // Removed 'f' to hide default search box, we use our own
            columnDefs: [
                { orderable: false, targets: [0] }
            ],
            language: {
                emptyTable: 'No books found.',
                zeroRecords: 'No books match your search.',
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

        // Custom Search Input
        $('#bookSearchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    /* ============================================================
       6. Custom delete confirmation
    ============================================================ */
    let $pendingDeleteForm = null;

    $(document).on('click', '.js-delete-form button[type="submit"]', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');
        $pendingDeleteForm = $form;

        const title = $form.data('delete-title') || 'Delete Book?';
        const message = $form.data('delete-message') || 'This action cannot be undone.';
        const buttonLabel = $form.data('delete-button') || 'Delete';

        $('#deleteConfirmTitle').text(title);
        $('#deleteConfirmMessage').html(message);

        const $confirmBtn = $('#deleteConfirmButton');
        $confirmBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> ' + buttonLabel);

        $('#deleteConfirmModal').modal('show');
    });

    $('#deleteConfirmButton').on('click', function () {
        if (!$pendingDeleteForm) return;

        const $btn = $(this);
        $btn.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

        $pendingDeleteForm[0].submit();
    });

    $('#deleteConfirmModal').on('hidden.bs.modal', function () {
        $pendingDeleteForm = null;
    });
</script>