@php
    $systemSettings = $systemSettings
        ?? \App\Models\SystemSetting::current()
        ?? new \App\Models\SystemSetting();

    // Auto-reload interval in seconds (default 60 seconds, configurable via system settings)
    $autoReloadSeconds = max(10, (int) ($systemSettings->kiosk_auto_reload_seconds ?? 60));

    // Safe fallbacks so the page never crashes when no SystemSetting row exists
    $timezone = $systemSettings->timezone ?? 'Asia/Manila';
    $institutionName = $systemSettings->institution_name ?? 'Lourdes College';
    $kioskTitle = $systemSettings->attendance_kiosk_title ?? 'Attendance Kiosk';
    $kioskSubtitle = $systemSettings->attendance_kiosk_subtitle ?? 'Learning Commons Attendance Monitoring';
    $logoUrl = method_exists($systemSettings, 'logoUrl')
        ? $systemSettings->logoUrl()
        : asset('images/default-logo.png');

    $resultDuration = max(1, (int) ($systemSettings->attendance_result_duration ?? 3));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title data-system-title-mode="attendance">{{ $kioskTitle }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =====================================================
           GLOBAL – NO SCROLL, FILL VIEWPORT
        ====================================================== */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            overflow: hidden;           /* prevent page scroll */
        }

        body {
            background: linear-gradient(135deg, #fdecec, #f7c8d0);

            font-family: 'Montserrat', Arial, Helvetica, sans-serif;

            color: #292929;
        }

        /* =====================================================
           KIOSK SHELL – full viewport, flex center
        ====================================================== */

        .attendance-wrapper {
            height: 100vh;
            width: 100vw;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: clamp(8px, 2vh, 28px) clamp(10px, 3vw, 40px);

            overflow: hidden;
        }

        /* =====================================================
           ATTENDANCE CARD – scales with viewport
        ====================================================== */

        .attendance-card {
            width: 100%;
            max-width: min(900px, 95vw);
            max-height: 96vh;

            background: rgba(255, 255, 255, 0.72);

            border: 2px solid rgba(255, 255, 255, 0.75);

            border-radius: clamp(18px, 3vh, 35px);

            padding: clamp(14px, 3vh, 40px) clamp(16px, 4vw, 50px);

            text-align: center;

            box-shadow: 0 25px 60px rgba(128, 62, 84, 0.20);

            backdrop-filter: blur(10px);

            display: flex;
            flex-direction: column;

            overflow: hidden;
        }

        /* =====================================================
           LOGO
        ====================================================== */

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;

            flex-shrink: 0;

            margin-bottom: clamp(2px, 1vh, 12px);
        }

        .attendance-logo {
            width: clamp(56px, 11vh, 120px);
            height: clamp(56px, 11vh, 120px);

            object-fit: contain;

            display: block;
        }

        /* =====================================================
           SCHOOL NAME
        ====================================================== */

        .school-name {
            flex-shrink: 0;

            color: #d94d82;

            font-size: clamp(0.7rem, 1.6vh, 1.2rem);
            font-weight: 700;

            letter-spacing: clamp(1px, 0.6vw, 4px);

            text-transform: uppercase;

            margin-bottom: clamp(2px, 0.5vh, 6px);
        }

        /* =====================================================
           ATTENDANCE TITLE
        ====================================================== */

        .attendance-title {
            flex-shrink: 0;

            color: #292929;

            font-size: clamp(1.2rem, 4vh, 2.6rem);
            font-weight: 600;

            letter-spacing: clamp(1px, 0.7vw, 4px);

            text-transform: uppercase;

            margin: 0;
        }

        /* =====================================================
           SUBTITLE
        ====================================================== */

        .attendance-subtitle {
            flex-shrink: 0;

            margin-top: clamp(2px, 0.8vh, 10px);
            margin-bottom: clamp(6px, 1.8vh, 22px);

            color: #6d6266;

            font-size: clamp(0.65rem, 1.3vh, 1rem);
            font-weight: 500;
        }

        /* =====================================================
           DATE TIME BOX
        ====================================================== */

        .date-time-box {
            flex-shrink: 0;

            margin-bottom: clamp(8px, 2vh, 24px);
        }

        .current-time {
            color: #d94d82;

            font-size: clamp(1.6rem, 6vh, 3rem);
            font-weight: 700;

            line-height: 1.1;
        }

        .current-date {
            margin-top: clamp(2px, 0.6vh, 8px);

            color: #555;

            font-size: clamp(0.65rem, 1.3vh, 1rem);
            font-weight: 500;
        }

        /* =====================================================
           SCANNER PANEL – takes remaining space
        ====================================================== */

        .scanner-panel {
            flex: 1;

            min-height: 0;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            padding: clamp(10px, 2.5vh, 30px);

            border: 3px dashed #ed97b4;
            border-radius: clamp(14px, 2.5vh, 25px);

            background: linear-gradient(135deg, #f9d8e2, #f3b8ca);

            cursor: pointer;

            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .scanner-panel:hover {
            transform: translateY(-4px);

            box-shadow: 0 15px 30px rgba(128, 62, 84, 0.18);
        }

        .scanner-panel.scanning {
            animation: scanningPulse 1s infinite;
        }

        @keyframes scanningPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(217, 77, 130, 0.25);
            }

            50% {
                box-shadow: 0 0 0 15px rgba(217, 77, 130, 0);
            }
        }

        .rfid-icon {
            width: clamp(48px, 12vh, 90px);
            height: clamp(48px, 12vh, 90px);

            display: flex;
            justify-content: center;
            align-items: center;

            margin-bottom: clamp(6px, 1.5vh, 20px);

            border: 3px solid #ffffff;
            border-radius: 50%;

            background: #e98eac;

            color: #ffffff;

            font-size: clamp(1.4rem, 3.5vh, 2.8rem);

            box-shadow: 0 10px 25px rgba(128, 62, 84, 0.20);
        }

        .scanner-title {
            margin: 0 0 clamp(2px, 0.8vh, 8px);

            color: #ffffff;

            font-size: clamp(1rem, 3vh, 1.8rem);
            font-weight: 700;

            letter-spacing: clamp(1px, 0.6vw, 3px);

            text-transform: uppercase;
        }

        .scanner-message {
            margin: 0;

            color: #704e5b;

            font-size: clamp(0.65rem, 1.4vh, 1rem);
            font-weight: 500;
        }

        /* =====================================================
           HIDDEN RFID INPUT
        ====================================================== */

        #rfidScanner {
            position: fixed;

            left: -9999px;
            top: -9999px;

            width: 1px;
            height: 1px;

            opacity: 0;

            border: 0;
            outline: none;
        }

        /* =====================================================
           RESULT OVERLAY
        ====================================================== */

        .result-overlay {
            position: fixed;
            inset: 0;

            z-index: 9999;

            display: none;
            justify-content: center;
            align-items: center;

            padding: 20px;

            background: rgba(63, 31, 43, 0.48);

            backdrop-filter: blur(6px);

            overflow: hidden;
        }

        .result-overlay.show {
            display: flex;
        }

        .result-card {
            width: 100%;
            max-width: min(650px, 95vw);
            max-height: 96vh;

            padding: clamp(16px, 3vh, 40px);

            background: #ffffff;

            border-radius: clamp(18px, 3vh, 30px);

            text-align: center;

            box-shadow: 0 30px 80px rgba(54, 25, 36, 0.35);

            animation: showResult 0.3s ease;

            overflow-y: auto;
        }

        @keyframes showResult {
            from {
                opacity: 0;
                transform: scale(0.90);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .result-icon {
            width: clamp(56px, 12vh, 100px);
            height: clamp(56px, 12vh, 100px);

            display: flex;
            justify-content: center;
            align-items: center;

            margin: 0 auto clamp(6px, 1.5vh, 20px);

            border-radius: 50%;

            color: #ffffff;

            font-size: clamp(1.6rem, 4vh, 3.2rem);
            font-weight: 700;
        }

        .result-icon.clock-in {
            background: linear-gradient(135deg, #e98eac, #d94d82);
        }

        .result-icon.clock-out {
            background: linear-gradient(135deg, #9c78d1, #7953ad);
        }

        .result-icon.error {
            background: linear-gradient(135deg, #ef6b6b, #ce3d3d);
        }

        .result-action {
            margin-bottom: clamp(2px, 0.6vh, 8px);

            color: #d94d82;

            font-size: clamp(0.7rem, 1.6vh, 1.1rem);
            font-weight: 700;

            letter-spacing: clamp(1px, 0.6vw, 3px);

            text-transform: uppercase;
        }

        .result-name {
            margin: 0;

            color: #292929;

            font-size: clamp(1.1rem, 3.5vh, 2.2rem);
            font-weight: 700;
        }

        .result-id {
            margin-top: clamp(2px, 0.6vh, 8px);

            color: #666;

            font-size: clamp(0.7rem, 1.5vh, 1.1rem);
            font-weight: 500;
        }

        .person-type-badge {
            display: inline-block;

            margin-top: clamp(4px, 1.2vh, 15px);
            padding: clamp(3px, 0.6vh, 8px) clamp(10px, 1.6vw, 22px);

            border-radius: 50px;

            background: #fde3ec;

            color: #cf4679;

            font-size: clamp(0.55rem, 1.2vh, 0.9rem);
            font-weight: 700;

            letter-spacing: 1px;

            text-transform: uppercase;
        }

        /* =====================================================
           INFORMATION BOX
        ====================================================== */

        .information-box {
            display: grid;

            grid-template-columns: repeat(2, minmax(0, 1fr));

            gap: clamp(6px, 1.2vh, 15px);

            margin-top: clamp(8px, 2vh, 25px);
        }

        .information-item {
            padding: clamp(8px, 1.5vh, 17px);

            border-radius: clamp(8px, 1.5vh, 16px);

            background: #fff3f7;

            text-align: left;
        }

        .information-label {
            display: block;

            margin-bottom: clamp(2px, 0.5vh, 6px);

            color: #a06c7e;

            font-size: clamp(0.5rem, 1vh, 0.75rem);
            font-weight: 700;

            letter-spacing: 1px;

            text-transform: uppercase;
        }

        .information-value {
            display: block;

            color: #333;

            font-size: clamp(0.65rem, 1.4vh, 1rem);
            font-weight: 600;

            word-break: break-word;
        }

        .result-message {
            margin: clamp(8px, 2vh, 25px) 0 0;

            color: #555;

            font-size: clamp(0.65rem, 1.4vh, 1rem);
            font-weight: 500;
        }

        /* =====================================================
           COUNTDOWN
        ====================================================== */

        .result-countdown {
            margin-top: clamp(6px, 1.5vh, 20px);
            padding: clamp(6px, 1vh, 12px) clamp(8px, 1.2vw, 14px);

            border-radius: clamp(8px, 1.2vh, 13px);

            background: #fff3f7;
            color: #9b526d;

            font-size: clamp(0.55rem, 1.2vh, 0.86rem);
            font-weight: 700;
        }

        .countdown-track {
            height: clamp(4px, 0.8vh, 7px);
            margin-top: clamp(4px, 0.8vh, 9px);

            overflow: hidden;

            border-radius: 20px;

            background: #f0d4df;
        }

        .countdown-progress {
            width: 100%;
            height: 100%;

            border-radius: inherit;

            background: linear-gradient(90deg, #e98eac, #d94d82);

            transition: width 1s linear;
        }

        .error-card .result-countdown {
            background: #fff0f0;
            color: #b64747;
        }

        .error-card .countdown-progress {
            background: linear-gradient(90deg, #ef8686, #ce3d3d);
        }

        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        .close-result-button {
            width: 100%;

            margin-top: clamp(8px, 2vh, 25px);
            padding: clamp(8px, 1.6vh, 15px) clamp(12px, 2vw, 25px);

            border: none;
            border-radius: clamp(8px, 1.4vh, 14px);

            background: linear-gradient(135deg, #e98eac, #d94d82);

            color: #ffffff;

            font-family: inherit;

            font-size: clamp(0.7rem, 1.5vh, 1rem);
            font-weight: 700;

            letter-spacing: 2px;

            text-transform: uppercase;

            cursor: pointer;
        }

        .close-result-button:hover {
            background: linear-gradient(135deg, #df789b, #c93e72);
        }

        .error-card .result-action {
            color: #ce3d3d;
        }

        .error-card .person-type-badge,
        .error-card .information-box {
            display: none;
        }

        /* =====================================================
           LOADING OVERLAY
        ====================================================== */

        .loading-overlay {
            position: fixed;
            inset: 0;

            z-index: 9998;

            display: none;
            justify-content: center;
            align-items: center;

            background: rgba(63, 31, 43, 0.40);
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-box {
            padding: clamp(20px, 3vh, 35px) clamp(28px, 4vw, 50px);

            border-radius: clamp(14px, 2.4vh, 24px);

            background: #ffffff;

            color: #d94d82;

            text-align: center;

            box-shadow: 0 25px 60px rgba(54, 25, 36, 0.30);
        }

        .loading-spinner {
            width: clamp(36px, 7vh, 55px);
            height: clamp(36px, 7vh, 55px);

            margin: 0 auto clamp(8px, 1.5vh, 18px);

            border: 5px solid #f8d4e0;
            border-top-color: #d94d82;
            border-radius: 50%;

            animation: spin 0.75s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            margin: 0;

            font-weight: 700;
            letter-spacing: 2px;

            font-size: clamp(0.7rem, 1.5vh, 1rem);
        }

        /* =====================================================
           MOBILE – stack information grid
        ====================================================== */

        @media (max-width: 500px) {
            .information-box {
                grid-template-columns: 1fr;
            }
        }

        /* =====================================================
           VERY SHORT SCREENS (landscape phones, etc.)
        ====================================================== */

        @media (max-height: 520px) {
            .attendance-wrapper {
                padding: 4px 10px;
            }

            .attendance-card {
                max-height: 98vh;
                padding: 8px 16px;
            }

            .attendance-logo {
                width: clamp(36px, 8vh, 60px);
                height: clamp(36px, 8vh, 60px);
            }

            .school-name {
                font-size: clamp(0.55rem, 1.4vh, 0.8rem);
                margin-bottom: 1px;
            }

            .attendance-title {
                font-size: clamp(0.9rem, 3vh, 1.4rem);
            }

            .attendance-subtitle {
                font-size: clamp(0.55rem, 1.1vh, 0.75rem);
                margin-top: 1px;
                margin-bottom: 2px;
            }

            .date-time-box {
                margin-bottom: 4px;
            }

            .current-time {
                font-size: clamp(1.1rem, 4vh, 1.8rem);
            }

            .current-date {
                font-size: clamp(0.55rem, 1.1vh, 0.75rem);
                margin-top: 1px;
            }

            .scanner-panel {
                padding: 6px;
                border-radius: 12px;
            }

            .rfid-icon {
                width: clamp(30px, 8vh, 48px);
                height: clamp(30px, 8vh, 48px);
                font-size: clamp(0.9rem, 2.4vh, 1.5rem);
                margin-bottom: 3px;
                border-width: 2px;
            }

            .scanner-title {
                font-size: clamp(0.75rem, 2.4vh, 1.1rem);
                margin-bottom: 2px;
            }

            .scanner-message {
                font-size: clamp(0.55rem, 1.1vh, 0.75rem);
            }
        }
    </style>
</head>

<body>

<input type="text" id="rfidScanner" autocomplete="off" autofocus>

<main class="attendance-wrapper">

    <section class="attendance-card">

        <div class="logo-container">
            <img src="{{ $logoUrl }}"
                data-system-logo
                class="attendance-logo"
                alt="Institution Logo"
            >
        </div>

        <p class="school-name">
            <span data-institution-name>{{ $institutionName }}</span>
        </p>

        <h1 class="attendance-title">
            <span data-attendance-kiosk-title>{{ $kioskTitle }}</span>
        </h1>

        <p class="attendance-subtitle">
            <span data-attendance-kiosk-subtitle>{{ $kioskSubtitle }}</span>
        </p>

        <div class="date-time-box">
            <div id="currentTime" class="current-time">
                --:--:--
            </div>

            <div id="currentDate" class="current-date">
                Loading date...
            </div>
        </div>

        <div id="scannerPanel" class="scanner-panel" role="button" tabindex="0">
            <div class="rfid-icon">
                )))
            </div>

            <h2 class="scanner-title">
                Tap Your RFID Card
            </h2>

            <p class="scanner-message">
                Place your card near the RFID scanner
            </p>
        </div>

    </section>

</main>

<!-- Loading -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-box">
        <div class="loading-spinner"></div>

        <p class="loading-text">
            CHECKING RFID...
        </p>
    </div>
</div>

<!-- Attendance result -->
<div id="resultOverlay" class="result-overlay">
    <div id="resultCard" class="result-card">
        <div id="resultIcon" class="result-icon clock-in">
            ✓
        </div>

        <div id="resultAction" class="result-action">
            Clock In
        </div>

        <h2 id="resultName" class="result-name">
            Person Name
        </h2>

        <div id="resultId" class="result-id">
            ID Number
        </div>

        <span id="personType" class="person-type-badge">
            Student
        </span>

        <div class="information-box">

            <div class="information-item">
                <span class="information-label">
                    Program / Department
                </span>

                <span id="programDepartment" class="information-value">
                    —
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Year Level
                </span>

                <span id="yearLevel" class="information-value">
                    —
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Date
                </span>

                <span id="attendanceDate" class="information-value">
                    —
                </span>
            </div>

            <div class="information-item">
                <span id="attendanceTimeLabel" class="information-label">
                    Time In
                </span>

                <span id="attendanceTime" class="information-value">
                    —
                </span>
            </div>

        </div>

        <p id="resultMessage" class="result-message">
        </p>

        <div id="resultCountdown" class="result-countdown" role="status" aria-live="polite">
            <span id="countdownText">Closing in 3 seconds</span>

            <div class="countdown-track" aria-hidden="true">
                <div id="countdownProgress" class="countdown-progress"></div>
            </div>
        </div>

        <button type="button" id="closeResultButton" class="close-result-button">
            Done
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rfidScanner = document.getElementById('rfidScanner');
        const scannerPanel = document.getElementById('scannerPanel');

        const loadingOverlay =
            document.getElementById('loadingOverlay');

        const resultOverlay =
            document.getElementById('resultOverlay');

        const resultCard =
            document.getElementById('resultCard');

        const resultIcon =
            document.getElementById('resultIcon');

        const resultAction =
            document.getElementById('resultAction');

        const resultName =
            document.getElementById('resultName');

        const resultId =
            document.getElementById('resultId');

        const personType =
            document.getElementById('personType');

        const programDepartment =
            document.getElementById('programDepartment');

        const yearLevel =
            document.getElementById('yearLevel');

        const attendanceDate =
            document.getElementById('attendanceDate');

        const attendanceTimeLabel =
            document.getElementById('attendanceTimeLabel');

        const attendanceTime =
            document.getElementById('attendanceTime');

        const resultMessage =
            document.getElementById('resultMessage');

        const closeResultButton =
            document.getElementById('closeResultButton');

        const countdownText =
            document.getElementById('countdownText');

        const countdownProgress =
            document.getElementById('countdownProgress');

        let isProcessing = false;
        let autoCloseTimer = null;
        let countdownTimer = null;

        /* ── Server-provided configuration ── */
        const successDisplaySeconds = {{ $resultDuration }};
        const AUTO_RELOAD_SECONDS = {{ $autoReloadSeconds }};
        const TIMEZONE = @json($timezone);

        @if (Route::has('attendance.scan'))
            const SCAN_URL = "{{ route('attendance.scan') }}";
        @else
            const SCAN_URL = null;
        @endif

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

                if (isProcessing) return;

                if (resultOverlay && resultOverlay.classList.contains('show')) return;

                if (loadingOverlay && loadingOverlay.classList.contains('show')) return;

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

        function startResultCountdown(seconds) {
            clearTimeout(autoCloseTimer);
            clearInterval(countdownTimer);

            let remaining = Math.max(1, Number(seconds));
            countdownProgress.style.transition = 'none';
            countdownProgress.style.width = '100%';

            function updateCountdown() {
                countdownText.textContent =
                    'Information closes in ' + remaining +
                    (remaining === 1 ? ' second' : ' seconds');

                countdownProgress.style.transition = 'width 1s linear';
                countdownProgress.style.width =
                    Math.max(0, (remaining / seconds) * 100) + '%';
            }

            updateCountdown();

            countdownTimer = setInterval(function () {
                remaining--;

                if (remaining <= 0) {
                    clearInterval(countdownTimer);
                    countdownText.textContent = 'Closing...';
                    return;
                }

                updateCountdown();
            }, 1000);

            autoCloseTimer = setTimeout(closeResult, remaining * 1000);
        }

        function focusScanner() {
            if (!isProcessing) {
                rfidScanner.value = '';
                rfidScanner.focus();
            }
        }

        scannerPanel.addEventListener('click', focusScanner);

        scannerPanel.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                focusScanner();
            }
        });

        document.addEventListener('click', function (event) {
            if (
                !resultOverlay.classList.contains('show') &&
                !loadingOverlay.classList.contains('show')
            ) {
                focusScanner();
            }
        });

        window.addEventListener('focus', focusScanner);

        rfidScanner.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();

            const rfid = rfidScanner.value.trim();

            rfidScanner.value = '';

            if (rfid === '' || isProcessing) {
                focusScanner();
                return;
            }

            scanRfid(rfid);
        });

        async function scanRfid(rfid) {
            if (!SCAN_URL) {
                showError('Attendance scan endpoint is not configured.');
                return;
            }

            isProcessing = true;
            pauseAutoReload();

            scannerPanel.classList.add('scanning');
            loadingOverlay.classList.add('show');

            try {
                const response = await fetch(SCAN_URL, {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',

                        'X-CSRF-TOKEN':
                            document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                    },

                    body: JSON.stringify({
                        rfid_tag_uid: rfid
                    })
                });

                let data;

                try {
                    data = await response.json();
                } catch (error) {
                    throw new Error(
                        'The server returned an invalid response.'
                    );
                }

                loadingOverlay.classList.remove('show');

                if (!response.ok || !data.success) {
                    // Check if it's a working hours error specifically
                    if (
                        response.status === 403 &&
                        typeof data.message === 'string' &&
                        data.message.includes('only allowed between')
                    ) {
                        showError(
                            data.message,
                            'Outside Working Hours'
                        );
                    } else {
                        showError(
                            data.message ||
                            'RFID card is not registered.'
                        );
                    }

                    return;
                }

                showSuccess(data);

            } catch (error) {
                loadingOverlay.classList.remove('show');

                showError(
                    error.message ||
                    'Unable to connect to the server.'
                );
            } finally {
                isProcessing = false;
                scannerPanel.classList.remove('scanning');
            }
        }

        function showSuccess(data) {
            resultCard.classList.remove('error-card');

            const isClockIn =
                data.action === 'clock_in';

            resultIcon.className =
                'result-icon ' +
                (isClockIn ? 'clock-in' : 'clock-out');

            resultIcon.textContent =
                isClockIn ? '✓' : '↗';

            resultAction.textContent =
                isClockIn ? 'Clock In' : 'Clock Out';

            resultName.textContent =
                data.name || 'Unknown Person';

            const idNumber =
                data.person_type === 'Student'
                    ? data.student_number
                    : data.employee_number;

            resultId.textContent =
                idNumber
                    ? 'ID Number: ' + idNumber
                    : 'ID Number: Not available';

            personType.textContent =
                data.person_type || 'Unknown';

            programDepartment.textContent =
                data.person_type === 'Student'
                    ? (data.course_program || 'Not available')
                    : (data.department || 'Not available');

            yearLevel.textContent =
                data.person_type === 'Student'
                    ? (data.year_level || 'Not available')
                    : 'Not applicable';

            attendanceDate.textContent =
                formatDate(data.date);

            attendanceTimeLabel.textContent =
                isClockIn ? 'Time In' : 'Time Out';

            attendanceTime.textContent =
                formatTime(
                    isClockIn
                        ? data.time_in
                        : data.time_out
                );

            resultMessage.textContent =
                data.message || 'Attendance recorded successfully.';

            resultOverlay.classList.add('show');
            startResultCountdown(successDisplaySeconds);
        }

        function showError(message, title = 'RFID Not Recognized') {
            resultCard.classList.add('error-card');

            resultIcon.className =
                'result-icon error';

            resultIcon.textContent = '!';

            resultAction.textContent = title;

            resultName.textContent =
                'Card Not Registered';

            resultId.textContent =
                'Please contact the administrator.';

            resultMessage.textContent =
                message;

            resultOverlay.classList.add('show');

            startResultCountdown(5);
        }

        function closeResult() {
            clearTimeout(autoCloseTimer);
            clearInterval(countdownTimer);

            resultOverlay.classList.remove('show');

            setTimeout(function () {
                focusScanner();
                resumeAutoReload();
            }, 100);
        }

        closeResultButton.addEventListener(
            'click',
            closeResult
        );

        function formatDate(value) {
            if (!value) {
                return 'Not available';
            }

            if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                const parts = value.split('-');

                const dateOnly = new Date(
                    Number(parts[0]),
                    Number(parts[1]) - 1,
                    Number(parts[2])
                );

                return dateOnly.toLocaleDateString(
                    'en-PH',
                    {
                        timeZone: TIMEZONE,
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }
                );
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleDateString(
                'en-PH',
                {
                    timeZone: TIMEZONE,
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }
            );
        }

        function formatTime(value) {
            if (!value) {
                return 'Not available';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleTimeString(
                'en-PH',
                {
                    timeZone: TIMEZONE,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                }
            );
        }

        function updateClock() {
            const now = new Date();

            document
                .getElementById('currentTime')
                .textContent =
                    now.toLocaleTimeString(
                        'en-PH',
                        {
                            timeZone: TIMEZONE,
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }
                    );

            document
                .getElementById('currentDate')
                .textContent =
                    now.toLocaleDateString(
                        'en-PH',
                        {
                            timeZone: TIMEZONE,
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }
                    );
        }

        updateClock();

        setInterval(updateClock, 1000);

        focusScanner();

        // Start the auto-reload countdown
        startAutoReload();

        // Trigger auto-cutoff for forgotten attendances (in case scheduler isn't set up)
        @if (Route::has('attendance.auto_cutoff'))
        fetch("{{ route('attendance.auto_cutoff') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        }).catch(function () {
            console.log('Auto-cutoff check completed.');
        });
        @endif

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