@include('layouts.header')
@include('layouts.css')

<style>
    :root {
        --policy-primary: #d6538c;
        --policy-primary-dark: #bd3f77;
        --policy-primary-soft: #fff2f7;
        --policy-primary-softer: #fff8fb;
        --policy-page: #f7f7fb;
        --policy-surface: #ffffff;
        --policy-border: #ebe7ed;
        --policy-border-soft: #f2eff3;
        --policy-text: #292631;
        --policy-muted: #797482;
        --policy-shadow: 0 10px 30px rgba(42, 35, 48, .06);
        --policy-shadow-hover: 0 14px 35px rgba(42, 35, 48, .09);
    }

    .policy-page {
        color: var(--policy-text);
    }

    /* ═══════════════════════════════════════
       PAGE TITLES
       ═══════════════════════════════════════ */
    .policy-page .page-titles {
        align-items: center;
        margin-bottom: 24px !important;
        padding: 22px 24px;
        border: 1px solid var(--policy-border);
        border-radius: 18px;
        background: linear-gradient(115deg, #fff 0%, #fff8fb 100%);
        box-shadow: var(--policy-shadow);
    }

    .policy-page .welcome-text h4 {
        margin-bottom: 5px;
        color: var(--policy-text);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -.35px;
    }

    .policy-page .welcome-text span {
        color: var(--policy-muted);
        font-size: 13px;
    }

    .policy-page .breadcrumb {
        margin: 0;
        padding: 9px 13px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .78);
    }

    .policy-page .breadcrumb-item a {
        color: var(--policy-primary);
        font-weight: 600;
    }

    /* ═══════════════════════════════════════
       ALERTS
       ═══════════════════════════════════════ */
    .policy-page .alert {
        border: 1px solid var(--policy-border);
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(42, 35, 48, .04);
    }

    .policy-page .alert-success {
        border-color: #cdeedb;
        background: #eaf8ef;
        color: #218143;
    }

    .policy-page .alert-danger {
        border-color: #f3cbd7;
        background: #ffeded;
        color: #bd3434;
    }

    /* ═══════════════════════════════════════
       CARDS
       ═══════════════════════════════════════ */
    .policy-summary-card,
    .policy-card {
        overflow: hidden;
        border: 1px solid var(--policy-border);
        border-radius: 18px;
        background: var(--policy-surface);
        box-shadow: var(--policy-shadow);
    }

    /* ═══════════════════════════════════════
       SUMMARY CARDS
       ═══════════════════════════════════════ */
    .policy-summary-card {
        height: calc(100% - 30px);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .policy-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--policy-shadow-hover);
    }

    .policy-summary-card .card-body {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 100px;
        padding: 22px 24px;
    }

    .summary-icon {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--policy-primary) 0%, var(--policy-primary-dark) 100%);
        color: #fff;
        font-size: 18px;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .summary-value {
        margin: 0;
        color: var(--policy-text);
        font-size: 24px;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -.35px;
    }

    .policy-summary-card .text-muted {
        display: block;
        margin-top: 4px;
        color: var(--policy-muted) !important;
        font-size: 12px;
        font-weight: 500;
    }

    /* ═══════════════════════════════════════
       POLICY CARD HEADERS
       ═══════════════════════════════════════ */
    .policy-card {
        margin-bottom: 24px;
    }

    .policy-card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        padding: 22px 26px;
        border-bottom: 1px solid var(--policy-border);
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .policy-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 4px;
        color: var(--policy-text);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .policy-section-title i {
        color: var(--policy-primary);
        font-size: 16px;
    }

    .policy-section-title i.text-danger {
        color: #d5435b !important;
    }

    .policy-section-description {
        margin: 0;
        color: var(--policy-muted);
        font-size: 12px;
        font-weight: 400;
    }

    .policy-card .card-body {
        padding: 24px 26px;
    }

    /* ═══════════════════════════════════════
       FORM FIELDS
       ═══════════════════════════════════════ */
    .policy-group {
        margin-bottom: 20px;
    }

    .policy-label {
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

    .policy-label i {
        width: auto;
        color: var(--policy-primary);
        font-size: 11px;
        opacity: .85;
        text-align: center;
    }

    .policy-input {
        min-height: 44px;
        border: 1px solid #dfdbe2;
        border-radius: 11px;
        background-color: #fff;
        color: var(--policy-text);
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .policy-input:hover {
        border-color: #c9c2ce;
    }

    .policy-input:focus {
        border-color: var(--policy-primary);
        background-color: var(--policy-primary-softer);
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .12);
    }

    /* Rounded input groups */
    .input-group > .policy-input:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group > .policy-input:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group-text {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 60px;
        border: 1px solid #dfdbe2;
        background: #f8f7fa;
        color: #625c68;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .input-group > .input-group-prepend > .input-group-text {
        border-top-left-radius: 11px;
        border-bottom-left-radius: 11px;
        border-right: 0;
    }

    .input-group > .input-group-append > .input-group-text {
        border-top-right-radius: 11px;
        border-bottom-right-radius: 11px;
        border-left: 0;
    }

    .validation-error {
        display: block;
        margin-top: 6px;
        color: #bd3434;
        font-size: 12px;
        font-weight: 500;
    }

    /* ═══════════════════════════════════════
       FINES ROW — equal-width, bottom-aligned
       ═══════════════════════════════════════ */
    .policy-fine-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        margin: 0 -12px;
    }

    .policy-fine-row > .policy-fine-column {
        flex: 1 1 0;
        min-width: 200px;
        padding: 0 12px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .policy-fine-row .policy-group {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        height: 100%;
        margin-bottom: 0;
    }

    .policy-fine-row .policy-label {
        min-height: 32px;
        align-items: flex-start;
    }

    .policy-fine-row .input-group {
        margin-top: auto;
    }

    /* ═══════════════════════════════════════
       SWITCHES
       ═══════════════════════════════════════ */
    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 82px;
        margin-bottom: 12px;
        padding: 18px 20px;
        border: 1px solid var(--policy-border);
        border-radius: 14px;
        background: #fdfcfe;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .switch-container:hover {
        border-color: #f3cbd7;
        background: var(--policy-primary-softer);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .06);
    }

    .switch-container:last-child {
        margin-bottom: 0;
    }

    .switch-information {
        padding-right: 15px;
    }

    .switch-title {
        display: block;
        margin-bottom: 4px;
        color: var(--policy-text);
        font-size: 14px;
        font-weight: 700;
    }

    .switch-description {
        margin: 0;
        color: var(--policy-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .policy-switch {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
        width: 52px;
        min-width: 52px;
        height: 28px;
    }

    .policy-switch input {
        width: 0;
        height: 0;
        opacity: 0;
    }

    .policy-slider {
        position: absolute;
        inset: 0;
        border-radius: 30px;
        background: #d5cdd9;
        cursor: pointer;
        transition: background .25s ease, box-shadow .25s ease;
    }

    .policy-slider::before {
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

    .policy-switch input:checked + .policy-slider {
        background: linear-gradient(135deg, var(--policy-primary) 0%, var(--policy-primary-dark) 100%);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .28);
    }

    .policy-switch input:checked + .policy-slider::before {
        transform: translateX(24px);
    }

    .policy-switch input:focus + .policy-slider {
        box-shadow: 0 0 0 .2rem rgba(213, 91, 145, .18);
    }

    /* ═══════════════════════════════════════
       ACTION CARD
       ═══════════════════════════════════════ */
    .policy-actions-card {
        margin-bottom: 0;
    }

    .policy-actions-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        padding: 22px 26px;
        background: linear-gradient(115deg, #fdfcfe 0%, #fff8fb 100%);
    }

    .policy-actions-card strong {
        color: var(--policy-text);
        font-size: 14px;
        font-weight: 700;
    }

    .policy-actions-card .text-muted {
        color: var(--policy-muted);
        font-size: 12px;
    }

    /* ═══════════════════════════════════════
       BUTTONS
       ═══════════════════════════════════════ */
    .save-policy-button,
    .reset-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 22px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .save-policy-button {
        border: 0;
        background: linear-gradient(135deg, var(--policy-primary) 0%, var(--policy-primary-dark) 100%);
        color: #fff !important;
        box-shadow: 0 6px 16px rgba(213, 91, 145, .28);
    }

    .save-policy-button:hover,
    .save-policy-button:focus {
        background: linear-gradient(135deg, var(--policy-primary-dark) 0%, #a83468 100%);
        color: #fff !important;
        box-shadow: 0 8px 22px rgba(213, 91, 145, .4);
        transform: translateY(-1px);
        outline: none;
    }

    .save-policy-button:active {
        transform: translateY(0);
    }

    .reset-button {
        border: 1px solid #e5dfe8;
        background: #fff;
        color: #68616e;
    }

    .reset-button i {
        font-size: 12px;
        transition: transform .3s ease;
    }

    .reset-button:hover,
    .reset-button:focus {
        border-color: #f3cbd7;
        background: var(--policy-primary-soft);
        color: var(--policy-primary-dark);
        box-shadow: 0 4px 12px rgba(213, 91, 145, .12);
        transform: translateY(-1px);
        outline: none;
    }

    .reset-button:hover i {
        transform: rotate(-90deg);
    }

    .reset-button:active {
        transform: translateY(0);
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════ */
    @media (max-width: 767px) {
        .policy-page .page-titles {
            padding: 18px;
        }

        .policy-card .card-header,
        .policy-card .card-body {
            padding-right: 18px;
            padding-left: 18px;
        }

        .policy-summary-card .card-body {
            padding: 18px;
        }

        .policy-actions-card .card-body {
            flex-direction: column;
            align-items: stretch;
            padding: 18px;
        }

        .policy-actions-card .card-body > div:last-child {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .save-policy-button,
        .reset-button {
            width: 100%;
        }

        .reset-button {
            margin-right: 0 !important;
        }

        .switch-container {
            align-items: flex-start;
        }

        .policy-fine-row {
            margin: 0;
        }

        .policy-fine-row > .policy-fine-column {
            flex: 1 1 100%;
            padding: 0;
            margin-bottom: 20px;
        }

        .policy-fine-row > .policy-fine-column:last-child {
            margin-bottom: 0;
        }

        .policy-fine-row .policy-label {
            min-height: 0;
        }
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

@php
    /*
     * Reusable number fields.
     */
    $borrowingFields = [
        [
            'name' => 'student_borrowing_limit',
            'label' => 'Student Borrowing Limit',
            'icon' => 'fa-graduation-cap',
            'unit' => 'books',
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ],
        [
            'name' => 'student_loan_period_days',
            'label' => 'Student Loan Period',
            'icon' => 'fa-calendar',
            'unit' => 'days',
            'min' => 1,
            'max' => 365,
            'step' => 1,
        ],
        [
            'name' => 'personnel_borrowing_limit',
            'label' => 'Personnel Borrowing Limit',
            'icon' => 'fa-id-badge',
            'unit' => 'books',
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ],
        [
            'name' => 'personnel_standard_return_days',
            'label' => 'Personnel Standard Return Period',
            'icon' => 'fa-calendar-check-o',
            'unit' => 'days',
            'min' => 1,
            'max' => 365,
            'step' => 1,
        ],
    ];

    $reservationFields = [
        [
            'name' => 'reserved_book_loan_period_hours',
            'label' => 'Reserved-Book Loan Period',
            'icon' => 'fa-clock-o',
            'unit' => 'hours',
            'min' => 1,
            'max' => 168,
            'step' => 1,
        ],
        [
            'name' => 'reservation_expiration_period_days',
            'label' => 'Reservation Expiration Period',
            'icon' => 'fa-hourglass-end',
            'unit' => 'days',
            'min' => 1,
            'max' => 365,
            'step' => 1,
        ],
        [
            'name' => 'maximum_active_reservations',
            'label' => 'Maximum Active Reservations',
            'icon' => 'fa-list-ol',
            'unit' => 'reservations',
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ],
    ];

    $fineFields = [
        [
            'name' => 'fine_per_overdue_day',
            'label' => 'General Fine per Overdue Day',
            'icon' => 'fa-money',
            'prefix' => '₱',
            'unit' => '',
            'min' => 0,
            'max' => 999999.99,
            'step' => 0.01,
        ],
        [
            'name' => 'reserved_book_fine_per_hour',
            'label' => 'Reserved-Book Fine per Hour',
            'icon' => 'fa-clock-o',
            'prefix' => '₱',
            'unit' => '',
            'min' => 0,
            'max' => 999999.99,
            'step' => 0.01,
        ],
        [
            'name' => 'fine_grace_period_days',
            'label' => 'Fine Grace Period',
            'icon' => 'fa-hourglass-half',
            'prefix' => '',
            'unit' => 'days',
            'min' => 0,
            'max' => 365,
            'step' => 1,
        ],
        [
            'name' => 'lost_book_processing_fee',
            'label' => 'Lost-Book Processing Fee',
            'icon' => 'fa-times-circle',
            'prefix' => '₱',
            'unit' => '',
            'min' => 0,
            'max' => 999999.99,
            'step' => 0.01,
        ],
        [
            'name' => 'damaged_book_penalty',
            'label' => 'Damaged-Book Penalty',
            'icon' => 'fa-warning',
            'prefix' => '₱',
            'unit' => '',
            'min' => 0,
            'max' => 999999.99,
            'step' => 0.01,
        ],
    ];

    $switches = [
        [
            'name' => 'require_fingerprint_for_borrowing',
            'title' => 'Require Fingerprint Scan to Borrow, Return, or Reserve',
            'description' =>
                'When enabled, users must scan their fingerprint before borrowing, returning, or reserving a book.',
        ],
        [
            'name' => 'allow_personnel_fine_exemption',
            'title' => 'Allow Personnel Fine Exemption',
            'description' =>
                'Allows qualified faculty transactions to be exempted from overdue fines.',
        ],
        [
            'name' => 'allow_reserved_book_renewal',
            'title' => 'Allow Reserved-Book Renewal',
            'description' =>
                'Allows a reserved book to be renewed when renewal requirements are satisfied.',
        ],
        [
            'name' => 'renew_reserved_only_without_request',
            'title' => 'Renew Only When There Is No Request',
            'description' =>
                'Prevents renewal when another borrower has requested the reserved book.',
        ],
        [
            'name' => 'lost_book_same_title_required',
            'title' => 'Require the Same Book Title',
            'description' =>
                'A physical replacement must have the same title as the lost book.',
        ],
        [
            'name' => 'lost_book_same_edition_required',
            'title' => 'Require the Same Book Edition',
            'description' =>
                'A physical replacement must use the same edition when available.',
        ],
        [
            'name' => 'use_current_price_for_lost_book',
            'title' => 'Use Current Price for Lost Books',
            'description' =>
                'Charge the current valid book price when physical replacement is unavailable.',
        ],
        [
            'name' => 'allow_borrowing_with_unpaid_fines',
            'title' => 'Allow Borrowing with Unpaid Fines',
            'description' =>
                'When disabled, borrowers must settle unpaid fines before borrowing again.',
        ],
    ];
@endphp

<div class="content-body policy-page">
    <div class="container-fluid py-4">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Library Policy</h4>
                    <span>Manage borrowing, reservation and fine policies</span>
                </div>
            </div>

            <div
                class="col-sm-6 p-md-0
                       justify-content-sm-end
                       mt-2 mt-sm-0 d-flex"
            >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('policy.index') }}">Library Policy</a>
                    </li>
                    <li class="breadcrumb-item active">Policy</li>
                </ol>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
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

        {{-- VALIDATION SUMMARY --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle mr-2"></i>
                Please correct the highlighted policy values.
            </div>
        @endif

        {{-- SUMMARY CARDS --}}
        <div class="row">

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card policy-summary-card">
                    <div class="card-body">
                        <div class="summary-icon">
                            <i class="fa fa-graduation-cap"></i>
                        </div>

                        <div>
                            <p class="summary-value">
                                {{
                                    old(
                                        'student_borrowing_limit',
                                        $policy->student_borrowing_limit
                                    )
                                }}
                            </p>

                            <span class="text-muted">
                                Student book limit
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card policy-summary-card">
                    <div class="card-body">
                        <div class="summary-icon">
                            <i class="fa fa-id-badge"></i>
                        </div>

                        <div>
                            <p class="summary-value">
                                {{
                                    old(
                                        'personnel_borrowing_limit',
                                        $policy->personnel_borrowing_limit
                                    )
                                }}
                            </p>

                            <span class="text-muted">
                                Personnel book limit
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card policy-summary-card">
                    <div class="card-body">
                        <div class="summary-icon">
                            <i class="fa fa-calendar"></i>
                        </div>

                        <div>
                            <p class="summary-value">
                                {{
                                    old(
                                        'student_loan_period_days',
                                        $policy->student_loan_period_days
                                    )
                                }}
                                days
                            </p>

                            <span class="text-muted">
                                Student loan period
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card policy-summary-card">
                    <div class="card-body">
                        <div class="summary-icon">
                            <i class="fa fa-money"></i>
                        </div>

                        <div>
                            <p class="summary-value">
                                ₱{{
                                    number_format(
                                        (float) old(
                                            'fine_per_overdue_day',
                                            $policy->fine_per_overdue_day
                                        ),
                                        2
                                    )
                                }}
                            </p>

                            <span class="text-muted">
                                General daily fine
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <form
            action="{{ route('policy.update') }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            {{-- BORROWING POLICY --}}
            <div class="card policy-card">
                <div class="card-header">
                    <div>
                        <h4 class="policy-section-title">
                            <i class="fa fa-book"></i>
                            Borrowing Policy
                        </h4>

                        <p class="policy-section-description">
                            Configure student and personnel borrowing rules.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">

                        @foreach($borrowingFields as $field)
                            <div class="col-lg-3 col-md-6">
                                <div class="policy-group">
                                    <label class="policy-label">
                                        <i class="fa {{ $field['icon'] }}"></i>
                                        {{ $field['label'] }}
                                    </label>

                                    <div class="input-group">
                                        <input
                                            type="number"
                                            name="{{ $field['name'] }}"
                                            class="form-control policy-input"
                                            min="{{ $field['min'] }}"
                                            max="{{ $field['max'] }}"
                                            step="{{ $field['step'] }}"
                                            required
                                            value="{{
                                                old(
                                                    $field['name'],
                                                    $policy->{$field['name']}
                                                )
                                            }}"
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                {{ $field['unit'] }}
                                            </span>
                                        </div>
                                    </div>

                                    @error($field['name'])
                                        <span class="validation-error">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                        {{-- PERSONNEL LOAN VALUE --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="policy-group">
                                <label class="policy-label">
                                    <i class="fa fa-calendar-o"></i>
                                    Personnel Loan Period
                                </label>

                                <input
                                    type="number"
                                    name="personnel_loan_period_value"
                                    class="form-control policy-input"
                                    min="1"
                                    max="365"
                                    required
                                    value="{{
                                        old(
                                            'personnel_loan_period_value',
                                            $policy->personnel_loan_period_value
                                        )
                                    }}"
                                >

                                @error('personnel_loan_period_value')
                                    <span class="validation-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- PERSONNEL LOAN UNIT --}}
                        <div class="col-lg-3 col-md-6">
                            <div class="policy-group">
                                <label class="policy-label">
                                    <i class="fa fa-calendar-o"></i>
                                    Personnel Period Unit
                                </label>

                                <select
                                    name="personnel_loan_period_unit"
                                    class="form-control policy-input"
                                    required
                                >
                                    @foreach(
                                        [
                                            'days' => 'Days',
                                            'months' => 'Months',
                                            'semester' => 'Semester',
                                        ]
                                        as $value => $label
                                    )
                                        <option
                                            value="{{ $value }}"
                                            {{
                                                old(
                                                    'personnel_loan_period_unit',
                                                    $policy->personnel_loan_period_unit
                                                ) === $value
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('personnel_loan_period_unit')
                                    <span class="validation-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RESERVATION POLICY --}}
            <div class="card policy-card">
                <div class="card-header">
                    <div>
                        <h4 class="policy-section-title">
                            <i class="fa fa-bookmark"></i>
                            Reserved Book and Reservation Policy
                        </h4>

                        <p class="policy-section-description">
                            Configure hourly reserved-book borrowing and
                            reservation limits.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">

                        @foreach($reservationFields as $field)
                            <div class="col-lg-4 col-md-6">
                                <div class="policy-group">
                                    <label class="policy-label">
                                        <i class="fa {{ $field['icon'] }}"></i>
                                        {{ $field['label'] }}
                                    </label>

                                    <div class="input-group">
                                        <input
                                            type="number"
                                            name="{{ $field['name'] }}"
                                            class="form-control policy-input"
                                            min="{{ $field['min'] }}"
                                            max="{{ $field['max'] }}"
                                            step="{{ $field['step'] }}"
                                            required
                                            value="{{
                                                old(
                                                    $field['name'],
                                                    $policy->{$field['name']}
                                                )
                                            }}"
                                        >

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                {{ $field['unit'] }}
                                            </span>
                                        </div>
                                    </div>

                                    @error($field['name'])
                                        <span class="validation-error">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- FINES --}}
            <div class="card policy-card">
                <div class="card-header">
                    <div>
                        <h4 class="policy-section-title">
                            <i class="fa fa-exclamation-triangle text-danger"></i>
                            Fines and Penalties
                        </h4>

                        <p class="policy-section-description">
                            Configure overdue, reserved, lost and damaged
                            book charges.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="policy-fine-row">

                        @foreach($fineFields as $field)
                            <div class="policy-fine-column">
                                <div class="policy-group">
                                    <label class="policy-label">
                                        <i class="fa {{ $field['icon'] }}"></i>
                                        {{ $field['label'] }}
                                    </label>

                                    <div class="input-group">
                                        @if($field['prefix'])
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    {{ $field['prefix'] }}
                                                </span>
                                            </div>
                                        @endif

                                        <input
                                            type="number"
                                            name="{{ $field['name'] }}"
                                            class="form-control policy-input"
                                            min="{{ $field['min'] }}"
                                            max="{{ $field['max'] }}"
                                            step="{{ $field['step'] }}"
                                            required
                                            value="{{
                                                old(
                                                    $field['name'],
                                                    $policy->{$field['name']}
                                                )
                                            }}"
                                        >

                                        @if($field['unit'])
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    {{ $field['unit'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @error($field['name'])
                                        <span class="validation-error">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- RULE SWITCHES --}}
            <div class="card policy-card">
                <div class="card-header">
                    <div>
                        <h4 class="policy-section-title">
                            <i class="fa fa-toggle-on"></i>
                            Policy Rules
                        </h4>

                        <p class="policy-section-description">
                            Enable or disable additional borrowing rules.
                        </p>
                    </div>
                </div>

                <div class="card-body">

                    @foreach($switches as $switch)
                        @php
                            /*
                             * Determine the switch state.
                             *
                             * - If there is old input (after validation error),
                             *   use that value.
                             * - Otherwise, use the saved value from the database.
                             *
                             * The value is normalized to a boolean so that
                             * "0", "1", 0, 1, true, false all work correctly.
                             */
                            if (old($switch['name']) !== null) {
                                $switchValue = old($switch['name']);
                            } else {
                                $switchValue = $policy->{$switch['name']} ?? 0;
                            }

                            $switchIsOn = in_array(
                                $switchValue,
                                [1, '1', true, 'true', 'on', 'yes'],
                                true
                            );
                        @endphp

                        <input
                            type="hidden"
                            name="{{ $switch['name'] }}"
                            value="0"
                        >

                        <div class="switch-container">
                            <div class="switch-information">
                                <span class="switch-title">
                                    {{ $switch['title'] }}
                                </span>

                                <p class="switch-description">
                                    {{ $switch['description'] }}
                                </p>
                            </div>

                            <label class="policy-switch">
                                <input
                                    type="checkbox"
                                    name="{{ $switch['name'] }}"
                                    value="1"
                                    {{ $switchIsOn ? 'checked' : '' }}
                                >

                                <span class="policy-slider"></span>
                            </label>
                        </div>

                        @error($switch['name'])
                            <span class="validation-error mb-3">
                                {{ $message }}
                            </span>
                        @enderror
                    @endforeach

                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="card policy-card policy-actions-card">
                <div class="card-body">
                    <div>
                        <strong>Save Policy Changes</strong>
                        <br>
                        <small class="text-muted">
                            Changes will affect future borrowing,
                            reservations and fine calculations.
                        </small>
                    </div>

                    <div>
                        <button
                            type="reset"
                            class="btn reset-button mr-2"
                        >
                            <i class="fa fa-undo"></i>
                            Reset
                        </button>

                        <button
                            type="submit"
                            class="btn save-policy-button"
                        >
                            <i class="fa fa-save"></i>
                            Save Policies
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

@include('layouts.footer')