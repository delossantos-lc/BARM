@php
    $systemSettings = $systemSettings ?? App\Models\SystemSetting::current();

    /*
     * Fingerprint policy flag.
     * When true  → borrower MUST verify fingerprint in STEP 1 before proceeding.
     * When false → fingerprint is skipped entirely; Step 1 → Step 2 → Step 3.
     */
    $libraryPolicy = App\Models\LibraryPolicy::current();
    $fingerprintRequired = (bool) (
        $libraryPolicy?->require_fingerprint_for_borrowing ?? false
    );

    /*
     * Full library policy snapshot for the current session.
     */
    $policySnapshot = [
        'borrowing_limit'                  => (int) ($libraryPolicy?->borrowing_limit ?? 3),
        'max_books_per_transaction'        => (int) ($libraryPolicy?->max_books_per_transaction ?? 3),
        'borrowing_period_days'            => (int) ($libraryPolicy?->borrowing_period_days ?? 7),
        'require_fingerprint_for_borrowing'=> (bool) ($libraryPolicy?->require_fingerprint_for_borrowing ?? false),
        'allow_student_borrowing'          => (bool) ($libraryPolicy?->allow_student_borrowing ?? true),
        'allow_personnel_borrowing'        => (bool) ($libraryPolicy?->allow_personnel_borrowing ?? true),
        'allow_renewal'                    => (bool) ($libraryPolicy?->allow_renewal ?? true),
        'max_renewals'                     => (int) ($libraryPolicy?->max_renewals ?? 1),
    ];

    $borrowStoreUrl = \Illuminate\Support\Facades\Route::has('borrow.store')
        ? route('borrow.store')
        : url('/borrow');
    $borrowExitUrl = \Illuminate\Support\Facades\Route::has('borrow.exit')
        ? route('borrow.exit')
        : url('/borrow/exit');
    $borrowerFindByRfidUrl = \Illuminate\Support\Facades\Route::has('borrowers.findByRfid')
        ? route('borrowers.findByRfid', ['rfid' => '__RFID__'])
        : url('/borrowers/find-by-rfid/__RFID__');

    $hasFingerprintComponent = view()->exists('components.fingerprint-transaction-verification');

    // Auto-reload interval in seconds (default 60 seconds, configurable via system settings)
    $autoReloadSeconds = max(10, (int) ($systemSettings->kiosk_auto_reload_seconds ?? 60));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Borrow Books | {{ $systemSettings->system_short_name ?? 'BARM' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --pink-background: #f8dadd;
            --pink-dark: #d76091;
            --pink-light: #fff2f6;
            --pink-hover: #c64f80;
            --gray: #737d86;
            --gray-hover: #616a72;
            --text-dark: #202124;
            --muted: #687079;
            --border: #d9dee3;
            --white: #ffffff;
            --shadow: 0 8px 18px rgba(115, 75, 84, 0.09);
            --radius: 18px;
            --radius-sm: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%; width: 100%; overflow: hidden;
            background: var(--pink-background); color: var(--text-dark);
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 16px; line-height: 1.4;
        }

        .kiosk-shell {
            height: 100vh; width: 100vw;
            display: flex; flex-direction: column; overflow: hidden;
        }

        .kiosk-container {
            width: min(98%, 1400px); height: 100%;
            margin: 0 auto; padding: 6px 8px 8px;
            display: flex; flex-direction: column; overflow: hidden;
        }

        .top-bar {
            flex-shrink: 0; display: flex;
            justify-content: space-between; align-items: center;
            padding: 4px 0 16px; gap: 12px;
        }

        .top-left { display: flex; align-items: center; gap: 12px; }

        .top-left-title {
            font-size: clamp(1rem, 2.2vw, 1.8rem);
            font-weight: 900; letter-spacing: 1.5px;
            text-transform: uppercase; color: #242424; margin: 0;
        }

        .top-left-logo {
            display: block; width: clamp(38px, 6vh, 64px);
            max-height: clamp(34px, 5.5vh, 58px); object-fit: contain;
        }

        .top-right { display: flex; align-items: center; gap: 8px; }

        .step-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: clamp(4px, 0.6vw, 8px) clamp(10px, 1.2vw, 18px);
            border-radius: 999px; background: var(--pink-dark); color: #fff;
            font-weight: 800; font-size: clamp(0.7rem, 0.9vw, 0.9rem);
            box-shadow: 0 3px 10px rgba(215, 96, 145, 0.35);
            white-space: nowrap; letter-spacing: 0.3px;
        }

        .step-badge i { font-size: 0.9em; }

        .main-content {
            flex: 7; min-height: 0; display: flex;
            flex-direction: column; overflow: hidden;
            margin-top: 4px; margin-bottom: 6px;
        }

        #identityStep {
            flex: 1; min-height: 0; display: flex;
            flex-direction: column;
            padding: clamp(10px, 1.5vw, 22px) clamp(12px, 1.8vw, 28px);
            background: var(--white); border-radius: var(--radius);
            box-shadow: var(--shadow); overflow: hidden;
        }

        .identity-two-columns {
            display: flex; gap: 20px; align-items: stretch;
            flex: 1; min-height: 0;
        }

        .identity-left-col {
            flex: 6; display: flex; flex-direction: column;
            gap: 10px; min-width: 0; overflow-y: auto;
        }

        .identity-right-col {
            flex: 5; display: flex; flex-direction: column;
            gap: 8px; min-width: 0;
            border-left: 2px solid #f0e0e6; padding-left: 20px;
            overflow-y: auto;
        }

        .section-title {
            font-size: clamp(1rem, 1.6vw, 1.3rem);
            font-weight: 800; color: #202020; margin-bottom: 2px;
        }

        .section-description {
            font-size: clamp(0.7rem, 0.95vw, 0.9rem);
            color: var(--muted); margin-bottom: 6px;
        }

        .scan-area {
            padding: clamp(12px, 2vw, 24px) clamp(12px, 2vw, 24px);
            border: 2px dashed var(--pink-dark);
            border-radius: 14px;
            background: linear-gradient(135deg, #fff8fa, #fde5ed);
            text-align: center; flex-shrink: 0;
        }

        .scan-icon {
            width: clamp(40px, 5vw, 64px); height: clamp(40px, 5vw, 64px);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px; border-radius: 50%;
            background: var(--pink-dark); color: #fff;
            font-size: clamp(1.1rem, 2vw, 1.8rem);
            box-shadow: 0 4px 12px rgba(215, 96, 145, 0.3);
        }

        .scan-heading {
            font-size: clamp(0.9rem, 1.3vw, 1.2rem);
            font-weight: 800; margin-bottom: 4px;
        }

        .scan-instruction {
            font-size: clamp(0.7rem, 0.95vw, 0.85rem);
            color: var(--muted); margin-bottom: 8px;
        }

        .rfid-input-wrapper { width: min(100%, 520px); margin: 0 auto; }

        .large-input {
            min-height: clamp(38px, 5vh, 52px);
            padding: clamp(8px, 1vw, 12px) clamp(10px, 1.2vw, 16px);
            border: 1px solid var(--border); border-radius: 10px;
            background: #fff; font-size: clamp(0.8rem, 1.1vw, 1rem);
            width: 100%; transition: border-color 0.2s, box-shadow 0.2s;
        }

        .large-input:focus {
            border-color: var(--pink-dark);
            box-shadow: 0 0 0 3px rgba(215, 96, 145, 0.13);
            outline: none;
        }

        .rfid-status {
            min-height: 20px; margin-top: 6px;
            font-size: clamp(0.7rem, 0.95vw, 0.85rem);
            font-weight: 700;
        }

        .borrower-found {
            padding: 4px 10px; border-radius: 6px;
            background: #e9f8ee; color: #18763c;
        }

        .borrower-error {
            padding: 4px 10px; border-radius: 6px;
            background: #ffeded; color: #bd3434;
        }

        .fingerprint-message {
            display: none; padding: 14px 18px;
            border-radius: var(--radius-sm); margin-top: 8px;
            text-align: center;
            transition: background 0.25s ease, border-color 0.25s ease;
        }

        .fingerprint-message.show { display: block; }

        .fingerprint-message.state-ready {
            background: linear-gradient(135deg, #e8f4fd, #d6ebf9);
            border: 2px solid #90c8e8;
        }
        .fingerprint-message.state-ready .fp-title { color: #1a5e8a; }
        .fingerprint-message.state-ready .fp-sub { color: #3a7ca5; }

        .fingerprint-message.state-error {
            background: linear-gradient(135deg, #fde8e8, #fbd5d5);
            border: 2px solid #e88a8a;
        }
        .fingerprint-message.state-error .fp-title { color: #a93226; }
        .fingerprint-message.state-error .fp-sub { color: #c0392b; }

        .fingerprint-message.state-conn {
            background: linear-gradient(135deg, #fef8e8, #fdeec8);
            border: 2px solid #e8c88a;
        }
        .fingerprint-message.state-conn .fp-title { color: #8a6d1a; }
        .fingerprint-message.state-conn .fp-sub { color: #a8882a; }

        .fingerprint-message .fp-title {
            font-weight: 800; font-size: clamp(0.85rem, 1.1vw, 1rem);
        }

        .fingerprint-message .fp-sub {
            font-size: clamp(0.68rem, 0.85vw, 0.8rem); margin-top: 3px;
        }

        .fingerprint-message .fp-actions { margin-top: 10px; }

        .btn-retry, .btn-retry-conn {
            color: #fff; border: none; border-radius: 8px;
            padding: 7px 22px; font-weight: 700;
            font-size: clamp(0.68rem, 0.85vw, 0.8rem);
            cursor: pointer; transition: background 0.15s;
        }

        .btn-retry { background: #c0392b; }
        .btn-retry:hover { background: #a93226; }
        .btn-retry-conn { background: #d4a017; }
        .btn-retry-conn:hover { background: #b88a12; }

        .fingerprint-hidden { display: none; }

        .user-info-card {
            background: linear-gradient(135deg, #fef9fb, #fff5f8);
            border-radius: var(--radius-sm); padding: 14px 18px;
            border: 1px solid #f0d9e2;
            box-shadow: 0 2px 8px rgba(215, 96, 145, 0.06);
            flex: 1; min-height: 0; overflow-y: auto;
        }

        .user-info-card .info-row {
            display: flex; justify-content: space-between;
            align-items: baseline; padding: 8px 0;
            border-bottom: 1px dashed #eddae1;
        }

        .user-info-card .info-row:last-child { border-bottom: none; }

        .user-info-card .info-label {
            font-size: clamp(0.6rem, 0.75vw, 0.75rem);
            font-weight: 700; color: #6b5b63;
            text-transform: uppercase; letter-spacing: 0.3px;
        }

        .user-info-card .info-value {
            font-size: clamp(0.8rem, 1vw, 0.95rem);
            font-weight: 800; color: #202124;
            text-align: right; word-break: break-word;
        }

        .user-info-card .info-value.placeholder {
            color: #b0a8ad; font-weight: 500;
        }

        .user-info-card .user-avatar {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 10px; padding-bottom: 10px;
            border-bottom: 2px solid #f0d9e2;
        }

        .user-info-card .user-avatar i {
            font-size: 2.2rem; color: var(--pink-dark);
            background: #fff; border-radius: 50%; padding: 8px;
            box-shadow: 0 2px 8px rgba(215, 96, 145, 0.15);
        }

        .user-info-card .user-avatar .avatar-text {
            font-weight: 800; font-size: 1.1rem; color: #202124;
        }

        .user-info-card .user-avatar .avatar-sub {
            font-size: 0.8rem; color: var(--muted);
        }

        .back-row {
            flex-shrink: 0; display: flex;
            justify-content: flex-start; padding: 4px 0 2px;
        }

        .back-row .btn {
            font-size: clamp(0.7rem, 0.95vw, 0.85rem);
            padding: 6px 16px; border-radius: 8px;
            font-weight: 700; border: 2px solid #d5d9dd;
            background: transparent; color: var(--gray);
            transition: all 0.15s ease;
        }

        .back-row .btn:hover { background: #f1f1f4; color: var(--gray-hover); }

        .kiosk-panel {
            background: var(--white); border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: clamp(8px, 1.2vw, 16px) clamp(10px, 1.5vw, 20px);
            margin-bottom: 6px; overflow: hidden;
            flex-shrink: 0; max-height: 100%;
            display: flex; flex-direction: column;
        }

        [data-flow-step][hidden] { display: none !important; }

        #bookSelectionSection, .review-panel { flex: 1; min-height: 0; }

        .book-selection-section {
            display: flex; flex-direction: column;
            overflow: hidden; transition: opacity 0.2s ease;
            flex: 1; min-height: 0;
        }

        .book-selection-section.locked {
            opacity: 0.5; pointer-events: none;
        }

        .book-selection-header {
            flex-shrink: 0; display: flex;
            align-items: center; justify-content: space-between;
            gap: 16px; margin-bottom: 8px; flex-wrap: wrap;
        }

        .book-selection-header-text { min-width: 0; }

        .policy-badge {
            display: inline-flex; flex-wrap: wrap; align-items: center;
            gap: 6px 12px; margin-top: 4px;
            padding: 6px 12px; border-radius: 10px;
            background: #fff8fa; border: 1px solid #f0d9e2;
            font-size: clamp(0.6rem, 0.78vw, 0.72rem);
            color: #6b5b63; font-weight: 600;
        }

        .policy-badge .policy-item {
            display: inline-flex; align-items: center; gap: 4px;
        }

        .policy-badge .policy-item i {
            color: var(--pink-dark); font-size: 0.9em;
        }

        .policy-badge .policy-item strong {
            color: #202124; font-weight: 800;
        }

        .book-scan-search-bar {
            display: flex; align-items: center; gap: 8px;
            flex: 1; max-width: 520px; min-width: 260px;
        }

        .book-scan-search-bar .input-group-text {
            background: var(--pink-light); border: 1px solid var(--border);
            color: var(--pink-dark); font-weight: 700;
            font-size: clamp(0.65rem, 0.85vw, 0.8rem);
            white-space: nowrap;
        }

        .book-scan-search-bar .form-control {
            min-height: clamp(36px, 4.5vh, 48px);
            font-size: clamp(0.72rem, 0.95vw, 0.9rem);
        }

        .book-selection-body {
            flex: 1; min-height: 0; display: flex;
            gap: 10px; overflow: hidden;
        }

        .book-list-col {
            flex: 7; display: flex; flex-direction: column; min-height: 0;
        }

        .selected-books-col {
            flex: 5; display: flex; flex-direction: column; min-height: 0;
        }

        .book-list {
            flex: 1; overflow-y: auto;
            padding-right: 4px; min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .book-list::-webkit-scrollbar { width: 5px; }
        .book-list::-webkit-scrollbar-thumb { background: #d9dee3; border-radius: 3px; }

        .book-card {
            position: relative; margin-bottom: 6px;
            padding: clamp(8px, 1vw, 12px);
            border: 2px solid #e1e4e8; border-radius: 10px;
            background: linear-gradient(135deg, #ffffff, #fbfbfd);
            cursor: pointer; transition: all 0.18s ease;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
            flex-shrink: 0;
        }

        .book-card:hover {
            border-color: var(--pink-dark);
            box-shadow: 0 4px 10px rgba(215, 96, 145, 0.15);
            transform: translateY(-1px);
        }

        .book-card.selected {
            border: 2px solid var(--pink-dark);
            background: linear-gradient(135deg, #fff2f6, #fde5ed);
            box-shadow: 0 4px 10px rgba(215, 96, 145, 0.22);
        }

        .book-card .book-title {
            font-size: clamp(0.75rem, 1vw, 0.95rem);
            font-weight: 900; margin-bottom: 3px;
            padding-right: 24px; color: #1a1a1a; line-height: 1.15;
        }

        .book-card .book-details {
            font-size: clamp(0.62rem, 0.8vw, 0.75rem);
            color: #5a5f66; margin: 0 0 1px 0;
            display: flex; align-items: center; gap: 4px;
        }

        .book-card .book-details i {
            color: var(--pink-dark); width: 14px;
            text-align: center; font-size: 0.75em;
        }

        .book-card .book-details strong {
            color: #3a3f45; font-weight: 700;
        }

        .selection-icon {
            color: var(--pink-dark);
            font-size: clamp(0.9rem, 1.2vw, 1.2rem);
            position: absolute; top: 10px; right: 10px;
        }

        .selected-books-box {
            flex: 1; min-height: 0; overflow-y: auto;
            padding: 8px; border: 2px dashed #d5d9dd;
            border-radius: 10px; background: #fff;
            display: flex;
            flex-direction: column;
        }

        .selected-books-box::-webkit-scrollbar { width: 5px; }
        .selected-books-box::-webkit-scrollbar-thumb { background: #d9dee3; border-radius: 3px; }

        .selected-item {
            display: flex; align-items: center; justify-content: space-between;
            gap: 4px; margin-bottom: 4px; padding: 6px 8px;
            border: 1px solid #edbdcf; border-radius: 6px;
            background: var(--pink-light);
            font-size: clamp(0.62rem, 0.8vw, 0.75rem);
            flex-shrink: 0;
        }

        .empty-selection {
            padding: 16px 8px; color: #8b9298;
            text-align: center;
            font-size: clamp(0.62rem, 0.8vw, 0.75rem);
        }

        .book-list > .empty-selection,
        .selected-books-box > .empty-selection {
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .empty-selection i {
            display: block; margin-bottom: 4px;
            color: var(--pink-dark);
            font-size: clamp(1.2rem, 1.8vw, 1.8rem);
        }

        .count-badge {
            padding: 1px 7px; border-radius: 20px;
            background: #5e6871; color: #fff;
            font-size: clamp(0.5rem, 0.65vw, 0.65rem);
            font-weight: 700; white-space: nowrap;
        }

        .count-badge.limit-reached { background: #c0392b; }

        .book-rfid-message {
            font-size: clamp(0.6rem, 0.8vw, 0.75rem);
            color: var(--muted); margin-bottom: 6px; flex-shrink: 0;
        }

        .review-actions {
            display: flex; flex-wrap: wrap;
            justify-content: center; gap: 12px;
            width: 100%; margin-top: 10px; flex-shrink: 0;
        }

        .btn-confirm, .btn-cancel, .btn-back {
            min-height: clamp(36px, 4.6vh, 48px);
            border: none; border-radius: 8px; color: #fff;
            font-size: clamp(0.72rem, 0.95vw, 0.9rem);
            font-weight: 700; padding: 0 clamp(10px, 1.4vw, 22px);
            display: inline-flex; align-items: center; justify-content: center;
            gap: 5px; transition: all 0.15s ease;
            white-space: nowrap; font-family: 'Segoe UI', Arial, sans-serif;
            text-decoration: none; cursor: pointer;
        }

        .btn-confirm { background: var(--pink-dark); }
        .btn-confirm:hover { background: var(--pink-hover); color: #fff; }
        .btn-confirm:disabled { background: #d8a9ba; cursor: not-allowed; }

        .btn-cancel { background: var(--gray); text-decoration: none; }
        .btn-cancel:hover { background: var(--gray-hover); color: #fff; }

        .btn-back {
            background: transparent; color: var(--gray);
            border: 2px solid #d5d9dd;
        }
        .btn-back:hover { background: #f1f1f4; color: var(--gray-hover); }

        .alert {
            border: none; border-radius: 8px;
            padding: clamp(3px, 0.5vw, 8px) clamp(6px, 0.8vw, 12px);
            font-size: clamp(0.6rem, 0.8vw, 0.8rem);
            margin-bottom: 3px; flex-shrink: 0;
        }

        .toast-container {
            position: fixed; top: 16px; right: 16px;
            z-index: 99999; display: flex; flex-direction: column;
            gap: 10px; max-width: min(420px, 90vw); pointer-events: none;
        }

        .toast-popup {
            pointer-events: auto; display: flex;
            align-items: flex-start; gap: 12px;
            padding: 14px 18px; border-radius: 14px;
            background: #fff;
            box-shadow: 0 12px 36px rgba(20, 20, 40, 0.18);
            border-left: 6px solid #999;
            animation: toastSlideIn 0.28s cubic-bezier(0.22, 1, 0.36, 1);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .toast-popup.toast-hide {
            opacity: 0; transform: translateX(24px);
        }

        .toast-popup.toast-success { border-left-color: #1e9e5a; }
        .toast-popup.toast-error   { border-left-color: #c0392b; }
        .toast-popup.toast-warning { border-left-color: #d4a017; }
        .toast-popup.toast-info    { border-left-color: #2b7bbd; }

        .toast-popup .toast-icon {
            flex-shrink: 0; width: 34px; height: 34px;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-size: 1rem; color: #fff;
        }

        .toast-success .toast-icon { background: #1e9e5a; }
        .toast-error   .toast-icon { background: #c0392b; }
        .toast-warning .toast-icon { background: #d4a017; }
        .toast-info    .toast-icon { background: #2b7bbd; }

        .toast-popup .toast-body-text { flex: 1; min-width: 0; }

        .toast-popup .toast-title {
            font-weight: 800; font-size: 0.92rem;
            color: #202124; line-height: 1.2;
        }

        .toast-popup .toast-message {
            font-size: 0.8rem; color: #5a5f66;
            margin-top: 3px; line-height: 1.35; word-break: break-word;
        }

        .toast-popup .toast-close {
            flex-shrink: 0; background: none; border: none;
            color: #a8adb3; font-size: 1rem; cursor: pointer;
            padding: 2px 4px; border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }

        .toast-popup .toast-close:hover {
            background: #f1f1f4; color: #5a5f66;
        }

        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Kiosk modal (warning / error / info popups) ── */
        .kiosk-modal-overlay {
            position: fixed; inset: 0; z-index: 100000;
            display: none; align-items: center; justify-content: center;
            padding: 20px;
            background: rgba(24, 18, 22, 0.55);
            backdrop-filter: blur(3px);
            animation: modalFadeIn 0.2s ease;
        }

        .kiosk-modal-overlay.show { display: flex; }

        .kiosk-modal {
            width: min(460px, 94vw); background: #fff;
            border-radius: 20px;
            border: 1.5px solid #f0d9e2;
            box-shadow: 0 24px 64px rgba(20, 10, 20, 0.3);
            padding: 30px 28px 24px;
            text-align: center;
            animation: modalPopIn 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .kiosk-modal .modal-icon {
            width: 76px; height: 76px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px; font-size: 2rem; color: #fff;
            animation: kioskIconPop 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes kioskIconPop {
            0%   { transform: scale(0.6); opacity: 0; }
            60%  { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }

        .kiosk-modal.modal-success .modal-icon {
            background: linear-gradient(135deg, #27ae60, #1e9e5a);
            box-shadow: 0 10px 26px rgba(39, 174, 96, 0.4);
        }
        .kiosk-modal.modal-error .modal-icon {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            box-shadow: 0 10px 26px rgba(231, 76, 60, 0.4);
        }
        .kiosk-modal.modal-warning .modal-icon {
            background: linear-gradient(135deg, #f5c518, #d4a017);
            box-shadow: 0 10px 26px rgba(241, 196, 15, 0.45);
        }
        .kiosk-modal.modal-info .modal-icon {
            background: linear-gradient(135deg, #3498db, #2b7bbd);
            box-shadow: 0 10px 26px rgba(52, 152, 219, 0.4);
        }

        .kiosk-modal .modal-title {
            font-size: clamp(1.15rem, 1.4vw, 1.35rem);
            font-weight: 900;
            color: #202124;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        .kiosk-modal.modal-success .modal-title { color: #1e9e5a; }
        .kiosk-modal.modal-error   .modal-title { color: #c0392b; }
        .kiosk-modal.modal-warning .modal-title { color: #202124; }
        .kiosk-modal.modal-info    .modal-title { color: #2b7bbd; }

        .kiosk-modal .modal-text {
            font-size: clamp(0.85rem, 1vw, 0.95rem);
            color: #5a5f66;
            line-height: 1.5;
            margin: 0 0 4px;
            word-break: break-word;
        }

        .kiosk-modal .modal-list {
            text-align: left;
            margin: 16px 0 4px;
            padding: 14px 18px;
            background: #f9f9fb;
            border: 1px solid #eef0f3;
            border-radius: 12px;
            font-size: clamp(0.78rem, 0.9vw, 0.88rem);
            color: #3a3f45;
            max-height: 200px;
            overflow-y: auto;
            list-style: none;
        }
        .kiosk-modal .modal-list::-webkit-scrollbar { width: 5px; }
        .kiosk-modal .modal-list::-webkit-scrollbar-thumb { background: #d9dee3; border-radius: 3px; }

        .kiosk-modal .modal-list li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
            line-height: 1.4;
            word-break: break-word;
        }
        .kiosk-modal .modal-list li:last-child { margin-bottom: 0; }

        .kiosk-modal .modal-list li::before {
            content: '';
            position: absolute;
            left: 4px; top: 7px;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--pink-dark);
            box-shadow: 0 0 0 3px rgba(215, 96, 145, 0.15);
        }
        .kiosk-modal.modal-warning .modal-list li::before {
            background: #d4a017;
            box-shadow: 0 0 0 3px rgba(212, 160, 23, 0.18);
        }
        .kiosk-modal.modal-error .modal-list li::before {
            background: #c0392b;
            box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.18);
        }
        .kiosk-modal.modal-success .modal-list li::before {
            background: #1e9e5a;
            box-shadow: 0 0 0 3px rgba(30, 158, 90, 0.18);
        }
        .kiosk-modal.modal-info .modal-list li::before {
            background: #2b7bbd;
            box-shadow: 0 0 0 3px rgba(43, 123, 189, 0.18);
        }

        .kiosk-modal .modal-actions {
            display: flex; gap: 10px; justify-content: center;
            margin-top: 22px; flex-wrap: wrap;
        }

        .kiosk-modal .modal-actions .modal-ok-btn {
            min-width: 140px;
            min-height: 46px;
            border-radius: 12px;
            font-size: clamp(0.82rem, 1vw, 0.95rem);
            font-weight: 800;
            letter-spacing: 0.3px;
            border: none; color: #fff;
            background: var(--pink-dark);
            display: inline-flex; align-items: center; justify-content: center;
            gap: 6px;
            padding: 0 24px;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kiosk-modal .modal-actions .modal-ok-btn:hover {
            background: var(--pink-hover);
            box-shadow: 0 6px 16px rgba(215, 96, 145, 0.35);
            transform: translateY(-1px);
        }
        .kiosk-modal .modal-actions .modal-ok-btn:active {
            transform: translateY(0);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }

        @keyframes modalPopIn {
            from { opacity: 0; transform: scale(0.9) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ── Transaction Complete modal (matches Reserve page) ── */
        .confirm-modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(32, 20, 28, 0.55);
            backdrop-filter: blur(2px);
            display: none; align-items: center; justify-content: center;
            z-index: 2000; padding: 20px;
        }
        .confirm-modal-backdrop.show { display: flex; }

        .complete-modal {
            width: min(100%, 480px); background: #fff;
            border-radius: var(--radius);
            box-shadow: 0 20px 50px rgba(115, 45, 75, 0.35);
            border: 2px solid #f0d9e2;
            padding: 24px 26px 20px; text-align: center;
            animation: modalPop 0.18s ease-out;
        }
        @keyframes modalPop {
            from { transform: scale(0.94); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .complete-modal .confirm-icon {
            width: 60px; height: 60px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; border-radius: 50%;
            background: linear-gradient(135deg, #27ae60, #1e9e5a);
            color: #fff; font-size: 1.6rem;
            box-shadow: 0 8px 22px rgba(39, 174, 96, 0.35);
            animation: completeIconPop 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        @keyframes completeIconPop {
            0% { transform: scale(0.6); opacity: 0; }
            60% { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }
        .complete-modal .confirm-title {
            font-size: clamp(1.05rem, 1.4vw, 1.25rem);
            font-weight: 900; color: #1e9e5a; margin-bottom: 6px;
        }
        .complete-modal .confirm-message {
            font-size: clamp(0.8rem, 1vw, 0.92rem);
            color: #5a5f66; margin-bottom: 18px; line-height: 1.5;
        }
        .complete-modal .complete-summary {
            text-align: left; margin-bottom: 18px;
            padding: 14px 16px; border-radius: 12px;
            background: var(--pink-light); border: 1px solid #f4d3e0;
        }
        .complete-modal .complete-summary .summary-row {
            display: flex; justify-content: space-between;
            align-items: baseline; gap: 12px;
            padding: 6px 0; border-bottom: 1px dashed #f0d9e2;
        }
        .complete-modal .complete-summary .summary-row:last-child { border-bottom: none; }
        .complete-modal .complete-summary .summary-label {
            font-size: clamp(0.62rem, 0.78vw, 0.72rem);
            font-weight: 800; color: #6b5b63;
            text-transform: uppercase; letter-spacing: 0.6px;
            flex-shrink: 0;
        }
        .complete-modal .complete-summary .summary-value {
            font-size: clamp(0.82rem, 1vw, 0.92rem);
            font-weight: 800; color: #202124;
            text-align: right; word-break: break-word;
        }
        .complete-modal .complete-summary .summary-value.pink { color: var(--pink-dark); }
        .complete-modal .complete-status-badge {
            display: inline-block; padding: 3px 12px;
            border-radius: 999px; background: #fff3cd; color: #856404;
            font-weight: 800; font-size: clamp(0.6rem, 0.75vw, 0.7rem);
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .complete-modal .complete-books-list {
            display: flex; flex-direction: column;
            gap: 6px; margin: 0 0 6px;
            max-height: 150px; overflow-y: auto;
            padding-right: 2px;
        }
        .complete-modal .complete-book-row {
            display: flex; justify-content: space-between;
            align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            background: #fff; border: 1px solid #f4d3e0;
            font-size: clamp(0.72rem, 0.88vw, 0.82rem);
            font-weight: 700; color: #202124;
            text-align: left;
        }
        .complete-modal .complete-book-row i {
            color: var(--pink-dark); flex-shrink: 0;
        }
        .complete-modal .confirm-actions {
            display: flex; gap: 10px; justify-content: center;
        }
        .complete-modal .confirm-actions .btn-confirm {
            flex: 1; min-height: 44px;
            font-size: clamp(0.78rem, 1vw, 0.92rem);
            border-radius: 10px;
        }

        /* ── STEP 3: Review / Confirmation step (matches Return page) ── */
        .review-panel {
            flex: 1; min-height: 0; display: flex;
            flex-direction: column; overflow: hidden;
            background: transparent;
            box-shadow: none;
            padding: 0;
            margin-bottom: 0;
        }

        .review-body {
            flex: 1; min-height: 0;
            display: flex;
            justify-content: center;
            align-items: stretch;
            width: 100%;
            overflow: hidden;
            padding: 0;
        }

        .confirm-card {
            width: 100%;
            max-width: 100%;
            height: 100%;
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #f0d9e2;
            box-shadow: 0 12px 40px rgba(215, 96, 145, 0.16);
            padding: clamp(16px, 2vw, 26px) clamp(18px, 2.4vw, 34px);
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            overflow: hidden;
            min-height: 0;
        }

        .confirm-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 6px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .confirm-card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: clamp(0.95rem, 1.4vw, 1.25rem);
            font-weight: 900;
            color: var(--pink-dark);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .confirm-card-title i {
            font-size: 1.15em;
            transform: rotate(-45deg);
        }

        .confirm-card-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 999px;
            background: #e9f8ee;
            color: #18763c;
            font-weight: 800;
            font-size: clamp(0.6rem, 0.78vw, 0.72rem);
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .confirm-card-status i { font-size: 0.9em; }

        .confirm-card-divider {
            border: none;
            border-top: 2.5px dashed #f0b8cd;
            margin: 10px 0 14px;
            opacity: 1;
            flex-shrink: 0;
        }

        .confirm-card-rows {
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .confirm-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 20px;
            padding: 10px 0;
            border-bottom: 1.5px dotted #f0c9d8;
        }
        .confirm-row:last-child { border-bottom: none; }

        .confirm-row-label {
            font-size: clamp(0.68rem, 0.85vw, 0.8rem);
            font-weight: 800;
            color: #6b5b63;
            text-transform: uppercase;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .confirm-row-value {
            font-size: clamp(0.88rem, 1.05vw, 1rem);
            font-weight: 800;
            color: #202124;
            text-align: right;
            word-break: break-word;
        }
        .confirm-row-value.pink { color: var(--pink-dark); }

        .confirm-books-block {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
            margin-top: 6px;
        }

        .confirm-books-label {
            font-size: clamp(0.68rem, 0.85vw, 0.8rem);
            font-weight: 800;
            color: #6b5b63;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .confirm-books-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding-right: 4px;
        }

        .confirm-books-list::-webkit-scrollbar { width: 5px; }
        .confirm-books-list::-webkit-scrollbar-thumb { background: #edbdcf; border-radius: 3px; }

        .confirm-book-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: 10px;
            background: var(--pink-light);
            border: 1px solid #f4d3e0;
            flex-shrink: 0;
        }

        .confirm-book-title {
            font-size: clamp(0.8rem, 0.95vw, 0.92rem);
            font-weight: 800;
            color: #202124;
            flex: 1;
            min-width: 0;
            word-break: break-word;
        }

        .confirm-book-meta {
            font-size: clamp(0.62rem, 0.75vw, 0.72rem);
            font-weight: 700;
            color: #8a6d76;
            margin-top: 2px;
            font-style: italic;
        }

        .confirm-book-badge {
            flex-shrink: 0;
            padding: 3px 10px;
            border-radius: 999px;
            font-weight: 800;
            font-size: clamp(0.55rem, 0.68vw, 0.66rem);
            letter-spacing: 0.4px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .confirm-book-badge.overdue { background: #fde8e8; color: #a93226; }
        .confirm-book-badge.ontime  { background: #e9f8ee; color: #18763c; }

        .confirm-card-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            flex-shrink: 0;
        }

        .confirm-card-actions .btn-confirm,
        .confirm-card-actions .btn-back,
        .confirm-card-actions .btn-cancel {
            flex: 1;
            min-height: clamp(42px, 5vh, 52px);
            border-radius: 12px;
            font-size: clamp(0.8rem, 0.95vw, 0.92rem);
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .confirm-card-actions .btn-back {
            background: #f4f4f7;
            color: #5a5f66;
            border: 2px solid #e6e6ee;
            font-weight: 700;
        }
        .confirm-card-actions .btn-back:hover { background: #ebeaf0; color: #3a3f45; }

        .confirm-card-actions .btn-cancel {
            background: #f4f4f7;
            color: #5a5f66;
            border: 2px solid #e6e6ee;
            font-weight: 700;
        }
        .confirm-card-actions .btn-cancel:hover { background: #ebeaf0; color: #3a3f45; }

        .confirm-cancel-link {
            text-align: center;
            margin-top: 8px;
            flex-shrink: 0;
        }
        .confirm-cancel-link a {
            font-size: 0.72rem;
            color: #9a8a92;
            text-decoration: underline;
        }

        @media (max-width: 991px) {
            .identity-two-columns { flex-direction: column; }
            .identity-right-col {
                border-left: none; padding-left: 0;
                border-top: 2px solid #f0e0e6; padding-top: 10px;
            }
            .book-selection-body { flex-direction: column; }
            .book-list-col, .selected-books-col { flex: 1; min-height: 0; }
            .selected-books-box { min-height: 80px; }
        }

        @media (max-width: 767px) {
            .kiosk-container { width: 98%; padding: 2px 4px; }
            .review-actions { flex-direction: column; }
            .btn-confirm, .btn-cancel, .btn-back { width: 100%; }
            .top-bar { flex-direction: column; align-items: flex-start; padding-bottom: 10px; }
            .top-right { width: 100%; justify-content: flex-start; }
            .top-left { width: 100%; }
            .book-selection-header { flex-direction: column; align-items: stretch; }
            .book-scan-search-bar { max-width: 100%; }

            .confirm-card { padding: 14px 14px 16px; border-radius: 16px; }
            .confirm-card-actions { flex-direction: column; gap: 8px; }
            .confirm-card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .confirm-row { flex-direction: column; gap: 3px; padding: 8px 0; }
            .confirm-row-value { text-align: left; }
        }

        @media (max-width: 575px) {
            .top-left-title { letter-spacing: 0.5px; }
            .kiosk-modal { padding: 32px 22px 24px; }
            .kiosk-modal .modal-icon { width: 78px; height: 78px; font-size: 2.2rem; }
            .kiosk-modal .modal-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="kiosk-shell">
    <div class="kiosk-container">

        <div class="top-bar">
            <div class="top-left">
                <img
                    src="{{ $systemSettings?->logoUrl() ?? asset('images/default-logo.png') }}"
                    data-system-logo
                    class="top-left-logo"
                    alt="{{ $systemSettings->institution_name ?? 'Lourdes College' }} Logo"
                >
                <h1 class="top-left-title">Borrow Books</h1>
            </div>
            <div class="top-right">
                <span class="step-badge" id="stepBadge">
                    <i class="fa-solid fa-id-card" id="stepBadgeIcon"></i>
                    <span id="stepBadgeText">Scan ID{{ $fingerprintRequired ? ' & Fingerprint' : '' }}</span>
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please check the following:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <form action="{{ $borrowStoreUrl }}" method="POST" id="borrowForm" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                @csrf

                {{-- STEP 1: IDENTITY (RFID + fingerprint authentication happens here) --}}
                <section id="identityStep" data-flow-step="identity">
                    <div class="identity-two-columns">
                        <div class="identity-left-col">
                            <h2 class="section-title">Scan Borrower RFID</h2>
                            <p class="section-description">Place the RFID card on the reader to retrieve the borrower.</p>

                            <div class="scan-area">
                                <div class="scan-icon"><i class="fa-solid fa-id-card"></i></div>
                                <div class="scan-heading">Scan Student or Personnel RFID</div>
                                <p class="scan-instruction">
                                    @if($fingerprintRequired)
                                        Borrower information will appear on the right, then verify the fingerprint.
                                    @else
                                        Borrower information will appear on the right.
                                    @endif
                                </p>
                                <div class="rfid-input-wrapper">
                                    <input type="text" id="rfid_scan_input" class="form-control large-input text-center"
                                           placeholder="Scan RFID card" autocomplete="off" autofocus>
                                    <input type="hidden" name="rfid_tag_uid" id="rfid_tag_uid" value="">
                                    <div id="rfidStatus" class="rfid-status text-muted">
                                        <i class="fa-solid fa-id-card me-1"></i>Waiting for RFID scan...
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="borrower_id" id="borrower_id">
                            <input type="hidden" name="borrower_type" id="borrower_type">

                            @if($fingerprintRequired)
                                <input type="hidden" name="fingerprint_id" id="fingerprint_id" value="">
                            @endif

                            @if($fingerprintRequired)
                                <div class="fingerprint-message" id="fingerprintMessage">
                                    <div class="fp-title" id="fpTitle">Please scan your fingerprint to continue</div>
                                    <div class="fp-sub" id="fpSub">Fingerprint scanner ready. Place your finger on the fingerprint scanner.</div>
                                    <div class="fp-actions" id="fpActions" style="display:none;">
                                        <button type="button" class="btn-retry" id="fingerprintRetry">
                                            <i class="fa-solid fa-rotate-right me-1"></i> Try Again
                                        </button>
                                    </div>
                                </div>

                                <div class="fingerprint-hidden" id="fingerprintDirect">
                                    @if($hasFingerprintComponent)
                                        <x-fingerprint-transaction-verification
                                            rfid-input-id="rfid_tag_uid"
                                            borrower-id-input-id="borrower_id"
                                            borrower-type-input-id="borrower_type"
                                            fingerprint-id-input-id="fingerprint_id"
                                            bridge-url="ws://127.0.0.1:8765"
                                        />
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                            Fingerprint component not available. Please contact the administrator.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="identity-right-col">
                            <h2 class="section-title" style="margin-top: 0;">Borrower Details</h2>
                            <p class="section-description">Scanned borrower information appears here.</p>

                            <div class="user-info-card" id="userInfoCard">
                                <div class="user-avatar">
                                    <i class="fa-solid fa-user-circle"></i>
                                    <div>
                                        <div class="avatar-text" id="displayBorrowerName">—</div>
                                        <div class="avatar-sub" id="displayBorrowerType">No RFID scanned</div>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Student / Employee No.</span>
                                    <span class="info-value placeholder" id="displayBorrowerNumber">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Borrower Type</span>
                                    <span class="info-value placeholder" id="displayBorrowerTypeRow">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Borrowing Period</span>
                                    <span class="info-value placeholder" id="displayBorrowingPeriod">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Remaining Limit</span>
                                    <span class="info-value placeholder" id="displayRemainingLimit">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Status</span>
                                    <span class="info-value placeholder" id="displayBorrowerStatus">—</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- STEP 2: BOOK SELECTION --}}
                <section id="bookSelectionSection" class="kiosk-panel book-selection-section locked" data-flow-step="books" hidden>

                    <div class="book-selection-header">
                        <div class="book-selection-header-text">
                            <h2 class="section-title">Select Books</h2>
                            <p id="bookSelectionDescription" class="section-description" style="margin-bottom:0;">
                                @if($fingerprintRequired)
                                    Verify the borrower fingerprint before selecting books.
                                @else
                                    Scan a registered RFID before selecting books.
                                @endif
                            </p>

                            <div class="policy-badge" id="policyBadge">
                                <span class="policy-item"><i class="fa-solid fa-book"></i>Limit: <strong id="policyLimit">{{ $policySnapshot['borrowing_limit'] }}</strong> book(s)</span>
                                <span class="policy-item"><i class="fa-solid fa-calendar-days"></i>Period: <strong id="policyPeriod">{{ $policySnapshot['borrowing_period_days'] }}</strong> day(s)</span>
                                @if($fingerprintRequired)
                                    <span class="policy-item" id="policyFingerprintItem">
                                        <i class="fa-solid fa-fingerprint"></i>Fingerprint:
                                        <strong id="policyFingerprint">Required</strong>
                                    </span>
                                @endif
                                <span class="policy-item"><i class="fa-solid fa-layer-group"></i>Per transaction: <strong id="policyMaxPerTx">{{ $policySnapshot['max_books_per_transaction'] }}</strong></span>
                            </div>
                        </div>

                        <div class="book-scan-search-bar">
                            <span class="input-group-text"><i class="fa-solid fa-barcode me-1"></i>Scan / Search</span>
                            <input
                                type="text"
                                id="bookRfidScan"
                                class="form-control"
                                placeholder="Scan book RFID or type to search..."
                                autocomplete="off"
                                disabled
                            >
                        </div>
                    </div>

                    <div id="bookRfidMessage" class="book-rfid-message" aria-live="polite">
                        @if($fingerprintRequired)
                            Scan a book after verifying your ID and fingerprint.
                        @else
                            Scan a book after verifying your ID.
                        @endif
                    </div>

                    <div class="book-selection-body">
                        <div class="book-list-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Available Books</strong>
                                <span class="count-badge">{{ $books->count() }} available</span>
                            </div>
                            <div id="bookList" class="book-list">
                                @forelse($books as $book)
                                    <div class="book-card" data-book-id="{{ $book->id }}"
                                         data-rfids="{{ $book->copies->where('status', 'available')->pluck('rfid_tag_uid')->filter()->values()->toJson() }}"
                                         data-title="{{ strtolower($book->title ?? '') }}"
                                         data-author="{{ strtolower($book->author ?? '') }}"
                                         data-call-number="{{ strtolower($book->call_number ?? '') }}"
                                         tabindex="-1">
                                        <span class="selection-icon"><i class="fa-regular fa-square fa-lg"></i></span>
                                        <div class="book-title">{{ $book->title ?? 'Untitled Book' }}</div>
                                        <p class="book-details"><i class="fa-solid fa-user-pen"></i><span>Author: <strong>{{ $book->author ?? 'Unknown' }}</strong></span></p>
                                        <p class="book-details"><i class="fa-solid fa-bookmark"></i><span>Call No: <strong>{{ $book->call_number ?? '-' }}</strong></span></p>
                                    </div>
                                @empty
                                    <div class="empty-selection">
                                        <i class="fa-solid fa-book"></i>
                                        <strong>No available books</strong>
                                        <p class="mb-0 mt-1">All books are unavailable.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="selected-books-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Books to Borrow</strong>
                                <span id="selectedCount" class="count-badge">0 selected</span>
                            </div>
                            <div class="selected-books-box">
                                <div id="emptySelection" class="empty-selection">
                                    <i class="fa-solid fa-book"></i>
                                    <strong>No books selected</strong>
                                    <p class="mb-0 mt-1">Select a book from the list.</p>
                                </div>
                                <div id="selectedBooksList"></div>
                            </div>
                            <div id="hiddenBookInputs"></div>
                        </div>
                    </div>

                    <div class="review-actions">
                        <button type="button" id="borrowBackIdentity" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Change ID</button>
                        <button type="button" id="borrowReviewNext" class="btn-confirm" disabled>Review borrowing <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </section>

                {{--
                    STEP 3: REVIEW & CONFIRM (matches Return page confirmation card)
                    This step performs NO fingerprint validation of its own.
                    It only re-displays the borrower/books already verified
                    in Steps 1 & 2, and submits the form. The hidden
                    fingerprint_id input (from Step 1) is sent as-is.
                --}}
                <section class="review-panel" data-flow-step="review" hidden>
                    <div class="review-body">
                        <div class="confirm-card">

                            <div class="confirm-card-header">
                                <div class="confirm-card-title">
                                    <i class="fa-solid fa-book-open"></i>
                                    Borrow Confirmation
                                </div>
                                <span class="confirm-card-status">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Ready to Confirm
                                </span>
                            </div>

                            <hr class="confirm-card-divider">

                            <div class="confirm-card-rows">
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Borrower</span>
                                    <span class="confirm-row-value" id="borrowReviewPersonName">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Borrowing Period</span>
                                    <span class="confirm-row-value" id="borrowReviewPersonPeriod">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Books to Borrow</span>
                                    <span class="confirm-row-value pink" id="borrowReviewTotal">0 book(s)</span>
                                </div>
                            </div>

                            <div class="confirm-books-block">
                                <div class="confirm-books-label">Selected Books</div>
                                <div class="confirm-books-list" id="borrowReviewBooks"></div>
                            </div>

                            <div class="confirm-card-actions">
                                <button type="button" id="borrowBackBooks" class="btn-back">
                                    <i class="fa-solid fa-arrow-left"></i> Go back
                                </button>
                                <button type="submit" id="borrowButton" class="btn-confirm" disabled>
                                    <i class="fa-solid fa-check"></i> Confirm Borrowing
                                </button>
                            </div>

                            <div class="confirm-cancel-link">
                                <a href="{{ $borrowExitUrl }}">Cancel and return to kiosk</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>

        <div class="back-row">
            <a href="{{ $borrowExitUrl }}" class="btn">
                <i class="fa-solid fa-arrow-left me-1"></i>Back to kiosk choices
            </a>
        </div>

    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

{{-- ── Kiosk modal (warning / error / info popups) ── --}}
<div class="kiosk-modal-overlay" id="kioskModalOverlay">
    <div class="kiosk-modal" id="kioskModal">
        <div class="modal-icon" id="kioskModalIcon"><i class="fa-solid fa-check"></i></div>
        <div class="modal-title" id="kioskModalTitle">Success</div>
        <div class="modal-text" id="kioskModalText">Operation completed.</div>
        <ul class="modal-list" id="kioskModalList" style="display:none;"></ul>
        <div class="modal-actions" id="kioskModalActions">
            <button type="button" class="modal-ok-btn" id="kioskModalOk">
                <i class="fa-solid fa-check me-1"></i> OK
            </button>
        </div>
    </div>
</div>

{{-- ── Transaction Complete notification (matches Reserve page UI) ── --}}
<div class="confirm-modal-backdrop" id="transactionCompleteBackdrop">
    <div class="complete-modal" role="dialog" aria-modal="true" aria-labelledby="transactionCompleteTitle">
        <div class="confirm-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <div class="confirm-title" id="transactionCompleteTitle">Transaction Complete</div>
        <div class="confirm-message" id="transactionCompleteMessage">
            The books have been borrowed successfully.
        </div>

        <div class="complete-summary">
            <div class="summary-row">
                <span class="summary-label">Borrower</span>
                <span class="summary-value" id="tcBorrowerName">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Books</span>
                <span class="summary-value pink" id="tcBookCount">0 book(s)</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Borrowing Period</span>
                <span class="summary-value" id="tcBorrowingPeriod">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Status</span>
                <span class="summary-value">
                    <span class="complete-status-badge" id="tcStatus">Borrowed</span>
                </span>
            </div>
        </div>

        <div class="complete-books-list" id="tcBooksList"></div>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm" id="transactionCompleteDone">
                <i class="fa-solid fa-check me-1"></i> Done
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const FINGERPRINT_REQUIRED = @json($fingerprintRequired);
    const POLICY = @json($policySnapshot);
    const AUTO_RELOAD_SECONDS = {{ $autoReloadSeconds }};

    /* ── step navigation ─────────────────────────────── */
    const steps = document.querySelectorAll('[data-flow-step]');
    const stepBadgeText = document.getElementById('stepBadgeText');
    const stepBadgeIcon = document.getElementById('stepBadgeIcon');

    function showStep(step) {
        steps.forEach(panel => {
            panel.hidden = panel.dataset.flowStep !== step;
        });
        if (step === 'identity') {
            if (stepBadgeText) stepBadgeText.textContent = FINGERPRINT_REQUIRED ? 'Scan ID & Fingerprint' : 'Scan ID';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-id-card';
        } else if (step === 'books') {
            if (stepBadgeText) stepBadgeText.textContent = 'Select Books';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-book';
        } else if (step === 'review') {
            if (stepBadgeText) stepBadgeText.textContent = 'Review & Confirm';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-check-circle';
        }
    }

    /* ── DOM references ──────────────────────────────── */
    const borrowForm = document.getElementById('borrowForm');
    const rfidInput = document.getElementById('rfid_scan_input');
    const verifiedRfidInput = document.getElementById('rfid_tag_uid');
    const rfidStatus = document.getElementById('rfidStatus');
    const borrowerIdInput = document.getElementById('borrower_id');
    const borrowerTypeInput = document.getElementById('borrower_type');
    const fingerprintIdInput = document.getElementById('fingerprint_id');
    const bookSelectionSection = document.getElementById('bookSelectionSection');
    const bookSelectionDescription = document.getElementById('bookSelectionDescription');
    const searchInput = document.getElementById('bookRfidScan');
    const bookRfidInput = document.getElementById('bookRfidScan');
    const bookRfidMessage = document.getElementById('bookRfidMessage');
    const bookCards = Array.from(document.querySelectorAll('.book-card'));
    const selectedBooksList = document.getElementById('selectedBooksList');
    const hiddenBookInputs = document.getElementById('hiddenBookInputs');
    const emptySelection = document.getElementById('emptySelection');
    const selectedCount = document.getElementById('selectedCount');
    const borrowButton = document.getElementById('borrowButton');
    const reviewNext = document.getElementById('borrowReviewNext');

    const borrowReviewPersonName = document.getElementById('borrowReviewPersonName');
    const borrowReviewPersonPeriod = document.getElementById('borrowReviewPersonPeriod');
    const borrowReviewBooks = document.getElementById('borrowReviewBooks');
    const borrowReviewTotal = document.getElementById('borrowReviewTotal');

    const fingerprintMessage = document.getElementById('fingerprintMessage');
    const fpTitle = document.getElementById('fpTitle');
    const fpSub = document.getElementById('fpSub');
    const fpActions = document.getElementById('fpActions');
    const fingerprintRetry = document.getElementById('fingerprintRetry');
    const fingerprintDirect = document.getElementById('fingerprintDirect');

    const displayBorrowerName = document.getElementById('displayBorrowerName');
    const displayBorrowerNumber = document.getElementById('displayBorrowerNumber');
    const displayBorrowerTypeRow = document.getElementById('displayBorrowerTypeRow');
    const displayBorrowingPeriod = document.getElementById('displayBorrowingPeriod');
    const displayRemainingLimit = document.getElementById('displayRemainingLimit');
    const displayBorrowerStatus = document.getElementById('displayBorrowerStatus');
    const displayBorrowerType = document.getElementById('displayBorrowerType');

    /* ── Transaction Complete modal elements ── */
    const transactionCompleteBackdrop = document.getElementById('transactionCompleteBackdrop');
    const transactionCompleteMessage = document.getElementById('transactionCompleteMessage');
    const transactionCompleteDone = document.getElementById('transactionCompleteDone');
    const tcBorrowerName = document.getElementById('tcBorrowerName');
    const tcBookCount = document.getElementById('tcBookCount');
    const tcBorrowingPeriod = document.getElementById('tcBorrowingPeriod');
    const tcStatus = document.getElementById('tcStatus');
    const tcBooksList = document.getElementById('tcBooksList');

    /* ── state ───────────────────────────────────────── */
    const selectedBooks = new Map();
    let borrowerFound = false;
    let borrowerCanBorrow = false;
    let borrowerRemainingLimit = 0;
    let borrowerData = null;

    let fingerprintVerified = !FINGERPRINT_REQUIRED;

    let scanTimer = null;
    let bookScanTimer = null;
    let activeRequest = null;
    let bookScanBuffer = '';
    let bookScanResetTimer = null;
    let borrowerScanBuffer = '';
    let borrowerScanResetTimer = null;

    let bookInputKeyTimes = [];
    let bookInputFirstKeyAt = 0;

    let lastToastSignature = '';
    let lastToastAt = 0;

    /* ── Auto-reload logic ── */
    let autoReloadRemaining = AUTO_RELOAD_SECONDS;
    let autoReloadTimer = null;
    let autoReloadActive = true;

    function resetAutoReloadCountdown() {
        autoReloadRemaining = AUTO_RELOAD_SECONDS;
    }

    function startAutoReload() {
        stopAutoReload();
        resetAutoReloadCountdown();

        autoReloadTimer = setInterval(function () {
            if (!autoReloadActive) return;

            // Don't reload while a transaction is in progress
            if (transactionCompleteBackdrop && transactionCompleteBackdrop.classList.contains('show')) {
                return;
            }
            if (kioskModalOverlay && kioskModalOverlay.classList.contains('show')) {
                return;
            }

            // Only auto-reload on the identity step
            const currentStep = document.querySelector('[data-flow-step]:not([hidden])');
            const stepName = currentStep ? currentStep.dataset.flowStep : 'identity';

            if (stepName !== 'identity') {
                return;
            }

            autoReloadRemaining--;

            if (autoReloadRemaining <= 0) {
                autoReloadActive = false;
                window.location.reload();
            }
        }, 1000);
    }

    function stopAutoReload() {
        if (autoReloadTimer) {
            clearInterval(autoReloadTimer);
            autoReloadTimer = null;
        }
    }

    function pauseAutoReload() {
        autoReloadActive = false;
    }

    function resumeAutoReload() {
        autoReloadActive = true;
        resetAutoReloadCountdown();
    }

    /* ── helpers ─────────────────────────────────────── */
    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function formatBorrowerType(type) {
        if (type === 'student') return 'Student';
        if (type === 'personnel' || type === 'employee' || type === 'faculty') return 'Personnel';
        return type ? (type.charAt(0).toUpperCase() + type.slice(1)) : 'Unknown';
    }

    function looksLikeRfidScan(value) {
        if (!value || value.length < 6) return false;
        return /^[a-zA-Z0-9\-_]+$/.test(value);
    }

    /* ── Perf: pre-index book cards by RFID (parsed once) ── */
    const bookCardByRfid = new Map();
    bookCards.forEach(function (card) {
        let rfids = [];
        try { rfids = JSON.parse(card.dataset.rfids || '[]'); } catch (e) { rfids = []; }
        card.__rfidSet = new Set(rfids);
        card.__titleEl = card.querySelector('.book-title');
        card.__authorCache = null;
        rfids.forEach(function (uid) {
            if (uid) bookCardByRfid.set(uid, card);
        });
    });

    function findBookCardByRfid(uid) {
        if (!uid) return null;
        return bookCardByRfid.get(uid) || null;
    }

    function getCardAuthor(card) {
        if (card.__authorCache !== null) return card.__authorCache;
        let author = '';
        card.querySelectorAll('.book-details').forEach(function (detail) {
            const text = detail.textContent || '';
            if (text.toLowerCase().indexOf('author:') !== -1) {
                author = text.replace(/Author:/i, '').trim();
            }
        });
        card.__authorCache = author;
        return author;
    }

    /* ── toast popups (deduplicated) ─────────────────── */
    function showToast(type, title, message, duration) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const signature = (type || '') + '|' + (title || '') + '|' + (message || '');
        const now = Date.now();
        if (signature === lastToastSignature && (now - lastToastAt) < 1200) {
            return;
        }
        lastToastSignature = signature;
        lastToastAt = now;

        const toast = document.createElement('div');
        toast.className = 'toast-popup toast-' + (type || 'info');
        const icons = {
            success: 'fa-circle-check',
            error: 'fa-circle-xmark',
            warning: 'fa-triangle-exclamation',
            info: 'fa-circle-info'
        };
        toast.innerHTML = `
            <div class="toast-icon"><i class="fa-solid ${icons[type] || icons.info}"></i></div>
            <div class="toast-body-text">
                <div class="toast-title">${escapeHtml(title)}</div>
                <div class="toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="toast-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        `;
        container.appendChild(toast);
        const closeBtn = toast.querySelector('.toast-close');
        function remove() {
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 260);
        }
        if (closeBtn) closeBtn.addEventListener('click', remove);
        setTimeout(remove, duration || 4000);
    }

    /* ── confirmation modal (errors / warnings) ──────── */
    const kioskModalOverlay = document.getElementById('kioskModalOverlay');
    const kioskModal = document.getElementById('kioskModal');
    const kioskModalIcon = document.getElementById('kioskModalIcon');
    const kioskModalTitle = document.getElementById('kioskModalTitle');
    const kioskModalText = document.getElementById('kioskModalText');
    const kioskModalList = document.getElementById('kioskModalList');
    const kioskModalOk = document.getElementById('kioskModalOk');

    function showKioskModal(options) {
        if (!kioskModalOverlay || !kioskModal) return;
        const type = options.type || 'info';
        const icons = {
            success: 'fa-check',
            error: 'fa-xmark',
            warning: 'fa-triangle-exclamation',
            info: 'fa-circle-info'
        };
        kioskModal.className = 'kiosk-modal modal-' + type;
        kioskModalIcon.innerHTML = '<i class="fa-solid ' + (icons[type] || icons.info) + '"></i>';
        kioskModalTitle.textContent = options.title || 'Notice';
        kioskModalText.textContent = options.message || '';
        if (options.list && options.list.length) {
            kioskModalList.style.display = 'block';
            kioskModalList.innerHTML = options.list.map(item => '<li>' + escapeHtml(item) + '</li>').join('');
        } else {
            kioskModalList.style.display = 'none';
            kioskModalList.innerHTML = '';
        }
        kioskModalOverlay.classList.add('show');
        if (kioskModalOk) {
            kioskModalOk.onclick = function () {
                kioskModalOverlay.classList.remove('show');
                if (typeof options.onClose === 'function') options.onClose();
            };
        }
    }

    kioskModalOverlay && kioskModalOverlay.addEventListener('click', function (event) {
        if (event.target === kioskModalOverlay) {
            kioskModalOverlay.classList.remove('show');
        }
    });

    /* ── Transaction Complete modal helpers ── */
    function showTransactionComplete(payload) {
        if (!transactionCompleteBackdrop) return;

        pauseAutoReload();

        /* Borrower name */
        if (tcBorrowerName) {
            tcBorrowerName.textContent = displayBorrowerName.textContent || '—';
        }

        /* Book count */
        if (tcBookCount) {
            const count = selectedBooks.size;
            tcBookCount.textContent = count + ' book' + (count === 1 ? '' : 's');
        }

        /* Borrowing period */
        if (tcBorrowingPeriod) {
            tcBorrowingPeriod.textContent = displayBorrowingPeriod.textContent || '—';
        }

        /* Status */
        if (tcStatus) {
            tcStatus.textContent = (payload && payload.status) || 'Borrowed';
        }

        /* Message */
        if (transactionCompleteMessage) {
            transactionCompleteMessage.textContent =
                (payload && payload.message) || 'The books have been borrowed successfully.';
        }

        /* Books list */
        if (tcBooksList) {
            tcBooksList.innerHTML = '';
            selectedBooks.forEach(function (book) {
                const row = document.createElement('div');
                row.className = 'complete-book-row';
                row.innerHTML = `
                    <span><i class="fa-solid fa-book me-2"></i>${escapeHtml(book.title)}</span>
                    <span style="color:#8a6d76;font-style:italic;font-weight:600;">
                        ${escapeHtml(book.author || 'Unknown')}
                    </span>
                `;
                tcBooksList.appendChild(row);
            });
        }

        transactionCompleteBackdrop.classList.add('show');
    }

    function closeTransactionComplete(redirectUrl) {
        if (!transactionCompleteBackdrop) return;
        transactionCompleteBackdrop.classList.remove('show');
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    if (transactionCompleteDone) {
        transactionCompleteDone.addEventListener('click', function () {
            closeTransactionComplete(window.__borrowRedirectUrl || @json($borrowExitUrl));
        });
    }

    if (transactionCompleteBackdrop) {
        transactionCompleteBackdrop.addEventListener('click', function (event) {
            if (event.target === transactionCompleteBackdrop) {
                closeTransactionComplete(window.__borrowRedirectUrl || @json($borrowExitUrl));
            }
        });
    }

    function isBookRfid(uid) {
        return findBookCardByRfid(uid) !== null;
    }

    function setRfidStatus(message, type) {
        const settings = {
            waiting: { className: 'text-muted', icon: 'fa-id-card' },
            loading: { className: 'text-primary', icon: 'fa-spinner fa-spin' },
            success: { className: 'borrower-found', icon: 'fa-circle-check' },
            error: { className: 'borrower-error', icon: 'fa-circle-xmark' }
        };
        const selected = settings[type] || settings.waiting;
        rfidStatus.className = 'rfid-status ' + selected.className;
        rfidStatus.innerHTML = '<i class="fa-solid ' + selected.icon + ' me-1"></i>' + escapeHtml(message);
    }

    function setBookSelectionEnabled(enabled) {
        if (!bookSelectionSection) return;
        bookSelectionSection.classList.toggle('locked', !enabled);
        if (searchInput) searchInput.disabled = !enabled;
        bookCards.forEach(function (card) {
            card.setAttribute('aria-disabled', enabled ? 'false' : 'true');
            card.tabIndex = enabled ? 0 : -1;
        });
        if (enabled && searchInput) {
            setTimeout(() => searchInput.focus(), 100);
        }
    }

    function updateBookSelection() {
        if (!selectedBooksList) return;
        selectedBooksList.innerHTML = '';
        if (hiddenBookInputs) hiddenBookInputs.innerHTML = '';
        selectedBooks.forEach(function (book) {
            const selectedItem = document.createElement('div');
            selectedItem.className = 'selected-item';
            selectedItem.innerHTML = `
                <div>
                    <strong>${escapeHtml(book.title)}</strong>
                    <div class="small text-muted">${escapeHtml(book.author)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger p-1" data-remove-book="${book.id}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            selectedBooksList.appendChild(selectedItem);
            if (hiddenBookInputs) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'book_ids[]';
                hiddenInput.value = book.id;
                hiddenBookInputs.appendChild(hiddenInput);
            }
        });
        if (emptySelection) emptySelection.style.display = selectedBooks.size > 0 ? 'none' : 'block';
        if (selectedCount) {
            selectedCount.textContent = selectedBooks.size + ' selected';
            if (borrowerFound && selectedBooks.size >= effectiveLimit()) {
                selectedCount.classList.add('limit-reached');
            } else {
                selectedCount.classList.remove('limit-reached');
            }
        }
        updateBorrowButton();
    }

    function effectiveLimit() {
        const policyLimit = Number(POLICY.borrowing_limit) || 0;
        const perTx = Number(POLICY.max_books_per_transaction) || 0;
        const borrowerLimit = Number(borrowerRemainingLimit) || 0;
        const candidates = [policyLimit, perTx, borrowerLimit].filter(n => n > 0);
        if (candidates.length === 0) return 0;
        return Math.min.apply(null, candidates);
    }

    function resetSelectedBooks() {
        selectedBooks.clear();
        bookCards.forEach(function (card) {
            card.classList.remove('selected');
            const icon = card.querySelector('.selection-icon');
            if (icon) {
                icon.innerHTML = '<i class="fa-regular fa-square fa-lg"></i>';
            }
        });
        updateBookSelection();
    }

    function canProceedToBookSelection() {
        const fingerprintOk = !FINGERPRINT_REQUIRED || fingerprintVerified;
        return borrowerFound && borrowerCanBorrow && fingerprintOk;
    }

    function updateBorrowButton() {
        if (!borrowButton) return;
        const limit = effectiveLimit();
        const fingerprintOk = !FINGERPRINT_REQUIRED || fingerprintVerified;

        const disabled =
            !borrowerFound ||
            !borrowerCanBorrow ||
            !fingerprintOk ||
            selectedBooks.size === 0 ||
            (limit > 0 && selectedBooks.size > limit);

        borrowButton.disabled = disabled;
        if (reviewNext) reviewNext.disabled = disabled;
    }

    function updateUserInfoDisplay(data) {
        if (!displayBorrowerName) return;
        if (!data) {
            displayBorrowerName.textContent = '—';
            displayBorrowerNumber.textContent = '—';
            displayBorrowerTypeRow.textContent = '—';
            displayBorrowingPeriod.textContent = '—';
            displayRemainingLimit.textContent = '—';
            displayBorrowerStatus.textContent = '—';
            displayBorrowerType.textContent = 'No RFID scanned';
            displayBorrowerNumber.classList.add('placeholder');
            displayBorrowerTypeRow.classList.add('placeholder');
            displayBorrowingPeriod.classList.add('placeholder');
            displayRemainingLimit.classList.add('placeholder');
            displayBorrowerStatus.classList.add('placeholder');
            return;
        }
        displayBorrowerName.textContent = data.borrower_name || '—';
        displayBorrowerNumber.textContent = data.borrower_number || '—';
        displayBorrowerTypeRow.textContent = formatBorrowerType(data.borrower_type);
        displayBorrowingPeriod.textContent = data.borrowing_period || '—';
        displayRemainingLimit.textContent = data.remaining_limit !== undefined ? data.remaining_limit : '—';
        displayBorrowerStatus.textContent = data.can_borrow ? 'Eligible to borrow' : 'Not eligible';
        displayBorrowerType.textContent = formatBorrowerType(data.borrower_type);

        displayBorrowerNumber.classList.remove('placeholder');
        displayBorrowerTypeRow.classList.remove('placeholder');
        displayBorrowingPeriod.classList.remove('placeholder');
        displayRemainingLimit.classList.remove('placeholder');
        displayBorrowerStatus.classList.remove('placeholder');
    }

    function setFingerprintMessage(state) {
        if (!FINGERPRINT_REQUIRED || !fingerprintMessage) return;

        fingerprintMessage.classList.remove('state-ready', 'state-error', 'state-conn', 'show');
        if (state === 'hidden') {
            if (fingerprintDirect) fingerprintDirect.style.display = 'none';
            return;
        }
        if (fingerprintDirect) fingerprintDirect.style.display = 'block';

        if (state === 'ready') {
            fingerprintMessage.classList.add('state-ready', 'show');
            if (fpTitle) fpTitle.textContent = 'Please scan your fingerprint to continue';
            if (fpSub) fpSub.textContent = 'Fingerprint scanner ready. Place your finger on the fingerprint scanner.';
            if (fpActions) fpActions.style.display = 'none';
        } else if (state === 'error') {
            fingerprintMessage.classList.add('state-error', 'show');
            if (fpTitle) fpTitle.textContent = 'Fingerprint not recognized';
            if (fpSub) fpSub.textContent = 'Please try again or scan your RFID card again.';
            if (fpActions) fpActions.style.display = 'block';
            if (fingerprintRetry) {
                fingerprintRetry.className = 'btn-retry';
                fingerprintRetry.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Try Again';
            }
        } else if (state === 'conn') {
            fingerprintMessage.classList.add('state-conn', 'show');
            if (fpTitle) fpTitle.textContent = 'Fingerprint scanner not connected';
            if (fpSub) fpSub.textContent = 'Make sure the bridge is running, then reconnect.';
            if (fpActions) fpActions.style.display = 'block';
            if (fingerprintRetry) {
                fingerprintRetry.className = 'btn-retry-conn';
                fingerprintRetry.innerHTML = '<i class="fa-solid fa-plug me-1"></i> Reconnect';
            }
        }
    }

    function hideFingerprintMessages() {
        if (!FINGERPRINT_REQUIRED) return;
        setFingerprintMessage('hidden');
    }

    function clearBorrower() {
        if (borrowerIdInput) borrowerIdInput.value = '';
        if (borrowerTypeInput) borrowerTypeInput.value = '';
        if (verifiedRfidInput) verifiedRfidInput.value = '';
        if (fingerprintIdInput) fingerprintIdInput.value = '';

        borrowerFound = false;
        borrowerCanBorrow = false;
        borrowerRemainingLimit = 0;
        borrowerData = null;
        fingerprintVerified = !FINGERPRINT_REQUIRED;

        if (bookSelectionDescription) {
            bookSelectionDescription.textContent = FINGERPRINT_REQUIRED
                ? 'Verify the borrower fingerprint before selecting books.'
                : 'Scan a registered RFID before selecting books.';
        }

        clearTimeout(bookScanTimer);
        clearTimeout(bookScanResetTimer);
        clearTimeout(borrowerScanResetTimer);
        bookScanBuffer = '';
        borrowerScanBuffer = '';
        if (searchInput) searchInput.value = '';
        if (bookRfidMessage) {
            bookRfidMessage.textContent = FINGERPRINT_REQUIRED
                ? 'Scan a book after verifying your ID and fingerprint.'
                : 'Scan a book after verifying your ID.';
        }

        bookCards.forEach(function (card) {
            card.style.display = 'block';
        });
        setBookSelectionEnabled(false);
        resetSelectedBooks();
        updateBorrowButton();
        updateUserInfoDisplay(null);
        hideFingerprintMessages();
        resumeAutoReload();
    }

    function checkPolicyOnBorrower(data) {
        const errors = [];
        const warnings = [];

        const type = (data.borrower_type || '').toLowerCase();
        if (type === 'student' && !POLICY.allow_student_borrowing) {
            errors.push('Students are not allowed to borrow books based on the current library policy.');
        }
        if ((type === 'personnel' || type === 'employee' || type === 'faculty') && !POLICY.allow_personnel_borrowing) {
            errors.push('Personnel are not allowed to borrow books based on the current library policy.');
        }

        if (POLICY.borrowing_limit > 0 && Number(data.remaining_limit) <= 0) {
            warnings.push('Borrowing limit reached: ' + (data.borrower_name || 'This borrower') + ' has no remaining capacity.');
        }

        return { errors, warnings };
    }

    function checkPolicyOnSelection() {
        const errors = [];
        const limit = effectiveLimit();
        if (limit > 0 && selectedBooks.size > limit) {
            errors.push('You can only borrow up to ' + limit + ' book(s) per transaction.');
        }
        if (POLICY.max_books_per_transaction > 0 && selectedBooks.size > POLICY.max_books_per_transaction) {
            errors.push('The policy allows a maximum of ' + POLICY.max_books_per_transaction + ' book(s) per transaction.');
        }
        return errors;
    }

    /* ── step navigation buttons ─────────────────────── */
    const borrowBackIdentity = document.getElementById('borrowBackIdentity');
    if (borrowBackIdentity) {
        borrowBackIdentity.addEventListener('click', () => {
            clearBorrower();
            if (rfidInput) rfidInput.value = '';
            setRfidStatus('Waiting for RFID scan...', 'waiting');
            showStep('identity');
            if (rfidInput) rfidInput.focus();
            resumeAutoReload();
        });
    }

    const borrowBackBooks = document.getElementById('borrowBackBooks');
    if (borrowBackBooks) {
        borrowBackBooks.addEventListener('click', () => {
            showStep('books');
            if (bookRfidInput) bookRfidInput.focus();
        });
    }

    if (reviewNext) {
        reviewNext.addEventListener('click', () => {
            if (reviewNext.disabled) return;

            if (FINGERPRINT_REQUIRED && !fingerprintVerified) {
                showKioskModal({
                    type: 'error',
                    title: 'Fingerprint verification required',
                    message: 'Please go back to the identity step and scan your fingerprint first.'
                });
                return;
            }

            const policyErrors = checkPolicyOnSelection();
            if (policyErrors.length) {
                showKioskModal({
                    type: 'warning',
                    title: 'Policy Limit Exceeded',
                    message: 'Your selection does not comply with the library policy.',
                    list: policyErrors
                });
                return;
            }

            if (borrowReviewPersonName) borrowReviewPersonName.textContent = displayBorrowerName.textContent;
            if (borrowReviewPersonPeriod) borrowReviewPersonPeriod.textContent = displayBorrowingPeriod.textContent || '—';

            if (borrowReviewBooks) {
                borrowReviewBooks.innerHTML = '';
                selectedBooks.forEach(book => {
                    const row = document.createElement('div');
                    row.className = 'confirm-book-row';
                    row.innerHTML = `
                        <div style="flex:1;min-width:0;">
                            <div class="confirm-book-title">${escapeHtml(book.title)}</div>
                            <div class="confirm-book-meta">
                                by ${escapeHtml(book.author || 'Unknown')}
                            </div>
                        </div>
                        <span class="confirm-book-badge ontime">To Borrow</span>
                    `;
                    borrowReviewBooks.appendChild(row);
                });
            }
            if (borrowReviewTotal) borrowReviewTotal.textContent = selectedBooks.size + ' book(s)';

            showStep('review');
        });
    }

    if (FINGERPRINT_REQUIRED && fingerprintRetry) {
        fingerprintRetry.addEventListener('click', function () {
            hideFingerprintMessages();
            window.dispatchEvent(new CustomEvent('transaction-fingerprint-reset'));
            if (borrowerFound && borrowerCanBorrow) {
                setFingerprintMessage('ready');
            }
            if (rfidInput) {
                rfidInput.value = '';
                rfidInput.focus();
            }
            setRfidStatus('Waiting for RFID scan...', 'waiting');
        });
    }

    /* ── find borrower ───────────────────────────────── */
    async function findBorrower(rfid) {
        if (isBookRfid(rfid)) {
            clearBorrower();
            setRfidStatus('This RFID belongs to a book, not a borrower.', 'error');
            showToast('warning', 'Book RFID detected', 'This RFID is a book tag. Please scan a borrower ID instead.');
            if (rfidInput) {
                rfidInput.value = '';
                rfidInput.focus();
            }
            return;
        }

        clearBorrower();
        pauseAutoReload();
        setRfidStatus('Searching the database...', 'loading');
        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        try {
            const routeTemplate = @json($borrowerFindByRfidUrl);
            const url = routeTemplate.replace('__RFID__', encodeURIComponent(rfid));
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: activeRequest.signal
            });
            const data = await response.json();
            if (!response.ok || !data.found) {
                throw new Error(data.message || 'The RFID card was not found.');
            }
            if (borrowerIdInput) borrowerIdInput.value = data.borrower_id;
            if (borrowerTypeInput) borrowerTypeInput.value = data.borrower_type;
            if (verifiedRfidInput) verifiedRfidInput.value = rfid;
            borrowerFound = true;
            borrowerCanBorrow = Boolean(data.can_borrow);
            borrowerRemainingLimit = Number(data.remaining_limit);
            borrowerData = data;

            updateUserInfoDisplay(data);

            const policy = checkPolicyOnBorrower(data);
            if (policy.errors.length) {
                borrowerCanBorrow = false;
                showKioskModal({
                    type: 'error',
                    title: 'Policy Violation',
                    message: 'The borrower is not allowed to borrow under the current library policy.',
                    list: policy.errors
                });
            }
            if (policy.warnings.length) {
                showKioskModal({
                    type: 'warning',
                    title: 'Borrowing Limit Reached',
                    message: policy.warnings[0],
                    list: [
                        'Borrowing limit: ' + (POLICY.borrowing_limit || borrowerRemainingLimit) + ' book(s)',
                        'Remaining limit: ' + (data.remaining_limit ?? 0),
                        'Please return borrowed books before borrowing again.'
                    ]
                });
            }

            if (borrowerCanBorrow) {
                if (FINGERPRINT_REQUIRED) {
                    setBookSelectionEnabled(false);
                    if (bookSelectionDescription) {
                        bookSelectionDescription.textContent = 'Verify the borrower fingerprint before selecting books.';
                    }
                    setFingerprintMessage('ready');
                } else {
                    fingerprintVerified = true;
                    if (fingerprintIdInput) fingerprintIdInput.value = '';
                    setBookSelectionEnabled(true);
                    if (bookSelectionDescription) {
                        bookSelectionDescription.textContent = 'Scan a book tag or select a book from the list.';
                    }
                    if (bookRfidMessage) {
                        bookRfidMessage.textContent = 'Ready to scan a book tag.';
                    }
                    setRfidStatus(
                        data.message || ('RFID found: ' + data.borrower_name),
                        'success'
                    );
                    updateBorrowButton();

                    if (borrowerFound && borrowerCanBorrow) {
                        showStep('books');
                        if (bookRfidInput) bookRfidInput.focus();
                    }
                }
            } else {
                setBookSelectionEnabled(false);
                if (bookSelectionDescription) {
                    bookSelectionDescription.textContent = 'This borrower cannot borrow additional books.';
                }
                hideFingerprintMessages();
            }

            if (FINGERPRINT_REQUIRED) {
                setRfidStatus(
                    data.message || ('RFID found: ' + data.borrower_name),
                    borrowerCanBorrow ? 'success' : 'error'
                );
                updateBorrowButton();
            }

            if (rfidInput) {
                rfidInput.value = '';
                rfidInput.focus();
            }
        } catch (error) {
            if (error.name === 'AbortError') return;
            clearBorrower();
            if (rfidInput) {
                rfidInput.value = '';
                rfidInput.focus();
            }
            setRfidStatus(error.message, 'error');
            resumeAutoReload();
        } finally {
            activeRequest = null;
        }
    }

    if (FINGERPRINT_REQUIRED) {
        window.addEventListener('transaction-fingerprint-verified', function (event) {
            fingerprintVerified = true;

            if (fingerprintIdInput) {
                const fromEvent =
                    (event && event.detail && (
                        event.detail.fingerprint_id ??
                        event.detail.fingerprintId ??
                        event.detail.id
                    )) || null;

                if (fromEvent !== null && fromEvent !== undefined && fromEvent !== '') {
                    fingerprintIdInput.value = fromEvent;
                }
            }

            hideFingerprintMessages();
            if (borrowerFound && borrowerCanBorrow) {
                setBookSelectionEnabled(true);
                if (bookSelectionDescription) {
                    bookSelectionDescription.textContent = 'Scan a book tag or select a book from the list.';
                }
                if (bookRfidMessage) {
                    bookRfidMessage.textContent = 'Ready to scan a book tag.';
                }
                showStep('books');
                if (bookRfidInput) bookRfidInput.focus();
            }
            updateBorrowButton();
            pauseAutoReload();
        });

        window.addEventListener('transaction-fingerprint-rejected', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            setFingerprintMessage('error');
            setBookSelectionEnabled(false);
            resetSelectedBooks();
            updateBorrowButton();
        });

        window.addEventListener('transaction-fingerprint-reset', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            hideFingerprintMessages();
            setBookSelectionEnabled(false);
            resetSelectedBooks();
            updateBorrowButton();
        });

        window.addEventListener('transaction-fingerprint-connection-error', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            setFingerprintMessage('conn');
            setBookSelectionEnabled(false);
            resetSelectedBooks();
            updateBorrowButton();
        });
    }

    /* ── STEP 1 RFID input handling ── */
    if (rfidInput) {
        rfidInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });

        rfidInput.addEventListener('input', function () {
            clearTimeout(scanTimer);
            pauseAutoReload();
            clearBorrower();
            const rfid = this.value.trim();
            if (!rfid) {
                setRfidStatus('Waiting for RFID scan...', 'waiting');
                resumeAutoReload();
                return;
            }
            setRfidStatus('Reading RFID...', 'loading');
            scanTimer = setTimeout(function () {
                findBorrower(rfid);
            }, 60);
        });
    }

    /* ── GLOBAL RFID LISTENER FOR STEP 1 (borrower) ── */
    document.addEventListener('keydown', function (event) {
        const identityStepVisible = document.getElementById('identityStep') && !document.getElementById('identityStep').hidden;
        if (!identityStepVisible) return;

        const tag = (event.target && event.target.tagName || '').toLowerCase();
        const isOtherInput = (tag === 'input' || tag === 'textarea' || tag === 'select')
            && event.target !== rfidInput;
        if (isOtherInput) return;

        if (event.key === 'Enter') {
            if (borrowerScanBuffer.trim()) {
                event.preventDefault();
                const code = borrowerScanBuffer.trim();
                borrowerScanBuffer = '';
                clearTimeout(borrowerScanResetTimer);
                if (rfidInput) rfidInput.value = code;
                findBorrower(code);
            }
            return;
        }

        if (event.key.length === 1 && /[a-zA-Z0-9\-_]/.test(event.key)) {
            borrowerScanBuffer += event.key;
            clearTimeout(borrowerScanResetTimer);
            borrowerScanResetTimer = setTimeout(() => {
                if (borrowerScanBuffer.trim()) {
                    const code = borrowerScanBuffer.trim();
                    borrowerScanBuffer = '';
                    if (rfidInput) rfidInput.value = code;
                    findBorrower(code);
                }
            }, 60);
        }
    });

    /* ── book toggling ───────────────────────────────── */
    function toggleBook(card) {
        if (!canProceedToBookSelection()) {
            showToast('warning', 'Verification required',
                FINGERPRINT_REQUIRED
                    ? 'Please verify your RFID and fingerprint first.'
                    : 'Please scan an eligible registered RFID card.');
            if (rfidInput) rfidInput.focus();
            return;
        }
        const id = card.dataset.bookId;
        if (selectedBooks.has(id)) {
            selectedBooks.delete(id);
            card.classList.remove('selected');
            const icon = card.querySelector('.selection-icon');
            if (icon) icon.innerHTML = '<i class="fa-regular fa-square fa-lg"></i>';
        } else {
            const limit = effectiveLimit();
            if (limit > 0 && selectedBooks.size >= limit) {
                showKioskModal({
                    type: 'warning',
                    title: 'Borrowing Limit Reached',
                    message: 'You have reached the maximum number of books allowed.',
                    list: [
                        'Policy limit: ' + (POLICY.borrowing_limit || limit) + ' book(s)',
                        'Max per transaction: ' + (POLICY.max_books_per_transaction || limit) + ' book(s)',
                        'Your remaining limit: ' + borrowerRemainingLimit + ' book(s)',
                        'Selected: ' + selectedBooks.size + ' book(s)'
                    ]
                });
                return;
            }
            selectedBooks.set(id, {
                id: id,
                title: card.__titleEl ? card.__titleEl.textContent.trim() : 'Untitled',
                author: getCardAuthor(card)
            });
            card.classList.add('selected');
            const icon = card.querySelector('.selection-icon');
            if (icon) icon.innerHTML = '<i class="fa-solid fa-square-check fa-lg text-danger"></i>';
        }
        updateBookSelection();
    }

    /* ── book RFID scanning ──────────────────────────── */
    function handleScannedBookUid(uid) {
        if (!uid) return;
        const card = findBookCardByRfid(uid);

        if (!card) {
            const currentBorrowerRfid = verifiedRfidInput ? verifiedRfidInput.value : '';
            if (currentBorrowerRfid && uid === currentBorrowerRfid && borrowerData) {
                const typeLabel = formatBorrowerType(borrowerData.borrower_type);
                const message = 'This RFID is only for books, not a user RFID. (Registered as ' + typeLabel + ')';
                if (bookRfidMessage) bookRfidMessage.textContent = message;
                showToast('warning', 'Invalid RFID', message);
            } else {
                const message = 'This RFID is not registered as a book.';
                if (bookRfidMessage) bookRfidMessage.textContent = message;
                showToast('warning', 'Unregistered RFID', message);
            }
            return;
        }

        if (selectedBooks.has(card.dataset.bookId)) {
            if (bookRfidMessage) bookRfidMessage.textContent = 'This book is already in Selected Books.';
            return;
        }

        toggleBook(card);
        if (selectedBooks.has(card.dataset.bookId)) {
            if (bookRfidMessage) bookRfidMessage.textContent = 'Book added to Selected Books.';
        }
    }

    /* ── Merged bar: manual input (search) + Enter to submit ── */
    if (bookRfidInput) {
        bookRfidInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                clearTimeout(bookScanTimer);
                const typed = bookRfidInput.value.trim();
                if (!typed) return;

                const matchedCard = findBookCardByRfid(typed);
                if (matchedCard) {
                    handleScannedBookUid(typed);
                    bookRfidInput.value = '';
                    bookRfidInput.focus();
                } else if (looksLikeRfidScan(typed)) {
                    handleScannedBookUid(typed);
                    bookRfidInput.value = '';
                    bookRfidInput.focus();
                } else {
                    const query = typed.toLowerCase();
                    const anyVisible = bookCards.some(function (card) {
                        const searchText = [card.dataset.title, card.dataset.author, card.dataset.callNumber].join(' ');
                        return searchText.includes(query);
                    });
                    if (!anyVisible) {
                        showToast('info', 'No matching book', 'No book matches "' + typed + '".');
                    }
                }
            }
        });

        bookRfidInput.addEventListener('input', function () {
            const value = this.value;
            const trimmed = value.trim();

            if (trimmed && findBookCardByRfid(trimmed)) {
                handleScannedBookUid(trimmed);
                bookRfidInput.value = '';
                return;
            }

            const now = Date.now();
            bookInputKeyTimes.push(now);
            bookInputKeyTimes = bookInputKeyTimes.filter(t => now - t < 800);
            if (bookInputKeyTimes.length === 1) bookInputFirstKeyAt = now;

            const elapsed = now - bookInputFirstKeyAt;
            const isFastScan = bookInputKeyTimes.length >= 6 && elapsed < 500;

            clearTimeout(bookScanTimer);
            bookScanTimer = setTimeout(() => {
                const current = bookRfidInput.value.trim();

                if (isFastScan && looksLikeRfidScan(current) && !findBookCardByRfid(current)) {
                    handleScannedBookUid(current);
                    bookRfidInput.value = '';
                    bookCards.forEach(function (card) { card.style.display = 'block'; });
                    bookInputKeyTimes = [];
                    return;
                }

                const query = current.toLowerCase();
                bookCards.forEach(function (card) {
                    const searchText = [card.dataset.title, card.dataset.author, card.dataset.callNumber].join(' ');
                    card.style.display = searchText.includes(query) ? 'block' : 'none';
                });
                bookInputKeyTimes = [];
            }, 50);
        });
    }

    /* ── GLOBAL RFID LISTENER FOR STEP 2 (book) ── */
    document.addEventListener('keydown', function (event) {
        const booksStepVisible = bookSelectionSection && !bookSelectionSection.hidden;
        if (!booksStepVisible) return;

        const tag = (event.target && event.target.tagName || '').toLowerCase();
        const isOtherInput = (tag === 'input' || tag === 'textarea' || tag === 'select')
            && event.target !== searchInput;
        if (isOtherInput) return;

        if (event.key.length === 1 && /[a-zA-Z0-9\-_]/.test(event.key)) {
            bookScanBuffer += event.key;
            clearTimeout(bookScanResetTimer);

            const matchedCard = findBookCardByRfid(bookScanBuffer);
            if (matchedCard) {
                handleScannedBookUid(bookScanBuffer);
                bookScanBuffer = '';
                bookScanResetTimer = null;
                return;
            }

            bookScanResetTimer = setTimeout(() => {
                if (!bookScanBuffer) return;
                const uid = bookScanBuffer;
                bookScanBuffer = '';
                bookScanResetTimer = null;
                handleScannedBookUid(uid);
                bookCards.forEach(function (card) { card.style.display = 'block'; });
            }, 40);
        }
    });

    /* ── book card click & keyboard ──────────────────── */
    bookCards.forEach(function (card) {
        card.addEventListener('click', function () {
            toggleBook(card);
        });
        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleBook(card);
            }
        });
    });

    /* ── remove from selected list ───────────────────── */
    if (selectedBooksList) {
        selectedBooksList.addEventListener('click', function (event) {
            const button = event.target.closest('[data-remove-book]');
            if (!button) return;
            const bookId = button.dataset.removeBook;
            const card = document.querySelector('.book-card[data-book-id="' + bookId + '"]');
            if (card) toggleBook(card);
        });
    }

    /* ── form submit guard + success/failure popup ───── */
    if (borrowForm) {
        borrowForm.addEventListener('submit', function (event) {
            if (!event.submitter || event.submitter !== borrowButton) {
                event.preventDefault();
                if (rfidInput) rfidInput.focus();
                return;
            }
            if (!borrowerFound || !borrowerCanBorrow || !verifiedRfidInput.value) {
                event.preventDefault();
                showKioskModal({
                    type: 'error',
                    title: 'Borrower not verified',
                    message: 'Please scan an eligible registered RFID card.'
                });
                if (rfidInput) rfidInput.focus();
                return;
            }

            if (FINGERPRINT_REQUIRED && !fingerprintVerified) {
                event.preventDefault();
                showKioskModal({
                    type: 'error',
                    title: 'Fingerprint verification required',
                    message: 'Please go back to the identity step and scan your fingerprint.'
                });
                return;
            }

            if (selectedBooks.size === 0) {
                event.preventDefault();
                showKioskModal({
                    type: 'warning',
                    title: 'No books selected',
                    message: 'Please select at least one book before borrowing.'
                });
                return;
            }

            const policyErrors = checkPolicyOnSelection();
            if (policyErrors.length) {
                event.preventDefault();
                showKioskModal({
                    type: 'warning',
                    title: 'Policy Limit Exceeded',
                    message: 'Your selection does not comply with the library policy.',
                    list: policyErrors
                });
                return;
            }

            const limit = effectiveLimit();
            if (limit > 0 && selectedBooks.size > limit) {
                event.preventDefault();
                showKioskModal({
                    type: 'warning',
                    title: 'Borrowing limit exceeded',
                    message: 'The selected books exceed your borrowing limit.',
                    list: [
                        'Policy limit: ' + POLICY.borrowing_limit + ' book(s)',
                        'Max per transaction: ' + POLICY.max_books_per_transaction + ' book(s)',
                        'Your remaining limit: ' + borrowerRemainingLimit + ' book(s)',
                        'Selected: ' + selectedBooks.size + ' book(s)'
                    ]
                });
                return;
            }

            event.preventDefault();

            pauseAutoReload();

            borrowButton.disabled = true;
            borrowButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Processing...';

            const formData = new FormData(borrowForm);

            if (!formData.get('borrower_id') && borrowerIdInput && borrowerIdInput.value) {
                formData.set('borrower_id', borrowerIdInput.value);
            }
            if (!formData.get('borrower_type') && borrowerTypeInput && borrowerTypeInput.value) {
                formData.set('borrower_type', borrowerTypeInput.value);
            }
            if (!formData.get('rfid_tag_uid') && verifiedRfidInput && verifiedRfidInput.value) {
                formData.set('rfid_tag_uid', verifiedRfidInput.value);
            }

            /*
             * CRITICAL: fingerprint payload policy.
             *
             * If the policy is ON  → fingerprint_id must be present and sent.
             * If the policy is OFF → fingerprint_id must be REMOVED from the
             *                        payload so the backend never validates
             *                        or requires it.
             */
            if (FINGERPRINT_REQUIRED) {
                if (!fingerprintIdInput || !fingerprintIdInput.value) {
                    borrowButton.disabled = false;
                    borrowButton.innerHTML = '<i class="fa-solid fa-check me-1"></i> Confirm Borrowing';
                    showKioskModal({
                        type: 'error',
                        title: 'Fingerprint verification required',
                        message: 'Please go back to the identity step and scan your fingerprint before confirming.'
                    });
                    return;
                }
                formData.set('fingerprint_id', fingerprintIdInput.value);
            } else {
                // Policy OFF — make absolutely sure fingerprint_id is not sent.
                formData.delete('fingerprint_id');
            }

            fetch(borrowForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async (response) => {
                let payload = null;
                try {
                    payload = await response.json();
                } catch (e) {
                    payload = null;
                }
                if (!response.ok) {
                    let message = 'The borrowing request could not be completed.';

                    if (payload) {
                        if (payload.message) {
                            message = payload.message;
                        }
                        if (payload.errors && typeof payload.errors === 'object') {
                            const firstKey = Object.keys(payload.errors)[0];
                            if (firstKey && Array.isArray(payload.errors[firstKey]) && payload.errors[firstKey][0]) {
                                message = payload.errors[firstKey][0];
                            }
                        }
                    }

                    const err = new Error(message);
                    err.errors = payload && payload.errors ? payload.errors : null;
                    throw err;
                }
                return payload || { success: true };
            })
            .then((payload) => {
                if (payload && payload.success === false) {
                    throw new Error(payload.message || 'The borrowing request could not be completed.');
                }

                /* Show the Transaction Complete notification (matches Reserve page UI) */
                const redirectUrl = (payload && payload.redirect) || @json($borrowExitUrl);
                window.__borrowRedirectUrl = redirectUrl;
                showTransactionComplete(payload || {});

                borrowButton.disabled = false;
                borrowButton.innerHTML = '<i class="fa-solid fa-check me-1"></i> Confirm Borrowing';
            })
            .catch((error) => {
                let message = error.message || 'The borrowing request could not be completed. Please try again.';

                showKioskModal({
                    type: 'error',
                    title: 'Borrowing Failed',
                    message: message
                });
                borrowButton.disabled = false;
                borrowButton.innerHTML = '<i class="fa-solid fa-check me-1"></i> Confirm Borrowing';
                resumeAutoReload();
            });
        });
    }

    /* ── init ────────────────────────────────────────── */
    updateBookSelection();
    setBookSelectionEnabled(false);
    if (rfidInput) rfidInput.focus();
    showStep('identity');
    hideFingerprintMessages();

    // Start the auto-reload countdown
    startAutoReload();

    // Pause/resume auto-reload when the page visibility changes
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            resumeAutoReload();
        } else {
            pauseAutoReload();
        }
    });
});
</script>
@include('layouts.system_settings_live')
</body>
</html>