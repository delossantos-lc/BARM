@include('layouts.header')
@include('layouts.css')

@php
    $systemSettings = $systemSettings ?? \App\Models\SystemSetting::current();
    $displayTimezone = $systemSettings->timezone ?: 'Asia/Manila';
    $displayDateFormat = $systemSettings->date_format ?: 'F j, Y';
    $displayTimeFormat = $systemSettings->time_format ?: 'h:i:s A';
@endphp

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
    .mgmt-page .welcome-text h4 { margin-bottom: 5px; font-size: 22px; font-weight: 700; letter-spacing: -.35px; }
    .mgmt-page .welcome-text span { color: var(--mgmt-muted); font-size: 13px; }
    .mgmt-page .breadcrumb { margin: 0; padding: 9px 13px; border-radius: 10px; background: rgba(255,255,255,.78); }
    .mgmt-page .breadcrumb-item a { color: var(--mgmt-primary); font-weight: 600; }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .form-card, .management-card {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        background: var(--mgmt-surface);
        box-shadow: var(--mgmt-shadow);
    }
    .form-card { margin-bottom: 24px; }
    .form-card .card-body { padding: 22px 24px; }
    .form-card h4 { color: var(--mgmt-text); font-size: 17px; font-weight: 700; }
    .form-card h4 i { color: var(--mgmt-primary); }

    .management-card .card-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 15px; flex-wrap: wrap;
        min-height: 78px; padding: 18px 22px;
        border-bottom: 1px solid var(--mgmt-border);
        background: #fff;
    }
    .management-card .card-title { color: var(--mgmt-text); font-size: 17px; font-weight: 700; }
    .management-card .card-title i { color: var(--mgmt-primary); }
    .management-card .card-body { padding: 22px; }

    /* ═══════════════════════════════════════
       FILTER BAR
       ═══════════════════════════════════════ */
    .filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .filter-group.search-group {
        flex: 2;
        min-width: 200px;
    }

    .filter-group label {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #57515e;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .25px;
        text-transform: uppercase;
        margin-bottom: 0;
    }

    .filter-group label i {
        color: var(--mgmt-primary);
        font-size: 11px;
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

    input[type="date"].filter-control {
        position: relative;
    }
    input[type="date"].filter-control::-webkit-calendar-picker-indicator {
        color: var(--mgmt-muted);
        cursor: pointer;
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

    .clear-filter-btn {
        width: 100%;
        min-height: 44px;
        border-radius: 11px;
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
        font-weight: 700;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s ease;
    }

    .clear-filter-btn:hover {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td, .table th { vertical-align: middle !important; }
    #reservationTable { width: 100% !important; margin-bottom: 0; }
    #reservationTable thead th {
        padding: 15px 14px;
        border: 0; border-bottom: 1px solid var(--mgmt-border);
        background: #f8f7fa; color: #625c68;
        font-size: 11px; font-weight: 800;
        letter-spacing: .3px; text-transform: uppercase; white-space: nowrap;
    }
    #reservationTable tbody td { padding: 14px; border-top: 1px solid #f0edf1; background: #fff; }
    #reservationTable.table-hover tbody tr:hover td { background: #fff7fa; }

    #reservationTable tbody tr.today-row td { background: var(--mgmt-warning-soft) !important; }
    #reservationTable.table-hover tbody tr.today-row:hover td { background: #ffeec7 !important; }
    #reservationTable tbody tr.expired-row td { background: var(--mgmt-danger-soft) !important; }
    #reservationTable.table-hover tbody tr.expired-row:hover td { background: #ffe2e2 !important; }

    #reservationTable tbody tr.today-row td:first-child,
    #reservationTable tbody tr.expired-row td:first-child { position: relative; }

    #reservationTable tbody tr.today-row td:first-child::before,
    #reservationTable tbody tr.expired-row td:first-child::before {
        position: absolute; top: 50%; left: 4px;
        width: 3px; height: 24px;
        border-radius: 3px;
        transform: translateY(-50%);
        content: "";
        animation: statusPulse 1.6s infinite ease-in-out;
    }
    #reservationTable tbody tr.today-row td:first-child::before { background: var(--mgmt-warning); }
    #reservationTable tbody tr.expired-row td:first-child::before { background: var(--mgmt-danger); }
    @keyframes statusPulse {
        0%, 100% { opacity: .55; }
        50%      { opacity: 1; }
    }

    /* ═══════════════════════════════════════
       TABLE CELLS
       ═══════════════════════════════════════ */
    .reservation-title { color: var(--mgmt-text); font-weight: 700; font-size: 13px; }
    .student-id { display: block; margin-top: 3px; color: var(--mgmt-muted); font-size: 12px; }
    .book-cell-title { color: var(--mgmt-text); font-weight: 700; font-size: 13px; }
    .book-cell-author { display: block; color: var(--mgmt-muted); font-size: 12px; margin-top: 2px; }
    .book-cell-callno {
        display: block; color: var(--mgmt-muted);
        font-size: 11px; margin-top: 2px;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    }

    /* ═══════════════════════════════════════
       STATUS BADGES
       ═══════════════════════════════════════ */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
        white-space: nowrap; letter-spacing: .2px;
    }
    .badge-warning.status-badge { background: var(--mgmt-warning-soft); color: var(--mgmt-warning); }
    .badge-info.status-badge { background: var(--mgmt-info-soft); color: var(--mgmt-info); }
    .badge-success.status-badge { background: var(--mgmt-success-soft); color: var(--mgmt-success); }
    .badge-secondary.status-badge { background: #f1f1f1; color: #555; }
    .badge-danger.status-badge { background: var(--mgmt-danger-soft); color: var(--mgmt-danger); }

    /* Borrowing limit indicator */
    .limit-indicator {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 6px; padding: 4px 10px;
        border-radius: 20px;
        font-size: 10.5px; font-weight: 800;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .limit-indicator.limit-ok {
        background: var(--mgmt-success-soft);
        color: var(--mgmt-success);
        border: 1px solid #c9e9d4;
    }
    .limit-indicator.limit-full {
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
        border: 1px solid #f3c9c9;
        animation: limitPulse 1.8s infinite ease-in-out;
    }
    @keyframes limitPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(189,52,52,.25); }
        50%      { box-shadow: 0 0 0 4px rgba(189,52,52,0); }
    }

    /* ═══════════════════════════════════════
       ACTION BUTTONS
       ═══════════════════════════════════════ */
    .action-button {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        margin: 2px; min-height: 34px; padding: 6px 12px;
        border: 0; border-radius: 9px;
        font-size: 12px; font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .action-button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(42,35,48,.12); }
    .btn-info.action-button { background: var(--mgmt-info-soft); color: var(--mgmt-info); }
    .btn-info.action-button:hover { background: var(--mgmt-info); color: #fff; }
    .btn-success.action-button { background: var(--mgmt-success-soft); color: var(--mgmt-success); }
    .btn-success.action-button:hover { background: var(--mgmt-success); color: #fff; }
    .btn-success.action-button.pickup-blocked {
        background: #f4f4f7;
        color: #9690a0;
        border: 1px dashed #d8d2dc;
    }
    .btn-success.action-button.pickup-blocked:hover {
        background: #fde8e8;
        color: #bd3434;
        border-color: #f3c9c9;
    }
    .btn-danger.action-button { background: var(--mgmt-danger-soft); color: var(--mgmt-danger); }
    .btn-danger.action-button:hover { background: var(--mgmt-danger); color: #fff; }

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
    .modal-title { color: var(--mgmt-text); font-size: 16px; font-weight: 700; }
    .modal-title i { color: var(--mgmt-primary); }
    .modal-body { padding: 22px; background: #fff; }
    .modal-footer {
        padding: 16px 22px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
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
        width: 100%; min-height: 44px;
        padding: 10px 14px;
        border: 1px solid #dfdbe2; border-radius: 11px;
        background-color: #fff; color: var(--mgmt-text);
        font-size: 13px; height: auto;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .form-control:hover { border-color: #c9c2ce; }
    .form-control:focus {
        border-color: var(--mgmt-primary);
        background-color: var(--mgmt-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213,91,145,.12);
        outline: none;
    }
    textarea.form-control { min-height: 100px; resize: vertical; }

    .modal .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 43px; padding: 10px 18px;
        border: 0; border-radius: 11px;
        font-size: 13px; font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .modal .btn-secondary {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }
    .modal .btn-secondary:hover, .modal .btn-secondary:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
    }
    .modal .btn-danger {
        background: var(--mgmt-danger);
        color: #fff;
        box-shadow: 0 6px 16px rgba(189,52,52,.28);
    }
    .modal .btn-danger:hover, .modal .btn-danger:focus {
        background: #a32c2c;
        color: #fff;
        box-shadow: 0 8px 20px rgba(189,52,52,.36);
        transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════
       CANCEL MODAL (Custom UI)
       ═══════════════════════════════════════ */
    .cancel-modal .modal-dialog { max-width: 520px; }
    .cancel-modal .modal-content {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(42,35,48,.22);
        animation: cancelModalIn .22s ease;
    }
    @keyframes cancelModalIn {
        from { opacity: 0; transform: translateY(-12px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .cancel-modal .modal-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fff8fb 0%, #fff2f7 100%);
    }
    .cancel-modal .cancel-header-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--mgmt-danger-soft);
        color: var(--mgmt-danger);
        font-size: 18px;
        flex-shrink: 0;
    }
    .cancel-modal .cancel-header-text { flex: 1; }
    .cancel-modal .modal-title {
        color: var(--mgmt-text);
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -.3px;
        margin: 0 0 3px;
    }
    .cancel-modal .cancel-subtitle {
        color: var(--mgmt-muted);
        font-size: 12px;
        font-weight: 500;
    }
    .cancel-modal .close { opacity: .5; transition: opacity .2s ease; }
    .cancel-modal .close:hover { opacity: 1; }
    .cancel-modal .modal-body { padding: 22px 24px 8px; background: #fff; }

    .cancel-info-card {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 16px 18px;
        margin-bottom: 20px;
        border: 1px solid var(--mgmt-border);
        border-radius: 14px;
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
    .cancel-info-row { display: flex; align-items: flex-start; gap: 12px; }
    .cancel-info-row i {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 8px;
        background: var(--mgmt-primary-soft); color: var(--mgmt-primary);
        font-size: 12px; flex-shrink: 0; margin-top: 2px;
    }
    .cancel-info-label {
        display: block; margin-bottom: 2px;
        color: var(--mgmt-muted); font-size: 10px; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
    }
    .cancel-info-value { color: var(--mgmt-text); font-size: 13.5px; font-weight: 600; line-height: 1.4; }
    .cancel-info-value.muted { color: var(--mgmt-muted); font-weight: 500; font-size: 12.5px; }

    .cancel-modal .form-label {
        display: flex; align-items: center; gap: 6px;
        margin-bottom: 8px; color: #57515e;
        font-size: 11px; font-weight: 700;
        letter-spacing: .25px; text-transform: uppercase;
    }
    .cancel-modal .form-label i { color: var(--mgmt-primary); font-size: 11px; }
    .cancel-modal .form-label .optional {
        margin-left: auto; color: var(--mgmt-muted);
        font-size: 10px; font-weight: 600; letter-spacing: .3px;
    }
    .cancel-modal textarea.form-control {
        min-height: 110px; padding: 12px 14px;
        font-size: 13px; font-family: inherit;
        line-height: 1.5; resize: vertical;
    }
    .cancel-warning {
        display: flex; align-items: flex-start; gap: 10px;
        margin-top: 14px; padding: 12px 14px;
        border: 1px solid #f5d6e1; border-radius: 12px;
        background: var(--mgmt-primary-softer);
        color: var(--mgmt-primary-dark);
        font-size: 12px; font-weight: 600; line-height: 1.5;
    }
    .cancel-warning i { margin-top: 2px; color: var(--mgmt-primary); font-size: 13px; flex-shrink: 0; }
    .cancel-modal .modal-footer {
        display: flex; gap: 10px;
        padding: 18px 24px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
    .cancel-modal .modal-footer .btn {
        flex: 1;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 46px; padding: 10px 18px;
        border: 0; border-radius: 12px;
        font-size: 13.5px; font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .cancel-modal .btn-close-modal {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }
    .cancel-modal .btn-close-modal:hover,
    .cancel-modal .btn-close-modal:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
        outline: none;
    }
    .cancel-modal .btn-confirm-cancel {
        background: var(--mgmt-danger);
        color: #fff;
        box-shadow: 0 6px 16px rgba(189,52,52,.28);
    }
    .cancel-modal .btn-confirm-cancel:hover,
    .cancel-modal .btn-confirm-cancel:focus {
        background: #a32c2c;
        color: #fff;
        box-shadow: 0 8px 20px rgba(189,52,52,.36);
        transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════
       PICKUP CONFIRMATION MODAL
       ═══════════════════════════════════════ */
    #pickupConfirmModal .modal-dialog { max-width: 440px; }
    #pickupConfirmModal .modal-content,
    #pickupBlockedModal .modal-content {
        overflow: hidden;
        border: 1px solid var(--mgmt-border);
        border-radius: 18px;
        box-shadow: 0 25px 60px rgba(42,35,48,.22);
        animation: cancelModalIn .22s ease;
    }
    #pickupConfirmModal .pickup-modal-body,
    #pickupBlockedModal .pickup-modal-body {
        padding: 32px 30px 24px;
        text-align: center;
        background: #fff;
    }
    #pickupConfirmModal .pickup-icon-wrap,
    #pickupBlockedModal .pickup-icon-wrap {
        position: relative;
        display: inline-flex; align-items: center; justify-content: center;
        width: 78px; height: 78px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #dcf7e4 0%, #c7f0d2 100%);
        color: var(--mgmt-success);
        font-size: 30px;
        box-shadow: 0 10px 24px rgba(33,129,67,.18);
    }
    #pickupConfirmModal .pickup-icon-wrap::before,
    #pickupConfirmModal .pickup-icon-wrap::after,
    #pickupBlockedModal .pickup-icon-wrap::before,
    #pickupBlockedModal .pickup-icon-wrap::after {
        position: absolute; border-radius: 50%; content: "";
    }
    #pickupConfirmModal .pickup-icon-wrap::before,
    #pickupBlockedModal .pickup-icon-wrap::before {
        inset: -8px;
        border: 1px dashed rgba(33,129,67,.3);
        animation: pickupPulse 2.4s linear infinite;
    }
    #pickupBlockedModal .pickup-icon-wrap::before {
        border-color: rgba(189,52,52,.3);
    }
    #pickupConfirmModal .pickup-icon-wrap::after,
    #pickupBlockedModal .pickup-icon-wrap::after {
        inset: -16px;
        border: 1px solid rgba(33,129,67,.14);
    }
    #pickupBlockedModal .pickup-icon-wrap::after {
        border-color: rgba(189,52,52,.14);
    }
    @keyframes pickupPulse {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    #pickupConfirmModal .pickup-title,
    #pickupBlockedModal .pickup-title {
        margin-bottom: 8px;
        color: var(--mgmt-text);
        font-size: 19px; font-weight: 800;
        letter-spacing: -.3px;
    }
    #pickupConfirmModal .pickup-message,
    #pickupBlockedModal .pickup-message {
        margin: 0 auto; max-width: 340px;
        color: var(--mgmt-muted); font-size: 13.5px;
        line-height: 1.55;
    }
    #pickupConfirmModal .pickup-message strong,
    #pickupBlockedModal .pickup-message strong { color: var(--mgmt-text); font-weight: 700; }
    #pickupConfirmModal .pickup-details,
    #pickupBlockedModal .pickup-details {
        display: flex; flex-direction: column; gap: 10px;
        margin-top: 18px; padding: 14px 16px;
        border: 1px solid #d4ecd9; border-radius: 12px;
        background: var(--mgmt-success-soft);
        text-align: left;
    }
    #pickupBlockedModal .pickup-details {
        border-color: #f3c9c9;
        background: var(--mgmt-danger-soft);
    }
    #pickupConfirmModal .pickup-detail-row,
    #pickupBlockedModal .pickup-detail-row {
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 12px; line-height: 1.5;
    }
    #pickupConfirmModal .pickup-detail-row i,
    #pickupBlockedModal .pickup-detail-row i {
        margin-top: 2px;
        color: var(--mgmt-success);
        font-size: 13px;
        flex-shrink: 0;
    }
    #pickupBlockedModal .pickup-detail-row i { color: var(--mgmt-danger); }
    #pickupConfirmModal .pickup-detail-label,
    #pickupBlockedModal .pickup-detail-label {
        display: block;
        color: var(--mgmt-muted);
        font-size: 10px; font-weight: 700;
        letter-spacing: .4px; text-transform: uppercase;
    }
    #pickupConfirmModal .pickup-detail-value,
    #pickupBlockedModal .pickup-detail-value {
        color: var(--mgmt-text);
        font-weight: 600; font-size: 12.5px;
    }
    #pickupConfirmModal .pickup-modal-footer,
    #pickupBlockedModal .pickup-modal-footer {
        display: flex; gap: 10px;
        padding: 18px 24px;
        border-top: 1px solid var(--mgmt-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }
    #pickupConfirmModal .pickup-modal-footer .btn,
    #pickupBlockedModal .pickup-modal-footer .btn {
        flex: 1;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        min-height: 45px; padding: 10px 18px;
        border: 0; border-radius: 12px;
        font-size: 13px; font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }
    #pickupConfirmModal .btn-cancel,
    #pickupBlockedModal .btn-cancel {
        border: 1px solid #e5dfe8;
        background: #fff; color: #68616e;
    }
    #pickupConfirmModal .btn-cancel:hover,
    #pickupConfirmModal .btn-cancel:focus,
    #pickupBlockedModal .btn-cancel:hover,
    #pickupBlockedModal .btn-cancel:focus {
        border-color: #f3cbd7;
        background: var(--mgmt-primary-soft);
        color: var(--mgmt-primary-dark);
        transform: translateY(-1px);
        outline: none;
    }
    #pickupConfirmModal .btn-confirm {
        background: var(--mgmt-success);
        color: #fff;
        box-shadow: 0 6px 16px rgba(33,129,67,.28);
    }
    #pickupConfirmModal .btn-confirm:hover,
    #pickupConfirmModal .btn-confirm:focus {
        background: #1a6a37;
        color: #fff;
        box-shadow: 0 8px 20px rgba(33,129,67,.36);
        transform: translateY(-1px);
    }
    #pickupConfirmModal .btn-confirm:disabled {
        cursor: not-allowed;
        opacity: .75;
        transform: none;
    }

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
    #reservationTable tbody td[colspan="8"] {
        padding: 55px 20px !important;
        text-align: center;
    }
    #reservationTable tbody td[colspan="8"] i { color: #ccc; font-size: 45px; }
    #reservationTable tbody td[colspan="8"] h5 {
        margin-top: 16px;
        color: var(--mgmt-text);
        font-weight: 700;
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .mgmt-page .page-titles { padding: 18px; }
        .management-card .card-header { align-items: stretch; flex-direction: column; }
        .management-card .card-body { padding: 16px; }
        
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-group { width: 100%; }
        .action-button { min-width: 40px; }

        .cancel-modal .modal-body,
        .cancel-modal .modal-header,
        .cancel-modal .modal-footer { padding-left: 18px; padding-right: 18px; }
        .cancel-modal .modal-footer { flex-direction: column-reverse; }
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
                    <h4>Reservation Monitoring</h4>
                    <span>Monitor book reservations and scheduled pickups</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Reservations</li>
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

        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle mr-2"></i>
                {{ Session::get('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- FILTER BAR --}}
        <div class="card form-card">
            <div class="card-body">
                <div class="filter-bar">
                    
                    {{-- Search --}}
                    <div class="filter-group search-group">
                        <label>
                            <i class="fa fa-search"></i>
                            Search
                        </label>
                        <div class="search-wrapper">
                            <i class="fa fa-search"></i>
                            <input 
                                type="text" 
                                id="reservationSearch" 
                                class="filter-control" 
                                placeholder="Search user name, ID, or book..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="filter-group">
                        <label>
                            <i class="fa fa-filter"></i>
                            Status
                        </label>
                        <select id="statusFilter" class="filter-control">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="ready">Ready</option>
                            <option value="picked_up">Picked Up</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    {{-- Date Filter --}}
                    <div class="filter-group">
                        <label>
                            <i class="fa fa-calendar"></i>
                            Date
                        </label>
                        <input 
                            type="date" 
                            id="dateFilter" 
                            class="filter-control"
                        >
                    </div>

                    {{-- Clear Button --}}
                    <div class="filter-group" style="flex: 0 0 auto; min-width: 120px;">
                        <label>&nbsp;</label>
                        <button type="button" id="clearFilters" class="clear-filter-btn">
                            <i class="fa fa-refresh"></i>
                            Clear
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- RESERVATION TABLE --}}
        <div class="card management-card">
            <div class="card-header">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fa fa-calendar mr-2"></i>
                        Reserved Books
                    </h4>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="reservationTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Book</th>
                                <th>Reserve Date</th>
                                <th>Borrow Date</th>
                                <th>Pickup Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($reservations as $reservation)
                            @php
                                $isToday = $reservation->borrow_date->isToday();
                                $isExpired = $reservation->status === 'expired';
                                $limitReached = (bool) ($reservation->borrowing_limit_reached ?? false);
                                $pickupBlocked = $limitReached
                                    && in_array($reservation->status, ['pending', 'ready'], true);
                            @endphp

                            <tr class="{{ $isToday ? 'today-row' : '' }} {{ $isExpired ? 'expired-row' : '' }}">
                                <td class="row-index"></td>

                                <td>
                                    <strong class="reservation-title">
                                        {{ $reservation->student_name }}
                                    </strong>
                                    <span class="student-id">
                                        ID: {{ $reservation->student_id }}
                                    </span>
                                </td>

                                <td style="min-width:250px;">
                                    <span class="book-cell-title">
                                        {{ $reservation->book->title ?? 'Unknown Book' }}
                                    </span>
                                    <span class="book-cell-author">
                                        {{ $reservation->book->author ?? 'Unknown Author' }}
                                    </span>
                                    <span class="book-cell-callno">
                                        Call Number: {{ $reservation->book->call_number ?? '-' }}
                                    </span>
                                </td>

                                <td data-order="{{ $reservation->created_at->timestamp }}">
                                    {{ $reservation->created_at->copy()->timezone($displayTimezone)->format($displayDateFormat) }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $reservation->created_at->copy()->timezone($displayTimezone)->format($displayTimeFormat) }}
                                    </small>
                                </td>

                                <td>
                                    @if($isToday)
                                        <strong class="text-primary">
                                            {{ $reservation->borrow_date->format($displayDateFormat) }}
                                        </strong>
                                        <br>
                                        <span class="badge badge-primary status-badge">TODAY</span>
                                    @else
                                        {{ $reservation->borrow_date->format($displayDateFormat) }}
                                    @endif
                                </td>

                                <td>
                                    <i class="fa fa-clock-o mr-1"></i>
                                    @if(!empty($reservation->pickup_time))
                                        {{
                                            \Carbon\Carbon::parse(
                                                $reservation->pickup_time,
                                                $displayTimezone
                                            )->format($displayTimeFormat)
                                        }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($reservation->status === 'pending')
                                        <span class="badge badge-warning status-badge">Pending</span>
                                    @elseif($reservation->status === 'ready')
                                        <span class="badge badge-info status-badge">Ready</span>
                                    @elseif($reservation->status === 'picked_up')
                                        <span class="badge badge-success status-badge">Picked Up</span>
                                    @elseif($reservation->status === 'cancelled')
                                        <span class="badge badge-secondary status-badge">Cancelled</span>
                                    @elseif($reservation->status === 'expired')
                                        <span class="badge badge-danger status-badge">Expired</span>
                                    @endif

                                    @if(in_array($reservation->status, ['pending', 'ready'], true))
                                        <br>
                                        @if($limitReached)
                                            <span class="limit-indicator limit-full"
                                                  title="{{ $reservation->pickup_block_reason }}">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                LIMIT REACHED
                                                ({{ $reservation->active_borrows }}/{{ $reservation->borrowing_limit }})
                                            </span>
                                        @else
                                            <span class="limit-indicator limit-ok">
                                                {{ $reservation->active_borrows }}/{{ $reservation->borrowing_limit }}
                                                borrowed
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <td style="min-width:220px;">
                                    @if($reservation->status === 'pending')
                                        <form
                                            action="{{ route('reserve.ready', $reservation->id) }}"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-info action-button">
                                                <i class="fa fa-check"></i>
                                                Ready
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($reservation->status, ['pending', 'ready']))
                                        <form
                                            action="{{ route('reserve.pickup', $reservation->id) }}"
                                            method="POST"
                                            class="d-inline js-pickup-form"
                                            data-pickup-student="{{ $reservation->student_name }}"
                                            data-pickup-id="{{ $reservation->student_id }}"
                                            data-pickup-book="{{ $reservation->book->title ?? 'Unknown Book' }}"
                                            data-pickup-blocked="{{ $pickupBlocked ? '1' : '0' }}"
                                            data-pickup-blocked-message="{{ $reservation->pickup_block_reason }}"
                                            data-pickup-active-borrows="{{ $reservation->active_borrows }}"
                                            data-pickup-limit="{{ $reservation->borrowing_limit }}"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <button
                                                type="submit"
                                                class="btn btn-success action-button {{ $pickupBlocked ? 'pickup-blocked' : '' }}"
                                                title="{{ $pickupBlocked ? 'Borrowing limit reached — book must be returned first' : 'Mark as picked up' }}"
                                            >
                                                <i class="fa {{ $pickupBlocked ? 'fa-lock' : 'fa-book' }}"></i>
                                                Picked Up
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="btn btn-danger action-button"
                                            data-toggle="modal"
                                            data-target="#cancelModal{{ $reservation->id }}"
                                        >
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </button>
                                    @else
                                        <span class="text-muted">No actions</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- CANCEL MODAL (Redesigned) --}}
                            <div class="modal fade cancel-modal" id="cancelModal{{ $reservation->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form
                                            action="{{ route('reserve.cancel', $reservation->id) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PUT')

                                            {{-- HEADER --}}
                                            <div class="modal-header">
                                                <div class="cancel-header-icon">
                                                    <i class="fa fa-times-circle"></i>
                                                </div>
                                                <div class="cancel-header-text">
                                                    <h5 class="modal-title">Cancel Reservation</h5>
                                                    <span class="cancel-subtitle">
                                                        This will mark the reservation as cancelled
                                                    </span>
                                                </div>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body">
                                                {{-- Reservation Context --}}
                                                <div class="cancel-info-card">
                                                    <div class="cancel-info-row">
                                                        <i class="fa fa-user"></i>
                                                        <div>
                                                            <span class="cancel-info-label">User</span>
                                                            <span class="cancel-info-value">
                                                                {{ $reservation->student_name }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="cancel-info-row">
                                                        <i class="fa fa-id-card"></i>
                                                        <div>
                                                            <span class="cancel-info-label">User ID</span>
                                                            <span class="cancel-info-value">
                                                                {{ $reservation->student_id }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="cancel-info-row">
                                                        <i class="fa fa-book"></i>
                                                        <div>
                                                            <span class="cancel-info-label">Book</span>
                                                            <span class="cancel-info-value">
                                                                {{ $reservation->book->title ?? 'Unknown Book' }}
                                                            </span>
                                                            <span class="cancel-info-value muted">
                                                                {{ $reservation->book->author ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Remarks --}}
                                                <label for="remarks{{ $reservation->id }}" class="form-label">
                                                    <i class="fa fa-commenting-o"></i>
                                                    Remarks
                                                    <span class="optional">Optional</span>
                                                </label>
                                                <textarea
                                                    id="remarks{{ $reservation->id }}"
                                                    name="remarks"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Please provide a reason for cancelling this reservation..."
                                                ></textarea>

                                                {{-- Warning --}}
                                                <div class="cancel-warning">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                    <span>
                                                        Cancelling this reservation cannot be undone. The user will need to reserve the book again.
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- FOOTER --}}
                                            <div class="modal-footer">
                                                <button
                                                    type="button"
                                                    class="btn btn-close-modal"
                                                    data-dismiss="modal"
                                                >
                                                    <i class="fa fa-times"></i>
                                                    Close
                                                </button>

                                                <button type="submit" class="btn btn-confirm-cancel">
                                                    <i class="fa fa-times-circle"></i>
                                                    Cancel Reservation
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fa fa-calendar"></i>
                                    <h5>No reservations found</h5>
                                    <p class="text-muted mb-0">
                                        User reservations will appear here.
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

