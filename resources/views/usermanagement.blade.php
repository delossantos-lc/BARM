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
        content: "\f080";
    }

    .summary-card.summary-staff .card-body::after {
        content: "\f0c0";
    }
    .summary-card.summary-active .card-body::after {
        content: "\f00c";
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }
    .summary-card.summary-inactive .card-body::after {
        content: "\f00d";
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }
    .summary-card.summary-admin .card-body::after {
        content: "\f132";
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
       PILL TAB (All Users)
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

    #staffTable {
        width: 100% !important;
        margin-bottom: 0;
    }

    #staffTable thead th {
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

    #staffTable tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    #staffTable.table-hover tbody tr:hover td {
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

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--mgmt-primary) 0%, var(--mgmt-primary-dark) 100%);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .5px;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(213, 91, 145, .22);
    }

    .badge-employee {
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

    .um-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-role-admin {
        background: var(--mgmt-warning-soft);
        color: var(--mgmt-warning);
    }

    .badge-role-staff {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
    }

    .badge-status-active {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
    }

    .badge-status-inactive {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
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

    .btn-edit.action-button {
        background: var(--mgmt-info-soft);
        color: var(--mgmt-info);
    }

    .btn-edit.action-button:hover {
        background: var(--mgmt-info);
        color: #fff;
    }

    .btn-delete.action-button {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
    }

    .btn-delete.action-button:hover {
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

    .modal .form-group label,
    .modal label {
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
       EMPTY STATE
       ═══════════════════════════════════════ */
    .empty-users {
        padding: 55px 20px !important;
        text-align: center;
    }

    .empty-users i {
        color: #ccc;
        font-size: 45px;
    }

    .empty-users h5 {
        margin-top: 16px;
        color: var(--mgmt-text);
        font-weight: 700;
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
                    <h4>User Management</h4>
                    <span>Manage staff and administrator accounts</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">User Management</li>
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
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-staff">
                    <div class="card-body">
                        <h5>Total Staff</h5>
                        <p class="summary-number">{{ $totalStaff ?? 0 }}</p>
                        <span class="text-muted">Registered staff accounts</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-active">
                    <div class="card-body">
                        <h5>Active Staff</h5>
                        <p class="summary-number">{{ $activeStaff ?? 0 }}</p>
                        <span class="text-muted">Active staff accounts</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-inactive">
                    <div class="card-body">
                        <h5>Inactive Staff</h5>
                        <p class="summary-number">{{ $inactiveStaff ?? 0 }}</p>
                        <span class="text-muted">Inactive staff accounts</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card summary-card summary-admin">
                    <div class="card-body">
                        <h5>Administrators</h5>
                        <p class="summary-number">{{ $totalAdmins ?? 0 }}</p>
                        <span class="text-muted">Administrator accounts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="card filter-card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-xl-6 col-lg-6 col-md-6 filter-column">
                        <label for="staffSearch" class="filter-label">
                            <i class="fa fa-search"></i>
                            Search
                        </label>

                        <div class="search-wrapper">
                            <i class="fa fa-search"></i>
                            <input
                                type="text"
                                id="staffSearch"
                                class="form-control filter-control"
                                placeholder="Search"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 filter-column">
                        <label for="accessLevelFilter" class="filter-label">
                            <i class="fa fa-shield"></i>
                            Access Level
                        </label>

                        <select id="accessLevelFilter" class="form-control filter-control">
                            <option value="">All Access Levels</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 filter-column">
                        <label class="filter-label">&nbsp;</label>
                        <button
                            type="button"
                            id="clearStaffFilters"
                            class="btn clear-filter-button"
                            title="Clear filters"
                            aria-label="Clear filters"
                        >
                            <i class="fa fa-refresh"></i> Clear
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
                        Staff Accounts
                    </h4>

                    {{-- PILL TAB: All Users --}}
                    <div class="pill-tab">
                        <i class="fa fa-users"></i>
                        <span class="pill-label">All Users</span>
                        <span class="pill-count">{{ count($users) }}</span>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn add-record-btn"
                    data-toggle="modal"
                    data-target="#addStaffModal"
                >
                    <i class="fa fa-plus"></i>
                    Add User
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="staffTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Employee ID</th>
                                <th>Email</th>
                                <th>Access Level</th>
                                <th>Status</th>
                                <th width="140">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $user)
                                <tr data-access-level="{{ strtolower($user->access_level) }}">
                                    <td>{{ $loop->iteration }}</td>

                                    {{-- USER --}}
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->firstname, 0, 1)) }}{{ strtoupper(substr($user->lastname, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="person-name">
                                                    {{ $user->firstname }} {{ $user->lastname }}
                                                </strong>
                                                <span class="person-number">
                                                    {{ ucfirst($user->access_level) }} Account
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- EMPLOYEE ID --}}
                                    <td>
                                        <span class="badge-employee">
                                            {{ $user->employeeid }}
                                        </span>
                                    </td>

                                    {{-- EMAIL --}}
                                    <td>{{ $user->email }}</td>

                                    {{-- ACCESS LEVEL --}}
                                    <td>
                                        @if($user->access_level === 'admin')
                                            <span class="um-badge badge-role-admin">
                                                <i class="fa fa-shield mr-1"></i> Admin
                                            </span>
                                        @else
                                            <span class="um-badge badge-role-staff">
                                                <i class="fa fa-user mr-1"></i> Staff
                                            </span>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="um-badge badge-status-active">
                                                <i class="fa fa-check-circle mr-1"></i> Active
                                            </span>
                                        @else
                                            <span class="um-badge badge-status-inactive">
                                                <i class="fa fa-times-circle mr-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td style="min-width:150px;">
                                        <button
                                            type="button"
                                            class="btn btn-edit action-button"
                                            data-toggle="modal"
                                            data-target="#editStaff{{ $user->id }}"
                                        >
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                            <form
                                                action="{{ route('staff.destroy', $user->id) }}"
                                                method="POST"
                                                class="d-inline js-delete-form"
                                                data-delete-title="Delete User?"
                                                data-delete-message="You are about to delete <strong>{{ $user->firstname }} {{ $user->lastname }}</strong> ({{ $user->employeeid }})."
                                                data-delete-button="Delete User"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-delete action-button"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                {{-- EDIT USER MODAL --}}
                                <div class="modal fade" id="editStaff{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="{{ route('staff.update', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fa fa-pencil mr-2"></i>
                                                        Edit User
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label>First Name</label>
                                                            <input type="text" name="firstname" value="{{ $user->firstname }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Last Name</label>
                                                            <input type="text" name="lastname" value="{{ $user->lastname }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Employee ID</label>
                                                            <input type="text" name="employeeid" value="{{ $user->employeeid }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Email</label>
                                                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Access Level</label>
                                                            <select name="access_level" class="form-control" required>
                                                                <option value="staff" {{ $user->access_level === 'staff' ? 'selected' : '' }}>Staff</option>
                                                                <option value="admin" {{ $user->access_level === 'admin' ? 'selected' : '' }}>Admin</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>New Password</label>
                                                            <input
                                                                type="password"
                                                                name="password"
                                                                class="form-control"
                                                                placeholder="Leave blank to keep current password"
                                                            >
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label>Confirm New Password</label>
                                                            <input
                                                                type="password"
                                                                name="password_confirmation"
                                                                class="form-control"
                                                                placeholder="Confirm new password"
                                                            >
                                                        </div>
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

                            @empty
                                <tr>
                                    <td colspan="7" class="empty-users">
                                        <i class="fa fa-users"></i>
                                        <h5>No users found</h5>
                                        <p class="text-muted mb-0">
                                            Add a staff account to get started.
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

{{-- ADD STAFF MODAL --}}
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-user-plus mr-2"></i>
                        Add Staff Account
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" value="{{ old('firstname') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" value="{{ old('lastname') }}" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Employee ID <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="employeeid"
                                value="{{ old('employeeid') }}"
                                class="form-control"
                                placeholder="Example: EMP-001"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control"
                                placeholder="staff@example.com"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Access Level</label>
                            <select name="access_level" class="form-control" required>
                                <option value="staff" {{ old('access_level', 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('access_level') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Add Staff
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

                <h5 class="delete-title" id="deleteConfirmTitle">Delete User?</h5>

                <p class="delete-message" id="deleteConfirmMessage">
                    This action cannot be undone.
                </p>

                <div class="delete-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>
                        The account will be permanently removed from the system. The user will lose access immediately.
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

    /* ============================================================
       DATATABLE INIT
    ============================================================ */
    const staffTable = $('#staffTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [],
        dom: 'lrtip',
        language: {
            emptyTable: 'No user accounts found.',
            zeroRecords: 'No users match your search.',
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

    /* ============================================================
       LIVE SEARCH (single filter card input drives the DataTable)
    ============================================================ */
    $('#staffSearch').on('input', function () {
        const value = $(this).val();
        staffTable.search(value).page('first').draw();
    });

    /* ============================================================
       ACCESS LEVEL CUSTOM FILTER
    ============================================================ */
    $.fn.dataTable.ext.search.push(function (settings, searchData, index) {
        if (settings.nTable.id !== 'staffTable') return true;

        const rowNode = settings.aoData[index] ? settings.aoData[index].nTr : null;
        if (!rowNode) return true;

        const $row = $(rowNode);
        const selectedAccess = ($('#accessLevelFilter').val() || '').trim().toLowerCase();
        const rowAccess = ($row.attr('data-access-level') || '').trim().toLowerCase();

        return selectedAccess === '' || rowAccess === selectedAccess;
    });

    $('#accessLevelFilter').on('change', function () {
        staffTable.page('first').draw();
    });

    /* ============================================================
       CLEAR FILTERS
    ============================================================ */
    $('#clearStaffFilters').on('click', function () {
        $('#staffSearch').val('');
        $('#accessLevelFilter').val('');
        staffTable.search('').page('first').draw();
    });

    /* ============================================================
       CUSTOM DELETE CONFIRMATION
    ============================================================ */
    let $pendingDeleteForm = null;

    $(document).on('click', '.js-delete-form button[type="submit"]', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');
        $pendingDeleteForm = $form;

        const title = $form.data('delete-title') || 'Delete User?';
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

        // Native submit bypasses jQuery handlers.
        $pendingDeleteForm[0].submit();
    });

    $('#deleteConfirmModal').on('hidden.bs.modal', function () {
        $pendingDeleteForm = null;
    });

    /* ============================================================
       REOPEN ADD STAFF MODAL ON VALIDATION ERROR
    ============================================================ */
    @if($errors->any() && old('firstname') !== null)
        $('#addStaffModal').modal('show');
    @endif
});
</script>