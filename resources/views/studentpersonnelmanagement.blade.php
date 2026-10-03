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
        content: "\f080";
    }

    .summary-card.summary-students .card-body::after {
        content: "\f19d";
    }
    .summary-card.summary-personnel .card-body::after {
        content: "\f007";
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }
    .summary-card.summary-total .card-body::after {
        content: "\f1c0";
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
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
       FILTER CARD (separated from management card)
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
       PILL TAB (All Students / All Personnel)
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
       ADD RECORD BUTTON (no dropdown)
       ═══════════════════════════════════════ */
    .add-record-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border: 0;
        border-radius: 11px;
        background: var(--mgmt-primary);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .add-record-btn:hover,
    .add-record-btn:focus {
        background: var(--mgmt-primary-dark);
        color: #fff;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
        transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    #studentManagementTable,
    #personnelManagementTable {
        width: 100% !important;
        margin-bottom: 0;
    }

    #studentManagementTable thead th,
    #personnelManagementTable thead th {
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

    #studentManagementTable tbody td,
    #personnelManagementTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    #studentManagementTable.table-hover tbody tr:hover td,
    #personnelManagementTable.table-hover tbody tr:hover td {
        background: #fff7fa;
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

    /* ═══════════════════════════════════════
       ACTION BUTTONS
       ═══════════════════════════════════════ */
    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 2px;
        min-height: 34px;
        padding: 6px 12px;
        border: 0;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .action-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(42, 35, 48, .12);
    }

    .btn-info.action-button {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
    }

    .btn-info.action-button:hover {
        background: var(--mgmt-info);
        color: #fff;
    }

    .btn-danger.action-button {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }

    .btn-danger.action-button:hover {
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

    .modal .form-group label {
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

    .form-control {
        width: 100%;
        min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--mgmt-text);
        font-size: 13px;
        font-weight: 400;
        height: auto;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    select.form-control,
    select.form-control option {
        font-weight: 400;
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

    .modal .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border: 0;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .modal .btn-secondary {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .modal .btn-secondary:hover {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
    }

    .modal .btn-primary {
        background: var(--mgmt-primary);
        color: #fff;
    }

    .modal .btn-primary:hover {
        background: var(--mgmt-primary-dark);
        color: #fff;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
        transform: translateY(-1px);
    }

    .modal .btn-success {
        background: var(--mgmt-success);
        color: #fff;
    }

    .modal .btn-success:hover {
        background: #1a6a37;
        color: #fff;
        box-shadow: 0 4px 12px rgba(33, 129, 67, .3);
        transform: translateY(-1px);
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
       CUSTOM DELETE CONFIRMATION MODAL
       ═══════════════════════════════════════ */
    #deleteConfirmModal .modal-dialog {
        max-width: 420px;
    }

    #deleteConfirmModal .modal-content {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(42, 35, 48, .22);
        animation: deleteModalIn .22s ease;
    }

    @keyframes deleteModalIn {
        from { opacity: 0; transform: translateY(-12px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    #deleteConfirmModal .delete-modal-body {
        padding: 32px 30px 24px;
        text-align: center;
        background: #fff;
    }

    #deleteConfirmModal .delete-icon-wrap {
        position: relative;
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
    }

    #deleteConfirmModal .delete-icon-wrap::before,
    #deleteConfirmModal .delete-icon-wrap::after {
        position: absolute;
        border-radius: 50%;
        content: "";
    }

    #deleteConfirmModal .delete-icon-wrap::before {
        inset: -8px;
        border: 1px dashed rgba(189, 52, 52, .25);
        animation: deletePulse 2.4s linear infinite;
    }

    #deleteConfirmModal .delete-icon-wrap::after {
        inset: -16px;
        border: 1px solid rgba(189, 52, 52, .12);
    }

    @keyframes deletePulse {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    #deleteConfirmModal .delete-title {
        margin-bottom: 8px;
        color: var(--mgmt-text);
        font-size: 19px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    #deleteConfirmModal .delete-message {
        margin: 0 auto;
        max-width: 320px;
        color: var(--mgmt-muted);
        font-size: 13.5px;
        line-height: 1.55;
    }

    #deleteConfirmModal .delete-message strong {
        color: var(--mgmt-text);
        font-weight: 700;
    }

    #deleteConfirmModal .delete-warning {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 18px;
        padding: 12px 14px;
        border: 1px solid #f5d6e1;
        border-radius: 12px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 12px;
        font-weight: 600;
        text-align: left;
        line-height: 1.5;
    }

    #deleteConfirmModal .delete-warning i {
        margin-top: 2px;
        color: var(--mgmt-primary);
        font-size: 13px;
    }

    #deleteConfirmModal .delete-modal-footer {
        display: flex;
        gap: 10px;
        padding: 18px 24px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    #deleteConfirmModal .delete-modal-footer .btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 45px;
        padding: 10px 18px;
        border: 0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    #deleteConfirmModal .btn-cancel {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    #deleteConfirmModal .btn-cancel:hover,
    #deleteConfirmModal .btn-cancel:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
        outline: none;
    }

    #deleteConfirmModal .btn-delete {
        background: var(--mgmt-danger);
        color: #fff;
        box-shadow: 0 6px 16px rgba(189, 52, 52, .28);
    }

    #deleteConfirmModal .btn-delete:hover,
    #deleteConfirmModal .btn-delete:focus {
        background: #a32c2c;
        color: #fff;
        box-shadow: 0 8px 20px rgba(189, 52, 52, .36);
        transform: translateY(-1px);
    }

    #deleteConfirmModal .btn-delete:disabled {
        cursor: not-allowed;
        opacity: .75;
        transform: none;
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
        font-weight: 400;
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

        .add-record-btn {
            width: 100%;
        }

        .action-button {
            min-width: 40px;
        }

        .summary-number {
            font-size: 26px;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    $activeTab = $activeTab ?? 'students';
    $isStudents = $activeTab === 'students';
@endphp

<div class="content-body mgmt-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        {{ $isStudents ? 'Student Management' : 'Personnel Management' }}
                    </h4>
                    <span>
                        {{ $isStudents
                            ? 'Manage registered students'
                            : 'Manage registered personnel' }}
                    </span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $isStudents ? 'Student Management' : 'Personnel Management' }}
                    </li>
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

        {{-- SUMMARY CARDS --}}
        <div class="row">
            @if($isStudents)
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="card summary-card summary-students">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <p class="summary-number">{{ $students->count() }}</p>
                            <span class="text-muted">Registered student records</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="card summary-card summary-personnel">
                        <div class="card-body">
                            <h5>Total Personnel</h5>
                            <p class="summary-number">{{ $personnel->count() }}</p>
                            <span class="text-muted">Registered personnel records</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-xl-4 col-lg-6 col-sm-6">
                <div class="card summary-card summary-total">
                    <div class="card-body">
                        <h5>Total Records</h5>
                        <p class="summary-number">
                            {{ $students->count() + $personnel->count() }}
                        </p>
                        <span class="text-muted">Students and personnel</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD (separated) --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-end">
                    @if($isStudents)
                        <div class="col-xl-4 col-lg-6 col-md-6 filter-column">
                            <label for="studentSearch" class="filter-label">
                                <i class="fa fa-search"></i>
                                Search
                            </label>
                            <div class="search-wrapper">
                                <i class="fa fa-search"></i>
                                <input
                                    type="text"
                                    id="studentSearch"
                                    class="form-control filter-control"
                                    placeholder="Search"
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-6 col-md-6 filter-column">
                            <label for="yearFilter" class="filter-label">
                                <i class="fa fa-calendar"></i>
                                Year Level
                            </label>
                            <select id="yearFilter" class="form-control filter-control">
                                <option value="">All Year Levels</option>
                                <option value="1st year">1st Year</option>
                                <option value="2nd year">2nd Year</option>
                                <option value="3rd year">3rd Year</option>
                                <option value="4th year">4th Year</option>
                            </select>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                            <label for="courseFilter" class="filter-label">
                                <i class="fa fa-graduation-cap"></i>
                                Course / Program
                            </label>
                            <select id="courseFilter" class="form-control filter-control">
                                <option value="">All Courses</option>
                                @foreach([
                                    'BSA', 'BSAIS', 'BSIT', 'BSIS', 'BSN', 'BSND',
                                    'BSPHARM', 'BACOMM', 'BAEL', 'BLIS', 'BM',
                                    'BSPSYCH', 'BSBA', 'BSHM', 'BSTM', 'BSSW',
                                    'BCAED', 'BECED', 'BEED', 'BTLED', 'BSED'
                                ] as $course)
                                    <option value="{{ strtolower($course) }}">{{ $course }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                            <label class="filter-label">&nbsp;</label>
                            <button
                                type="button"
                                id="clearStudentFilters"
                                class="btn clear-filter-button"
                                title="Clear filters"
                                aria-label="Clear filters"
                            >
                                <i class="fa fa-refresh"></i> Clear
                            </button>
                        </div>
                    @else
                        <div class="col-xl-5 col-lg-6 col-md-6 filter-column">
                            <label for="personnelSearch" class="filter-label">
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

                        <div class="col-xl-4 col-lg-6 col-md-6 filter-column">
                            <label for="departmentFilter" class="filter-label">
                                <i class="fa fa-building"></i>
                                Department
                            </label>
                            <select id="departmentFilter" class="form-control filter-control">
                                <option value="">All Departments</option>
                                @foreach([
                                    'Arts and Sciences Program',
                                    'Teacher Education Program',
                                    'Allied Health Program',
                                    'Business, Accountancy, and Information Technology Program',
                                    'Hospitality Management Program',
                                    'Social Work Program'
                                ] as $dept)
                                    <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                            <label class="filter-label">&nbsp;</label>
                            <button
                                type="button"
                                id="clearPersonnelFilters"
                                class="btn clear-filter-button"
                                title="Clear filters"
                                aria-label="Clear filters"
                            >
                                <i class="fa fa-refresh"></i> Clear
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MANAGEMENT CARD --}}
        <div class="card management-card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <h4 class="card-title mb-0">
                        <i class="fa {{ $isStudents ? 'fa-graduation-cap' : 'fa-users' }} mr-2"></i>
                        {{ $isStudents ? 'Student Records' : 'Personnel Records' }}
                    </h4>

                    {{-- PILL TAB: All Students / All Personnel --}}
                    <div class="pill-tab">
                        <i class="fa {{ $isStudents ? 'fa-users' : 'fa-user-circle' }}"></i>
                        <span class="pill-label">
                            {{ $isStudents ? 'All Students' : 'All Personnel' }}
                        </span>
                        <span class="pill-count">
                            {{ $isStudents ? $students->count() : $personnel->count() }}
                        </span>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn add-record-btn"
                    data-toggle="modal"
                    data-target="{{ $isStudents ? '#addStudentModal' : '#addPersonnelModal' }}"
                >
                    <i class="fa fa-plus"></i>
                    Add {{ $isStudents ? 'Student' : 'Personnel' }}
                </button>
            </div>

            <div class="card-body">
                {{-- TABLE --}}
                @if($isStudents)
                    <div class="table-responsive">
                        <table id="studentManagementTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Year Level</th>
                                    <th>Program</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($students->sortByDesc('id') as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong class="person-name">
                                            {{ $student->firstname }} {{ $student->lastname }}
                                        </strong>
                                        <span class="person-number">
                                            Student No: {{ $student->student_number }}
                                        </span>
                                    </td>
                                    <td>{{ $student->year_level ?? '-' }}</td>
                                    <td>{{ $student->course_program ?? '-' }}</td>
                                    <td>{{ $student->email ?? '-' }}</td>
                                    <td style="min-width:150px;">
                                        <button
                                            type="button"
                                            class="btn btn-info action-button"
                                            data-toggle="modal"
                                            data-target="#editStudentModal{{ $student->id }}"
                                        >
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                        @if(strtolower(session('session_access_level')) === 'admin')
                                            <form
                                                action="{{ route('management.students.destroy', $student) }}"
                                                method="POST"
                                                class="d-inline js-delete-form"
                                                data-delete-title="Delete Student?"
                                                data-delete-message="You are about to delete <strong>{{ $student->firstname }} {{ $student->lastname }}</strong> (Student No: {{ $student->student_number }})."
                                                data-delete-button="Delete Student"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger action-button">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <div
                                    class="modal fade"
                                    id="editStudentModal{{ $student->id }}"
                                    data-fingerprint-id="{{ $student->fingerprint_id }}"
                                    tabindex="-1"
                                >
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form
                                                action="{{ route('management.students.update', $student) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fa fa-pencil mr-2"></i>
                                                        Edit Student
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>First Name</label>
                                                                <input type="text" name="firstname" class="form-control" value="{{ $student->firstname }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Last Name</label>
                                                                <input type="text" name="lastname" class="form-control" value="{{ $student->lastname }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Student Number</label>
                                                                <input type="text" name="student_number" class="form-control" value="{{ $student->student_number }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Year Level</label>
                                                                <select name="year_level" class="form-control">
                                                                    <option value="">Select Year Level</option>
                                                                    @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year'] as $year)
                                                                        <option value="{{ $year }}" {{ $student->year_level === $year ? 'selected' : '' }}>
                                                                            {{ $year }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Course / Program</label>
                                                                <select name="course_program" class="form-control">
                                                                    <option value="">Select Course / Program</option>
                                                                    @foreach([
                                                                        'BSA', 'BSAIS', 'BSIT', 'BSIS', 'BSN', 'BSND',
                                                                        'BSPHARM', 'BACOMM', 'BAEL', 'BLIS', 'BM',
                                                                        'BSPSYCH', 'BSBA', 'BSHM', 'BSTM', 'BSSW',
                                                                        'BCAED', 'BECED', 'BEED', 'BTLED', 'BSED'
                                                                    ] as $course)
                                                                        <option value="{{ $course }}" {{ $student->course_program === $course ? 'selected' : '' }}>
                                                                            {{ $course }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>RFID UID</label>
                                                                <input type="text" name="rfid_tag_uid" class="form-control" value="{{ $student->rfid_tag_uid }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input
                                                                    type="email"
                                                                    name="email"
                                                                    class="form-control"
                                                                    value="{{ old('email', $student->email) }}"
                                                                    placeholder="example@lccdo.edu.ph"
                                                                    pattern="[A-Za-z0-9._%+\-]+@lccdo\.edu\.ph"
                                                                    title="Only @lccdo.edu.ph email addresses are allowed"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <small class="text-muted">
                                                                    Current fingerprint ID: {{ $student->fingerprint_id ?? 'Not enrolled' }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <x-fingerprint-form-input
                                                            input-id="editStudentFingerprintId{{ $student->id }}"
                                                            label="Student Fingerprint"
                                                            bridge-url="ws://127.0.0.1:8765"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i>
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="personnelManagementTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Personnel</th>
                                    <th>Department</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($personnel->sortByDesc('id') as $person)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong class="person-name">
                                            {{ $person->firstname }} {{ $person->lastname }}
                                        </strong>
                                        <span class="person-number">
                                            Employee No: {{ $person->employee_number }}
                                        </span>
                                    </td>
                                    <td>{{ $person->department ?? '-' }}</td>
                                    <td>{{ $person->email ?? '-' }}</td>
                                    <td style="min-width:150px;">
                                        <button
                                            type="button"
                                            class="btn btn-info action-button"
                                            data-toggle="modal"
                                            data-target="#editPersonnelModal{{ $person->id }}"
                                        >
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                        @if(strtolower(session('session_access_level')) === 'admin')
                                            <form
                                                action="{{ route('management.personnel.destroy', $person) }}"
                                                method="POST"
                                                class="d-inline js-delete-form"
                                                data-delete-title="Delete Personnel?"
                                                data-delete-message="You are about to delete <strong>{{ $person->firstname }} {{ $person->lastname }}</strong> (Employee No: {{ $person->employee_number }})."
                                                data-delete-button="Delete Personnel"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger action-button">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                <div
                                    class="modal fade"
                                    id="editPersonnelModal{{ $person->id }}"
                                    data-fingerprint-id="{{ $person->fingerprint_id }}"
                                    tabindex="-1"
                                >
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form
                                                action="{{ route('management.personnel.update', $person) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fa fa-pencil mr-2"></i>
                                                        Edit Personnel
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>First Name</label>
                                                                <input type="text" name="firstname" class="form-control" value="{{ $person->firstname }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Last Name</label>
                                                                <input type="text" name="lastname" class="form-control" value="{{ $person->lastname }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Employee Number</label>
                                                                <input type="text" name="employee_number" class="form-control" value="{{ $person->employee_number }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Department</label>
                                                                <select name="department" class="form-control">
                                                                    <option value="">Select Department</option>
                                                                    @foreach([
                                                                        'Arts and Sciences Program',
                                                                        'Teacher Education Program',
                                                                        'Allied Health Program',
                                                                        'Business, Accountancy, and Information Technology Program',
                                                                        'Hospitality Management Program',
                                                                        'Social Work Program'
                                                                    ] as $department)
                                                                        <option value="{{ $department }}" {{ $person->department === $department ? 'selected' : '' }}>
                                                                            {{ $department }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>RFID UID</label>
                                                                <input type="text" name="rfid_tag_uid" class="form-control" value="{{ $person->rfid_tag_uid }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input
                                                                    type="email"
                                                                    name="email"
                                                                    class="form-control"
                                                                    value="{{ old('email', $person->email) }}"
                                                                    placeholder="example@lccdo.edu.ph"
                                                                    pattern="[A-Za-z0-9._%+\-]+@lccdo\.edu\.ph"
                                                                    title="Only @lccdo.edu.ph email addresses are allowed"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <small class="text-muted">
                                                                    Current fingerprint ID: {{ $person->fingerprint_id ?? 'Not enrolled' }}
                                                                </small>
                                                            </div>
                                                        </div>

                                                        <x-fingerprint-form-input
                                                            input-id="editPersonnelFingerprintId{{ $person->id }}"
                                                            label="Personnel Fingerprint"
                                                            bridge-url="ws://127.0.0.1:8765"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-save"></i>
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ADD STUDENT MODAL --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                id="addStudentForm"
                action="{{ route('management.students.store') }}"
                method="POST"
            >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-graduation-cap mr-2"></i>
                        Add New Student
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Student Number</label>
                                <input type="text" name="student_number" class="form-control" value="{{ old('student_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Year Level</label>
                                <select name="year_level" class="form-control">
                                    <option value="">Select Year Level</option>
                                    <option value="1st Year" {{ old('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd Year" {{ old('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd Year" {{ old('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th Year" {{ old('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Course / Program</label>
                                <select name="course_program" class="form-control">
                                    <option value="">Select Course / Program</option>
                                    @foreach([
                                        'BSA', 'BSAIS', 'BSIT', 'BSIS', 'BSN', 'BSND',
                                        'BSPHARM', 'BACOMM', 'BAEL', 'BLIS', 'BM',
                                        'BSPSYCH', 'BSBA', 'BSHM', 'BSTM', 'BSSW',
                                        'BCAED', 'BECED', 'BEED', 'BTLED', 'BSED'
                                    ] as $course)
                                        <option value="{{ $course }}" {{ old('course_program') === $course ? 'selected' : '' }}>
                                            {{ $course }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RFID UID</label>
                                <input type="text" name="rfid_tag_uid" class="form-control" value="{{ old('rfid_tag_uid') }}" placeholder="Scan or enter RFID">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="example@lccdo.edu.ph"
                                    pattern="[A-Za-z0-9._%+\-]+@lccdo\.edu\.ph"
                                    title="Only @lccdo.edu.ph email addresses are allowed"
                                >
                            </div>
                        </div>

                        <x-fingerprint-form-input
                            input-id="studentFingerprintId"
                            label="Student Fingerprint"
                            bridge-url="ws://127.0.0.1:8765"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="confirmAddStudent" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ADD PERSONNEL MODAL --}}
<div class="modal fade" id="addPersonnelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form
                id="addPersonnelForm"
                action="{{ route('management.personnel.store') }}"
                method="POST"
            >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-user mr-2"></i>
                        Add New Personnel
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee Number</label>
                                <input type="text" name="employee_number" class="form-control" value="{{ old('employee_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach([
                                        'Arts and Sciences Program',
                                        'Teacher Education Program',
                                        'Allied Health Program',
                                        'Business, Accountancy, and Information Technology Program',
                                        'Hospitality Management Program',
                                        'Social Work Program'
                                    ] as $department)
                                        <option value="{{ $department }}" {{ old('department') === $department ? 'selected' : '' }}>
                                            {{ $department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RFID UID</label>
                                <input type="text" name="rfid_tag_uid" class="form-control" value="{{ old('rfid_tag_uid') }}" placeholder="Scan or enter RFID">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="example@lccdo.edu.ph"
                                    pattern="[A-Za-z0-9._%+\-]+@lccdo\.edu\.ph"
                                    title="Only @lccdo.edu.ph email addresses are allowed"
                                >
                            </div>
                        </div>

                        <x-fingerprint-form-input
                            input-id="personnelFingerprintId"
                            label="Personnel Fingerprint"
                            bridge-url="ws://127.0.0.1:8765"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="confirmAddPersonnel" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        Add Personnel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CUSTOM DELETE CONFIRMATION MODAL --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="delete-modal-body">
                <div class="delete-icon-wrap">
                    <i class="fa fa-trash-o"></i>
                </div>

                <h5 class="delete-title" id="deleteConfirmTitle">Delete Record?</h5>

                <p class="delete-message" id="deleteConfirmMessage">
                    This action cannot be undone.
                </p>

                <div class="delete-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>
                        The record will be permanently removed from the system along with any associated data.
                    </span>
                </div>
            </div>

            <div class="delete-modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    Cancel
                </button>

                <button type="button" class="btn btn-delete" id="deleteConfirmButton">
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
$(document).ready(function () {
    const isStudents = @json($isStudents);

    /* ============================================================
       DATATABLES INITIALISATION
    ============================================================ */
    const studentTable = $('#studentManagementTable').length
        ? $('#studentManagementTable').DataTable({
            pageLength: 10,
            order: [],
            dom: 'lrtip',
            language: {
                emptyTable: 'No student records found.',
                zeroRecords: 'No students match your search.',
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
        })
        : null;

    const personnelTable = $('#personnelManagementTable').length
        ? $('#personnelManagementTable').DataTable({
            pageLength: 10,
            order: [],
            dom: 'lrtip',
            language: {
                emptyTable: 'No personnel records found.',
                zeroRecords: 'No personnel match your search.',
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
        })
        : null;

    /* ============================================================
       SHARED SEARCH (top filter card drives the DataTable)
    ============================================================ */
    if (isStudents) {
        $('#studentSearch').on('input', function () {
            if (studentTable) studentTable.search($(this).val()).draw();
        });
    } else {
        $('#personnelSearch').on('input', function () {
            if (personnelTable) personnelTable.search($(this).val()).draw();
        });
    }

    /* ============================================================
       STUDENT CUSTOM FILTER (Year Level + Course / Program)
    ============================================================ */
    $.fn.dataTable.ext.search.push(function (settings, searchData, index) {
        if (settings.nTable.id !== 'studentManagementTable') return true;

        const rowNode = settings.aoData[index] ? settings.aoData[index].nTr : null;
        if (!rowNode) return true;

        const $row = $(rowNode);

        const selectedYear = ($('#yearFilter').val() || '').trim().toLowerCase();
        const selectedCourse = ($('#courseFilter').val() || '').trim().toLowerCase();

        const rowYear = ($row.find('td').eq(2).text() || '').trim().toLowerCase();
        const rowCourse = ($row.find('td').eq(3).text() || '').trim().toLowerCase();

        if (selectedYear && rowYear !== selectedYear) return false;
        if (selectedCourse && rowCourse !== selectedCourse) return false;

        return true;
    });

    $('#yearFilter, #courseFilter').on('change', function () {
        if (studentTable) studentTable.draw();
    });

    /* ============================================================
       PERSONNEL CUSTOM FILTER (Department)
    ============================================================ */
    $.fn.dataTable.ext.search.push(function (settings, searchData, index) {
        if (settings.nTable.id !== 'personnelManagementTable') return true;

        const rowNode = settings.aoData[index] ? settings.aoData[index].nTr : null;
        if (!rowNode) return true;

        const $row = $(rowNode);
        const selectedDept = $('#departmentFilter').val();
        const rowDept = ($row.find('td').eq(2).text() || '').trim().toLowerCase();

        return selectedDept === '' || rowDept === selectedDept;
    });

    $('#departmentFilter').on('change', function () {
        if (personnelTable) personnelTable.draw();
    });

    /* ============================================================
       CLEAR FILTERS
    ============================================================ */
    $('#clearStudentFilters').on('click', function () {
        $('#studentSearch').val('');
        $('#yearFilter').val('');
        $('#courseFilter').val('');
        if (studentTable) studentTable.search('').draw();
    });

    $('#clearPersonnelFilters').on('click', function () {
        $('#personnelSearch').val('');
        $('#departmentFilter').val('');
        if (personnelTable) personnelTable.search('').draw();
    });

    /* ============================================================
       EDIT MODALS – FINGERPRINT PRE-FILL & ENTER KEY PROTECTION
    ============================================================ */
    document.querySelectorAll('[id^="editStudentModal"], [id^="editPersonnelModal"]').forEach(modal => {
        const input = modal.querySelector('input[name="fingerprint_id"]');
        if (!input) return;
        const currentId = modal.dataset.fingerprintId || '';
        $(modal).on('show.bs.modal', function () {
            input.value = currentId;
        });
        modal.querySelector('form').addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
            }
        });
    });

    /* ============================================================
       RFID UID CHANGE DETECTION
    ============================================================ */
    document.querySelectorAll('[id^="editStudentModal"], [id^="editPersonnelModal"]').forEach(modal => {
        const rfidInput = modal.querySelector('input[name="rfid_tag_uid"]');
        if (!rfidInput) return;

        $(modal).on('show.bs.modal', function () {
            rfidInput.dataset.originalRfid = rfidInput.value || '';
            rfidInput.style.borderColor = '';
            rfidInput.style.boxShadow = '';
        });

        rfidInput.addEventListener('blur', function () {
            if (rfidInput.value.trim() === '') {
                rfidInput.value = rfidInput.dataset.originalRfid || '';
                rfidInput.style.borderColor = '';
                rfidInput.style.boxShadow = '';
                return;
            }
            if (rfidInput.value !== rfidInput.dataset.originalRfid) {
                rfidInput.style.borderColor = '#f59e0b';
                rfidInput.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.2)';
            } else {
                rfidInput.style.borderColor = '';
                rfidInput.style.boxShadow = '';
            }
        });
    });

    /* ============================================================
       RFID FORM PROTECTION FOR ADD MODALS
    ============================================================ */
    function protectRfidAddForm(formSelector, buttonSelector) {
        const form = document.querySelector(formSelector);
        const button = document.querySelector(buttonSelector);
        if (!form || !button) return;

        form.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        button.addEventListener('click', function () {
            const fingerprintInput = form.querySelector('input[name="fingerprint_id"]');
            if (fingerprintInput && !fingerprintInput.value) {
                const fingerprintBox = fingerprintInput.closest('.form-group');
                const status = fingerprintBox
                    ? fingerprintBox.querySelector('.fingerprint-status')
                    : null;
                if (status) {
                    status.innerHTML = '<div class="alert alert-warning mb-0 py-2">Please enroll the fingerprint before adding this record.</div>';
                }
                return;
            }
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            form.dataset.explicitSubmit = '1';
            form.requestSubmit();
        });

        form.addEventListener('submit', function (event) {
            if (form.dataset.explicitSubmit !== '1') {
                event.preventDefault();
                return;
            }
            delete form.dataset.explicitSubmit;
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';
        });
    }
    protectRfidAddForm('#addStudentForm', '#confirmAddStudent');
    protectRfidAddForm('#addPersonnelForm', '#confirmAddPersonnel');

    /* ============================================================
       RESET ADD MODALS WHEN CLOSED
    ============================================================ */
    $('#addStudentModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('addStudentForm');
        if (!form) return;

        form.reset();

        const fingerprintInput = form.querySelector('input[name="fingerprint_id"]');
        if (fingerprintInput) fingerprintInput.value = '';

        const fingerprintStatus = form.querySelector('.fingerprint-status');
        if (fingerprintStatus) fingerprintStatus.innerHTML = '';

        const submitBtn = document.getElementById('confirmAddStudent');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-save mr-1"></i> Add Student';
        }

        delete form.dataset.explicitSubmit;
    });

    $('#addPersonnelModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('addPersonnelForm');
        if (!form) return;

        form.reset();

        const fingerprintInput = form.querySelector('input[name="fingerprint_id"]');
        if (fingerprintInput) fingerprintInput.value = '';

        const fingerprintStatus = form.querySelector('.fingerprint-status');
        if (fingerprintStatus) fingerprintStatus.innerHTML = '';

        const submitBtn = document.getElementById('confirmAddPersonnel');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-save mr-1"></i> Add Personnel';
        }

        delete form.dataset.explicitSubmit;
    });

    /* ============================================================
       CUSTOM DELETE CONFIRMATION
    ============================================================ */
    let $pendingDeleteForm = null;

    $(document).on('click', '.js-delete-form button[type="submit"]', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');
        $pendingDeleteForm = $form;

        const title = $form.data('delete-title') || 'Delete Record?';
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
});
</script>