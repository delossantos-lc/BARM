@php
    $systemSettings = $systemSettings ?? App\Models\SystemSetting::current();

    $libraryPolicy = App\Models\LibraryPolicy::current();
    $fingerprintRequired = (bool) (
        $libraryPolicy?->require_fingerprint_for_borrowing ?? false
    );

    $reserveStoreUrl = \Illuminate\Support\Facades\Route::has('reserve.store')
        ? route('reserve.store')
        : url('/reserve');
    $reserveStudentRfidUrl = \Illuminate\Support\Facades\Route::has('reserve.student.rfid')
        ? route('reserve.student.rfid', ['rfid' => '__RFID__'])
        : url('/reserve/student/__RFID__');

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
    <title data-system-title-prefix="Reserve a Book">Reserve a Book | {{ $systemSettings->system_short_name ?? 'BARM' }}</title>
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
            cursor: text;
        }

        .scan-area.scanning { animation: pulse 1s infinite; }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(215, 96, 145, .3); }
            50% { box-shadow: 0 0 0 13px rgba(215, 96, 145, 0); }
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

        .search-row {
            display: flex; align-items: center; gap: 8px;
            flex: 1; max-width: 520px; min-width: 260px;
        }

        .search-row .search-field {
            flex: 1; min-width: 0;
            display: flex; align-items: center;
            gap: 8px;
        }

        .search-row .input-group-text {
            background: var(--pink-light); border: 1px solid var(--border);
            color: var(--pink-dark); font-weight: 700;
            font-size: clamp(0.65rem, 0.85vw, 0.8rem);
            white-space: nowrap;
            display: inline-flex; align-items: center;
            padding: 0 12px;
            min-height: clamp(36px, 4.5vh, 48px);
            border-radius: 10px;
        }

        .search-row .form-control {
            min-height: clamp(36px, 4.5vh, 48px);
            font-size: clamp(0.72rem, 0.95vw, 0.9rem);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .search-row .clear-btn {
            min-height: clamp(36px, 4.5vh, 48px);
            border: none; border-radius: 10px;
            background: var(--gray); color: #fff;
            font-size: clamp(0.72rem, 0.95vw, 0.9rem);
            font-weight: 700; padding: 0 clamp(10px, 1.4vw, 18px);
            display: inline-flex; align-items: center; justify-content: center;
            gap: 5px; white-space: nowrap; cursor: pointer;
            transition: background 0.15s;
        }
        .search-row .clear-btn:hover:not(:disabled) { background: var(--gray-hover); color: #fff; }
        .search-row .clear-btn:disabled { background: #b8bdc2; cursor: not-allowed; }

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

        /* ── STEP 3: PICKUP SCHEDULE ─────────────────── */
        #scheduleStep {
            flex: 1; min-height: 0;
            display: flex; flex-direction: column;
            padding: clamp(12px, 1.8vw, 24px) clamp(14px, 2vw, 30px);
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .schedule-step-header {
            flex-shrink: 0; margin-bottom: 14px;
        }

        .schedule-step-title {
            display: flex; align-items: center; gap: 10px;
            font-size: clamp(1rem, 1.6vw, 1.3rem);
            font-weight: 800; color: #202020;
        }

        .schedule-step-title i {
            color: var(--pink-dark);
        }

        .schedule-step-desc {
            font-size: clamp(0.72rem, 0.95vw, 0.9rem);
            color: var(--muted); margin-top: 4px;
        }

        .schedule-body {
            flex: 1; min-height: 0;
            overflow-y: auto; padding: 4px 4px 8px;
            display: flex; flex-direction: column;
            justify-content: center;
        }
        .schedule-body::-webkit-scrollbar { width: 6px; }
        .schedule-body::-webkit-scrollbar-thumb { background: #edbdcf; border-radius: 3px; }

        .schedule-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            align-items: start;
        }
        @media (max-width: 767px) { .schedule-grid { grid-template-columns: 1fr; gap: 16px; } }

        /* ── Schedule cards ── */
        .schedule-card {
            background: linear-gradient(135deg, #ffffff, #fef9fb);
            border: 2px solid #f0d9e2;
            border-radius: 18px;
            padding: 22px 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .schedule-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f8dadd, var(--pink-dark), #f8dadd);
            opacity: 0.45;
            transition: opacity 0.2s ease;
        }
        .schedule-card:focus-within {
            border-color: var(--pink-dark);
            box-shadow: 0 12px 32px rgba(215, 96, 145, 0.18);
            transform: translateY(-2px);
        }
        .schedule-card:focus-within::before { opacity: 1; }
        .schedule-card.has-value {
            border-color: var(--pink-dark);
            background: linear-gradient(135deg, #fff5f8, #fde5ed);
        }
        .schedule-card.has-value::before { opacity: 1; }

        .schedule-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .schedule-card-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--pink-dark), #b34d7a);
            color: #fff;
            font-size: 1.35rem;
            box-shadow: 0 6px 16px rgba(215, 96, 145, 0.35);
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .schedule-card:focus-within .schedule-card-icon {
            transform: scale(1.06);
        }
        .schedule-card-title {
            font-size: clamp(0.95rem, 1.15vw, 1.1rem);
            font-weight: 800;
            color: #202124;
            line-height: 1.15;
            letter-spacing: 0.2px;
        }
        .schedule-card-subtitle {
            font-size: clamp(0.68rem, 0.82vw, 0.8rem);
            color: var(--muted);
            font-weight: 600;
            margin-top: 3px;
        }

        .schedule-input-wrapper {
            position: relative;
            width: 100%;
        }

        .schedule-input {
            width: 100%;
            min-height: 58px;
            padding: 14px 18px 14px 54px;
            border: 2px solid #edd4de;
            border-radius: 14px;
            background: #fff;
            font-size: clamp(0.95rem, 1.2vw, 1.1rem);
            font-weight: 700;
            color: #202124;
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
            letter-spacing: 0.3px;
        }
        .schedule-input::placeholder {
            color: #b0a8ad;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .schedule-input:hover {
            border-color: #e0b8c8;
            background: #fffafc;
        }
        .schedule-input:focus {
            border-color: var(--pink-dark);
            box-shadow: 0 0 0 5px rgba(215, 96, 145, 0.12);
            outline: none;
            background: #fff;
        }
        .schedule-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.75;
            padding: 6px;
            border-radius: 6px;
            transition: opacity 0.15s, background 0.15s;
        }
        .schedule-input::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
            background: rgba(215, 96, 145, 0.12);
        }

        .schedule-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pink-dark);
            font-size: 1.2rem;
            pointer-events: none;
            transition: color 0.2s, transform 0.2s;
        }
        .schedule-input-wrapper:focus-within .schedule-input-icon {
            color: var(--pink-hover);
            transform: translateY(-50%) scale(1.1);
        }

        .schedule-input-hint {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: clamp(0.65rem, 0.8vw, 0.78rem);
            color: var(--muted);
            font-weight: 600;
            padding: 10px 14px;
            background: #fff8fa;
            border-radius: 10px;
            border: 1px dashed #f0d9e2;
        }
        .schedule-input-hint i {
            color: var(--pink-dark);
            font-size: 0.9em;
        }
        .schedule-input-hint strong {
            color: #202124;
        }

        /* ── Selected-value summary card ── */
        .schedule-selected {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 14px;
            background: #fff;
            border: 2px solid var(--pink-dark);
            box-shadow: 0 6px 18px rgba(215, 96, 145, 0.18);
            min-height: 58px;
        }
        .schedule-selected.show {
            display: flex;
            animation: fadeSlideIn 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(-6px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .schedule-selected-info {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .schedule-selected-info > i {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff2f6, #fde5ed);
            color: var(--pink-dark);
            border-radius: 12px;
            font-size: 1.15rem;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(215, 96, 145, 0.15);
        }
        .schedule-selected-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .schedule-selected-label {
            font-size: clamp(0.6rem, 0.75vw, 0.72rem);
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .schedule-selected-value {
            font-size: clamp(0.95rem, 1.15vw, 1.1rem);
            font-weight: 900;
            color: #202124;
            line-height: 1.2;
            word-break: break-word;
            margin-top: 2px;
        }
        .schedule-change-btn {
            background: transparent;
            border: 2px solid var(--pink-dark);
            color: var(--pink-dark);
            border-radius: 10px;
            padding: 9px 18px;
            font-size: clamp(0.7rem, 0.85vw, 0.85rem);
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Segoe UI', Arial, sans-serif;
            letter-spacing: 0.3px;
        }
        .schedule-change-btn:hover {
            background: var(--pink-dark);
            color: #fff;
            box-shadow: 0 4px 12px rgba(215, 96, 145, 0.3);
            transform: translateY(-1px);
        }
        .schedule-change-btn:active {
            transform: translateY(0);
        }

        /* ── STEP 4: Review / Confirmation card ── */
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
            padding: 12px 16px;
            border-radius: 10px;
            background: var(--pink-light);
            border: 1px solid #f4d3e0;
            flex-shrink: 0;
        }

        .confirm-book-title {
            font-size: clamp(0.85rem, 1vw, 0.98rem);
            font-weight: 800;
            color: #202124;
            flex: 1;
            min-width: 0;
            word-break: break-word;
        }

        .confirm-book-meta {
            font-size: clamp(0.65rem, 0.78vw, 0.75rem);
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

        .confirm-card-actions .btn-back,
        .confirm-card-actions .btn-cancel {
            background: #f4f4f7;
            color: #5a5f66;
            border: 2px solid #e6e6ee;
            font-weight: 700;
        }
        .confirm-card-actions .btn-back:hover,
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

        /* ── Modals ── */
        .confirm-modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(32, 20, 28, 0.55);
            backdrop-filter: blur(2px);
            display: none; align-items: center; justify-content: center;
            z-index: 2000; padding: 20px;
        }
        .confirm-modal-backdrop.show { display: flex; }
        .confirm-modal {
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
        .confirm-modal .confirm-icon {
            width: 60px; height: 60px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; border-radius: 50%;
            background: var(--pink-dark); color: #fff;
            font-size: 1.6rem;
            box-shadow: 0 6px 16px rgba(215, 96, 145, 0.35);
        }
        .confirm-modal .confirm-title {
            font-size: clamp(1.05rem, 1.4vw, 1.25rem);
            font-weight: 900; color: #202124; margin-bottom: 6px;
        }
        .confirm-modal .confirm-message {
            font-size: clamp(0.8rem, 1vw, 0.92rem);
            color: #5a5f66; margin-bottom: 18px; line-height: 1.5;
        }
        .confirm-modal .confirm-actions { display: flex; gap: 10px; justify-content: center; }
        .confirm-modal .confirm-actions .btn-confirm,
        .confirm-modal .confirm-actions .btn-back {
            min-height: 44px; flex: 1;
            font-size: clamp(0.78rem, 1vw, 0.92rem);
        }

        /* ── Transaction complete modal ── */
        .complete-modal .confirm-icon {
            background: linear-gradient(135deg, #27ae60, #1e9e5a);
            box-shadow: 0 8px 22px rgba(39, 174, 96, 0.35);
            animation: completeIconPop 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        @keyframes completeIconPop {
            0% { transform: scale(0.6); opacity: 0; }
            60% { transform: scale(1.08); }
            100% { transform: scale(1); opacity: 1; }
        }
        .complete-modal .confirm-title { color: #1e9e5a; }
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
        .complete-modal .complete-book-title {
            font-weight: 900; color: #202124;
            font-size: clamp(0.85rem, 1vw, 0.95rem);
        }
        .complete-modal .complete-status-badge {
            display: inline-block; padding: 3px 12px;
            border-radius: 999px; background: #fff3cd; color: #856404;
            font-weight: 800; font-size: clamp(0.6rem, 0.75vw, 0.7rem);
            text-transform: uppercase; letter-spacing: 0.5px;
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
            .search-row { max-width: 100%; }
            .search-row { flex-direction: column; align-items: stretch; }

            .confirm-card { padding: 14px 14px 16px; border-radius: 16px; }
            .confirm-card-actions { flex-direction: column; gap: 8px; }
            .confirm-card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .confirm-row { flex-direction: column; gap: 3px; padding: 8px 0; }
            .confirm-row-value { text-align: left; }

            .schedule-card { padding: 16px 16px; }
            .schedule-card-icon { width: 44px; height: 44px; font-size: 1.15rem; }
            .confirm-modal .confirm-actions { flex-direction: column; }
        }

        @media (max-width: 575px) {
            .top-left-title { letter-spacing: 0.5px; }
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
                <h1 class="top-left-title">Reserve a Book</h1>
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
            <form action="{{ $reserveStoreUrl }}" method="POST" id="reservationForm" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                @csrf

                {{-- STEP 1: IDENTITY --}}
                <section id="identityStep" data-flow-step="identity">
                    <div class="identity-two-columns">
                        <div class="identity-left-col">
                            <h2 class="section-title">Scan Borrower RFID</h2>
                            <p class="section-description">Place the RFID card on the reader to retrieve the borrower.</p>

                            <div class="scan-area" id="scanArea">
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
                                    <input type="text" id="rfidInput" class="form-control large-input text-center"
                                           placeholder="Scan RFID card" autocomplete="off" autofocus>
                                    <input type="hidden" name="rfid_tag_uid" id="rfid_tag_uid_hidden" value="">
                                    <div id="rfidStatus" class="rfid-status text-muted">
                                        <i class="fa-solid fa-id-card me-1"></i>Waiting for RFID scan...
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="student_id" id="studentId" value="{{ old('student_id') }}">
                            <input type="hidden" name="student_name" id="studentNameInput" value="{{ old('student_name') }}">
                            <input type="hidden" name="borrower_type" id="borrowerTypeInput" value="{{ old('borrower_type') }}">

                            @if($fingerprintRequired)
                                <input type="hidden" name="fingerprint_id" id="fingerprintIdInput" value="">

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
                                            rfid-input-id="rfidInput"
                                            borrower-number-input-id="studentId"
                                            borrower-type-input-id="borrowerTypeInput"
                                            fingerprint-id-input-id="fingerprintIdInput"
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
                                    <span class="info-label">Active Reservations</span>
                                    <span class="info-value placeholder" id="displayActiveReservations">—</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Remaining Slots</span>
                                    <span class="info-value placeholder" id="displayRemainingSlots">—</span>
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
                            <h2 class="section-title">Find a Book</h2>
                            <p id="bookSelectionDescription" class="section-description" style="margin-bottom:0;">
                                @if($fingerprintRequired)
                                    Verify the borrower fingerprint before selecting a book.
                                @else
                                    Search and pick one book to reserve.
                                @endif
                            </p>
                        </div>

                        <div class="search-row">
                            <div class="search-field">
                                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</span>
                                <input type="search" id="bookSearch" class="form-control"
                                       placeholder="Title, author, ISBN, call number" autocomplete="off" disabled>
                            </div>
                            <div class="clear-field">
                                <button type="button" id="clearSearch" class="clear-btn" disabled>
                                    <i class="fa-solid fa-rotate-left"></i>
                                    <span class="d-none d-sm-inline">Clear</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="bookRfidMessage" class="book-rfid-message" aria-live="polite">
                        @if($fingerprintRequired)
                            Verify your ID and fingerprint first.
                        @else
                            Verify your ID first.
                        @endif
                    </div>

                    <div class="book-selection-body">
                        <div class="book-list-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Available Books</strong>
                                @php
                                    $reservableBooksCount = $books->filter(function ($book) {
                                        return $book->copies->where('status', 'available')->count() > 0;
                                    })->count();
                                @endphp
                                <span id="availableBooksCount" class="count-badge">{{ $reservableBooksCount }} available</span>
                            </div>
                            <div id="booksContainer" class="book-list">
                                @forelse($books as $book)
                                    @php
                                        $availableCopies = $book->copies->where('status', 'available')->count();
                                        $totalCopies     = $book->copies->count();
                                    @endphp

                                    @if($availableCopies > 0)
                                        <div class="book-card" id="bookCard{{ $book->id }}"
                                             data-book-id="{{ $book->id }}"
                                             data-title="{{ strtolower($book->title ?? '') }}"
                                             data-author="{{ strtolower($book->author ?? '') }}"
                                             data-isbn="{{ strtolower($book->isbn ?? '') }}"
                                             data-call-number="{{ strtolower($book->call_number ?? '') }}"
                                             data-available-copies="{{ $availableCopies }}"
                                             data-total-copies="{{ $totalCopies }}"
                                             tabindex="0">
                                            <span class="selection-icon"><i class="fa-regular fa-square fa-lg"></i></span>
                                            <div class="book-title">{{ $book->title ?? 'Untitled Book' }}</div>
                                            <p class="book-details"><i class="fa-solid fa-user-pen"></i><span>Author: <strong>{{ $book->author ?? 'Unknown' }}</strong></span></p>
                                            <p class="book-details"><i class="fa-solid fa-bookmark"></i><span>Call No: <strong>{{ $book->call_number ?: '-' }}</strong></span></p>
                                            <p class="book-details"><i class="fa-solid fa-barcode"></i><span>ISBN: <strong>{{ $book->isbn ?: '-' }}</strong></span></p>
                                            <p class="book-details"><i class="fa-solid fa-layer-group"></i><span>Available: <strong>{{ $availableCopies }} of {{ $totalCopies }} copy(ies)</strong></span></p>
                                        </div>
                                    @endif
                                @empty
                                    <div class="empty-selection">
                                        <i class="fa-solid fa-book"></i>
                                        <strong>No available books</strong>
                                        <p class="mb-0 mt-1">All books are unavailable.</p>
                                    </div>
                                @endforelse

                                <div id="noAvailableBooks" class="empty-selection d-none">
                                    <i class="fa-solid fa-book"></i>
                                    <strong>No available books</strong>
                                    <p class="mb-0 mt-1">All books are currently borrowed or reserved.</p>
                                </div>

                                <div id="noResults" class="empty-selection d-none">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <strong>No matching books found</strong>
                                    <p class="mb-0 mt-1">Try a different search term.</p>
                                </div>
                            </div>
                        </div>

                        <div class="selected-books-col">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small" style="font-size: clamp(0.65rem, 0.85vw, 0.8rem);">Book to Reserve</strong>
                                <span id="selectedCount" class="count-badge">0 selected</span>
                            </div>
                            <div class="selected-books-box">
                                <div id="emptySelection" class="empty-selection">
                                    <i class="fa-solid fa-book"></i>
                                    <strong>No book selected</strong>
                                    <p class="mb-0 mt-1">Pick one book from the list.</p>
                                </div>
                                <div id="selectedBooksList"></div>
                            </div>
                            <input type="hidden" name="book_id" id="selectedBookId" value="{{ old('book_id') }}" required>
                        </div>
                    </div>

                    <div class="review-actions">
                        <button type="button" id="reserveBackIdentity" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Change ID</button>
                        <button type="button" id="reserveReviewNext" class="btn-confirm" disabled>Select pickup time <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </section>

                {{-- STEP 3: PICKUP SCHEDULE --}}
                <section id="scheduleStep" data-flow-step="schedule" hidden>
                    <div class="schedule-step-header">
                        <div class="schedule-step-title">
                            <i class="fa-solid fa-calendar-check"></i>
                            Select Pickup Schedule
                        </div>
                        <div class="schedule-step-desc">
                            Choose when you'd like to pick up your reserved book.
                        </div>
                    </div>

                    <div class="schedule-body">
                        <div class="schedule-grid">
                            {{-- DATE CARD --}}
                            <div class="schedule-card" id="dateCard">
                                <div class="schedule-card-header">
                                    <div class="schedule-card-icon">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <div>
                                        <div class="schedule-card-title">Borrow Date</div>
                                        <div class="schedule-card-subtitle">Pick your preferred date</div>
                                    </div>
                                </div>

                                <div class="schedule-input-wrapper" id="dateInputWrapper">
                                    <i class="fa-regular fa-calendar schedule-input-icon"></i>
                                    <input type="date" name="borrow_date" id="borrowDate"
                                           class="schedule-input"
                                           min="{{ now()->toDateString() }}" required>
                                </div>

                                <div class="schedule-selected" id="dateSelectedSummary">
                                    <div class="schedule-selected-info">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <div class="schedule-selected-text">
                                            <span class="schedule-selected-label">Selected Date</span>
                                            <span class="schedule-selected-value" id="dateSelectedValue">—</span>
                                        </div>
                                    </div>
                                    <button type="button" class="schedule-change-btn" id="dateChangeBtn">
                                        <i class="fa-solid fa-pen"></i> Change
                                    </button>
                                </div>

                                <div class="schedule-input-hint">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>Earliest available: <strong>{{ now()->format('M d, Y') }}</strong></span>
                                </div>
                            </div>

                            {{-- TIME CARD --}}
                            <div class="schedule-card" id="timeCard">
                                <div class="schedule-card-header">
                                    <div class="schedule-card-icon">
                                        <i class="fa-solid fa-clock"></i>
                                    </div>
                                    <div>
                                        <div class="schedule-card-title">Pickup Time</div>
                                        <div class="schedule-card-subtitle">Select a time slot</div>
                                    </div>
                                </div>

                                <div class="schedule-input-wrapper" id="timeInputWrapper">
                                    <i class="fa-regular fa-clock schedule-input-icon"></i>
                                    <input type="time" name="pickup_time" id="pickupTime"
                                           class="schedule-input"
                                           min="08:00" max="18:00" step="900" required>
                                </div>

                                <div class="schedule-selected" id="timeSelectedSummary">
                                    <div class="schedule-selected-info">
                                        <i class="fa-solid fa-clock"></i>
                                        <div class="schedule-selected-text">
                                            <span class="schedule-selected-label">Selected Time</span>
                                            <span class="schedule-selected-value" id="timeSelectedValue">—</span>
                                        </div>
                                    </div>
                                    <button type="button" class="schedule-change-btn" id="timeChangeBtn">
                                        <i class="fa-solid fa-pen"></i> Change
                                    </button>
                                </div>

                                <div class="schedule-input-hint">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>Available: <strong>8:00 AM – 6:00 PM</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="review-actions">
                        <button type="button" id="scheduleBackBooks" class="btn-back">
                            <i class="fa-solid fa-arrow-left"></i> Back to books
                        </button>
                        <button type="button" id="scheduleReviewNext" class="btn-confirm" disabled>
                            Review reservation <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </section>

                {{-- STEP 4: CONFIRM RESERVATION --}}
                <section class="review-panel" data-flow-step="review" hidden>
                    <div class="review-body">
                        <div class="confirm-card">

                            <div class="confirm-card-header">
                                <div class="confirm-card-title">
                                    <i class="fa-solid fa-bookmark"></i>
                                    Reservation Confirmation
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
                                    <span class="confirm-row-value" id="reviewBorrowerName">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Student / Employee No.</span>
                                    <span class="confirm-row-value" id="reviewBorrowerNumber">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Pickup Date</span>
                                    <span class="confirm-row-value" id="reviewPickupDate">—</span>
                                </div>
                                <div class="confirm-row">
                                    <span class="confirm-row-label">Pickup Time</span>
                                    <span class="confirm-row-value pink" id="reviewPickupTime">—</span>
                                </div>
                            </div>

                            <div class="confirm-books-block">
                                <div class="confirm-books-label">Book to Reserve</div>
                                <div class="confirm-books-list" id="reviewBooksList"></div>
                            </div>

                            <div class="confirm-card-actions">
                                <button type="button" id="reserveBackSchedule" class="btn-back">
                                    <i class="fa-solid fa-arrow-left"></i> Go back
                                </button>
                                <button type="button" id="confirmReservation" class="btn-confirm" disabled>
                                    <i class="fa-solid fa-calendar-check"></i> Confirm Reservation
                                </button>
                            </div>

                            <div class="confirm-cancel-link">
                                <a href="{{ url()->previous() }}" data-cancel-flow>Cancel and return to kiosk</a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>

        <div class="back-row">
            <a href="{{ url()->previous() }}" class="btn">
                <i class="fa-solid fa-arrow-left me-1"></i>Back to kiosk choices
            </a>
        </div>

    </div>
</div>

{{-- CANCEL confirmation modal --}}
<div class="confirm-modal-backdrop" id="cancelConfirmBackdrop">
    <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="cancelConfirmTitle">
        <div class="confirm-icon">
            <i class="fa-solid fa-circle-question"></i>
        </div>
        <div class="confirm-title" id="cancelConfirmTitle">Cancel this reservation?</div>
        <div class="confirm-message">
            Your ID and everything you've selected will be cleared.
        </div>
        <div class="confirm-actions">
            <button type="button" class="btn-back" id="cancelModalKeep">
                <i class="fa-solid fa-arrow-left me-1"></i> Keep reservation
            </button>
            <button type="button" class="btn-confirm" id="cancelModalConfirm">
                <i class="fa-solid fa-xmark me-1"></i> Yes, cancel
            </button>
        </div>
    </div>
</div>

{{-- TRANSACTION COMPLETE modal (AJAX success notification) --}}
<div class="confirm-modal-backdrop" id="transactionCompleteBackdrop">
    <div class="confirm-modal complete-modal" role="dialog" aria-modal="true" aria-labelledby="transactionCompleteTitle">
        <div class="confirm-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <div class="confirm-title" id="transactionCompleteTitle">Transaction Complete</div>
        <div class="confirm-message" id="transactionCompleteMessage">
            Your reservation has been submitted successfully.
        </div>

        <div class="complete-summary">
            <div class="summary-row">
                <span class="summary-label">Borrower</span>
                <span class="summary-value" id="tcBorrowerName">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Book</span>
                <span class="summary-value" id="tcBookTitle">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Pickup Date</span>
                <span class="summary-value pink" id="tcPickupDate">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Pickup Time</span>
                <span class="summary-value pink" id="tcPickupTime">—</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Status</span>
                <span class="summary-value">
                    <span class="complete-status-badge" id="tcStatus">Pending</span>
                </span>
            </div>
        </div>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm" id="transactionCompleteDone">
                <i class="fa-solid fa-check me-1"></i> Done
            </button>
        </div>
    </div>
</div>

@if(session('reservation_popup'))
    <div class="modal fade" id="reservationSuccessModal" tabindex="-1"
         aria-labelledby="reservationSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-body p-4 p-md-5 text-center">
                    <div class="d-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle text-white"
                         style="width: 72px; height: 72px; background: var(--pink-dark); font-size: 2rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="fw-bold mb-2" id="reservationSuccessModalLabel">
                        Reservation Submitted
                    </h3>
                    <p class="text-muted mb-3">
                        You successfully reserved:
                    </p>
                    <div class="p-3 mb-3 rounded-3" style="background: var(--pink-light);">
                        <div class="fw-bold fs-5">
                            {{ session('reservation_popup.book_title') }}
                        </div>
                    </div>
                    <p class="mb-4">
                        Status:
                        <span class="badge rounded-pill text-bg-warning px-3 py-2">
                            {{ session('reservation_popup.status', 'Pending') }}
                        </span>
                        <br>
                        <small class="text-muted">Please wait for the admin to review your reservation.</small>
                    </p>
                    <button type="button" class="btn px-4 py-2 fw-bold text-white"
                            style="background: var(--pink-dark);" data-bs-dismiss="modal">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="reservationLimitModal" tabindex="-1"
     aria-labelledby="reservationLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4 p-md-5 text-center">
                <div class="d-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle text-white"
                     style="width: 72px; height: 72px; background: #dc3545; font-size: 2rem;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 class="fw-bold mb-2" id="reservationLimitModalLabel">
                    Reservation Limit Reached
                </h3>
                <p class="text-muted mb-4" id="reservationLimitMessage">
                    You have reached the maximum number of active reservations.
                </p>
                <button type="button" class="btn btn-danger px-4 py-2 fw-bold"
                        data-bs-dismiss="modal">
                    Okay
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const FINGERPRINT_REQUIRED = @json($fingerprintRequired);
    const AUTO_RELOAD_SECONDS = {{ $autoReloadSeconds }};

    const reservationSuccessModalEl = document.getElementById('reservationSuccessModal');
    if (reservationSuccessModalEl) {
        bootstrap.Modal.getOrCreateInstance(reservationSuccessModalEl).show();
    }

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
            if (stepBadgeText) stepBadgeText.textContent = 'Select Book';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-book';
        } else if (step === 'schedule') {
            if (stepBadgeText) stepBadgeText.textContent = 'Pickup Schedule';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-calendar-days';
        } else if (step === 'review') {
            if (stepBadgeText) stepBadgeText.textContent = 'Review & Confirm';
            if (stepBadgeIcon) stepBadgeIcon.className = 'fa-solid fa-check-circle';
        }
    }

    const reservationForm = document.getElementById('reservationForm');
    const rfidInput = document.getElementById('rfidInput');
    const rfidStatus = document.getElementById('rfidStatus');
    const scanArea = document.getElementById('scanArea');
    const studentId = document.getElementById('studentId');
    const studentNameInput = document.getElementById('studentNameInput');
    const borrowerTypeInput = document.getElementById('borrowerTypeInput');
    const bookSelectionSection = document.getElementById('bookSelectionSection');
    const bookSelectionDescription = document.getElementById('bookSelectionDescription');
    const bookSearch = document.getElementById('bookSearch');
    const clearSearchButton = document.getElementById('clearSearch');
    const bookRfidMessage = document.getElementById('bookRfidMessage');
    const booksContainer = document.getElementById('booksContainer');
    const selectedBooksList = document.getElementById('selectedBooksList');
    const selectedBookId = document.getElementById('selectedBookId');
    const emptySelection = document.getElementById('emptySelection');
    const selectedCount = document.getElementById('selectedCount');
    const confirmReservation = document.getElementById('confirmReservation');
    const reviewNext = document.getElementById('reserveReviewNext');
    const scheduleReviewNext = document.getElementById('scheduleReviewNext');
    const borrowDate = document.getElementById('borrowDate');
    const pickupTime = document.getElementById('pickupTime');

    const dateCard = document.getElementById('dateCard');
    const dateInputWrapper = document.getElementById('dateInputWrapper');
    const dateSelectedSummary = document.getElementById('dateSelectedSummary');
    const dateSelectedValue = document.getElementById('dateSelectedValue');
    const dateChangeBtn = document.getElementById('dateChangeBtn');

    const timeCard = document.getElementById('timeCard');
    const timeInputWrapper = document.getElementById('timeInputWrapper');
    const timeSelectedSummary = document.getElementById('timeSelectedSummary');
    const timeSelectedValue = document.getElementById('timeSelectedValue');
    const timeChangeBtn = document.getElementById('timeChangeBtn');

    const reviewBorrowerName = document.getElementById('reviewBorrowerName');
    const reviewBorrowerNumber = document.getElementById('reviewBorrowerNumber');
    const reviewPickupDate = document.getElementById('reviewPickupDate');
    const reviewPickupTime = document.getElementById('reviewPickupTime');
    const reviewBooksList = document.getElementById('reviewBooksList');

    const fingerprintMessage = document.getElementById('fingerprintMessage');
    const fpTitle = document.getElementById('fpTitle');
    const fpSub = document.getElementById('fpSub');
    const fpActions = document.getElementById('fpActions');
    const fingerprintRetry = document.getElementById('fingerprintRetry');
    const fingerprintDirect = document.getElementById('fingerprintDirect');
    const fingerprintIdInput = document.getElementById('fingerprintIdInput');

    const displayBorrowerName = document.getElementById('displayBorrowerName');
    const displayBorrowerNumber = document.getElementById('displayBorrowerNumber');
    const displayBorrowerTypeRow = document.getElementById('displayBorrowerTypeRow');
    const displayActiveReservations = document.getElementById('displayActiveReservations');
    const displayRemainingSlots = document.getElementById('displayRemainingSlots');
    const displayBorrowerStatus = document.getElementById('displayBorrowerStatus');
    const displayBorrowerType = document.getElementById('displayBorrowerType');

    const cancelBackdrop = document.getElementById('cancelConfirmBackdrop');
    const cancelModalKeep = document.getElementById('cancelModalKeep');
    const cancelModalConfirm = document.getElementById('cancelModalConfirm');

    const reservationLimitModal = document.getElementById('reservationLimitModal');
    const reservationLimitMessage = document.getElementById('reservationLimitMessage');

    /* ── Transaction Complete modal elements ── */
    const transactionCompleteBackdrop = document.getElementById('transactionCompleteBackdrop');
    const transactionCompleteMessage = document.getElementById('transactionCompleteMessage');
    const transactionCompleteDone = document.getElementById('transactionCompleteDone');
    const tcBorrowerName = document.getElementById('tcBorrowerName');
    const tcBookTitle = document.getElementById('tcBookTitle');
    const tcPickupDate = document.getElementById('tcPickupDate');
    const tcPickupTime = document.getElementById('tcPickupTime');
    const tcStatus = document.getElementById('tcStatus');

    let scanTimer = null;
    let processing = false;

    let fingerprintVerified = !FINGERPRINT_REQUIRED;

    let refreshingBooks = false;
    let scanGeneration = 0;

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
            if (processing || (transactionCompleteBackdrop && transactionCompleteBackdrop.classList.contains('show'))) {
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

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function formatDate(isoDate) {
        if (!isoDate) return '—';
        const d = new Date(isoDate + 'T00:00:00');
        if (isNaN(d.getTime())) return isoDate;
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function formatTime(hhmm) {
        if (!hhmm) return '—';
        const [h, m] = hhmm.split(':').map(Number);
        if (isNaN(h) || isNaN(m)) return hhmm;
        const period = h >= 12 ? 'PM' : 'AM';
        const hour12 = ((h + 11) % 12) + 1;
        return hour12 + ':' + String(m).padStart(2, '0') + ' ' + period;
    }

    function pruneUnavailableBooks() {
        let visibleCount = 0;

        booksContainer.querySelectorAll('.book-card').forEach(function (card) {
            const available = parseInt(card.dataset.availableCopies || '0', 10);
            if (available > 0) {
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.setAttribute('data-unavailable', 'true');
            }
        });

        const noAvailable = document.getElementById('noAvailableBooks');
        if (noAvailable) {
            const hasSearch = bookSearch.value.trim() !== '';
            noAvailable.classList.toggle('d-none', visibleCount > 0 || hasSearch);
        }
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
        bookSearch.disabled = !enabled;
        clearSearchButton.disabled = !enabled;
    }

    function updateUserInfoDisplay(data) {
        if (!data) {
            displayBorrowerName.textContent = '—';
            displayBorrowerNumber.textContent = '—';
            displayBorrowerTypeRow.textContent = '—';
            displayActiveReservations.textContent = '—';
            displayRemainingSlots.textContent = '—';
            displayBorrowerStatus.textContent = '—';
            displayBorrowerType.textContent = 'No RFID scanned';
            [displayBorrowerNumber, displayBorrowerTypeRow, displayActiveReservations, displayRemainingSlots, displayBorrowerStatus]
                .forEach(el => el.classList.add('placeholder'));
            return;
        }
        displayBorrowerName.textContent = data.borrower_name || data.student_name || '—';
        displayBorrowerNumber.textContent = data.borrower_number || data.student_number || '—';
        displayBorrowerTypeRow.textContent = data.borrower_type === 'personnel' ? 'Personnel' : 'Student';
        displayActiveReservations.textContent = data.active_reservations ?? '—';
        displayRemainingSlots.textContent = data.remaining_reservations ?? '—';
        displayBorrowerStatus.textContent = 'Eligible to reserve';
        displayBorrowerType.textContent = data.borrower_type === 'personnel' ? 'Personnel' : 'Student';
        [displayBorrowerNumber, displayBorrowerTypeRow, displayActiveReservations, displayRemainingSlots, displayBorrowerStatus]
            .forEach(el => el.classList.remove('placeholder'));
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

    /* ── Transaction Complete modal helpers ── */
    function showTransactionComplete(payload) {
        if (!transactionCompleteBackdrop) return;

        pauseAutoReload();

        if (tcBorrowerName) {
            tcBorrowerName.textContent = displayBorrowerName.textContent || '—';
        }
        if (tcBookTitle) {
            const card = booksContainer.querySelector('.book-card[data-book-id="' + selectedBookId.value + '"]');
            tcBookTitle.textContent = card
                ? (card.querySelector('.book-title')?.textContent.trim() || '—')
                : (payload?.book_title || '—');
        }
        if (tcPickupDate) tcPickupDate.textContent = formatDate(borrowDate.value);
        if (tcPickupTime) tcPickupTime.textContent = formatTime(pickupTime.value);
        if (tcStatus) tcStatus.textContent = payload?.status || 'Pending';
        if (transactionCompleteMessage) {
            transactionCompleteMessage.textContent =
                payload?.message || 'Your reservation has been submitted successfully.';
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
            closeTransactionComplete(window.__reservationRedirectUrl || @json($reserveStoreUrl));
        });
    }

    if (transactionCompleteBackdrop) {
        transactionCompleteBackdrop.addEventListener('click', function (event) {
            if (event.target === transactionCompleteBackdrop) {
                closeTransactionComplete(window.__reservationRedirectUrl || @json($reserveStoreUrl));
            }
        });
    }

    /* ── Delay to let the native picker popup close before swapping UI ── */
    const PICKER_CLOSE_DELAY_MS = 220;

    let dateSwapTimer = null;
    let timeSwapTimer = null;

    function commitDateDisplay() {
        if (dateSwapTimer) clearTimeout(dateSwapTimer);
        dateSwapTimer = setTimeout(() => {
            const hasValue = !!borrowDate.value;
            if (hasValue) {
                dateSelectedValue.textContent = formatDate(borrowDate.value);
                dateInputWrapper.style.display = 'none';
                dateSelectedSummary.classList.add('show');
                dateCard.classList.add('has-value');
            } else {
                dateInputWrapper.style.display = '';
                dateSelectedSummary.classList.remove('show');
                dateCard.classList.remove('has-value');
            }
            updateScheduleNextButton();
        }, PICKER_CLOSE_DELAY_MS);
    }

    function commitTimeDisplay() {
        if (timeSwapTimer) clearTimeout(timeSwapTimer);
        timeSwapTimer = setTimeout(() => {
            const hasValue = !!pickupTime.value;
            if (hasValue) {
                timeSelectedValue.textContent = formatTime(pickupTime.value);
                timeInputWrapper.style.display = 'none';
                timeSelectedSummary.classList.add('show');
                timeCard.classList.add('has-value');
            } else {
                timeInputWrapper.style.display = '';
                timeSelectedSummary.classList.remove('show');
                timeCard.classList.remove('has-value');
            }
            updateScheduleNextButton();
        }, PICKER_CLOSE_DELAY_MS);
    }

    function updateDateDisplay() {
        const hasValue = !!borrowDate.value;
        if (hasValue) {
            dateSelectedValue.textContent = formatDate(borrowDate.value);
            dateInputWrapper.style.display = 'none';
            dateSelectedSummary.classList.add('show');
            dateCard.classList.add('has-value');
        } else {
            dateInputWrapper.style.display = '';
            dateSelectedSummary.classList.remove('show');
            dateCard.classList.remove('has-value');
        }
        updateScheduleNextButton();
    }

    function updateTimeDisplay() {
        const hasValue = !!pickupTime.value;
        if (hasValue) {
            timeSelectedValue.textContent = formatTime(pickupTime.value);
            timeInputWrapper.style.display = 'none';
            timeSelectedSummary.classList.add('show');
            timeCard.classList.add('has-value');
        } else {
            timeInputWrapper.style.display = '';
            timeSelectedSummary.classList.remove('show');
            timeCard.classList.remove('has-value');
        }
        updateScheduleNextButton();
    }

    function updateScheduleNextButton() {
        if (!scheduleReviewNext) return;
        const dateOk = !!borrowDate.value;
        const timeOk = !!pickupTime.value && pickupTime.value >= '08:00' && pickupTime.value <= '18:00';
        scheduleReviewNext.disabled = !(dateOk && timeOk);
    }

    dateChangeBtn.addEventListener('click', function () {
        if (dateSwapTimer) clearTimeout(dateSwapTimer);
        borrowDate.value = '';
        dateInputWrapper.style.display = '';
        dateSelectedSummary.classList.remove('show');
        dateCard.classList.remove('has-value');
        updateScheduleNextButton();
        setTimeout(() => {
            borrowDate.focus();
            if (typeof borrowDate.showPicker === 'function') {
                try { borrowDate.showPicker(); } catch (e) {}
            }
        }, 50);
    });

    timeChangeBtn.addEventListener('click', function () {
        if (timeSwapTimer) clearTimeout(timeSwapTimer);
        pickupTime.value = '';
        timeInputWrapper.style.display = '';
        timeSelectedSummary.classList.remove('show');
        timeCard.classList.remove('has-value');
        updateScheduleNextButton();
        setTimeout(() => {
            pickupTime.focus();
            if (typeof pickupTime.showPicker === 'function') {
                try { pickupTime.showPicker(); } catch (e) {}
            }
        }, 50);
    });

    borrowDate.addEventListener('input', commitDateDisplay);
    borrowDate.addEventListener('change', commitDateDisplay);
    pickupTime.addEventListener('input', commitTimeDisplay);
    pickupTime.addEventListener('change', commitTimeDisplay);

    function resetStudent() {
        showStep('identity');
        reviewNext.disabled = true;
        if (scheduleReviewNext) scheduleReviewNext.disabled = true;
        confirmReservation.disabled = true;
        studentId.value = '';
        studentNameInput.value = '';
        borrowerTypeInput.value = '';
        if (fingerprintIdInput) fingerprintIdInput.value = '';
        fingerprintVerified = !FINGERPRINT_REQUIRED;
        setBookSelectionEnabled(false);
        setSelectedBook(null);
        updateUserInfoDisplay(null);
        hideFingerprintMessages();
        resumeAutoReload();
    }

    function setSelectedBook(book) {
        selectedBookId.value = book ? book.id : '';
        selectedBooksList.innerHTML = '';
        if (book) {
            const item = document.createElement('div');
            item.className = 'selected-item';
            item.innerHTML = `
                <div>
                    <strong>${escapeHtml(book.title)}</strong>
                    <div class="small text-muted">${escapeHtml(book.author || 'Unknown')}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger p-1" data-remove-book>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            selectedBooksList.appendChild(item);
            emptySelection.style.display = 'none';
            selectedCount.textContent = '1 selected';
        } else {
            emptySelection.style.display = 'block';
            selectedCount.textContent = '0 selected';
        }
        booksContainer.querySelectorAll('.book-card').forEach(card => {
            const isSelected = book && card.dataset.bookId === String(book.id);
            card.classList.toggle('selected', isSelected);
            const icon = card.querySelector('.selection-icon');
            if (icon) {
                icon.innerHTML = isSelected
                    ? '<i class="fa-solid fa-square-check fa-lg text-danger"></i>'
                    : '<i class="fa-regular fa-square fa-lg"></i>';
            }
        });

        reviewNext.disabled = !(fingerprintVerified && !!book);
    }

    function updateReviewSummary() {
        reviewBorrowerName.textContent = displayBorrowerName.textContent || '—';
        reviewBorrowerNumber.textContent = displayBorrowerNumber.textContent || '—';
        reviewPickupDate.textContent = formatDate(borrowDate.value);
        reviewPickupTime.textContent = formatTime(pickupTime.value);

        reviewBooksList.innerHTML = '';
        const card = booksContainer.querySelector('.book-card[data-book-id="' + selectedBookId.value + '"]');
        if (card) {
            const title = card.querySelector('.book-title')?.textContent || '—';
            const details = card.querySelectorAll('.book-details');
            let author = 'Unknown';
            details.forEach(d => {
                if (d.textContent.toLowerCase().includes('author:')) {
                    author = d.textContent.replace(/Author:/i, '').trim();
                }
            });
            const row = document.createElement('div');
            row.className = 'confirm-book-row';
            row.innerHTML = `
                <div style="flex:1;min-width:0;">
                    <div class="confirm-book-title">${escapeHtml(title)}</div>
                    <div class="confirm-book-meta">by ${escapeHtml(author)}</div>
                </div>
                <span class="confirm-book-badge ontime">To Reserve</span>
            `;
            reviewBooksList.appendChild(row);
        }
    }

    document.getElementById('reserveBackIdentity').addEventListener('click', () => {
        resetStudent();
        rfidInput.value = '';
        setRfidStatus('Waiting for RFID scan...', 'waiting');
        showStep('identity');
        rfidInput.focus();
        resumeAutoReload();
    });

    document.getElementById('scheduleBackBooks').addEventListener('click', () => {
        showStep('books');
        bookSearch.focus();
    });

    document.getElementById('reserveBackSchedule').addEventListener('click', () => {
        showStep('schedule');
    });

    reviewNext.addEventListener('click', () => {
        if (reviewNext.disabled) return;
        pauseAutoReload();
        borrowDate.value = '';
        pickupTime.value = '';
        updateDateDisplay();
        updateTimeDisplay();
        showStep('schedule');
        setTimeout(() => borrowDate.focus(), 100);
    });

    scheduleReviewNext.addEventListener('click', () => {
        if (scheduleReviewNext.disabled) return;
        updateReviewSummary();
        confirmReservation.disabled = false;
        showStep('review');
    });

    if (FINGERPRINT_REQUIRED && fingerprintRetry) {
        fingerprintRetry.addEventListener('click', function () {
            hideFingerprintMessages();
            window.dispatchEvent(new CustomEvent('transaction-fingerprint-reset'));
            if (studentId.value && studentNameInput.value) {
                setFingerprintMessage('ready');
            }
            if (fingerprintIdInput) fingerprintIdInput.value = '';
            rfidInput.value = '';
            rfidInput.focus();
            setRfidStatus('Waiting for RFID scan...', 'waiting');
        });
    }

    async function processRfid() {
        if (processing) return;
        const rfid = rfidInput.value.trim();
        if (!rfid) return;
        processing = true;
        pauseAutoReload();
        const currentScan = ++scanGeneration;
        resetStudent();
        scanArea.classList.add('scanning');
        setRfidStatus('Checking your ID...', 'loading');
        try {
            const template = @json($reserveStudentRfidUrl);
            const response = await fetch(template.replace('__RFID__', encodeURIComponent(rfid)), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (currentScan !== scanGeneration) return;

            if (data.reservation_limit_reached) {
                resetStudent();
                rfidInput.value = '';
                setRfidStatus(data.message, 'error');
                reservationLimitMessage.textContent = data.message;
                bootstrap.Modal.getOrCreateInstance(reservationLimitModal).show();
                return;
            }

            if (!response.ok || !data.found) {
                throw new Error(data.message || 'RFID was not found.');
            }

            const borrowerType = data.borrower_type === 'personnel' ? 'Personnel' : 'Student';
            const borrowerNumber = data.borrower_number || data.student_number || '';
            const borrowerName = data.borrower_name || data.student_name || '';

            studentId.value = borrowerNumber;
            studentNameInput.value = borrowerName;
            borrowerTypeInput.value = data.borrower_type;

            const reservationUsageText = 'Active reservations: '
                + (data.active_reservations ?? 0) + ' of ' + (data.reservation_limit ?? '—')
                + '. You can reserve ' + (data.remaining_reservations ?? 0) + ' more.';

            updateUserInfoDisplay(data);

            if (FINGERPRINT_REQUIRED) {
                setBookSelectionEnabled(false);
                bookSelectionDescription.textContent = 'Verify the borrower fingerprint before selecting a book.';
                setFingerprintMessage('ready');
                setRfidStatus(borrowerType + ' RFID accepted. ' + reservationUsageText, 'success');
            } else {
                fingerprintVerified = true;
                setBookSelectionEnabled(true);
                bookSelectionDescription.textContent = 'Search for a book or pick one from the list.';
                bookRfidMessage.textContent = 'Ready to select a book.';
                setRfidStatus(borrowerType + ' RFID accepted. ' + reservationUsageText, 'success');

                reviewNext.disabled = !selectedBookId.value;

                setTimeout(() => {
                    if (currentScan === scanGeneration && studentId.value) {
                        showStep('books');
                        bookSearch.focus();
                    }
                }, 350);
            }
        } catch (error) {
            if (currentScan !== scanGeneration) return;
            resetStudent();
            rfidInput.value = '';
            setRfidStatus(error.message, 'error');
            rfidInput.focus();
        } finally {
            if (currentScan === scanGeneration) {
                processing = false;
                scanArea.classList.remove('scanning');
            }
        }
    }

    scanArea.addEventListener('click', () => {
        if (!processing && bookSelectionSection.hidden) rfidInput.focus();
    });

    rfidInput.addEventListener('input', function () {
        clearTimeout(scanTimer);
        pauseAutoReload();
        resetStudent();
        const val = this.value.trim();
        if (!val) {
            setRfidStatus('Waiting for RFID scan...', 'waiting');
            resumeAutoReload();
            return;
        }
        setRfidStatus('Reading RFID...', 'loading');
        scanTimer = setTimeout(processRfid, 350);
    });

    rfidInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(scanTimer);
            processRfid();
        }
    });

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
        if (studentId.value && studentNameInput.value) {
            setBookSelectionEnabled(true);
            bookSelectionDescription.textContent = 'Search for a book or pick one from the list.';
            bookRfidMessage.textContent = 'Ready to select a book.';
            showStep('books');
            bookSearch.focus();
            reviewNext.disabled = !selectedBookId.value;
            pauseAutoReload();
        }
    });

    window.addEventListener('transaction-fingerprint-rejected', function () {
        fingerprintVerified = false;
        if (fingerprintIdInput) fingerprintIdInput.value = '';
        setFingerprintMessage('error');
        setBookSelectionEnabled(false);
        setSelectedBook(null);
        reviewNext.disabled = true;
        confirmReservation.disabled = true;
    });

    window.addEventListener('transaction-fingerprint-reset', function () {
        fingerprintVerified = !FINGERPRINT_REQUIRED;
        if (fingerprintIdInput) fingerprintIdInput.value = '';
        hideFingerprintMessages();
        setBookSelectionEnabled(false);
        setSelectedBook(null);
        reviewNext.disabled = true;
        confirmReservation.disabled = true;
    });

    window.addEventListener('transaction-fingerprint-connection-error', function () {
        fingerprintVerified = false;
        if (fingerprintIdInput) fingerprintIdInput.value = '';
        setFingerprintMessage('conn');
        setBookSelectionEnabled(false);
        setSelectedBook(null);
        reviewNext.disabled = true;
        confirmReservation.disabled = true;
    });

    booksContainer.addEventListener('click', function (event) {
        const card = event.target.closest('.book-card');
        if (!card || !fingerprintVerified) return;
        if (card.getAttribute('data-unavailable') === 'true') return;
        const id = card.dataset.bookId;
        const title = card.querySelector('.book-title')?.textContent.trim() || '';
        const details = card.querySelectorAll('.book-details');
        let author = 'Unknown';
        details.forEach(d => {
            if (d.textContent.toLowerCase().includes('author:')) {
                author = d.textContent.replace(/Author:/i, '').trim();
            }
        });
        if (selectedBookId.value === id) {
            setSelectedBook(null);
        } else {
            setSelectedBook({ id, title, author });
        }
    });

    booksContainer.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const card = event.target.closest('.book-card');
        if (!card || !fingerprintVerified) return;
        if (card.getAttribute('data-unavailable') === 'true') return;
        event.preventDefault();
        card.click();
    });

    selectedBooksList.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-book]');
        if (!button) return;
        setSelectedBook(null);
    });

    function applyBookVisibility() {
        const search = bookSearch.value.toLowerCase().trim();
        booksContainer.querySelectorAll('.book-card').forEach(function (item) {
            if (item.getAttribute('data-unavailable') === 'true') {
                item.style.display = 'none';
                return;
            }
            if (!search) {
                item.style.display = '';
                return;
            }
            const haystack = [
                item.dataset.title,
                item.dataset.author,
                item.dataset.isbn,
                item.dataset.callNumber
            ].join(' ');
            item.style.display = haystack.includes(search) ? '' : 'none';
        });

        const noAvailable = document.getElementById('noAvailableBooks');
        if (noAvailable) {
            const visibleCount = booksContainer.querySelectorAll(
                '.book-card:not([data-unavailable="true"])'
            ).length;
            noAvailable.classList.toggle('d-none', visibleCount > 0 || search !== '');
        }
    }

    bookSearch.addEventListener('input', applyBookVisibility);
    clearSearchButton.addEventListener('click', function () {
        bookSearch.value = '';
        applyBookVisibility();
        bookSearch.focus();
    });

    function openCancelModal() { cancelBackdrop.classList.add('show'); }
    function closeCancelModal() { cancelBackdrop.classList.remove('show'); }

    document.querySelectorAll('[data-cancel-flow]').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            openCancelModal();
        });
    });
    cancelModalKeep.addEventListener('click', closeCancelModal);
    cancelBackdrop.addEventListener('click', function (event) {
        if (event.target === cancelBackdrop) closeCancelModal();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && cancelBackdrop.classList.contains('show')) {
            closeCancelModal();
        }
    });

    cancelModalConfirm.addEventListener('click', function () {
        window.location.href = @json(url()->previous());
    });

    /* ── Confirm reservation (AJAX) + Transaction Complete notification ── */
    confirmReservation.addEventListener('click', function (event) {
        event.preventDefault();
        if (confirmReservation.disabled) return;

        if (!studentId.value || !studentNameInput.value) {
            alert('Please scan and verify a student or personnel RFID card first.');
            rfidInput.focus();
            return;
        }
        if (FINGERPRINT_REQUIRED && !fingerprintVerified) {
            alert('Please verify the RFID owner fingerprint first.');
            return;
        }
        if (!selectedBookId.value) {
            alert('Please select a book before confirming the reservation.');
            bookSearch.focus();
            return;
        }
        if (!borrowDate.value) {
            alert('Please choose a pickup date.');
            borrowDate.focus();
            return;
        }
        if (!pickupTime.value || pickupTime.value < '08:00' || pickupTime.value > '18:00') {
            alert('Pickup time must be within the library office hours, from 8:00 AM to 6:00 PM.');
            pickupTime.focus();
            return;
        }

        pauseAutoReload();

        confirmReservation.disabled = true;
        confirmReservation.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Processing...';

        const formData = new FormData(reservationForm);
        if (!FINGERPRINT_REQUIRED) {
            formData.delete('fingerprint_id');
        }

        fetch(reservationForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async (response) => {
            let payload = null;
            try { payload = await response.json(); } catch (e) { payload = null; }
            if (!response.ok) {
                let message = 'The reservation request could not be completed.';
                if (payload) {
                    if (payload.message) message = payload.message;
                    if (payload.errors && typeof payload.errors === 'object') {
                        const firstKey = Object.keys(payload.errors)[0];
                        if (firstKey && Array.isArray(payload.errors[firstKey]) && payload.errors[firstKey][0]) {
                            message = payload.errors[firstKey][0];
                        }
                    }
                }
                throw new Error(message);
            }
            return payload || { success: true };
        })
        .then((payload) => {
            const redirectUrl = (payload && payload.redirect) || @json($reserveStoreUrl);
            window.__reservationRedirectUrl = redirectUrl;
            showTransactionComplete(payload || {});
            confirmReservation.disabled = false;
            confirmReservation.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirm Reservation';
        })
        .catch((error) => {
            alert(error.message || 'The reservation request could not be completed. Please try again.');
            confirmReservation.disabled = false;
            confirmReservation.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirm Reservation';
            resumeAutoReload();
        });
    });

    async function refreshAvailableBooks() {
        if (refreshingBooks || document.hidden) return;
        refreshingBooks = true;
        try {
            const response = await fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                },
                cache: 'no-store'
            });
            if (!response.ok) throw new Error('Unable to refresh available books.');
            const html = await response.text();
            const freshDocument = new DOMParser().parseFromString(html, 'text/html');
            const freshBooksContainer = freshDocument.getElementById('booksContainer');
            if (!freshBooksContainer) return;
            const freshHtml = freshBooksContainer.innerHTML;
            if (booksContainer.innerHTML.trim() !== freshHtml.trim()) {
                booksContainer.innerHTML = freshHtml;
                if (selectedBookId.value) {
                    const card = booksContainer.querySelector('.book-card[data-book-id="' + selectedBookId.value + '"]');
                    if (card) {
                        card.classList.add('selected');
                        const icon = card.querySelector('.selection-icon');
                        if (icon) icon.innerHTML = '<i class="fa-solid fa-square-check fa-lg text-danger"></i>';
                    } else {
                        setSelectedBook(null);
                    }
                }
                applyBookVisibility();
                pruneUnavailableBooks();
            }
        } catch (error) {
            console.warn(error.message);
        } finally {
            refreshingBooks = false;
        }
    }

    const bookRefreshMilliseconds = Math.max(
        1,
        Number(@json((int) ($systemSettings->table_refresh_seconds ?? 3)))
    ) * 1000;
    window.setInterval(refreshAvailableBooks, bookRefreshMilliseconds);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshAvailableBooks();
            resumeAutoReload();
        } else {
            pauseAutoReload();
        }
    });

    const oldBookId = selectedBookId.value;
    if (oldBookId) {
        const card = booksContainer.querySelector('.book-card[data-book-id="' + oldBookId + '"]');
        if (card) {
            card.classList.add('selected');
        } else {
            selectedBookId.value = '';
        }
    }

    applyBookVisibility();
    pruneUnavailableBooks();
    setBookSelectionEnabled(false);
    updateUserInfoDisplay(null);
    hideFingerprintMessages();
    updateDateDisplay();
    updateTimeDisplay();
    showStep('identity');
    rfidInput.focus();

    // Start the auto-reload countdown
    startAutoReload();
});
</script>
@include('layouts.system_settings_live')
</body>
</html>