{{-- PICKUP CONFIRMATION MODAL --}}
<div class="modal fade" id="pickupConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="pickup-modal-body">
                <div class="pickup-icon-wrap">
                    <i class="fa fa-book"></i>
                </div>

                <h5 class="pickup-title">Confirm Book Pickup?</h5>

                <p class="pickup-message">
                    You are about to mark this reservation as
                    <strong>Picked Up</strong>. This action will move
                    the book into the active borrowing records.
                </p>

                <div class="pickup-details">
                    <div class="pickup-detail-row">
                        <i class="fa fa-user"></i>
                        <div>
                            <span class="pickup-detail-label">User</span>
                            <span class="pickup-detail-value" id="pickupStudentName">—</span>
                        </div>
                    </div>

                    <div class="pickup-detail-row">
                        <i class="fa fa-id-card"></i>
                        <div>
                            <span class="pickup-detail-label">User ID</span>
                            <span class="pickup-detail-value" id="pickupStudentId">—</span>
                        </div>
                    </div>

                    <div class="pickup-detail-row">
                        <i class="fa fa-book"></i>
                        <div>
                            <span class="pickup-detail-label">Book</span>
                            <span class="pickup-detail-value" id="pickupBookTitle">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pickup-modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    Cancel
                </button>

                <button type="button" class="btn btn-confirm" id="pickupConfirmButton">
                    <i class="fa fa-check"></i>
                    Yes, Confirm Pickup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- BORROWING LIMIT REACHED MODAL --}}
