@include('layouts.header')
@include('layouts.css')

<style>
    :root {
        --settings-primary: #d6538c;
        --settings-primary-dark: #bd3f77;
        --settings-primary-soft: #fff2f7;
        --settings-primary-softer: #fff8fb;
        --settings-page: #f7f7fb;
        --settings-surface: #ffffff;
        --settings-border: #ebe7ed;
        --settings-border-soft: #f2eff3;
        --settings-text: #292631;
        --settings-muted: #797482;
        --settings-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --settings-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .settings-page {
        color: var(--settings-text);
    }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .settings-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--settings-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--settings-shadow);
    }

    .settings-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--settings-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }

    .settings-page .welcome-text span {
        color: var(--settings-muted);
        font-size: 13px;
    }

    .settings-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .78);
    }

    .settings-page .breadcrumb-item a {
        color: var(--settings-primary);
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       ALERTS
       ═══════════════════════════════════════ */
    .settings-alert {
        border: 1px solid var(--settings-border);
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(42, 35, 48, .04);
        font-size: 13px;
    }

    .settings-page .alert-success {
        border-color: #cdeedb;
        background: #eaf8ef;
        color: #218143;
    }

    .settings-page .alert-danger {
        border-color: #f3cbd7;
        background: #ffeded;
        color: #bd3434;
    }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .settings-card,
    .settings-summary-card {
        overflow: hidden;
        border: 1px solid var(--settings-border);
        border-radius: 18px;
        background: var(--settings-surface);
        box-shadow: var(--settings-shadow);
    }

    .settings-card {
        margin-bottom: 24px;
    }

    /* ═══════════════════════════════════════
       SUMMARY ROW — tighter gutters
       ═══════════════════════════════════════ */
    .settings-summary-row {
        margin-right: -10px;
        margin-left: -10px;
        margin-bottom: 24px;
    }

    .settings-summary-row > [class*="col-"] {
        display: flex;
        padding-right: 10px;
        padding-left: 10px;
        margin-bottom: 20px;
    }

    .settings-summary-row > [class*="col-"] > .settings-summary-card {
        width: 100%;
        margin-bottom: 0;
    }

    /* Remove the bottom margin on the last row of cards for tighter spacing */
    .settings-summary-row > [class*="col-"]:nth-last-child(-n+4) {
        margin-bottom: 0;
    }

    @media (max-width: 1199px) {
        /* On tablets, bottom row still needs spacing */
        .settings-summary-row > [class*="col-"]:nth-last-child(-n+4) {
            margin-bottom: 20px;
        }

        .settings-summary-row > [class*="col-"]:nth-last-child(-n+2) {
            margin-bottom: 0;
        }
    }

    @media (max-width: 767px) {
        .settings-summary-row > [class*="col-"] {
            margin-bottom: 16px;
        }

        .settings-summary-row > [class*="col-"]:last-child {
            margin-bottom: 0;
        }
    }

    /* ═══════════════════════════════════════
       SUMMARY CARDS
       ═══════════════════════════════════════ */
    .settings-summary-card {
        margin-bottom: 0;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .settings-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--settings-shadow-hover);
    }

    .settings-summary-card .card-body {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 96px;
        padding: 20px 22px;
    }

    .settings-summary-card .card-body,
    .settings-summary-card .card {
        border-radius: 18px;
    }

    .settings-summary-icon {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--settings-primary) 0%, var(--settings-primary-dark) 100%);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .settings-summary-value {
        max-width: 180px;
        margin: 0;
        overflow: hidden;
        color: var(--settings-text);
        font-size: 22px;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -.35px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .settings-summary-card .text-muted {
        display: block;
        margin-top: 4px;
        color: var(--settings-muted) !important;
        font-size: 12px;
        font-weight: 500;
    }

    /* ═══════════════════════════════════════
       CARD HEADERS
       ═══════════════════════════════════════ */
    .settings-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 0;
        padding: 20px 24px;
        border-bottom: 1px solid var(--settings-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .settings-card-icon {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 13px;
        background: linear-gradient(135deg, var(--settings-primary) 0%, var(--settings-primary-dark) 100%);
        color: #fff;
        font-size: 17px;
        box-shadow: 0 5px 14px rgba(213, 91, 145, .26);
    }

    .settings-card-title {
        margin: 0 0 3px;
        color: var(--settings-text);
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.15px;
    }

    .settings-card-description {
        margin: 0;
        color: var(--settings-muted);
        font-size: 12px;
        font-weight: 400;
        line-height: 1.4;
    }

    .settings-card-body {
        padding: 22px 24px;
    }

    /* Kill Bootstrap row negative margins inside card body */
    .settings-card-body > .row {
        margin-right: 0;
        margin-left: 0;
    }

    .settings-card-body > .row > [class*="col-"] {
        padding-right: 12px;
        padding-left: 12px;
    }

    /* First row's columns should align with the card padding */
    .settings-card-body > .row:first-child > [class*="col-"]:first-child {
        padding-left: 0;
    }

    .settings-card-body > .row:first-child > [class*="col-"]:last-child {
        padding-right: 0;
    }

    /* ═══════════════════════════════════════
       FORM FIELDS
       ═══════════════════════════════════════ */
    .settings-page .form-group {
        margin-bottom: 20px;
    }

    .settings-label {
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

    .settings-label i {
        color: var(--settings-primary);
        font-size: 11px;
        opacity: .85;
    }

    .settings-required {
        color: #d5435b;
        margin-left: 2px;
    }

    .settings-control {
        min-height: 44px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--settings-text);
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .settings-control:hover {
        border-color: #c9c2ce;
    }

    .settings-control:focus {
        border-color: var(--settings-primary);
        background-color: var(--settings-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12) !important;
        outline: none;
    }

    textarea.settings-control {
        min-height: 100px;
        padding-top: 12px;
        resize: vertical;
    }

    select.settings-control {
        background-position: right 12px center;
        padding-right: 36px;
    }

    .settings-help {
        display: block;
        margin-top: 6px;
        color: var(--settings-muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .settings-page .invalid-feedback {
        display: block;
        margin-top: 6px;
        color: #bd3434;
        font-size: 12px;
        font-weight: 500;
    }

    /* Rounded input group */
    .settings-page .input-group > .settings-control:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .settings-page .input-group > .settings-control:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .settings-page .input-group-text {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        border: 1px solid #dfdbe2;
        background: #f8f7fa;
        color: #625c68;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .settings-page .input-group > .input-group-append > .input-group-text {
        border-top-right-radius: 11px;
        border-bottom-right-radius: 11px;
        border-left: 0;
    }

    .settings-page .input-group > .input-group-prepend > .input-group-text {
        border-top-left-radius: 11px;
        border-bottom-left-radius: 11px;
        border-right: 0;
    }

    /* ═══════════════════════════════════════
       SECTION NOTES
       ═══════════════════════════════════════ */
    .settings-section-note {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 20px;
        padding: 12px 14px;
        border: 1px solid #f5d6e1;
        border-radius: 12px;
        background: var(--settings-primary-softer);
        color: var(--settings-primary-dark);
        font-size: 12px;
        line-height: 1.55;
    }

    .settings-section-note::before {
        flex-shrink: 0;
        margin-top: 1px;
        color: var(--settings-primary);
        font-family: FontAwesome;
        font-size: 13px;
        content: "\f05a";
    }

    /* ═══════════════════════════════════════
       SWITCH ROWS
       ═══════════════════════════════════════ */
    .settings-switch-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 82px;
        margin-bottom: 12px;
        padding: 18px 20px;
        border: 1px solid var(--settings-border);
        border-radius: 14px;
        background: #fdfcfe;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .settings-switch-row:hover {
        border-color: #f3cbd7;
        background: var(--settings-primary-softer);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .06);
    }

    .settings-switch-row:last-child {
        margin-bottom: 0;
    }

    .settings-switch-title {
        margin: 0 0 4px;
        color: var(--settings-text);
        font-size: 14px;
        font-weight: 700;
    }

    .settings-switch-description {
        margin: 0;
        color: var(--settings-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    /* ═══════════════════════════════════════
       TOGGLE SWITCH
       ═══════════════════════════════════════ */
    .settings-toggle {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
        width: 52px;
        min-width: 52px;
        height: 28px;
        margin: 0;
    }

    .settings-toggle input {
        position: absolute;
        width: 0;
        height: 0;
        opacity: 0;
    }

    .settings-toggle-slider {
        position: absolute;
        inset: 0;
        border-radius: 30px;
        background: #d5cdd9;
        cursor: pointer;
        transition: background .25s ease, box-shadow .25s ease;
    }

    .settings-toggle-slider::before {
        position: absolute;
        bottom: 3px;
        left: 3px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .18);
        content: "";
        transition: transform .25s cubic-bezier(.4, 0, .2, 1);
    }

    .settings-toggle input:checked + .settings-toggle-slider {
        background: linear-gradient(135deg, var(--settings-primary) 0%, var(--settings-primary-dark) 100%);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .28);
    }

    .settings-toggle input:checked + .settings-toggle-slider::before {
        transform: translateX(24px);
    }

    .settings-toggle input:focus + .settings-toggle-slider {
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .18);
    }

    .settings-toggle input:disabled + .settings-toggle-slider {
        cursor: not-allowed;
        opacity: .5;
    }

    /* ═══════════════════════════════════════
       LOGO AREA
       ═══════════════════════════════════════ */
    .settings-logo-area {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 18px;
        border: 1px dashed #e0d5dd;
        border-radius: 14px;
        background: var(--settings-primary-softer);
    }

    .settings-logo-preview {
        width: 92px;
        height: 92px;
        flex: 0 0 92px;
        padding: 8px;
        border: 1px solid var(--settings-border);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(42, 35, 48, .05);
        object-fit: contain;
    }

    .settings-file-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 0 0 6px 0;
        min-height: 40px;
        padding: 9px 16px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--settings-primary) 0%, var(--settings-primary-dark) 100%);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(213, 91, 145, .22);
        transition: all .2s ease;
    }

    .settings-file-label:hover,
    .settings-file-label:focus {
        background: linear-gradient(135deg, var(--settings-primary-dark) 0%, #a83468 100%);
        color: #fff;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .32);
        transform: translateY(-1px);
    }

    .settings-file-input {
        display: none;
    }

    /* ═══════════════════════════════════════
       ACTION BAR
       ═══════════════════════════════════════ */
    .settings-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 25px;
        padding: 22px 26px;
        border: 1px solid var(--settings-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
        box-shadow: var(--settings-shadow);
    }

    .settings-action-bar strong {
        color: var(--settings-text);
        font-size: 14px;
        font-weight: 700;
    }

    .settings-action-bar .text-muted {
        color: var(--settings-muted);
        font-size: 12px;
    }

    /* ═══════════════════════════════════════
       BUTTONS
       ═══════════════════════════════════════ */
    .settings-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        min-width: 130px;
        padding: 10px 22px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .settings-button-secondary {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .settings-button-secondary i {
        font-size: 12px;
        transition: transform .3s ease;
    }

    .settings-button-secondary:hover,
    .settings-button-secondary:focus {
        border-color: #f3cbd7;
        background: var(--settings-primary-soft);
        color: var(--settings-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .12);
        transform: translateY(-1px);
        outline: none;
    }

    .settings-button-secondary:hover i {
        transform: rotate(-90deg);
    }

    .settings-button-secondary:active {
        transform: translateY(0);
    }

    .settings-button-primary {
        border: 0;
        background: linear-gradient(135deg, var(--settings-primary) 0%, var(--settings-primary-dark) 100%);
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .settings-button-primary:hover,
    .settings-button-primary:focus {
        background: linear-gradient(135deg, var(--settings-primary-dark) 0%, #a83468 100%);
        color: #fff !important;
        box-shadow: 0 8px 22px rgba(213, 91, 145, .4);
        transform: translateY(-1px);
        outline: none;
    }

    .settings-button-primary:disabled {
        cursor: not-allowed;
        opacity: .7;
        transform: none;
    }

    .settings-button-primary:active {
        transform: translateY(0);
    }

    /* ═══════════════════════════════════════
       DIVIDERS
       ═══════════════════════════════════════ */
    .settings-card hr {
        margin: 24px 0;
        border: 0;
        border-top: 1px solid var(--settings-border-soft);
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .settings-page .page-titles {
            padding: 18px;
        }

        .settings-card-header,
        .settings-card-body {
            padding: 18px;
        }

        .settings-summary-card .card-body {
            padding: 18px;
            min-height: 88px;
        }

        .settings-logo-area {
            align-items: flex-start;
            flex-direction: column;
        }

        .settings-action-bar {
            align-items: stretch;
            flex-direction: column;
            padding: 18px;
        }

        .settings-action-bar .d-flex {
            width: 100%;
            flex-direction: column;
            gap: 10px;
        }

        .settings-button {
            width: 100%;
            margin-right: 0 !important;
        }

        .settings-switch-row {
            align-items: flex-start;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

<div class="content-body settings-page">
    <div class="container-fluid py-4">

        {{-- PAGE HEADER --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>System Settings</h4>
                    <span>
                        Manage system identity, attendance and general settings
                    </span>
                </div>
            </div>

            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('settings.index') }}">
                            System Settings
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Settings
                    </li>
                </ol>
            </div>
        </div>

        {{-- ALERTS --}}
        <div id="settingsAlertContainer" aria-live="polite">

            @if (session('success'))
                <div class="alert alert-success settings-alert alert-dismissible fade show">
                    <i class="fa fa-check-circle mr-2"></i>
                    {{ session('success') }}

                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger settings-alert alert-dismissible fade show">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}

                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger settings-alert">
                    <strong>Please correct the following:</strong>

                    <ul class="mb-0 mt-2 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        {{-- SUMMARY --}}
        <div class="row settings-summary-row">

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card settings-summary-card">
                    <div class="card-body">

                        <div class="settings-summary-icon">
                            <i class="fa fa-desktop"></i>
                        </div>

                        <div>
                            <p
                                id="summaryShortName"
                                class="settings-summary-value"
                                title="{{ $settings->system_title ?? 'RFID-BARM System' }}"
                            >
                                {{ $settings->system_short_name ?? 'BARM' }}
                            </p>

                            <span class="text-muted">
                                System short name
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card settings-summary-card">
                    <div class="card-body">

                        <div class="settings-summary-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>

                        <div>
                            <p id="summaryTimezone" class="settings-summary-value">
                                UTC+08:00
                            </p>

                            <span class="text-muted">
                                Asia/Manila timezone
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card settings-summary-card">
                    <div class="card-body">

                        <div class="settings-summary-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>

                        <div>
                            <p id="summaryAcademicYear" class="settings-summary-value">
                                {{ $settings->academic_year ?: 'Not set' }}
                            </p>

                            <span class="text-muted">
                                Academic year
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card settings-summary-card">
                    <div class="card-body">

                        <div class="settings-summary-icon">
                            <i class="fa fa-refresh"></i>
                        </div>

                        <div>
                            <p id="summaryRefreshSeconds" class="settings-summary-value">
                                {{ $settings->table_refresh_seconds ?? 3 }}
                                seconds
                            </p>

                            <span class="text-muted">
                                Table auto-refresh
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- FORM --}}
        <form
            id="settingsForm"
            action="{{ route('settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <div class="row">

                {{-- LEFT COLUMN --}}
                <div class="col-xl-8">

                    {{-- SYSTEM IDENTITY --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-desktop"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    System Identity
                                </h3>

                                <p class="settings-card-description">
                                    Names and branding used by the system.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="row">

                                <div class="col-md-8 form-group">

                                    <label
                                        class="settings-label"
                                        for="system_title"
                                    >
                                        System Title
                                        <span class="settings-required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="system_title"
                                        name="system_title"
                                        class="form-control settings-control @error('system_title') is-invalid @enderror"
                                        value="{{ old('system_title', $settings->system_title ?? 'RFID-BARM System') }}"
                                        maxlength="150"
                                        required
                                    >

                                    <small class="settings-help">
                                        Used in page titles and browser tabs.
                                    </small>

                                    @error('system_title')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-4 form-group">

                                    <label
                                        class="settings-label"
                                        for="system_short_name"
                                    >
                                        Short Name
                                        <span class="settings-required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="system_short_name"
                                        name="system_short_name"
                                        class="form-control settings-control @error('system_short_name') is-invalid @enderror"
                                        value="{{ old('system_short_name', $settings->system_short_name ?? 'BARM') }}"
                                        maxlength="30"
                                        required
                                    >

                                    @error('system_short_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="institution_name"
                                    >
                                        Institution Name
                                        <span class="settings-required">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="institution_name"
                                        name="institution_name"
                                        class="form-control settings-control"
                                        value="{{ old('institution_name', $settings->institution_name ?? 'Lourdes College') }}"
                                        maxlength="150"
                                        required
                                    >

                                </div>

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="department_name"
                                    >
                                        Department or Office
                                    </label>

                                    <input
                                        type="text"
                                        id="department_name"
                                        name="department_name"
                                        class="form-control settings-control"
                                        value="{{ old('department_name', $settings->department_name ?? 'Learning Commons') }}"
                                        maxlength="150"
                                    >

                                </div>

                                <div class="col-12 form-group mb-0">

                                    <label
                                        class="settings-label"
                                        for="system_subtitle"
                                    >
                                        System Subtitle
                                    </label>

                                    <input
                                        type="text"
                                        id="system_subtitle"
                                        name="system_subtitle"
                                        class="form-control settings-control"
                                        value="{{ old('system_subtitle', $settings->system_subtitle ?? 'Attendance and Resources Processing with RFID Integration') }}"
                                        maxlength="255"
                                    >

                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- ATTENDANCE KIOSK --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-id-card"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Attendance Kiosk
                                </h3>

                                <p class="settings-card-description">
                                    Configure RFID attendance behavior.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="row">

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="attendance_kiosk_title"
                                    >
                                        Kiosk Title
                                    </label>

                                    <input
                                        type="text"
                                        id="attendance_kiosk_title"
                                        name="attendance_kiosk_title"
                                        class="form-control settings-control"
                                        value="{{ old('attendance_kiosk_title', $settings->attendance_kiosk_title ?? 'Attendance Kiosk') }}"
                                        maxlength="150"
                                    >

                                </div>

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="attendance_result_duration"
                                    >
                                        Result Display Duration
                                    </label>

                                    <div class="input-group">

                                        <input
                                            type="number"
                                            id="attendance_result_duration"
                                            name="attendance_result_duration"
                                            class="form-control settings-control"
                                            value="{{ old('attendance_result_duration', $settings->attendance_result_duration ?? 3) }}"
                                            min="1"
                                            max="30"
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                seconds
                                            </span>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-12 form-group">

                                    <label
                                        class="settings-label"
                                        for="attendance_kiosk_subtitle"
                                    >
                                        Kiosk Subtitle
                                    </label>

                                    <input
                                        type="text"
                                        id="attendance_kiosk_subtitle"
                                        name="attendance_kiosk_subtitle"
                                        class="form-control settings-control"
                                        value="{{ old('attendance_kiosk_subtitle', $settings->attendance_kiosk_subtitle ?? 'Learning Commons Attendance Monitoring') }}"
                                        maxlength="255"
                                    >

                                </div>

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="automatic_time_out_at"
                                    >
                                        Automatic Time-Out Time
                                    </label>

                                    <input
                                        type="time"
                                        id="automatic_time_out_at"
                                        name="automatic_time_out_at"
                                        class="form-control settings-control"
                                        value="{{ old('automatic_time_out_at', isset($settings->automatic_time_out_at) ? substr($settings->automatic_time_out_at, 0, 5) : '') }}"
                                    >

                                </div>

                                <div class="col-md-6 form-group">

                                    <label
                                        class="settings-label"
                                        for="table_refresh_seconds"
                                    >
                                        Monitoring Auto-Refresh
                                    </label>

                                    <div class="input-group">

                                        <input
                                            type="number"
                                            id="table_refresh_seconds"
                                            name="table_refresh_seconds"
                                            class="form-control settings-control"
                                            value="{{ old('table_refresh_seconds', $settings->table_refresh_seconds ?? 3) }}"
                                            min="1"
                                            max="300"
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                seconds
                                            </span>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <hr>

                            {{-- STUDENT ATTENDANCE --}}
                            <div class="settings-switch-row">

                                <div>
                                    <p class="settings-switch-title">
                                        Student Attendance
                                    </p>

                                    <p class="settings-switch-description">
                                        Allow registered students to time in and out.
                                    </p>
                                </div>

                                <label class="settings-toggle">

                                    <input
                                        type="hidden"
                                        name="student_attendance_enabled"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="student_attendance_enabled"
                                        value="1"
                                        {{ old('student_attendance_enabled', $settings->student_attendance_enabled ?? true) ? 'checked' : '' }}
                                    >

                                    <span class="settings-toggle-slider"></span>

                                </label>

                            </div>

                            {{-- PERSONNEL ATTENDANCE --}}
                            <div class="settings-switch-row">

                                <div>
                                    <p class="settings-switch-title">
                                        Personnel Attendance
                                    </p>

                                    <p class="settings-switch-description">
                                        Allow registered personnel to time in and out.
                                    </p>
                                </div>

                                <label class="settings-toggle">

                                    <input
                                        type="hidden"
                                        name="personnel_attendance_enabled"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="personnel_attendance_enabled"
                                        value="1"
                                        {{ old('personnel_attendance_enabled', $settings->personnel_attendance_enabled ?? true) ? 'checked' : '' }}
                                    >

                                    <span class="settings-toggle-slider"></span>

                                </label>

                            </div>

                            {{-- MULTIPLE SESSIONS --}}
                            <div class="settings-switch-row">

                                <div>
                                    <p class="settings-switch-title">
                                        Allow Multiple Attendance Sessions
                                    </p>

                                    <p class="settings-switch-description">
                                        Allow more than one attendance session per day.
                                    </p>
                                </div>

                                <label class="settings-toggle">

                                    <input
                                        type="hidden"
                                        name="allow_multiple_attendance_sessions"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        name="allow_multiple_attendance_sessions"
                                        value="1"
                                        {{ old('allow_multiple_attendance_sessions', $settings->allow_multiple_attendance_sessions ?? false) ? 'checked' : '' }}
                                    >

                                    <span class="settings-toggle-slider"></span>

                                </label>

                            </div>

                            {{-- AUTOMATIC TIMEOUT --}}
                            <div class="settings-switch-row">

                                <div>
                                    <p class="settings-switch-title">
                                        Automatic Time-Out
                                    </p>

                                    <p class="settings-switch-description">
                                        Automatically close open attendance records at the selected time.
                                    </p>
                                </div>

                                <label class="settings-toggle">

                                    <input
                                        type="hidden"
                                        name="automatic_time_out_enabled"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        id="automatic_time_out_enabled"
                                        name="automatic_time_out_enabled"
                                        value="1"
                                        {{ old('automatic_time_out_enabled', $settings->automatic_time_out_enabled ?? false) ? 'checked' : '' }}
                                    >

                                    <span class="settings-toggle-slider"></span>

                                </label>

                            </div>

                        </div>

                    </section>

                    {{-- LIBRARY MODULES --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-book"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Library Modules
                                </h3>

                                <p class="settings-card-description">
                                    Enable or disable library functions.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="settings-section-note">
                                Borrowing limits, loan periods and overdue fines
                                are managed under Policy and Settings.
                            </div>

                            @php
                                $moduleSettings = [
                                    [
                                        'borrowing_enabled',
                                        'Borrowing',
                                        'Allow student and personnel borrowing transactions.',
                                        true
                                    ],
                                    [
                                        'returning_enabled',
                                        'Returning',
                                        'Allow borrowed resources to be returned.',
                                        true
                                    ],
                                    [
                                        'reservation_enabled',
                                        'Reservations',
                                        'Allow users to reserve available resources.',
                                        true
                                    ],
                                    [
                                        'rfid_required',
                                        'Require RFID',
                                        'Require an RFID scan before kiosk transactions.',
                                        true
                                    ],
                                ];
                            @endphp

                            @foreach ($moduleSettings as [$field, $title, $description, $default])

                                <div class="settings-switch-row">

                                    <div>
                                        <p class="settings-switch-title">
                                            {{ $title }}
                                        </p>

                                        <p class="settings-switch-description">
                                            {{ $description }}
                                        </p>
                                    </div>

                                    <label class="settings-toggle">

                                        <input
                                            type="hidden"
                                            name="{{ $field }}"
                                            value="0"
                                        >

                                        <input
                                            type="checkbox"
                                            name="{{ $field }}"
                                            value="1"
                                            {{ old($field, $settings->{$field} ?? $default) ? 'checked' : '' }}
                                        >

                                        <span class="settings-toggle-slider"></span>

                                    </label>

                                </div>

                            @endforeach

                            <div class="form-group mt-4 mb-0">

                                <label
                                    class="settings-label"
                                    for="reservation_pickup_instructions"
                                >
                                    Reservation Pickup Instructions
                                </label>

                                <textarea
                                    id="reservation_pickup_instructions"
                                    name="reservation_pickup_instructions"
                                    class="form-control settings-control"
                                    maxlength="1000"
                                >{{ old('reservation_pickup_instructions', $settings->reservation_pickup_instructions ?? 'Please proceed to the Learning Commons counter to claim your reservation.') }}</textarea>

                            </div>

                        </div>

                    </section>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-xl-4">

                    {{-- LOGO --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-image"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Logo
                                </h3>

                                <p class="settings-card-description">
                                    System and kiosk branding.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            @php
                                $baseLogoSource = $settings->logoUrl();

                                $logoVersion = $settings->updated_at?->getTimestamp()
                                    ?? time();

                                $logoSource = $baseLogoSource
                                    . (str_contains($baseLogoSource, '?') ? '&' : '?')
                                    . 'v='
                                    . $logoVersion;
                            @endphp

                            <div class="settings-logo-area">

                                <img
                                    id="logoPreview"
                                    src="{{ $logoSource }}"
                                    class="settings-logo-preview"
                                    alt="System logo preview"
                                    data-system-logo
                                >

                                <div>

                                    <label
                                        for="logo"
                                        class="settings-file-label"
                                    >
                                        <i class="fa fa-upload"></i>
                                        Choose Logo
                                    </label>

                                    <input
                                        type="file"
                                        id="logo"
                                        name="logo"
                                        class="settings-file-input"
                                        accept="image/png,image/jpeg,image/webp"
                                    >

                                    <small
                                        id="logoFileName"
                                        class="settings-help"
                                    >
                                        PNG, JPG or WEBP. Maximum 2 MB.
                                    </small>

                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- LOCALIZATION --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-clock-o"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Localization
                                </h3>

                                <p class="settings-card-description">
                                    Time, date and record display.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="timezone"
                                >
                                    Timezone
                                </label>

                                <select
                                    id="timezone"
                                    name="timezone"
                                    class="form-control settings-control"
                                >
                                    <option
                                        value="Asia/Manila"
                                        {{ old('timezone', $settings->timezone ?? 'Asia/Manila') === 'Asia/Manila' ? 'selected' : '' }}
                                    >
                                        Asia/Manila (UTC+08:00)
                                    </option>
                                </select>

                                <small class="settings-help">
                                    Philippine time for attendance records.
                                </small>

                            </div>

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="date_format"
                                >
                                    Date Format
                                </label>

                                <select
                                    id="date_format"
                                    name="date_format"
                                    class="form-control settings-control"
                                >

                                    @foreach ([
                                        'F j, Y' => 'September 1, 2026',
                                        'M d, Y' => 'Sep 01, 2026',
                                        'm/d/Y' => '09/01/2026',
                                        'd/m/Y' => '01/09/2026'
                                    ] as $value => $label)

                                        <option
                                            value="{{ $value }}"
                                            {{ old('date_format', $settings->date_format ?? 'F j, Y') === $value ? 'selected' : '' }}
                                        >
                                            {{ $label }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="time_format"
                                >
                                    Time Format
                                </label>

                                <select
                                    id="time_format"
                                    name="time_format"
                                    class="form-control settings-control"
                                >
                                    <option
                                        value="h:i:s A"
                                        {{ old('time_format', $settings->time_format ?? 'h:i:s A') === 'h:i:s A' ? 'selected' : '' }}
                                    >
                                        10:05:24 AM (12-hour)
                                    </option>
                                </select>

                            </div>

                            <div class="form-group mb-0">

                                <label
                                    class="settings-label"
                                    for="records_per_page"
                                >
                                    Records per Page
                                </label>

                                <select
                                    id="records_per_page"
                                    name="records_per_page"
                                    class="form-control settings-control"
                                >

                                    @foreach ([10, 25, 50, 100] as $amount)

                                        <option
                                            value="{{ $amount }}"
                                            {{ (int) old('records_per_page', $settings->records_per_page ?? 10) === $amount ? 'selected' : '' }}
                                        >
                                            {{ $amount }} records
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </section>

                    {{-- ACADEMIC PERIOD --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Academic Period
                                </h3>

                                <p class="settings-card-description">
                                    Current school period information.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="academic_year"
                                >
                                    Academic Year
                                </label>

                                <input
                                    type="text"
                                    id="academic_year"
                                    name="academic_year"
                                    class="form-control settings-control"
                                    value="{{ old('academic_year', $settings->academic_year ?? '') }}"
                                    placeholder="2026-2027"
                                    maxlength="20"
                                >

                            </div>

                            <div class="form-group mb-0">

                                <label
                                    class="settings-label"
                                    for="semester"
                                >
                                    Semester
                                </label>

                                <select
                                    id="semester"
                                    name="semester"
                                    class="form-control settings-control"
                                >

                                    <option value="">
                                        Select semester
                                    </option>

                                    @foreach ([
                                        'First Semester',
                                        'Second Semester',
                                        'Summer'
                                    ] as $semester)

                                        <option
                                            value="{{ $semester }}"
                                            {{ old('semester', $settings->semester ?? '') === $semester ? 'selected' : '' }}
                                        >
                                            {{ $semester }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </section>

                    {{-- CONTACT INFORMATION --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-envelope"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Contact Information
                                </h3>

                                <p class="settings-card-description">
                                    Contact details shown to users.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="administrator_email"
                                >
                                    Administrator Email
                                </label>

                                <input
                                    type="email"
                                    id="administrator_email"
                                    name="administrator_email"
                                    class="form-control settings-control"
                                    value="{{ old('administrator_email', $settings->administrator_email ?? '') }}"
                                    maxlength="255"
                                >

                            </div>

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="learning_commons_email"
                                >
                                    Learning Commons Email
                                </label>

                                <input
                                    type="email"
                                    id="learning_commons_email"
                                    name="learning_commons_email"
                                    class="form-control settings-control"
                                    value="{{ old('learning_commons_email', $settings->learning_commons_email ?? '') }}"
                                    maxlength="255"
                                >

                            </div>

                            <div class="form-group">

                                <label
                                    class="settings-label"
                                    for="contact_number"
                                >
                                    Contact Number
                                </label>

                                <input
                                    type="text"
                                    id="contact_number"
                                    name="contact_number"
                                    class="form-control settings-control"
                                    value="{{ old('contact_number', $settings->contact_number ?? '') }}"
                                    maxlength="50"
                                >

                            </div>

                            <div class="form-group mb-0">

                                <label
                                    class="settings-label"
                                    for="office_address"
                                >
                                    Office Address
                                </label>

                                <textarea
                                    id="office_address"
                                    name="office_address"
                                    class="form-control settings-control"
                                    maxlength="500"
                                >{{ old('office_address', $settings->office_address ?? '') }}</textarea>

                            </div>

                        </div>

                    </section>

                    {{-- MAINTENANCE --}}
                    <section class="settings-card">

                        <div class="settings-card-header">

                            <div class="settings-card-icon">
                                <i class="fa fa-wrench"></i>
                            </div>

                            <div>
                                <h3 class="settings-card-title">
                                    Maintenance
                                </h3>

                                <p class="settings-card-description">
                                    Temporarily restrict system access.
                                </p>
                            </div>

                        </div>

                        <div class="settings-card-body">

                            <div class="settings-switch-row">

                                <div>
                                    <p class="settings-switch-title">
                                        Maintenance Mode
                                    </p>

                                    <p class="settings-switch-description">
                                        Only administrators should have access while enabled.
                                    </p>
                                </div>

                                <label class="settings-toggle">

                                    <input
                                        type="hidden"
                                        name="maintenance_mode"
                                        value="0"
                                    >

                                    <input
                                        type="checkbox"
                                        id="maintenance_mode"
                                        name="maintenance_mode"
                                        value="1"
                                        {{ old('maintenance_mode', $settings->maintenance_mode ?? false) ? 'checked' : '' }}
                                    >

                                    <span class="settings-toggle-slider"></span>

                                </label>

                            </div>

                            <div class="form-group mt-4 mb-0">

                                <label
                                    class="settings-label"
                                    for="maintenance_message"
                                >
                                    Maintenance Message
                                </label>

                                <textarea
                                    id="maintenance_message"
                                    name="maintenance_message"
                                    class="form-control settings-control"
                                    maxlength="1000"
                                >{{ old('maintenance_message', $settings->maintenance_message ?? 'The system is temporarily unavailable due to scheduled maintenance.') }}</textarea>

                            </div>

                        </div>

                    </section>

                </div>

            </div>

            {{-- SAVE BAR --}}
            <div class="settings-action-bar">

                <div>
                    <strong>Save System Settings</strong>

                    <br>

                    <small class="text-muted">
                        Changes will affect branding, attendance and enabled modules.
                    </small>
                </div>

                <div class="d-flex">

                    <button
                        type="reset"
                        class="btn settings-button settings-button-secondary mr-2"
                    >
                        <i class="fa fa-undo"></i>
                        Reset
                    </button>

                    <button
                        type="submit"
                        class="btn settings-button settings-button-primary"
                    >
                        <i class="fa fa-save"></i>
                        Save Settings
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('settingsForm');

    if (!form) {
        return;
    }

    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const logoFileName = document.getElementById('logoFileName');

    const alertContainer =
        document.getElementById('settingsAlertContainer');

    const automaticTimeOut =
        document.getElementById('automatic_time_out_enabled');

    const automaticTimeOutAt =
        document.getElementById('automatic_time_out_at');

    const maintenanceMode =
        document.getElementById('maintenance_mode');

    const maintenanceMessage =
        document.getElementById('maintenance_message');

    let originalLogo =
        logoPreview ? logoPreview.src : '';

    let temporaryLogoUrl = null;


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value).replace(/[&<>"']/g, function (character) {

            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return entities[character];
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    function showSettingsAlert(type, message, errors) {

        let html =
            '<div class="alert alert-' +
            type +
            ' settings-alert alert-dismissible fade show">';

        html += escapeHtml(message);

        html +=
            '<button type="button" class="close" data-dismiss="alert">' +
            '<span>&times;</span>' +
            '</button>';

        if (errors && Object.keys(errors).length) {

            html += '<ul class="mb-0 mt-2 pl-4">';

            Object.values(errors)
                .flat()
                .forEach(function (error) {

                    html +=
                        '<li>' +
                        escapeHtml(error) +
                        '</li>';

                });

            html += '</ul>';
        }

        html += '</div>';

        alertContainer.innerHTML = html;

        alertContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Automatic Time-Out
    |--------------------------------------------------------------------------
    */

    function updateAutomaticTimeOutField() {

        if (!automaticTimeOut || !automaticTimeOutAt) {
            return;
        }

        automaticTimeOutAt.disabled =
            !automaticTimeOut.checked;
    }

    if (automaticTimeOut) {

        automaticTimeOut.addEventListener(
            'change',
            updateAutomaticTimeOutField
        );

        updateAutomaticTimeOutField();
    }


    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    */

    function updateMaintenanceField() {

        if (!maintenanceMode || !maintenanceMessage) {
            return;
        }

        maintenanceMessage.disabled =
            !maintenanceMode.checked;
    }

    if (maintenanceMode) {

        maintenanceMode.addEventListener(
            'change',
            updateMaintenanceField
        );

        updateMaintenanceField();
    }


    /*
    |--------------------------------------------------------------------------
    | Logo Preview
    |--------------------------------------------------------------------------
    */

    if (logoInput) {

        logoInput.addEventListener('change', function () {

            const file =
                this.files && this.files[0];

            if (!file) {

                logoPreview.src =
                    originalLogo;

                logoFileName.textContent =
                    'PNG, JPG or WEBP. Maximum 2 MB.';

                return;
            }

            const allowedTypes = [
                'image/png',
                'image/jpeg',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {

                showSettingsAlert(
                    'danger',
                    'Please select a PNG, JPG or WEBP image.'
                );

                this.value = '';

                logoPreview.src =
                    originalLogo;

                logoFileName.textContent =
                    'PNG, JPG or WEBP. Maximum 2 MB.';

                return;
            }

            if (file.size > 2 * 1024 * 1024) {

                showSettingsAlert(
                    'danger',
                    'The logo must not be larger than 2 MB.'
                );

                this.value = '';

                logoPreview.src =
                    originalLogo;

                logoFileName.textContent =
                    'PNG, JPG or WEBP. Maximum 2 MB.';

                return;
            }

            if (temporaryLogoUrl) {

                URL.revokeObjectURL(
                    temporaryLogoUrl
                );
            }

            temporaryLogoUrl =
                URL.createObjectURL(file);

            logoPreview.src =
                temporaryLogoUrl;

            logoFileName.textContent =
                file.name;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    form.addEventListener('reset', function () {

        setTimeout(function () {

            if (temporaryLogoUrl) {

                URL.revokeObjectURL(
                    temporaryLogoUrl
                );

                temporaryLogoUrl = null;
            }

            if (logoInput) {
                logoInput.value = '';
            }

            if (logoPreview) {
                logoPreview.src = originalLogo;
            }

            if (logoFileName) {

                logoFileName.textContent =
                    'PNG, JPG or WEBP. Maximum 2 MB.';
            }

            updateAutomaticTimeOutField();
            updateMaintenanceField();

        }, 0);
    });


    /*
    |--------------------------------------------------------------------------
    | Save Settings
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', async function (event) {

        event.preventDefault();

        const submitButton =
            form.querySelector(
                'button[type="submit"]'
            );

        if (!submitButton) {
            return;
        }

        const originalButtonHtml =
            submitButton.innerHTML;

        submitButton.disabled = true;

        submitButton.innerHTML =
            '<i class="fa fa-spinner fa-spin"></i>' +
            ' Saving...';

        try {

            const response = await fetch(
                form.action,
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: new FormData(form)
                }
            );

            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';

            /*
             * Laravel may return an HTML page if
             * the session expires or an exception occurs.
             */
            if (!contentType.includes('application/json')) {

                throw new Error(
                    'The server returned an unexpected response. Please refresh the page and try again.'
                );
            }

            const data =
                await response.json();

            /*
             * Validation / server error
             */
            if (!response.ok) {

                showSettingsAlert(
                    'danger',
                    data.message ||
                    'The settings could not be saved.',
                    data.errors || {}
                );

                return;
            }

            const settings =
                data.settings || {};


            /*
             * Update summary cards
             */

            const summaryShortName =
                document.getElementById(
                    'summaryShortName'
                );

            const summaryTimezone =
                document.getElementById(
                    'summaryTimezone'
                );

            const summaryAcademicYear =
                document.getElementById(
                    'summaryAcademicYear'
                );

            const summaryRefreshSeconds =
                document.getElementById(
                    'summaryRefreshSeconds'
                );


            if (summaryShortName) {

                summaryShortName.textContent =
                    settings.system_short_name ||
                    'BARM';

                summaryShortName.title =
                    settings.system_title ||
                    '';
            }


            if (summaryTimezone) {

                summaryTimezone.textContent =
                    'UTC+08:00';
            }


            if (summaryAcademicYear) {

                summaryAcademicYear.textContent =
                    settings.academic_year ||
                    'Not set';
            }


            if (summaryRefreshSeconds) {

                summaryRefreshSeconds.textContent =
                    (settings.table_refresh_seconds || 3) +
                    ' seconds';
            }


            /*
             * Update logo
             */

            if (
                settings.logo_url &&
                logoPreview
            ) {

                if (temporaryLogoUrl) {

                    URL.revokeObjectURL(
                        temporaryLogoUrl
                    );

                    temporaryLogoUrl = null;
                }

                const separator =
                    settings.logo_url.includes('?')
                        ? '&'
                        : '?';

                const newLogoUrl =
                    settings.logo_url +
                    separator +
                    'v=' +
                    Date.now();

                originalLogo =
                    newLogoUrl;

                logoPreview.src =
                    newLogoUrl;

                logoInput.value = '';

                logoFileName.textContent =
                    'PNG, JPG or WEBP. Maximum 2 MB.';
            }


            /*
             * Allow other parts of the system
             * to receive the updated settings.
             */

            if (
                typeof window.publishBarmSystemSettings ===
                'function'
            ) {
                window.publishBarmSystemSettings(
                    settings
                );
            }


            showSettingsAlert(
                'success',
                data.message ||
                'System settings updated successfully.'
            );

        } catch (error) {

            console.error(
                'Settings save error:',
                error
            );

            showSettingsAlert(
                'danger',
                error.message ||
                'Unable to save the settings. Please try again.'
            );

        } finally {

            submitButton.disabled = false;

            submitButton.innerHTML =
                originalButtonHtml;
        }
    });

});
</script>

@include('layouts.footer')