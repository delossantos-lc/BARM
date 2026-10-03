@php
    $systemSettings = $systemSettings ?? \App\Models\SystemSetting::current();

    $libraryPolicy = \App\Models\LibraryPolicy::current();
    $fingerprintRequired = (bool) (
        $libraryPolicy?->require_fingerprint_for_borrowing ?? false
    );

    $returnStoreUrl = \Illuminate\Support\Facades\Route::has('return.store')
        ? route('return.store')
        : url('/return');
    $returnExitUrl = \Illuminate\Support\Facades\Route::has('return.exit')
        ? route('return.exit')
        : url('/return/exit');
    $returnBorrowerFindUrl = \Illuminate\Support\Facades\Route::has('return.borrower.find')
        ? route('return.borrower.find', ['rfid' => '__RFID__'])
        : url('/return/borrower/__RFID__');

    $hasFingerprintComponent = view()->exists('components.fingerprint-transaction-verification');

    /*
     * Global index of every book RFID so Step 1 can immediately recognise a
     * book tag scan and show the "Book RFID detected" toast.
     */
    $allBookRfids = \App\Models\BookCopy::query()
        ->whereNotNull('rfid_tag_uid')
        ->pluck('rfid_tag_uid')
        ->filter()
        ->map(fn ($v) => (string) $v)
        ->values()
        ->all();

    // Auto-reload interval in seconds (default 60 seconds, configurable via system settings)
    $autoReloadSeconds = max(10, (int) ($systemSettings->kiosk_auto_reload_seconds ?? 60));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title data-system-title-prefix="Return Books">Return Books | {{ $systemSettings->system_short_name ?? 'BARM' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script>
        /* Global set of every book RFID so Step 1 can distinguish book tags. */
        window.__ALL_BOOK_RFIDS = @json($allBookRfids);
    </script>
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

        #bookSelectionSection { flex: 1; min-height: 0; }

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
            display: flex; flex-direction: column;
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

        .summary-book-meta .overdue-badge {
            display: inline-block;
            padding: 1px 8px; border-radius: 20px;
            background: #fde8e8; color: #a93226;
            font-weight: 700;
            font-size: clamp(0.65rem, 0.8vw, 0.78rem);
            margin-left: 6px;
        }

        .summary-book-meta .ontime-badge {
            display: inline-block;
            padding: 1px 8px; border-radius: 20px;
            background: #e9f8ee; color: #18763c;
            font-weight: 700;
            font-size: clamp(0.65rem, 0.8vw, 0.78rem);
            margin-left: 6px;
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

        /* ── toasts ─────────────────────────────────────── */
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

        /* ── Kiosk modal (retained for optional fallback) ── */
        .kiosk-modal-overlay {
            position: fixed; inset: 0; z-index: 100000;
            display: none; align-items: center; justify-content: center;
            padding: 20px;
            background: rgba(24, 18, 22, 0.55);
            backdrop-filter: blur(4px);
            animation: modalFadeIn 0.22s ease;
        }

        .kiosk-modal-overlay.show { display: flex; }

        .kiosk-modal {
            width: min(480px, 94vw); background: #fff;
            border-radius: 22px;
            border: 1.5px solid #f0d9e2;
            box-shadow: 0 28px 72px rgba(20, 10, 20, 0.3);
            padding: 32px 30px 26px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: modalPopIn 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .kiosk-modal::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 6px;
        }
        .kiosk-modal.modal-success::before { background: linear-gradient(90deg, #27ae60, #1e9e5a); }
        .kiosk-modal.modal-error::before   { background: linear-gradient(90deg, #e74c3c, #c0392b); }
        .kiosk-modal.modal-warning::before { background: linear-gradient(90deg, #f1c40f, #d4a017); }
        .kiosk-modal.modal-info::before    { background: linear-gradient(90deg, #3498db, #2b7bbd); }

        .kiosk-modal .modal-icon {
            width: 84px; height: 84px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 2.4rem; color: #fff;
            position: relative;
            animation: kioskIconPop 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .kiosk-modal .modal-icon::after {
            content: '';
            position: absolute; inset: -10px;
            border-radius: 50%;
            opacity: 0.18;
            animation: iconPulse 2.2s ease-in-out infinite;
        }

        @keyframes kioskIconPop {
            0%   { transform: scale(0.6); opacity: 0; }
            60%  { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); opacity: 0.18; }
            50%      { transform: scale(1.14); opacity: 0.06; }
        }

        .kiosk-modal.modal-success .modal-icon {
            background: linear-gradient(135deg, #2ecc71, #1e9e5a);
            box-shadow: 0 12px 28px rgba(39, 174, 96, 0.42);
        }
        .kiosk-modal.modal-success .modal-icon::after { background: #27ae60; }

        .kiosk-modal.modal-error .modal-icon {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            box-shadow: 0 12px 28px rgba(231, 76, 60, 0.42);
        }
        .kiosk-modal.modal-error .modal-icon::after { background: #e74c3c; }

        .kiosk-modal.modal-warning .modal-icon {
            background: linear-gradient(135deg, #f5c518, #d4a017);
            box-shadow: 0 12px 28px rgba(241, 196, 15, 0.45);
        }
        .kiosk-modal.modal-warning .modal-icon::after { background: #f1c40f; }

        .kiosk-modal.modal-info .modal-icon {
            background: linear-gradient(135deg, #3498db, #2b7bbd);
            box-shadow: 0 12px 28px rgba(52, 152, 219, 0.42);
        }
        .kiosk-modal.modal-info .modal-icon::after { background: #3498db; }

        .kiosk-modal .modal-title {
            font-size: clamp(1.2rem, 1.5vw, 1.42rem);
            font-weight: 900;
            color: #202124;
            margin-bottom: 10px;
            line-height: 1.2;
            letter-spacing: 0.2px;
        }
        .kiosk-modal.modal-success .modal-title { color: #1e9e5a; }
        .kiosk-modal.modal-error   .modal-title { color: #c0392b; }
        .kiosk-modal.modal-warning .modal-title { color: #202124; }
        .kiosk-modal.modal-info    .modal-title { color: #2b7bbd; }

        .kiosk-modal .modal-text {
            font-size: clamp(0.88rem, 1vw, 0.98rem);
            color: #5a5f66;
            line-height: 1.55;
            margin: 0 auto 4px;
            max-width: 42ch;
            word-break: break-word;
        }

        .kiosk-modal .modal-list {
            text-align: left;
            margin: 18px 0 4px;
            padding: 16px 20px;
            background: #f9f9fb;
            border: 1px solid #ececf2;
            border-radius: 14px;
            font-size: clamp(0.8rem, 0.92vw, 0.9rem);
            color: #3a3f45;
            max-height: 220px;
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
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--pink-dark);
            box-shadow: 0 0 0 3px rgba(215, 96, 145, 0.15);
        }

        .kiosk-modal .modal-list li.receipt-line {
            font-weight: 800;
            color: var(--pink-dark);
            background: #fff0f5;
            padding: 8px 12px 8px 22px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #f4d3e0;
        }
        .kiosk-modal .modal-list li.receipt-line::before {
            background: var(--pink-dark);
            top: 13px;
        }

        .kiosk-modal .modal-actions {
            display: flex; gap: 10px; justify-content: center;
            margin-top: 22px; flex-wrap: wrap;
        }

        .kiosk-modal .modal-actions .modal-ok-btn {
            min-width: 150px;
            min-height: 48px;
            border-radius: 12px;
            font-size: clamp(0.85rem, 1vw, 0.95rem);
            font-weight: 800;
            letter-spacing: 0.3px;
            border: none; color: #fff;
            background: var(--pink-dark);
            display: inline-flex; align-items: center; justify-content: center;
            gap: 6px;
            padding: 0 26px;
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

        /* ── Transaction Complete modal (success card) ──── */
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
            animation: completeModalPop 0.18s ease-out;
        }
        @keyframes completeModalPop {
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
            text-align: left; margin-bottom: 14px;
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
            border-radius: 999px; background: #dff3e5; color: #18763c;
            font-weight: 800; font-size: clamp(0.6rem, 0.75vw, 0.7rem);
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .complete-modal .complete-books-list {
            display: flex; flex-direction: column;
            gap: 6px; margin: 0 0 6px;
            max-height: 170px; overflow-y: auto;
            padding-right: 2px;
        }
        .complete-modal .complete-books-list::-webkit-scrollbar { width: 5px; }
        .complete-modal .complete-books-list::-webkit-scrollbar-thumb { background: #edbdcf; border-radius: 3px; }
        .complete-modal .complete-book-row {
            display: flex; justify-content: space-between;
            align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            background: #fff; border: 1px solid #f4d3e0;
            font-size: clamp(0.72rem, 0.88vw, 0.82rem);
            font-weight: 700; color: #202124;
            text-align: left;
        }
        .complete-modal .complete-book-row.receipt {
            background: #fff0f5; border-color: #f4d3e0;
            color: var(--pink-dark); font-weight: 800;
        }
        .complete-modal .complete-book-row i {
            color: var(--pink-dark); flex-shrink: 0;
        }
        .complete-modal .confirm-actions {
            display: flex; gap: 10px; justify-content: center;
            margin-top: 6px;
        }
        .complete-modal .confirm-actions .btn-confirm {
            flex: 1; min-height: 44px;
            font-size: clamp(0.78rem, 1vw, 0.92rem);
            border-radius: 10px;
        }

        /* ── Review / Confirmation step ─────────────────── */
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
        .confirm-card-actions .btn-back {
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
                <h1 class="top-left-title">Return Books</h1>
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
            <form action="{{ $returnStoreUrl }}" method="POST" id="returnForm" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                @csrf

                {{-- STEP 1: IDENTITY --}}
                <section id="identityStep" data-flow-step="identity">
                    <div class="identity-two-columns">
                        <div class="identity-left-col">
                            <h2 class="section-title">Scan Borrower RFID</h2>
                            <p class="section-description">Place the RFID card on the reader to retrieve the borrower and their borrowed books.</p>

                            <div class="scan-area">
                                <div class="scan-icon"><i class="fa-solid fa-id-card"></i></div>
                                <div class="scan-heading">Scan Student or Personnel RFID</div>
                                <p class="scan-instruction">
                                    @if($fingerprintRequired)
                                        Borrower information and borrowed books will appear on the right, then verify the fingerprint.
                                    @else
                                        Borrower information and borrowed books will appear on the right.
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
                                    <span class="info-label">Books Borrowed</span>
                                    <span class="info-value placeholder" id="displayBooksBorrowed">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Overdue Books</span>
                                    <span class="info-value placeholder" id="displayOverdueCount">—</span>
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
                            <h2 class="section-title">Select Books to Return</h2>
                            <p id="bookSelectionDescription" class="section-description" style="margin-bottom:0;">
                                @if($fingerprintRequired)
                                    Verify the borrower fingerprint before selecting books.
                                @else
                                    Scan a registered RFID before selecting books.
                                @endif
                            </p>
                        </div>

                        <div class="book-scan-search-bar">
                            <span class="input-group-text"><i class="fa-solid fa-barcode me-1"></i>Scan / Search</span>
                            <input
                                type="text"
                                id="returnBookRfid"
                                class="form-control"
                                placeholder="Scan book RFID or type to search..."
                                autocomplete="off"
                                disabled
                            >
                        </div>
                    </div>

                    <div id="returnBookMessage" class="book-rfid-message" aria-live="polite">
                        @if($fingerprintRequired)
                            Scan a book after verifying your ID and fingerprint.
                        @else
                            Scan a book after verifying your ID.
                        @endif
                    </div>

                    <div class="book-selection-body">
                        <div class="book-list-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Borrowed Books</strong>
                                <span id="availableBorrowsCount" class="count-badge">0 borrowed</span>
                            </div>
                            <div id="borrowedBooksArea" class="book-list">
                                <div class="empty-selection">
                                    <i class="fa-solid fa-book"></i>
                                    <strong>Waiting for RFID</strong>
                                    <p class="mb-0 mt-1">Scan a registered RFID card to display borrowed books.</p>
                                </div>
                            </div>
                        </div>

                        <div class="selected-books-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Books to Return</strong>
                                <span id="selectedCount" class="count-badge">0 selected</span>
                            </div>
                            <div class="selected-books-box">
                                <div id="emptySelection" class="empty-selection">
                                    <i class="fa-solid fa-book"></i>
                                    <strong>No books selected</strong>
                                    <p class="mb-0 mt-1">Select a borrowed book from the list.</p>
                                </div>
                                <div id="selectedBooksList"></div>
                            </div>
                            <div id="selectedBorrowInputs"></div>
                        </div>
                    </div>

                    <div class="review-actions">
                        <button type="button" id="returnBackIdentity" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Change ID</button>
                        <button type="button" id="returnReviewNext" class="btn-confirm" disabled>Review return <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </section>

                {{-- STEP 3: REVIEW & CONFIRM --}}
                <section class="review-panel" data-flow-step="review" hidden>
                    <div class="review-body">
                        <div class="confirm-card">

                            <div class="confirm-card-header">
                                <div class="confirm-card-title">
                                    <i class="fa-solid fa-book-open"></i>
                                    Return Confirmation
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
                                    <span class="confirm-row-value" id="returnReviewPersonName">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Books Borrowed</span>
                                    <span class="confirm-row-value" id="returnReviewBooksBorrowed">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Books to Return</span>
                                    <span class="confirm-row-value pink" id="returnReviewTotal">0 book(s)</span>
                                </div>
                            </div>

                            <div class="confirm-books-block">
                                <div class="confirm-books-label">Selected Books</div>
                                <div class="confirm-books-list" id="returnReviewBooks"></div>
                            </div>

                            <div class="confirm-card-actions">
                                <button type="button" id="returnBackBooks" class="btn-back">
                                    <i class="fa-solid fa-arrow-left"></i> Go back
                                </button>
                                <button type="submit" id="confirmReturnButton" class="btn-confirm" disabled>
                                    <i class="fa-solid fa-calendar-check"></i> Confirm Return
                                </button>
                            </div>

                            <div class="confirm-cancel-link">
                                <a href="{{ $returnExitUrl }}">Cancel and return to kiosk</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>

        <div class="back-row">
            <a href="{{ $returnExitUrl }}" class="btn">
                <i class="fa-solid fa-arrow-left me-1"></i>Back to kiosk choices
            </a>
        </div>

    </div>
</div>

{{-- toast container --}}
<div class="toast-container" id="toastContainer"></div>

{{-- KIOSK MODAL — retained for optional future use --}}
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

{{-- TRANSACTION COMPLETE modal (success card) --}}
<div class="confirm-modal-backdrop" id="transactionCompleteBackdrop">
    <div class="complete-modal" role="dialog" aria-modal="true" aria-labelledby="transactionCompleteTitle">
        <div class="confirm-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <div class="confirm-title" id="transactionCompleteTitle">Transaction Complete</div>
        <div class="confirm-message" id="transactionCompleteMessage">
            The books have been returned successfully.
        </div>

        <div class="complete-summary">
            <div class="summary-row">
                <span class="summary-label">Borrower</span>
                <span class="summary-value" id="tcBorrowerName">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Books Returned</span>
                <span class="summary-value pink" id="tcBookCount">0 book(s)</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Overdue</span>
                <span class="summary-value" id="tcOverdueCount">0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Status</span>
                <span class="summary-value">
                    <span class="complete-status-badge" id="tcStatus">Returned</span>
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
    const AUTO_RELOAD_SECONDS = {{ $autoReloadSeconds }};

    /* Global set of every book RFID in the system (from the blade). */
    const ALL_BOOK_RFIDS = new Set(
        Array.isArray(window.__ALL_BOOK_RFIDS)
            ? window.__ALL_BOOK_RFIDS.map(String)
            : []
    );

    const RFID_SCAN_MIN_LENGTH   = 6;
    const RFID_SCAN_MAX_TOTAL_MS = 250;
    const RFID_SCAN_MAX_GAP_MS   = 50;
    const BOOK_SCAN_IDLE_MS      = 120;

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
    const returnForm = document.getElementById('returnForm');
    const rfidInput = document.getElementById('rfid_scan_input');
    const verifiedRfidInput = document.getElementById('rfid_tag_uid');
    const rfidStatus = document.getElementById('rfidStatus');
    const borrowerIdInput = document.getElementById('borrower_id');
    const borrowerTypeInput = document.getElementById('borrower_type');
    const fingerprintIdInput = document.getElementById('fingerprint_id');
    const bookSelectionSection = document.getElementById('bookSelectionSection');
    const bookSelectionDescription = document.getElementById('bookSelectionDescription');
    const searchInput = document.getElementById('returnBookRfid');
    const bookRfidInput = document.getElementById('returnBookRfid');
    const bookRfidMessage = document.getElementById('returnBookMessage');
    const borrowedBooksArea = document.getElementById('borrowedBooksArea');
    const availableBorrowsCount = document.getElementById('availableBorrowsCount');
    const selectedBooksList = document.getElementById('selectedBooksList');
    const selectedBorrowInputs = document.getElementById('selectedBorrowInputs');
    const emptySelection = document.getElementById('emptySelection');
    const selectedCount = document.getElementById('selectedCount');
    const confirmReturnButton = document.getElementById('confirmReturnButton');
    const reviewNext = document.getElementById('returnReviewNext');

    const returnReviewPersonName = document.getElementById('returnReviewPersonName');
    const returnReviewBooksBorrowed = document.getElementById('returnReviewBooksBorrowed');
    const returnReviewBooks = document.getElementById('returnReviewBooks');
    const returnReviewTotal = document.getElementById('returnReviewTotal');

    const fingerprintMessage = document.getElementById('fingerprintMessage');
    const fpTitle = document.getElementById('fpTitle');
    const fpSub = document.getElementById('fpSub');
    const fpActions = document.getElementById('fpActions');
    const fingerprintRetry = document.getElementById('fingerprintRetry');
    const fingerprintDirect = document.getElementById('fingerprintDirect');

    const displayBorrowerName = document.getElementById('displayBorrowerName');
    const displayBorrowerNumber = document.getElementById('displayBorrowerNumber');
    const displayBorrowerTypeRow = document.getElementById('displayBorrowerTypeRow');
    const displayBooksBorrowed = document.getElementById('displayBooksBorrowed');
    const displayOverdueCount = document.getElementById('displayOverdueCount');
    const displayBorrowerStatus = document.getElementById('displayBorrowerStatus');
    const displayBorrowerType = document.getElementById('displayBorrowerType');

    /* ── Transaction Complete modal elements ── */
    const transactionCompleteBackdrop = document.getElementById('transactionCompleteBackdrop');
    const transactionCompleteMessage = document.getElementById('transactionCompleteMessage');
    const transactionCompleteDone = document.getElementById('transactionCompleteDone');
    const tcBorrowerName = document.getElementById('tcBorrowerName');
    const tcBookCount = document.getElementById('tcBookCount');
    const tcOverdueCount = document.getElementById('tcOverdueCount');
    const tcStatus = document.getElementById('tcStatus');
    const tcBooksList = document.getElementById('tcBooksList');

    /* ── state ───────────────────────────────────────── */
    const selectedBorrows = new Map();
    let borrowerFound = false;
    let fingerprintVerified = !FINGERPRINT_REQUIRED;

    let availableBorrows = [];
    let borrowerData = null;
    let scanTimer = null;
    let activeRequest = null;

    /* ── Perf: RFID index built once when borrower is found ── */
    let bookRfidToBorrowId = new Map();

    /* ── Perf: cached per-card book text for search filter ── */
    const bookCardTextCache = new WeakMap();

    let bookScanBuffer = '';
    let bookScanResetTimer = null;

    let borrowerScanBuffer = '';
    let borrowerScanResetTimer = null;
    let borrowerScanFirstKeyAt = 0;
    let borrowerScanLastKeyAt = 0;
    let borrowerScanMaxGap = 0;

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

            if (transactionCompleteBackdrop && transactionCompleteBackdrop.classList.contains('show')) {
                return;
            }
            if (kioskModalOverlay && kioskModalOverlay.classList.contains('show')) {
                return;
            }

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

    function isHardwareScan(buffer, firstAt, lastAt, maxGap) {
        if (!buffer || buffer.length < RFID_SCAN_MIN_LENGTH) return false;
        if (!firstAt || !lastAt) return false;
        const totalDuration = lastAt - firstAt;
        if (totalDuration > RFID_SCAN_MAX_TOTAL_MS) return false;
        if (maxGap > RFID_SCAN_MAX_GAP_MS) return false;
        return /^[a-zA-Z0-9\-_]+$/.test(buffer);
    }

    function formatBorrowerType(type) {
        if (type === 'student') return 'Student';
        if (type === 'personnel' || type === 'employee' || type === 'faculty') return 'Personnel';
        return type ? (type.charAt(0).toUpperCase() + type.slice(1)) : 'Unknown';
    }

    function extractErrorMessage(payload) {
        if (payload == null) return null;

        if (typeof payload === 'string') {
            const trimmed = payload.trim();
            if (!trimmed) return null;
            const stripped = trimmed.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            return stripped.slice(0, 300) || null;
        }

        if (typeof payload !== 'object') return null;

        if (typeof payload.message === 'string' && payload.message.trim()) {
            return payload.message.trim();
        }

        if (typeof payload.error === 'string' && payload.error.trim()) {
            return payload.error.trim();
        }

        if (payload.errors && typeof payload.errors === 'object') {
            const keys = Object.keys(payload.errors);
            if (keys.length) {
                const first = payload.errors[keys[0]];
                if (Array.isArray(first) && typeof first[0] === 'string') {
                    return first[0];
                }
                if (typeof first === 'string') {
                    return first;
                }
            }
        }

        return null;
    }

    function looksLikePhpNotice(text) {
        if (!text) return false;
        return /Attempt to read property|Trying to access array offset|Undefined (property|array key|variable)|Call to a member function/i.test(text);
    }

    /* ── toast popups (matching Borrow page style) ── */
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

    /* ── kiosk modal (retained for optional use) ─────── */
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
            kioskModalList.innerHTML = options.list.map(function (item) {
                if (item && typeof item === 'object' && item.receipt) {
                    return '<li class="receipt-line">' + escapeHtml(item.text) + '</li>';
                }
                const text = (item && typeof item === 'object') ? (item.text || '') : item;
                return '<li>' + escapeHtml(text) + '</li>';
            }).join('');
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

        if (tcBorrowerName) {
            tcBorrowerName.textContent = displayBorrowerName.textContent || '—';
        }

        if (tcBookCount) {
            const count = selectedBorrows.size;
            tcBookCount.textContent = count + ' book' + (count === 1 ? '' : 's');
        }

        if (tcOverdueCount) {
            let overdue = 0;
            selectedBorrows.forEach(function (b) { if (b.is_overdue) overdue++; });
            tcOverdueCount.textContent = overdue;
        }

        if (tcStatus) {
            const statusText = (payload && typeof payload === 'object' && typeof payload.status === 'string')
                ? payload.status
                : 'Returned';
            tcStatus.textContent = statusText;
        }

        if (transactionCompleteMessage) {
            const msg = (payload && typeof payload === 'object' && typeof payload.message === 'string' && payload.message.trim())
                ? payload.message
                : 'The books have been returned successfully.';
            transactionCompleteMessage.textContent = msg;
        }

        if (tcBooksList) {
            tcBooksList.innerHTML = '';

            const receiptNumber = (payload && typeof payload === 'object')
                ? (payload.receipt_number || payload.transaction_id || null)
                : null;

            if (receiptNumber) {
                const receiptRow = document.createElement('div');
                receiptRow.className = 'complete-book-row receipt';
                receiptRow.innerHTML = `
                    <span><i class="fa-solid fa-receipt me-2"></i>Receipt No.: ${escapeHtml(String(receiptNumber))}</span>
                `;
                tcBooksList.appendChild(receiptRow);
            }

            selectedBorrows.forEach(function (borrow) {
                const row = document.createElement('div');
                row.className = 'complete-book-row';
                const badgeText = borrow.is_overdue ? 'Overdue' : 'On Time';
                const badgeColor = borrow.is_overdue ? '#a93226' : '#18763c';
                row.innerHTML = `
                    <span><i class="fa-solid fa-book me-2"></i>${escapeHtml(borrow.title)}</span>
                    <span style="color:${badgeColor};font-style:italic;font-weight:700;">
                        ${escapeHtml(badgeText)}
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
            closeTransactionComplete(window.__returnRedirectUrl || @json($returnExitUrl));
        });
    }

    if (transactionCompleteBackdrop) {
        transactionCompleteBackdrop.addEventListener('click', function (event) {
            if (event.target === transactionCompleteBackdrop) {
                closeTransactionComplete(window.__returnRedirectUrl || @json($returnExitUrl));
            }
        });
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
        bookSelectionSection.classList.toggle('locked', !enabled);
        searchInput.disabled = !enabled;
    }

    function getOverdueCount() {
        let n = 0;
        for (let i = 0; i < availableBorrows.length; i++) {
            if (availableBorrows[i].is_overdue) n++;
        }
        return n;
    }

    function isBookRfid(uid) {
        if (!uid) return false;
        return bookRfidToBorrowId.has(String(uid));
    }

    function rebuildBookRfidIndex() {
        bookRfidToBorrowId = new Map();
        availableBorrows.forEach(function (borrow) {
            const tags = Array.isArray(borrow.rfid_tags) ? borrow.rfid_tags : [];
            const id = String(borrow.borrow_id);
            tags.forEach(function (tag) {
                if (tag) bookRfidToBorrowId.set(String(tag), id);
            });
        });
    }

    function updateUserInfoDisplay(data) {
        if (!data) {
            displayBorrowerName.textContent = '—';
            displayBorrowerNumber.textContent = '—';
            displayBorrowerTypeRow.textContent = '—';
            displayBooksBorrowed.textContent = '—';
            displayOverdueCount.textContent = '—';
            displayBorrowerStatus.textContent = '—';
            displayBorrowerType.textContent = 'No RFID scanned';
            displayBorrowerNumber.classList.add('placeholder');
            displayBorrowerTypeRow.classList.add('placeholder');
            displayBooksBorrowed.classList.add('placeholder');
            displayOverdueCount.classList.add('placeholder');
            displayBorrowerStatus.classList.add('placeholder');
            return;
        }
        displayBorrowerName.textContent = data.borrower_name || '—';
        displayBorrowerNumber.textContent = data.borrower_number || '—';
        displayBorrowerTypeRow.textContent = formatBorrowerType(data.borrower_type);
        displayBooksBorrowed.textContent = availableBorrows.length;
        displayOverdueCount.textContent = getOverdueCount();
        displayBorrowerStatus.textContent = availableBorrows.length > 0 ? 'Has borrowed books' : 'No borrowed books';
        displayBorrowerType.textContent = formatBorrowerType(data.borrower_type);

        displayBorrowerNumber.classList.remove('placeholder');
        displayBorrowerTypeRow.classList.remove('placeholder');
        displayBooksBorrowed.classList.remove('placeholder');
        displayOverdueCount.classList.remove('placeholder');
        displayBorrowerStatus.classList.remove('placeholder');
    }

    function setFingerprintMessage(state) {
        if (!FINGERPRINT_REQUIRED || !fingerprintMessage) return;

        fingerprintMessage.classList.remove('state-ready', 'state-error', 'state-conn', 'show');
        if (state === 'hidden') {
            fingerprintDirect.style.display = 'none';
            return;
        }
        fingerprintDirect.style.display = 'block';

        if (state === 'ready') {
            fingerprintMessage.classList.add('state-ready', 'show');
            fpTitle.textContent = 'Please scan your fingerprint to continue';
            fpSub.textContent = 'Fingerprint scanner ready. Place your finger on the fingerprint scanner.';
            fpActions.style.display = 'none';
        } else if (state === 'error') {
            fingerprintMessage.classList.add('state-error', 'show');
            fpTitle.textContent = 'Fingerprint not recognized';
            fpSub.textContent = 'Please try again or scan your RFID card again.';
            fpActions.style.display = 'block';
            fingerprintRetry.className = 'btn-retry';
            fingerprintRetry.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Try Again';
        } else if (state === 'conn') {
            fingerprintMessage.classList.add('state-conn', 'show');
            fpTitle.textContent = 'Fingerprint scanner not connected';
            fpSub.textContent = 'Make sure the bridge is running, then reconnect.';
            fpActions.style.display = 'block';
            fingerprintRetry.className = 'btn-retry-conn';
            fingerprintRetry.innerHTML = '<i class="fa-solid fa-plug me-1"></i> Reconnect';
        }
    }

    function hideFingerprintMessages() {
        if (!FINGERPRINT_REQUIRED) return;
        setFingerprintMessage('hidden');
    }

    function clearBorrower() {
        borrowerIdInput.value = '';
        borrowerTypeInput.value = '';
        verifiedRfidInput.value = '';
        if (fingerprintIdInput) fingerprintIdInput.value = '';

        borrowerFound = false;
        fingerprintVerified = !FINGERPRINT_REQUIRED;
        availableBorrows = [];
        borrowerData = null;
        selectedBorrows.clear();
        bookRfidToBorrowId = new Map();

        bookSelectionDescription.textContent = FINGERPRINT_REQUIRED
            ? 'Verify the borrower fingerprint before selecting books.'
            : 'Scan a registered RFID before selecting books.';

        searchInput.value = '';
        bookRfidMessage.textContent = FINGERPRINT_REQUIRED
            ? 'Scan a book after verifying your ID and fingerprint.'
            : 'Scan a book after verifying your ID.';

        setBookSelectionEnabled(false);
        renderBorrowedBooks();
        updateSelection();
        updateUserInfoDisplay(null);
        hideFingerprintMessages();
    }

    /* ── step navigation buttons ─────────────────────── */
    document.getElementById('returnBackIdentity').addEventListener('click', () => {
        clearBorrower();
        rfidInput.value = '';
        setRfidStatus('Waiting for RFID scan...', 'waiting');
        showStep('identity');
        rfidInput.focus();
        resumeAutoReload();
    });

    document.getElementById('returnBackBooks').addEventListener('click', () => {
        showStep('books');
        bookRfidInput.focus();
    });

    reviewNext.addEventListener('click', () => {
        if (reviewNext.disabled) return;

        returnReviewPersonName.textContent = displayBorrowerName.textContent;
        returnReviewBooksBorrowed.textContent = availableBorrows.length;

        const frag = document.createDocumentFragment();
        let overdueInSelection = 0;
        selectedBorrows.forEach(borrow => {
            if (borrow.is_overdue) overdueInSelection++;

            const badgeClass = borrow.is_overdue ? 'overdue' : 'ontime';
            const badgeText = borrow.is_overdue ? 'Overdue' : 'On Time';

            const row = document.createElement('div');
            row.className = 'confirm-book-row';
            row.innerHTML = `
                <div style="flex:1;min-width:0;">
                    <div class="confirm-book-title">${escapeHtml(borrow.title)}</div>
                    <div class="confirm-book-meta">
                        by ${escapeHtml(borrow.author || 'Unknown')} • Due: ${escapeHtml(borrow.due_date)}
                    </div>
                </div>
                <span class="confirm-book-badge ${badgeClass}">${badgeText}</span>
            `;
            frag.appendChild(row);
        });
        returnReviewBooks.innerHTML = '';
        returnReviewBooks.appendChild(frag);

        returnReviewTotal.textContent = selectedBorrows.size + ' book(s)' +
            (overdueInSelection > 0 ? ' — ' + overdueInSelection + ' overdue' : '');

        showStep('review');
    });

    if (FINGERPRINT_REQUIRED && fingerprintRetry) {
        fingerprintRetry.addEventListener('click', function () {
            hideFingerprintMessages();
            window.dispatchEvent(new CustomEvent('transaction-fingerprint-reset'));
            if (borrowerFound) {
                setFingerprintMessage('ready');
            }
            rfidInput.value = '';
            rfidInput.focus();
            setRfidStatus('Waiting for RFID scan...', 'waiting');
        });
    }

    /* ── find borrower ───────────────────────────────── */
    async function findBorrower(rfid) {
        clearBorrower();
        pauseAutoReload();
        setRfidStatus('Searching the database...', 'loading');
        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        try {
            const routeTemplate = @json($returnBorrowerFindUrl);
            const url = routeTemplate.replace('__RFID__', encodeURIComponent(rfid));
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: activeRequest.signal,
                cache: 'no-store'
            });
            const data = await response.json();

            if (!response.ok || !data.found) {
                /*
                 * Decide whether the scanned RFID is a book:
                 *  1. Prefer the server's own hint (data.is_book / data.type).
                 *  2. Fall back to the global client-side index of every book
                 *     RFID in the system so Step 1 always shows the correct
                 *     "Book RFID detected" toast (matching Borrow page).
                 */
                const serverSaysBook = data && (data.is_book === true || data.type === 'book');
                const clientSaysBook = ALL_BOOK_RFIDS.has(String(rfid));
                const isBook = serverSaysBook || clientSaysBook;

                if (isBook) {
                    clearBorrower();
                    setRfidStatus('This RFID belongs to a book, not a borrower.', 'error');
                    showToast(
                        'warning',
                        'Book RFID detected',
                        'This RFID is a book tag. Please scan a borrower ID instead.'
                    );
                } else {
                    clearBorrower();
                    setRfidStatus(data.message || 'The RFID card was not found.', 'error');
                    showToast('warning', 'RFID not found', data.message || 'The RFID card was not found.');
                }

                rfidInput.value = '';
                rfidInput.focus();
                resumeAutoReload();
                return;
            }

            borrowerIdInput.value = data.borrower_id;
            borrowerTypeInput.value = data.borrower_type;
            verifiedRfidInput.value = rfid;
            borrowerFound = true;
            availableBorrows = Array.isArray(data.borrowed_books) ? data.borrowed_books : [];
            borrowerData = data;

            rebuildBookRfidIndex();

            updateUserInfoDisplay(data);
            renderBorrowedBooks();
            updateSelection();

            if (FINGERPRINT_REQUIRED) {
                setBookSelectionEnabled(false);
                bookSelectionDescription.textContent = 'Verify the borrower fingerprint before selecting books.';
                setFingerprintMessage('ready');
                setRfidStatus('RFID found: ' + data.borrower_name, 'success');
                updateSelection();
            } else {
                fingerprintVerified = true;
                if (fingerprintIdInput) fingerprintIdInput.value = '';
                setBookSelectionEnabled(true);
                bookSelectionDescription.textContent = 'Scan a book tag or select a borrowed book from the list.';
                bookRfidMessage.textContent = 'Ready to scan a borrowed book tag.';
                setRfidStatus('RFID found: ' + data.borrower_name, 'success');
                updateSelection();

                if (borrowerFound) {
                    showStep('books');
                    bookRfidInput.focus();
                }
            }

            rfidInput.value = '';
            rfidInput.focus();
        } catch (error) {
            if (error.name === 'AbortError') return;
            clearBorrower();
            rfidInput.value = '';
            setRfidStatus(error.message, 'error');
            showToast('error', 'RFID lookup failed', error.message || 'The RFID card could not be looked up.');
            rfidInput.focus();
            resumeAutoReload();
        } finally {
            activeRequest = null;
        }
    }

    /* ── fingerprint events ──────────────────────────── */
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
            if (borrowerFound) {
                setBookSelectionEnabled(true);
                bookSelectionDescription.textContent = 'Scan a book tag or select a borrowed book from the list.';
                bookRfidMessage.textContent = 'Ready to scan a borrowed book tag.';
                showStep('books');
                bookRfidInput.focus();
            }
            updateSelection();
            pauseAutoReload();
        });

        window.addEventListener('transaction-fingerprint-rejected', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            setFingerprintMessage('error');
            setBookSelectionEnabled(false);
            selectedBorrows.clear();
            renderBorrowedBooks();
            updateSelection();
        });

        window.addEventListener('transaction-fingerprint-reset', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            hideFingerprintMessages();
            setBookSelectionEnabled(false);
            selectedBorrows.clear();
            renderBorrowedBooks();
            updateSelection();
        });

        window.addEventListener('transaction-fingerprint-connection-error', function () {
            fingerprintVerified = false;
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            setFingerprintMessage('conn');
            setBookSelectionEnabled(false);
            selectedBorrows.clear();
            renderBorrowedBooks();
            updateSelection();
        });
    }

    /* ── RFID input handling (Step 1) ─────────────────── */
    rfidInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') event.preventDefault();
    });

    rfidInput.addEventListener('input', function () {
        clearTimeout(scanTimer);
        clearBorrower();
        pauseAutoReload();
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

    document.addEventListener('keydown', function (event) {
        const identityStepVisible = document.getElementById('identityStep') && !document.getElementById('identityStep').hidden;
        if (!identityStepVisible) return;

        const tag = (event.target && event.target.tagName || '').toLowerCase();
        const isOtherInput = (tag === 'input' || tag === 'textarea' || tag === 'select')
            && event.target !== rfidInput;
        if (isOtherInput) return;

        if (event.key === 'Enter') {
            if (borrowerScanBuffer.trim()) {
                const code = borrowerScanBuffer.trim();
                const now = Date.now();
                const hardwareScan = isHardwareScan(
                    code,
                    borrowerScanFirstKeyAt,
                    borrowerScanLastKeyAt || now,
                    borrowerScanMaxGap
                );
                borrowerScanBuffer = '';
                borrowerScanFirstKeyAt = 0;
                borrowerScanLastKeyAt = 0;
                borrowerScanMaxGap = 0;
                clearTimeout(borrowerScanResetTimer);

                if (hardwareScan) {
                    event.preventDefault();
                    rfidInput.value = code;
                    findBorrower(code);
                }
            }
            return;
        }

        if (event.key.length === 1 && /[a-zA-Z0-9\-_]/.test(event.key)) {
            const now = Date.now();
            if (!borrowerScanBuffer) {
                borrowerScanFirstKeyAt = now;
                borrowerScanMaxGap = 0;
            } else {
                const gap = now - borrowerScanLastKeyAt;
                if (gap > borrowerScanMaxGap) borrowerScanMaxGap = gap;
            }
            borrowerScanLastKeyAt = now;
            borrowerScanBuffer += event.key;

            clearTimeout(borrowerScanResetTimer);
            borrowerScanResetTimer = setTimeout(() => {
                if (!borrowerScanBuffer) return;
                const code = borrowerScanBuffer;
                const hardwareScan = isHardwareScan(
                    code,
                    borrowerScanFirstKeyAt,
                    borrowerScanLastKeyAt,
                    borrowerScanMaxGap
                );
                borrowerScanBuffer = '';
                borrowerScanFirstKeyAt = 0;
                borrowerScanLastKeyAt = 0;
                borrowerScanMaxGap = 0;

                if (hardwareScan) {
                    rfidInput.value = code;
                    findBorrower(code);
                }
            }, RFID_SCAN_MAX_GAP_MS + 30);
        }
    });

    /* ── render borrowed books ───────────────────────── */
    function renderBorrowedBooks() {
        if (!borrowerFound) {
            borrowedBooksArea.innerHTML = `
                <div class="empty-selection">
                    <i class="fa-solid fa-book"></i>
                    <strong>Waiting for RFID</strong>
                    <p class="mb-0 mt-1">Scan a registered RFID card to display borrowed books.</p>
                </div>
            `;
            availableBorrowsCount.textContent = '0 borrowed';
            return;
        }
        if (availableBorrows.length === 0) {
            borrowedBooksArea.innerHTML = `
                <div class="empty-selection">
                    <i class="fa-solid fa-circle-check"></i>
                    <strong>No borrowed books</strong>
                    <p class="mb-0 mt-1">This borrower has no books currently marked as borrowed.</p>
                </div>
            `;
            availableBorrowsCount.textContent = '0 borrowed';
            return;
        }
        availableBorrowsCount.textContent = availableBorrows.length + ' borrowed';

        const frag = document.createDocumentFragment();
        availableBorrows.forEach(function (borrow) {
            const id = String(borrow.borrow_id);
            const isSelected = selectedBorrows.has(id);
            const selectedClass = isSelected ? 'selected' : '';
            const checkboxIcon = isSelected ? 'fa-solid fa-square-check fa-lg text-danger' : 'fa-regular fa-square fa-lg';
            const deadlineBadge = borrow.is_overdue
                ? '<span class="overdue-badge">Overdue</span>'
                : '<span class="ontime-badge">On Time</span>';

            const card = document.createElement('div');
            card.className = 'book-card ' + selectedClass;
            card.dataset.borrowId = id;
            card.tabIndex = 0;

            const title = borrow.title || '';
            const author = borrow.author || 'Unknown';
            const callNumber = borrow.call_number || '-';
            const borrowedAt = borrow.borrowed_at || '-';
            const dueDate = borrow.due_date || '-';

            card.innerHTML = `
                <span class="selection-icon"><i class="${checkboxIcon}"></i></span>
                <div class="book-title">${escapeHtml(title)}</div>
                <p class="book-details"><i class="fa-solid fa-user-pen"></i><span>Author: <strong>${escapeHtml(author)}</strong></span></p>
                <p class="book-details"><i class="fa-solid fa-bookmark"></i><span>Call No: <strong>${escapeHtml(callNumber)}</strong></span></p>
                <p class="book-details"><i class="fa-solid fa-calendar-plus"></i><span>Borrowed: <strong>${escapeHtml(borrowedAt)}</strong></span></p>
                <p class="book-details"><i class="fa-solid fa-calendar-check"></i><span>Due: <strong>${escapeHtml(dueDate)}</strong></span> ${deadlineBadge}</p>
            `;

            /* Cache lowercase text for fast search filtering */
            bookCardTextCache.set(card, card.textContent.toLowerCase());

            frag.appendChild(card);
        });

        borrowedBooksArea.innerHTML = '';
        borrowedBooksArea.appendChild(frag);
    }

    /* ── update selected list ────────────────────────── */
    function updateSelection() {
        const frag = document.createDocumentFragment();
        const inputFrag = document.createDocumentFragment();
        selectedBorrows.forEach(function (borrow) {
            const selectedItem = document.createElement('div');
            selectedItem.className = 'selected-item';
            selectedItem.innerHTML = `
                <div>
                    <strong>${escapeHtml(borrow.title)}</strong>
                    <div class="small text-muted">Due: ${escapeHtml(borrow.due_date)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger p-1" data-remove-borrow="${borrow.borrow_id}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            frag.appendChild(selectedItem);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'borrow_ids[]';
            hiddenInput.value = borrow.borrow_id;
            inputFrag.appendChild(hiddenInput);
        });
        selectedBooksList.innerHTML = '';
        selectedBooksList.appendChild(frag);
        selectedBorrowInputs.innerHTML = '';
        selectedBorrowInputs.appendChild(inputFrag);

        emptySelection.style.display = selectedBorrows.size > 0 ? 'none' : 'block';
        selectedCount.textContent = selectedBorrows.size + ' selected';

        if (borrowerFound) {
            displayOverdueCount.textContent = getOverdueCount();
        }

        confirmReturnButton.disabled = !borrowerFound || !fingerprintVerified || selectedBorrows.size === 0;
        reviewNext.disabled = confirmReturnButton.disabled;
    }

    /* ── toggle a borrowed book ─────────────────────── */
    function toggleBorrow(borrowID) {
        if (!borrowerFound) {
            showToast('warning', 'No borrower', 'Please scan a registered RFID card first.');
            rfidInput.focus();
            return;
        }
        if (FINGERPRINT_REQUIRED && !fingerprintVerified) {
            showToast('warning', 'Fingerprint required', 'Please verify the RFID owner fingerprint before selecting books.');
            return;
        }
        const record = availableBorrows.find(function (borrow) {
            return String(borrow.borrow_id) === String(borrowID);
        });
        if (!record) return;
        const id = String(borrowID);
        if (selectedBorrows.has(id)) {
            selectedBorrows.delete(id);
        } else {
            selectedBorrows.set(id, record);
        }
        renderBorrowedBooks();
        updateSelection();
    }

    /*
     * Clear the search input and reset any list filtering applied by typing.
     * Called after a hardware scan so the search bar stays empty.
     */
    function clearSearchAndFilter() {
        if (searchInput) searchInput.value = '';
        const cards = borrowedBooksArea.children;
        for (let i = 0; i < cards.length; i++) {
            const card = cards[i];
            if (card.classList && card.classList.contains('book-card')) {
                card.style.display = '';
            }
        }
    }

    /* ── book RFID handling (Step 2) ─────────────────── */
    /*
     * Returns true when the scan was actually handled (success or a
     * recognised non-book RFID) so the caller knows to reset the search box.
     *
     * Toast messages here mirror the Borrow page wording exactly.
     */
    function handleScannedBookUid(uid) {
        if (!uid) return false;

        if (!borrowerFound || (FINGERPRINT_REQUIRED && !fingerprintVerified)) {
            const message = FINGERPRINT_REQUIRED
                ? 'Verify your ID and fingerprint first.'
                : 'Verify your ID first.';
            bookRfidMessage.textContent = message;
            showToast('warning', 'Verification required', message);
            return false;
        }

        /*
         * Detect whether the scanned RFID belongs to the borrower.
         *
         * The reader may return the tag in a slightly different format than
         * what was stored during Step 1 (case, whitespace, leading zeros,
         * or a formatted variant). We compare against several normalizations
         * so the "Invalid RFID" toast always shows when the user scans their
         * own ID at the book-scan step.
         */
        const currentBorrowerRfid = verifiedRfidInput ? String(verifiedRfidInput.value || '') : '';
        const borrowerNumber = borrowerData ? String(borrowerData.borrower_number || '') : '';
        const scanned = String(uid);

        function normalize(value) {
            return String(value || '')
                .trim()
                .toUpperCase()
                .replace(/[\s\-_]/g, '')
                .replace(/^0+/, '');
        }

        const normalizedScanned = normalize(scanned);
        const normalizedBorrowerRfid = normalize(currentBorrowerRfid);
        const normalizedBorrowerNumber = normalize(borrowerNumber);

        const matchesBorrowerRfid =
            normalizedBorrowerRfid &&
            normalizedScanned &&
            normalizedScanned === normalizedBorrowerRfid;

        const matchesBorrowerNumber =
            normalizedBorrowerNumber &&
            normalizedScanned &&
            (
                normalizedScanned === normalizedBorrowerNumber ||
                normalizedScanned.endsWith(normalizedBorrowerNumber) ||
                normalizedBorrowerNumber.endsWith(normalizedScanned)
            );

        if (matchesBorrowerRfid || matchesBorrowerNumber) {
            const typeLabel = formatBorrowerType(borrowerData && borrowerData.borrower_type);
            const message = 'This RFID is only for books, not a user RFID. (Registered as ' + typeLabel + ')';
            bookRfidMessage.textContent = message;
            showToast('warning', 'Invalid RFID', message);
            return true;
        }

        const borrowId = bookRfidToBorrowId.get(String(uid));
        if (!borrowId) {
            const message = 'This RFID is not registered as a borrowed book for this borrower.';
            bookRfidMessage.textContent = message;
            showToast('warning', 'Unregistered RFID', message);
            return true;
        }

        if (!selectedBorrows.has(borrowId)) {
            toggleBorrow(borrowId);
        }
        if (selectedBorrows.has(borrowId)) {
            bookRfidMessage.textContent = 'Book added to Books to Return.';

            const record = availableBorrows.find(function (b) {
                return String(b.borrow_id) === String(borrowId);
            });
            showToast(
                'success',
                'Book added',
                (record && record.title ? record.title : 'Book') + ' has been added to the return list.'
            );
        }
        return true;
    }

    /*
     * Manual book input.
     * - Enter: treat the typed value as an RFID (or ignore if not a known tag).
     * - Input: only filter the borrowed-books list. Hardware scans are handled
     *   by the global keydown listener below and NEVER auto-fill this input.
     */
    bookRfidInput.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter') return;
        event.preventDefault();

        const typed = bookRfidInput.value.trim();
        if (!typed) return;

        const borrowId = bookRfidToBorrowId.get(String(typed));
        if (borrowId) {
            handleScannedBookUid(typed);
        } else {
            bookRfidMessage.textContent = 'No matching borrowed book was found for that value.';
        }
        bookRfidInput.value = '';
        clearSearchAndFilter();
    });

    bookRfidInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        const cards = borrowedBooksArea.children;
        for (let i = 0; i < cards.length; i++) {
            const card = cards[i];
            if (!card.classList || !card.classList.contains('book-card')) continue;
            const cached = bookCardTextCache.get(card);
            const text = cached !== undefined ? cached : card.textContent.toLowerCase();
            card.style.display = (!query || text.includes(query)) ? '' : 'none';
        }
    });

    /*
     * Global keydown for Step 2 hardware scans.
     *
     * When focus is NOT in the search input, every printable keystroke is
     * buffered. When the buffer stops growing for a short idle window, we
     * treat it as a scanned RFID and process it. The search input is ALWAYS
     * cleared afterwards so a scan never leaves text in the box and never
     * filters the list.
     *
     * UPDATE: This now also captures scans when the search input IS focused,
     * preventing the RFID characters from being typed into the box and
     * ensuring the "Invalid RFID" error appears immediately.
     */
    document.addEventListener('keydown', function (event) {
        const booksStepVisible = bookSelectionSection && !bookSelectionSection.hidden;
        if (!booksStepVisible) return;

        const tag = (event.target && event.target.tagName || '').toLowerCase();
        const isOtherInput = (tag === 'input' || tag === 'textarea' || tag === 'select')
            && event.target !== searchInput;
        if (isOtherInput) return;

        // Check if the search input is currently focused
        const isSearchFocused = document.activeElement === searchInput;

        if (event.key.length === 1 && /[a-zA-Z0-9\-_]/.test(event.key)) {
            // If the search box is focused, prevent the character from being typed.
            // The RFID reader will send the full string, and we will capture it here.
            if (isSearchFocused) {
                event.preventDefault();
            }

            bookScanBuffer += event.key;

            clearTimeout(bookScanResetTimer);
            bookScanResetTimer = setTimeout(function () {
                const uid = bookScanBuffer;
                bookScanBuffer = '';
                bookScanResetTimer = null;

                if (!uid) return;

                const handled = handleScannedBookUid(uid);
                if (handled) {
                    clearSearchAndFilter();
                }
            }, BOOK_SCAN_IDLE_MS);
        }
    });

    borrowedBooksArea.addEventListener('click', function (event) {
        const card = event.target.closest('[data-borrow-id]');
        if (!card) return;
        toggleBorrow(card.dataset.borrowId);
    });

    borrowedBooksArea.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const card = event.target.closest('[data-borrow-id]');
        if (!card) return;
        event.preventDefault();
        toggleBorrow(card.dataset.borrowId);
    });

    selectedBooksList.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-borrow]');
        if (!button) return;
        toggleBorrow(button.dataset.removeBorrow);
    });

    /* ── form submit ─────────────────────────────────── */
    returnForm.addEventListener('submit', function (event) {
        if (!event.submitter || event.submitter !== confirmReturnButton) {
            event.preventDefault();
            rfidInput.focus();
            return;
        }
        if (!borrowerFound || !verifiedRfidInput.value) {
            event.preventDefault();
            showToast('error', 'Borrower not verified', 'Please scan a registered RFID card.');
            rfidInput.focus();
            return;
        }
        if (FINGERPRINT_REQUIRED && !fingerprintVerified) {
            event.preventDefault();
            showToast('error', 'Fingerprint verification required', 'Please complete fingerprint verification first.');
            return;
        }
        if (selectedBorrows.size === 0) {
            event.preventDefault();
            showToast('warning', 'No books selected', 'Please select at least one book to return.');
            return;
        }

        event.preventDefault();

        pauseAutoReload();

        confirmReturnButton.disabled = true;
        confirmReturnButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Processing...';

        const formData = new FormData(returnForm);

        if (FINGERPRINT_REQUIRED) {
            if (!fingerprintIdInput || !fingerprintIdInput.value) {
                confirmReturnButton.disabled = false;
                confirmReturnButton.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirm Return';
                showToast('error', 'Fingerprint verification required',
                    'Please go back to the identity step and scan your fingerprint before confirming.');
                return;
            }
            formData.set('fingerprint_id', fingerprintIdInput.value);
        } else {
            formData.delete('fingerprint_id');
        }

        fetch(returnForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async (response) => {
            let rawText = '';
            try {
                rawText = await response.text();
            } catch (e) {
                rawText = '';
            }

            let payload = null;
            if (rawText) {
                try {
                    payload = JSON.parse(rawText);
                } catch (e) {
                    payload = rawText;
                }
            }

            if (payload && typeof payload === 'object' && payload.success === false) {
                let message = extractErrorMessage(payload);
                if (!message || looksLikePhpNotice(message)) {
                    message = 'The return could not be completed. Please try again, or ask staff for assistance.';
                }
                const err = new Error(message);
                err.payload = payload;
                throw err;
            }

            if (!payload || typeof payload !== 'object') {
                payload = { success: true };
            } else if (typeof payload.success === 'undefined') {
                payload.success = true;
            }

            return payload;
        })
        .then((payload) => {
            const redirectUrl = (payload && typeof payload === 'object' && payload.redirect)
                ? payload.redirect
                : @json($returnExitUrl);
            window.__returnRedirectUrl = redirectUrl;
            showTransactionComplete(payload || {});

            confirmReturnButton.disabled = false;
            confirmReturnButton.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirm Return';
        })
        .catch((error) => {
            let message = (error && typeof error.message === 'string' && error.message.trim())
                ? error.message
                : 'The return request could not be completed. Please try again.';

            if (looksLikePhpNotice(message)) {
                message = 'The return could not be completed. Please try again, or ask staff for assistance.';
            }

            showToast('error', 'Return Failed', message);

            confirmReturnButton.disabled = false;
            confirmReturnButton.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirm Return';
            resumeAutoReload();
        });
    });

    /* ── init ────────────────────────────────────────── */
    updateSelection();
    setBookSelectionEnabled(false);
    rfidInput.focus();
    showStep('identity');
    hideFingerprintMessages();

    startAutoReload();

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