<div class="modal fade" id="pickupBlockedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="pickup-modal-body">
                <div class="pickup-icon-wrap"
                     style="background: linear-gradient(135deg,#fde8e8 0%,#fbd5d5 100%);
                            color:#bd3434;
                            box-shadow:0 10px 24px rgba(189,52,52,.18);">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>

                <h5 class="pickup-title">Borrowing Limit Reached</h5>

                <p class="pickup-message" id="pickupBlockedMessage">
                    This borrower has reached the borrowing limit.
                </p>

                <div class="pickup-details">
                    <div class="pickup-detail-row">
                        <i class="fa fa-user"></i>
                        <div>
                            <span class="pickup-detail-label">User</span>
                            <span class="pickup-detail-value" id="pickupBlockedStudentName">—</span>
                        </div>
                    </div>

                    <div class="pickup-detail-row">
                        <i class="fa fa-id-card"></i>
                        <div>
                            <span class="pickup-detail-label">User ID</span>
                            <span class="pickup-detail-value" id="pickupBlockedStudentId">—</span>
                        </div>
                    </div>

                    <div class="pickup-detail-row">
                        <i class="fa fa-book"></i>
                        <div>
                            <span class="pickup-detail-label">Book</span>
                            <span class="pickup-detail-value" id="pickupBlockedBookTitle">—</span>
                        </div>
                    </div>

                    <div class="pickup-detail-row">
                        <i class="fa fa-balance-scale"></i>
                        <div>
                            <span class="pickup-detail-label">Active Borrows</span>
                            <span class="pickup-detail-value" id="pickupBlockedBorrowCount">—</span>
                        </div>
                    </div>
                </div>

                <div class="pickup-detail-row"
                     style="margin-top:14px;
                            padding:12px 14px;
                            background:#fff6e5;
                            border:1px solid #f0d9a8;
                            border-radius:10px;
                            color:#8a6d1a;
                            font-size:12px;
                            font-weight:600;
                            text-align:left;">
                    <i class="fa fa-info-circle" style="color:#b57b16;"></i>
                    <span>
                        The borrower must return at least one borrowed book before this
                        reservation can be marked as <strong>Picked Up</strong>.
                    </span>
                </div>
            </div>

            <div class="pickup-modal-footer">
                <button type="button" class="btn btn-cancel" data-dismiss="modal">
                    <i class="fa fa-check"></i> Got it
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
       DATATABLE INIT & CUSTOM SORTING
       - Prioritize Pending (3), then Actionable (2), then No Actions (1)
       - Secondary sort by Date (Newest to Oldest)
    ============================================================ */
    const reservationTable = $('#reservationTable').DataTable({
        pageLength: 10,
        order: [], // We will set custom order below
        dom: 'lrtip',
        columnDefs: [
            { 
                orderable: true, 
                targets: [3], // Make date sortable
                type: 'num'   // Use numeric timestamp for sorting
            },
            { 
                orderable: true, 
                targets: [6], // Make status sortable for our custom priority
                type: 'dom-priority' 
            },
            { orderable: false, targets: [0, 7] } // Disable sorting for # and Action
        ],
        language: {
            emptyTable: 'No reservations found.',
            zeroRecords: 'No reservations match your search.',
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

            // Renumber rows on current page
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = startIndex + i + 1;
            });
        }
    });

    // Define custom sorting priority
    $.fn.dataTable.ext.order['dom-priority'] = function (settings, col) {
        return this.api().column(col, { order: 'index' }).nodes().map(function (td, i) {
            const statusText = $('<div>').html($(td).text()).text().trim().toLowerCase();
            
            // Priority 3: Pending (Highest)
            if (statusText.includes('pending')) return 3;
            
            // Priority 2: Ready or Picked Up (Has Actions)
            if (statusText.includes('ready') || statusText.includes('picked up')) return 2;
            
            // Priority 1: Cancelled, Expired, or No Actions (Lowest)
            return 1;
        });
    };

    // Apply the custom sort: Priority (desc) -> Date (desc)
    reservationTable.order([[6, 'desc'], [3, 'desc']]).draw();

    /* ============================================================
       LIVE SEARCH
    ============================================================ */
    $('#reservationSearch').on('input', function () {
        const value = $(this).val();
        reservationTable.search(value).page('first').draw();
    });

    /* ============================================================
       STATUS FILTER
    ============================================================ */
    $('#statusFilter').on('change', function () {
        const value = $(this).val();
        
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'reservationTable') {
                    return true;
                }

                if (!value) {
                    return true;
                }

                const statusCell = data[6] || '';
                const statusText = $('<div>').html(statusCell).text().trim().toLowerCase();
                
                const statusMap = {
                    'pending': 'pending',
                    'ready': 'ready',
                    'picked_up': 'picked up',
                    'cancelled': 'cancelled',
                    'expired': 'expired'
                };

                const targetText = statusMap[value] || value;
                return statusText.includes(targetText);
            }
        );

        reservationTable.draw();
        
        setTimeout(() => {
            $.fn.dataTable.ext.search.pop();
        }, 100);
    });

    /* ============================================================
       DATE FILTER
    ============================================================ */
    $('#dateFilter').on('change', function () {
        const value = $(this).val(); // Format: YYYY-MM-DD
        
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'reservationTable') {
                    return true;
                }

                if (!value) {
                    return true;
                }

                // The date is in column 3 (index 3)
                const dateCell = data[3] || '';
                const dateText = $('<div>').html(dateCell).text().trim();
                
                // Format the date from the input (YYYY-MM-DD) to match the display format
                const filterDate = new Date(value);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                const formattedFilterDate = filterDate.toLocaleDateString('en-US', options);

                return dateText.includes(formattedFilterDate);
            }
        );

        reservationTable.draw();
        
        setTimeout(() => {
            $.fn.dataTable.ext.search.pop();
        }, 100);
    });

    /* ============================================================
       CLEAR FILTERS
    ============================================================ */
    $('#clearFilters').on('click', function () {
        $('#reservationSearch').val('');
        $('#statusFilter').val('');
        $('#dateFilter').val('');
        
        // Clear all custom filters
        $.fn.dataTable.ext.search.pop(); 
        $.fn.dataTable.ext.search.pop(); 
        
        // Clear search and redraw
        reservationTable.search('').draw();
        
        // Reset sorting to default (Pending priority, then Date)
        reservationTable.order([[6, 'desc'], [3, 'desc']]).draw();
    });

    /* ============================================================
       CUSTOM PICKUP CONFIRMATION + BORROWING LIMIT GUARD
    ============================================================ */
    let $pendingPickupForm = null;

    $(document).on('click', '.js-pickup-form button[type="submit"]', function (e) {
        e.preventDefault();

        const $form = $(this).closest('form');

        if (String($form.data('pickup-blocked')) === '1') {
            const activeBorrows = $form.data('pickup-active-borrows') || '—';
            const limit         = $form.data('pickup-limit') || '—';

            $('#pickupBlockedStudentName').text($form.data('pickup-student') || '—');
            $('#pickupBlockedStudentId').text($form.data('pickup-id') || '—');
            $('#pickupBlockedBookTitle').text($form.data('pickup-book') || '—');
            $('#pickupBlockedBorrowCount').text(activeBorrows + ' / ' + limit);

            $('#pickupBlockedMessage').text(
                $form.data('pickup-blocked-message')
                || 'This borrower has reached the borrowing limit. A book must be returned first.'
            );

            $('#pickupBlockedModal').modal('show');
            return;
        }

        $pendingPickupForm = $form;

        $('#pickupStudentName').text($form.data('pickup-student') || '—');
        $('#pickupStudentId').text($form.data('pickup-id') || '—');
        $('#pickupBookTitle').text($form.data('pickup-book') || '—');

        const $confirmBtn = $('#pickupConfirmButton');
        $confirmBtn.prop('disabled', false)
            .html('<i class="fa fa-check"></i> Yes, Confirm Pickup');

        $('#pickupConfirmModal').modal('show');
    });

    $('#pickupConfirmButton').on('click', function () {
        if (!$pendingPickupForm) return;

        const $btn = $(this);
        $btn.prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $pendingPickupForm[0].submit();
    });

    $('#pickupConfirmModal').on('hidden.bs.modal', function () {
        $pendingPickupForm = null;
    });

    $('#pickupBlockedModal').on('hidden.bs.modal', function () {
        $('#pickupBlockedStudentName, #pickupBlockedStudentId, #pickupBlockedBookTitle, #pickupBlockedBorrowCount')
            .text('—');
    });

    /* ============================================================
       SERVER-SIDE ERROR → AUTO-OPEN BLOCKED MODAL
    ============================================================ */
    @if(Session::has('error'))
        (function () {
            var serverError = @json(Session::get('error'));
            if (serverError && serverError.toLowerCase().indexOf('borrowing limit') !== -1) {
                $('#pickupBlockedMessage').text(serverError);
                $('#pickupBlockedStudentName, #pickupBlockedStudentId, #pickupBlockedBookTitle, #pickupBlockedBorrowCount')
                    .text('—');
                $('#pickupBlockedModal').modal('show');
            }
        })();
    @endif

});
</script>