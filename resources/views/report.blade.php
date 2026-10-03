@include('layouts.header')
@include('layouts.css')

<link href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

{{-- ═══════════════════════════════════════════════════════════════
     EARLY REFRESH DETECTION
     Runs BEFORE any HTML renders. If the page is a browser refresh
     of a report URL, we redirect to the clean /reports URL so the
     backend renders a completely fresh page — no report, no filters.
     ═══════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    try {
        const navEntries = performance.getEntriesByType('navigation');
        const navType = navEntries.length
            ? navEntries[0].type
            : (performance.navigation && performance.navigation.type);

        const isReload = navType === 'reload' || navType === 1;

        if (isReload) {
            const hasQuery = window.location.search.length > 1;
            const hasFragment = window.location.hash.length > 1;
            const cleanPath = window.location.pathname;

            if (hasQuery || hasFragment) {
                window.location.replace(cleanPath);
            }
        }
    } catch (e) {
        // Silently fail — worst case, filters stay
    }
})();
</script>

<style>
    :root {
        --report-primary: #d6538c;
        --report-primary-dark: #bd3f77;
        --report-primary-soft: #fff2f7;
        --report-primary-softer: #fff8fb;
        --report-page: #f7f7fb;
        --report-surface: #ffffff;
        --report-border: #ebe7ed;
        --report-border-soft: #f2eff3;
        --report-text: #292631;
        --report-muted: #797482;
        --report-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --report-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .reports-page {
        color: var(--report-text);
    }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .reports-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--report-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--report-shadow);
    }

    .reports-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--report-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }

    .reports-page .welcome-text span {
        color: var(--report-muted);
        font-size: 13px;
    }

    .reports-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .78);
    }

    .reports-page .breadcrumb-item a {
        color: var(--report-primary);
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .summary-card,
    .filter-card,
    .report-card {
        overflow: hidden;
        border: 1px solid var(--report-border);
        border-radius: 18px;
        background: var(--report-surface);
        box-shadow: var(--report-shadow);
    }

    .summary-card {
        height: calc(100% - 30px);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--report-shadow-hover);
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
        background: var(--report-primary-soft);
        color: var(--report-primary);
        font-family: FontAwesome;
        font-size: 16px;
        line-height: 40px;
        text-align: center;
        content: "\f080";
    }

    .summary-card h5 {
        max-width: calc(100% - 55px);
        margin-bottom: 12px;
        color: var(--report-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .35px;
        text-transform: uppercase;
    }

    .summary-number {
        margin-bottom: 6px;
        color: var(--report-text);
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
        border-bottom: 1px solid var(--report-border);
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
        background: linear-gradient(135deg, var(--report-primary) 0%, var(--report-primary-dark) 100%);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .filter-heading h5 {
        margin: 0 0 4px;
        color: var(--report-text);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .filter-heading small {
        display: block;
        color: var(--report-muted);
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
        color: var(--report-primary);
        font-size: 11px;
        opacity: .85;
    }

    .filter-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--report-text);
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .filter-control:hover {
        border-color: #c9c2ce;
    }

    .filter-control:focus {
        border-color: var(--report-primary);
        background-color: var(--report-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
    }

    .filter-control.is-empty {
        color: #999;
    }

    .filter-control.is-empty:focus {
        color: var(--report-text);
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: var(--report-primary);
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
        border-top: 1px solid var(--report-border);
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
        background: var(--report-primary-soft);
        color: var(--report-primary-dark);
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
       CONFIRM MODAL
       ═══════════════════════════════════════ */
    .report-confirm-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(41, 38, 49, .45);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s ease, visibility .25s ease;
    }

    .report-confirm-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .report-confirm-dialog {
        width: 100%;
        max-width: 420px;
        overflow: hidden;
        border: 1px solid var(--report-border);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 25px 60px rgba(42, 35, 48, .22);
        transform: translateY(12px) scale(.97);
        transition: transform .25s cubic-bezier(.34, 1.56, .64, 1);
    }

    .report-confirm-overlay.active .report-confirm-dialog {
        transform: translateY(0) scale(1);
    }

    .report-confirm-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 24px 24px 18px;
    }

    .report-confirm-icon {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--report-primary) 0%, var(--report-primary-dark) 100%);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .3);
    }

    .report-confirm-title {
        margin: 0 0 4px;
        color: var(--report-text);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .report-confirm-message {
        margin: 0;
        color: var(--report-muted);
        font-size: 13px;
        line-height: 1.55;
    }

    .report-confirm-body {
        padding: 0 24px 4px;
    }

    .report-confirm-warning {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 12px 14px;
        border: 1px solid #f5d6e1;
        border-radius: 12px;
        background: var(--report-primary-softer);
        color: var(--report-primary-dark);
        font-size: 12px;
        line-height: 1.5;
    }

    .report-confirm-warning i {
        margin-top: 1px;
        font-size: 13px;
    }

    .report-confirm-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 24px 22px;
    }

    .report-confirm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 108px;
        min-height: 42px;
        padding: 10px 18px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .report-confirm-btn-cancel {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .report-confirm-btn-cancel:hover,
    .report-confirm-btn-cancel:focus {
        border-color: #d5cdd9;
        background: #f8f6f9;
        color: #4a4450;
        outline: none;
    }

    .report-confirm-btn-confirm {
        border: 0;
        background: var(--report-primary);
        color: #fff;
    }

    .report-confirm-btn-confirm:hover,
    .report-confirm-btn-confirm:focus {
        background: var(--report-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
        transform: translateY(-1px);
        outline: none;
    }

    .report-confirm-btn:active {
        transform: translateY(0);
    }

    /* ═══════════════════════════════════════
       DOWNLOAD / EXPORT BUTTONS
       ═══════════════════════════════════════ */
    .print-report-button,
    .export-csv-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 18px;
        border: 0;
        border-radius: 11px;
        background: var(--report-primary);
        color: #fff !important;
        font-size: 13px;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .print-report-button i,
    .export-csv-button i {
        font-size: 13px;
    }

    .print-report-button:hover,
    .print-report-button:focus,
    .export-csv-button:hover,
    .export-csv-button:focus {
        background: var(--report-primary-dark);
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .3);
        transform: translateY(-1px);
    }

    .print-report-button:disabled,
    .export-csv-button:disabled {
        cursor: not-allowed;
        opacity: .65;
        transform: none;
    }

    /* ═══════════════════════════════════════
       REPORT CARD
       ═══════════════════════════════════════ */
    .report-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        min-height: 78px;
        padding: 18px 22px;
        border-bottom: 1px solid var(--report-border);
        background: #fff;
    }

    .report-card .card-title {
        color: var(--report-text);
        font-size: 17px;
        font-weight: 700;
    }

    .report-card .card-title i {
        color: var(--report-primary);
    }

    .report-card .card-body {
        padding: 0;
    }

    /* ═══════════════════════════════════════
       TABLE
       ═══════════════════════════════════════ */
    .table td,
    .table th {
        vertical-align: middle !important;
    }

    .report-table thead th {
        padding: 15px 14px;
        border: 0;
        border-bottom: 1px solid var(--report-border);
        background: #f8f7fa;
        color: #625c68;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .3px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .report-table tbody td {
        padding: 14px;
        border-top: 1px solid #f0edf1;
        background: #fff;
    }

    .report-table.table-striped tbody tr:nth-of-type(odd) td {
        background: #fdfcfd;
    }

    .report-table.table-hover tbody tr:hover td {
        background: #fff7fa;
    }

    .report-cell {
        display: block;
        min-width: 80px;
        color: #504a56;
        font-size: 12px;
        line-height: 1.45;
    }

    .report-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 18px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-student {
        background: #eef3ff;
        color: #315caa;
    }

    .badge-personnel {
        background: #fff0f3;
        color: #c4365f;
    }

    .badge-success {
        background: #eaf8ef;
        color: #218143;
    }

    .badge-primary {
        background: #eef3ff;
        color: #315caa;
    }

    .badge-danger {
        background: #ffeded;
        color: #bd3434;
    }

    .badge-warning {
        background: #fff5df;
        color: #a76d00;
    }

    .badge-neutral {
        background: #f1f1f1;
        color: #555;
    }

    /* ═══════════════════════════════════════
       EMPTY STATES
       ═══════════════════════════════════════ */
    .empty-state-card {
        padding: 64px 24px;
        border: 1px solid var(--report-border);
        border-radius: 18px;
        background: linear-gradient(145deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--report-shadow);
        text-align: center;
    }

    .empty-state-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin-bottom: 15px;
        border-radius: 18px;
        background: var(--report-primary-soft);
        color: var(--report-primary);
        font-size: 25px;
    }

    .empty-state-card h4 {
        margin-bottom: 8px;
        color: var(--report-text);
        font-size: 19px;
        font-weight: 700;
    }

    .empty-state-card p {
        max-width: 520px;
        margin: 0 auto;
        color: var(--report-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .empty-report {
        padding: 55px 20px !important;
        text-align: center;
    }

    .empty-report i {
        color: #ccc;
        font-size: 45px;
    }

    .report-alert {
        border: 1px solid #f3cbd7;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(117, 40, 65, .05);
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
        color: var(--report-muted);
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
        border-color: var(--report-primary) !important;
        background: var(--report-primary) !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: var(--report-primary-dark) !important;
        background: var(--report-primary-dark) !important;
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
        border-top: 1px solid var(--report-border);
        background: var(--report-primary-softer);
    }

    .active-filters:not(:empty)::before {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-right: 4px;
        color: var(--report-muted);
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
        color: var(--report-primary-dark);
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(213, 91, 145, .08);
        transition: all .2s ease;
    }

    .active-filter-tag:hover {
        border-color: var(--report-primary);
        box-shadow: 0 3px 10px rgba(213, 91, 145, .16);
    }

    .active-filter-tag strong {
        color: var(--report-muted);
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
        color: var(--report-primary-dark);
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        opacity: .75;
        transition: all .2s ease;
    }

    .active-filter-tag .remove-tag:hover {
        background: var(--report-primary);
        color: #fff;
        opacity: 1;
        transform: scale(1.1);
    }

    /* ═══════════════════════════════════════
       HIDE STALE REPORT CONTENT WHILE THE
       NEXT PAGE IS LOADING
       ═══════════════════════════════════════ */
    .reports-page.is-loading .report-card,
    .reports-page.is-loading .summary-card,
    .reports-page.is-loading .empty-state-card,
    .reports-page.is-loading .empty-report {
        visibility: hidden !important;
        opacity: 0 !important;
    }

    /* ═══════════════════════════════════════
       PRINT
       ═══════════════════════════════════════ */
    .print-only {
        display: none;
    }

    .report-type-highlight {
        animation: pulse-highlight 1s ease;
    }

    @keyframes pulse-highlight {
        0%, 100% { box-shadow: 0 0 0 0 rgba(213, 91, 145, 0); }
        50% { box-shadow: 0 0 0 4px rgba(213, 91, 145, .18); }
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .reports-page .page-titles {
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

        .report-card .card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .clear-filter-button,
        .print-report-button,
        .export-csv-button {
            width: 100%;
        }

        .report-actions {
            flex-direction: column;
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

    @media (max-width: 480px) {
        .report-confirm-actions {
            flex-direction: column-reverse;
        }

        .report-confirm-btn {
            width: 100%;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 10mm;
        }

        .header,
        .nav-header,
        .header-content,
        .quixnav,
        .deznav,
        .sidebar,
        .top-navbar,
        .footer,
        footer,
        .page-titles,
        .filter-card,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .no-print,
        .report-confirm-overlay {
            display: none !important;
        }

        .content-body {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        .print-only {
            display: block !important;
        }

        .summary-card,
        .report-card {
            border: 1px solid #ddd;
            border-radius: 0;
            box-shadow: none;
        }

        .summary-card .card-body {
            min-height: auto;
            padding: 10px;
        }

        .summary-number {
            font-size: 18px;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .report-table {
            width: 100% !important;
        }

        .report-table th,
        .report-table td {
            padding: 5px !important;
            font-size: 8px !important;
        }

        .report-badge {
            padding: 3px 5px;
            border: 1px solid #aaa;
            background: transparent !important;
            color: #000 !important;
            font-size: 8px;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    $reportGenerated = $reportGenerated ?? request()->boolean('generated');
    $selectedReportType = $reportGenerated ? ($reportType ?? '') : '';
@endphp

<div class="content-body reports-page">
    <div class="container-fluid py-4">

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Reports and Analytics</h4>
                    <span>Generate and review library reports</span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Reports and Analytics</li>
                </ol>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger report-alert">
                <strong>Please check the filters.</strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.index') }}" id="reportForm">
                    <input type="hidden" name="generated" value="1">

                    <div class="filter-heading">
                        <div class="filter-heading-title">
                            <span class="filter-heading-icon">
                                <i class="fa fa-sliders"></i>
                            </span>

                            <div>
                                <h5>Report Options</h5>

                                <small>
                                    Choose a report and narrow the results using the filters below.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="filter-fields">
                        <div class="row align-items-end">

                            <div class="col-xl-3 col-lg-6 col-md-6 filter-column">
                                <label class="filter-label" for="reportType">
                                    <i class="fa fa-file-text-o"></i>
                                    Report Type
                                </label>

                                <select name="report_type" id="reportType" class="form-control filter-control">
                                    <option value="" {{ $selectedReportType === '' ? 'selected' : '' }} disabled>
                                        Select a report type
                                    </option>

                                    <option value="attendance" {{ $selectedReportType === 'attendance' ? 'selected' : '' }}>
                                        Attendance Report
                                    </option>

                                    <option value="borrowing" {{ $selectedReportType === 'borrowing' ? 'selected' : '' }}>
                                        Borrowing Report
                                    </option>

                                    <option value="return" {{ $selectedReportType === 'return' ? 'selected' : '' }}>
                                        Return Report
                                    </option>

                                    <option value="overdue" {{ $selectedReportType === 'overdue' ? 'selected' : '' }}>
                                        Overdue Report
                                    </option>

                                    <option value="reservation" {{ $selectedReportType === 'reservation' ? 'selected' : '' }}>
                                        Reservation Report
                                    </option>

                                    <option value="inventory" {{ $selectedReportType === 'inventory' ? 'selected' : '' }}>
                                        Inventory Report
                                    </option>

                                    <option value="fine_collection" {{ $selectedReportType === 'fine_collection' ? 'selected' : '' }}>
                                        Fine Collection Report
                                    </option>

                                    <option value="lost_damaged" {{ $selectedReportType === 'lost_damaged' ? 'selected' : '' }}>
                                        Lost and Damaged Books
                                    </option>

                                    <option value="most_borrowed" {{ $selectedReportType === 'most_borrowed' ? 'selected' : '' }}>
                                        Most Borrowed Books
                                    </option>

                                    <option value="active_users" {{ $selectedReportType === 'active_users' ? 'selected' : '' }}>
                                        Most Active Library Users
                                    </option>
                                </select>
                            </div>

                            <div class="col-xl-2 col-lg-6 col-md-6 filter-column" id="startDateColumn">
                                <label class="filter-label" for="startDate">
                                    <i class="fa fa-calendar-o"></i>
                                    Start Date
                                </label>

                                <input
                                    type="date"
                                    id="startDate"
                                    name="start_date"
                                    class="form-control filter-control"
                                    value="{{ request('start_date') }}"
                                >
                            </div>

                            <div class="col-xl-2 col-lg-6 col-md-6 filter-column" id="endDateColumn">
                                <label class="filter-label" for="endDate">
                                    <i class="fa fa-calendar-check-o"></i>
                                    End Date
                                </label>

                                <input
                                    type="date"
                                    id="endDate"
                                    name="end_date"
                                    class="form-control filter-control"
                                    value="{{ request('end_date') }}"
                                >
                            </div>

                            <div class="col-xl-2 col-lg-6 col-md-6 filter-column" id="courseColumn">
                                <label class="filter-label" for="courseFilter">
                                    <i class="fa fa-graduation-cap"></i>
                                    Course / Program
                                </label>

                                <select name="course" id="courseFilter" class="form-control filter-control">
                                    <option value="">All Programs</option>

                                    @foreach($courses as $course)
                                        <option
                                            value="{{ $course }}"
                                            {{ request('course') === $course ? 'selected' : '' }}
                                        >
                                            {{ $course }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-2 col-lg-6 col-md-6 filter-column" id="userTypeColumn">
                                <label class="filter-label" for="userTypeFilter">
                                    <i class="fa fa-users"></i>
                                    User Type
                                </label>

                                <select name="user_type" id="userTypeFilter" class="form-control filter-control">
                                    <option value="">All Users</option>

                                    <option value="student" {{ request('user_type') === 'student' ? 'selected' : '' }}>
                                        Student
                                    </option>

                                    <option value="personnel" {{ request('user_type') === 'personnel' ? 'selected' : '' }}>
                                        Personnel
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-end mt-2">

                            <div class="col-xl-3 col-lg-6 col-md-6 filter-column" id="bookStatusColumn">
                                <label class="filter-label" for="bookStatusFilter">
                                    <i class="fa fa-tag"></i>
                                    Book / Transaction Status
                                </label>

                                <select name="book_status" id="bookStatusFilter" class="form-control filter-control">
                                    <option value="">All Statuses</option>

                                    @foreach([
                                        'available' => 'Available',
                                        'borrowed' => 'Borrowed',
                                        'returned' => 'Returned',
                                        'overdue' => 'Overdue',
                                        'reserved' => 'Reserved',
                                        'pending' => 'Pending',
                                        'lost' => 'Lost',
                                        'damaged' => 'Damaged'
                                    ] as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            {{ request('book_status') === $value ? 'selected' : '' }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($reportGenerated)
                                <div class="col-xl-4 col-lg-6 col-md-6 filter-column">
                                    <label class="filter-label" for="reportSearch">
                                        <i class="fa fa-search"></i>
                                        Search Generated Report
                                    </label>

                                    <div class="search-wrapper">
                                        <i class="fa fa-search"></i>

                                        <input
                                            type="text"
                                            id="reportSearch"
                                            class="form-control filter-control"
                                            placeholder="Search generated records"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                            @endif

                        </div>

                        {{-- Active filter tags --}}
                        <div id="activeFilters" class="active-filters"></div>

                        <div class="filter-actions">
                            <a
                                href="{{ route('reports.index') }}"
                                class="btn clear-filter-button"
                                id="clearFilters"
                            >
                                <i class="fa fa-refresh"></i>
                                Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($reportGenerated)
            <div class="row">
                @foreach($summary as $item)
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card summary-card">
                            <div class="card-body">
                                <h5>{{ $item['label'] }}</h5>

                                <p class="summary-number {{ $item['class'] ?? '' }}">
                                    {{ $item['value'] }}
                                </p>

                                <span class="text-muted">
                                    {{ $reportTitle }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="print-only text-center mb-3">
                <h3 class="mb-1">Lourdes College</h3>
                <h5 class="mb-1">Learning Commons</h5>

                <strong>{{ $reportTitle }}</strong>

                <div style="font-size: 11px; margin-top: 5px;">
                    @if(request('start_date') || request('end_date'))
                        Period:
                        {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'Beginning' }}
                        -
                        {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'Present' }}
                        <br>
                    @endif

                    Generated: {{ now()->format('M d, Y h:i A') }}
                </div>
            </div>

            <div class="card report-card">
                <div class="card-header">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="fa fa-bar-chart mr-2"></i>
                            {{ $reportTitle }}
                        </h4>

                        <small class="text-muted">
                            {{ $rows->count() }} record(s) found.
                        </small>
                    </div>

                    <div class="d-flex flex-wrap report-actions" style="gap: 8px;">
                        <button
                            type="button"
                            id="downloadCsv"
                            class="btn export-csv-button no-print"
                            {{ $rows->isEmpty() ? 'disabled' : '' }}
                        >
                            <i class="fa fa-file-excel-o"></i>
                            Download CSV
                        </button>

                        <button
                            type="button"
                            id="printReport"
                            class="btn print-report-button no-print"
                        >
                            <i class="fa fa-file-pdf-o"></i>
                            Download PDF
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if($rows->count())

                        <div class="table-responsive">
                            <table id="reportTable" class="table table-striped table-hover report-table">
                                <thead>
                                    <tr>
                                        @foreach($columns as $column)
                                            <th>{{ $column }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            @foreach($columns as $column)
                                                @php
                                                    $value = $row[$column] ?? '-';
                                                    $normalized = strtolower(trim($value));

                                                    $statusColumns = [
                                                        'Status',
                                                        'Return Status',
                                                        'Payment Status',
                                                        'User Type'
                                                    ];

                                                    $badgeClass = '';

                                                    if ($normalized === 'student') {
                                                        $badgeClass = 'badge-student';
                                                    } elseif ($normalized === 'personnel') {
                                                        $badgeClass = 'badge-personnel';
                                                    } elseif (in_array($normalized, [
                                                        'available',
                                                        'returned',
                                                        'paid',
                                                        'returned on time'
                                                    ])) {
                                                        $badgeClass = 'badge-success';
                                                    } elseif (in_array($normalized, [
                                                        'borrowed',
                                                        'reserved',
                                                        'pending'
                                                    ])) {
                                                        $badgeClass = 'badge-primary';
                                                    } elseif (in_array($normalized, [
                                                        'overdue',
                                                        'lost',
                                                        'unpaid'
                                                    ])) {
                                                        $badgeClass = 'badge-danger';
                                                    } elseif (in_array($normalized, [
                                                        'damaged',
                                                        'partially paid',
                                                        'returned late'
                                                    ])) {
                                                        $badgeClass = 'badge-warning';
                                                    } else {
                                                        $badgeClass = 'badge-neutral';
                                                    }
                                                @endphp

                                                <td>
                                                    @if(in_array($column, $statusColumns, true))
                                                        <span class="report-badge {{ $badgeClass }}">
                                                            {{ $value }}
                                                        </span>
                                                    @else
                                                        <span class="report-cell">
                                                            {{ $value }}
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else

                        <div class="empty-report">
                            <i class="fa fa-bar-chart"></i>

                            <h5 class="mt-3">No records found</h5>

                            <p class="text-muted">
                                There are currently no records matching the selected report filters.
                            </p>
                        </div>

                    @endif
                </div>
            </div>

        @else

            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <i class="fa fa-bar-chart"></i>
                </div>

                <h4>Select a report type to begin</h4>

                <p>
                    Choose a report type from the dropdown above and the report
                    will be generated automatically. You can refine the results
                    with the additional filters that appear.
                </p>
            </div>

        @endif
    </div>
</div>

{{-- Clear Filters Confirmation Modal --}}
<div class="report-confirm-overlay" id="clearFiltersModal" role="dialog" aria-modal="true" aria-labelledby="clearFiltersTitle">
    <div class="report-confirm-dialog">
        <div class="report-confirm-header">
            <span class="report-confirm-icon">
                <i class="fa fa-refresh"></i>
            </span>

            <div>
                <h5 class="report-confirm-title" id="clearFiltersTitle">
                    Clear all filters?
                </h5>

                <p class="report-confirm-message">
                    This will remove all active filters and reset the report to its default state.
                </p>
            </div>
        </div>

        <div class="report-confirm-body">
            <div class="report-confirm-warning">
                <i class="fa fa-exclamation-circle"></i>
                <span>
                    The currently generated report will be cleared. You can generate it again by selecting a report type.
                </span>
            </div>
        </div>

        <div class="report-confirm-actions">
            <button
                type="button"
                class="report-confirm-btn report-confirm-btn-cancel"
                id="cancelClearFilters"
            >
                Cancel
            </button>

            <button
                type="button"
                class="report-confirm-btn report-confirm-btn-confirm"
                id="confirmClearFilters"
            >
                <i class="fa fa-check"></i>
                Clear Filters
            </button>
        </div>
    </div>
</div>

@include('layouts.footer')

<script src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function () {
    let reportTable = null;

    const reportForm = $('#reportForm');
    const reportType = $('#reportType');
    const startDateColumn = $('#startDateColumn');
    const endDateColumn = $('#endDateColumn');
    const courseColumn = $('#courseColumn');
    const userTypeColumn = $('#userTypeColumn');
    const bookStatusColumn = $('#bookStatusColumn');
    const bookStatusFilter = $('#bookStatusFilter');

    const reportsPage = $('.reports-page');

    let autoSubmitTimer = null;

    // ─────────────────────────────────────────
    // Book / Transaction Status options per report type.
    // Default = every status. Specific report types may
    // override this to show only the relevant statuses.
    // ─────────────────────────────────────────
    const ALL_STATUS_OPTIONS = [
        { value: 'available', label: 'Available' },
        { value: 'borrowed',  label: 'Borrowed' },
        { value: 'returned',  label: 'Returned' },
        { value: 'overdue',   label: 'Overdue' },
        { value: 'reserved',  label: 'Reserved' },
        { value: 'pending',   label: 'Pending' },
        { value: 'lost',      label: 'Lost' },
        { value: 'damaged',   label: 'Damaged' }
    ];

    const STATUS_OPTIONS_BY_REPORT = {
        lost_damaged: [
            { value: 'lost',    label: 'Lost' },
            { value: 'damaged', label: 'Damaged' }
        ]
    };

    function rebuildBookStatusOptions(reportTypeValue) {
        const options =
            STATUS_OPTIONS_BY_REPORT[reportTypeValue] || ALL_STATUS_OPTIONS;

        // Remember current selection so we can keep it if still valid
        const currentValue = bookStatusFilter.val();

        // Rebuild the <select> contents
        bookStatusFilter.empty();

        bookStatusFilter.append(
            $('<option>', {
                value: '',
                text: 'All Statuses'
            })
        );

        options.forEach(function (option) {
            bookStatusFilter.append(
                $('<option>', {
                    value: option.value,
                    text: option.label
                })
            );
        });

        // Restore selection if it's still a valid option
        const stillValid = options.some(function (option) {
            return option.value === currentValue;
        });

        bookStatusFilter.val(stillValid ? currentValue : '');
    }

    // ─────────────────────────────────────────
    // Hide the currently rendered report immediately so the old
    // report table never flashes while the next page is loading.
    // ─────────────────────────────────────────
    function hideCurrentReport() {
        reportsPage.addClass('is-loading');
    }

    // If the browser restores this page from the back/forward cache,
    // make sure the hidden state is cleared so content is visible.
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            reportsPage.removeClass('is-loading');
        }
    });

    // Safety net: if the form submit is cancelled or navigation fails,
    // restore visibility after a short grace period.
    function scheduleRestore() {
        window.setTimeout(function () {
            reportsPage.removeClass('is-loading');
        }, 8000);
    }

    // ─────────────────────────────────────────
    // DataTable init
    // ─────────────────────────────────────────
    if ($('#reportTable').length) {
        reportTable = $('#reportTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            order: [],
            dom: 'lrtip',
            autoWidth: false,
            language: {
                emptyTable: 'No report records found.',
                zeroRecords: 'No records match your search.',
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
    }

    // ─────────────────────────────────────────
    // Report search
    // ─────────────────────────────────────────
    $('#reportSearch').on('input', function () {
        if (!reportTable) {
            return;
        }

        reportTable
            .search($(this).val())
            .page('first')
            .draw();
    });

    // ─────────────────────────────────────────
    // CSV export
    // ─────────────────────────────────────────
    $('#downloadCsv').on('click', function () {
        if (!reportTable) {
            return;
        }

        function csvCell(value) {
            let text = $('<div>').html(value ?? '').text().trim();

            if (/^[=+\-@]/.test(text)) {
                text = "'" + text;
            }

            return '"' + text.replace(/"/g, '""') + '"';
        }

        const csvRows = [];

        const headers = reportTable
            .columns()
            .header()
            .toArray()
            .map(function (header) {
                return csvCell(header.textContent);
            });

        csvRows.push(headers.join(','));

        reportTable.rows({ search: 'applied' }).every(function () {
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
        const reportName = @json($reportTitle ?? 'library-report');

        const safeReportName = String(reportName || 'library-report')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');

        link.href = downloadUrl;
        link.download =
            safeReportName +
            '-' +
            @json(now()->format('Y-m-d-His')) +
            '.csv';

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(downloadUrl);
    });

    // ─────────────────────────────────────────
    // PDF download
    // ─────────────────────────────────────────
    $('#printReport').on('click', function () {
        const parameters = new URLSearchParams(window.location.search);
        parameters.delete('generated');

        hideCurrentReport();

        window.location.href =
            @json(route('reports.pdf'))
            + '?'
            + parameters.toString();
    });

    // ─────────────────────────────────────────
    // Smart filter visibility
    // ─────────────────────────────────────────
    function showAllFilters() {
        startDateColumn.show();
        endDateColumn.show();
        courseColumn.show();
        userTypeColumn.show();
        bookStatusColumn.show();
    }

    function updateFilters() {
        const type = reportType.val();

        if (!type) {
            startDateColumn.hide();
            endDateColumn.hide();
            courseColumn.hide();
            userTypeColumn.hide();
            bookStatusColumn.hide();
            return;
        }

        showAllFilters();

        if ([
            'attendance',
            'return',
            'overdue',
            'fine_collection',
            'most_borrowed',
            'active_users'
        ].includes(type)) {
            bookStatusColumn.hide();
        }

        if (type === 'inventory') {
            startDateColumn.hide();
            endDateColumn.hide();
            courseColumn.hide();
            userTypeColumn.hide();
        }

        if (type === 'reservation') {
            userTypeColumn.hide();
        }

        // Rebuild status dropdown so its options match the report type
        rebuildBookStatusOptions(type);
    }

    function updateReportTypeStyle() {
        if (reportType.val()) {
            reportType.removeClass('is-empty');
        } else {
            reportType.addClass('is-empty');
        }
    }

    // ─────────────────────────────────────────
    // Auto-submit
    // Hides the current report instantly, then submits the form.
    // ─────────────────────────────────────────
    function scheduleAutoSubmit(delay) {
        if (autoSubmitTimer) {
            window.clearTimeout(autoSubmitTimer);
        }

        hideCurrentReport();

        autoSubmitTimer = window.setTimeout(function () {
            reportForm.trigger('submit');
        }, delay);
    }

    // Also hide the report if the form is submitted via Enter key or
    // any other path that triggers a native submit.
    reportForm.on('submit', function () {
        hideCurrentReport();
        scheduleRestore();
    });

    // ─────────────────────────────────────────
    // Report type change → auto-generate
    // ─────────────────────────────────────────
    reportType.on('change', function () {
        updateFilters();
        updateReportTypeStyle();
        updateActiveFilters();

        if (reportType.val()) {
            scheduleAutoSubmit(350);
        }
    });

    // ─────────────────────────────────────────
    // Filter changes → auto-regenerate
    // ─────────────────────────────────────────
    $('#startDate, #endDate, #courseFilter, #userTypeFilter, #bookStatusFilter')
        .on('change', function () {
            updateActiveFilters();

            if (reportType.val() && @json($reportGenerated)) {
                scheduleAutoSubmit(350);
            }
        });

    // ─────────────────────────────────────────
    // Active filter tags
    // ─────────────────────────────────────────
    function updateActiveFilters() {
        const container = $('#activeFilters');
        container.empty();

        const type = reportType.val();
        const start = $('#startDate').val();
        const end = $('#endDate').val();
        const course = $('#courseFilter').val();
        const userType = $('#userTypeFilter').val();
        const bookStatus = $('#bookStatusFilter').val();

        if (type) {
            const typeText = $('#reportType option:selected').text().trim();
            container.append(createFilterTag('Report', typeText, 'report_type'));
        }

        if (start && startDateColumn.is(':visible')) {
            container.append(createFilterTag('From', start, 'start_date'));
        }

        if (end && endDateColumn.is(':visible')) {
            container.append(createFilterTag('To', end, 'end_date'));
        }

        if (course && courseColumn.is(':visible')) {
            const courseText = $('#courseFilter option:selected').text().trim();
            container.append(createFilterTag('Program', courseText, 'course'));
        }

        if (userType && userTypeColumn.is(':visible')) {
            const userTypeText = $('#userTypeFilter option:selected').text().trim();
            container.append(createFilterTag('User', userTypeText, 'user_type'));
        }

        if (bookStatus && bookStatusColumn.is(':visible')) {
            const statusText = $('#bookStatusFilter option:selected').text().trim();
            container.append(createFilterTag('Status', statusText, 'book_status'));
        }
    }

    function createFilterTag(label, value, type) {
        const tag = $('<span class="active-filter-tag"></span>');

        tag.html(
            '<strong>' + label + ':</strong> ' +
            $('<div>').text(value).html() +
            '<span class="remove-tag" data-type="' + type + '" title="Remove filter">&times;</span>'
        );

        tag.find('.remove-tag').on('click', function () {
            const filterType = $(this).data('type');
            clearFilter(filterType);
        });

        return tag;
    }

    function clearFilter(type) {
        if (type === 'report_type') {
            reportType.val('');
            updateFilters();
            updateReportTypeStyle();
            updateActiveFilters();
            return;
        } else if (type === 'start_date') {
            $('#startDate').val('');
        } else if (type === 'end_date') {
            $('#endDate').val('');
        } else if (type === 'course') {
            $('#courseFilter').val('');
        } else if (type === 'user_type') {
            $('#userTypeFilter').val('');
        } else if (type === 'book_status') {
            $('#bookStatusFilter').val('');
        }

        updateActiveFilters();

        if (reportType.val() && @json($reportGenerated)) {
            scheduleAutoSubmit(350);
        }
    }

    // Initialize
    rebuildBookStatusOptions(reportType.val());
    updateFilters();
    updateReportTypeStyle();
    updateActiveFilters();

    // ─────────────────────────────────────────
    // Clear filters with custom modal confirmation
    // ─────────────────────────────────────────
    const clearFiltersModal = $('#clearFiltersModal');
    const clearFiltersLink = $('#clearFilters');

    function hasActiveFilters() {
        return Boolean(
            reportType.val()
            || $('#startDate').val()
            || $('#endDate').val()
            || $('#courseFilter').val()
            || $('#userTypeFilter').val()
            || $('#bookStatusFilter').val()
        );
    }

    function openClearFiltersModal() {
        clearFiltersModal.addClass('active');
        window.setTimeout(function () {
            $('#confirmClearFilters').trigger('focus');
        }, 100);
    }

    function closeClearFiltersModal() {
        clearFiltersModal.removeClass('active');
    }

    clearFiltersLink.on('click', function (e) {
        if (!hasActiveFilters()) {
            return;
        }

        e.preventDefault();
        openClearFiltersModal();
    });

    $('#cancelClearFilters').on('click', function () {
        closeClearFiltersModal();
        clearFiltersLink.trigger('focus');
    });

    $('#confirmClearFilters').on('click', function () {
        closeClearFiltersModal();
        hideCurrentReport();
        window.location.href = clearFiltersLink.attr('href');
    });

    clearFiltersModal.on('click', function (e) {
        if (e.target === this) {
            closeClearFiltersModal();
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && clearFiltersModal.hasClass('active')) {
            closeClearFiltersModal();
            clearFiltersLink.trigger('focus');
        }
    });
});
</script>