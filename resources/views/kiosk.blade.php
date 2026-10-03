<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Book Borrow / Return</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

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
            overflow: hidden;
        }

        body {
            background:
                linear-gradient(
                    135deg,
                    #FDECEC,
                    #F7C8D0
                );

            font-family:
                'Montserrat',
                Arial,
                Helvetica,
                sans-serif;

            color: #252525;
        }

        /* =====================================================
           KIOSK SHELL – full viewport, flex column
        ====================================================== */

        .kiosk-shell {
            height: 100vh;
            width: 100vw;

            display: flex;
            flex-direction: column;

            align-items: center;
            justify-content: center;

            padding: clamp(8px, 2vh, 24px) clamp(10px, 3vw, 40px);

            gap: clamp(8px, 2vh, 24px);
        }

        /* =====================================================
           LOGO – scales with viewport height & width
        ====================================================== */

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;

            flex-shrink: 0;
        }

        .kiosk-logo {
            width: clamp(70px, 11vh, 140px);
            height: clamp(70px, 11vh, 140px);

            object-fit: contain;
        }

        /* =====================================================
           TITLE – fluid typography, compact
        ====================================================== */

        .title {
            flex-shrink: 0;

            font-size: clamp(1.1rem, 4vh, 2.8rem);
            font-weight: 500;

            letter-spacing: clamp(1px, 0.5vw, 4px);
            line-height: 1.25;

            text-align: center;
            text-transform: uppercase;

            color: #252525;
        }

        /* =====================================================
           BUTTON ROW – takes 60% of viewport height
        ====================================================== */

        .button-row {
            flex: 0 0 auto;

            height: 60vh;                /* ← 60% of screen height */
            max-height: 60vh;

            width: 100%;
            max-width: 1000px;

            display: flex;
            flex-wrap: wrap;

            gap: clamp(10px, 2vw, 30px);

            align-items: stretch;
            justify-content: center;

            min-height: 0;
        }

        /* =====================================================
           BUTTON COLUMN – equal width, full height
        ====================================================== */

        .button-col {
            flex: 1 1 0;

            min-width: 160px;
            max-width: 480px;

            display: flex;

            min-height: 0;
        }

        .button-col form {
            flex: 1;
            display: flex;
        }

        /* =====================================================
           KIOSK BUTTON – fills its column (100% of 60vh row)
        ====================================================== */

        .kiosk-box {
            flex: 1;

            width: 100%;
            height: 100%;

            border:
                2px solid
                rgba(255, 255, 255, 0.65);

            border-radius: clamp(14px, 2.2vh, 24px);

            background:
                linear-gradient(
                    135deg,
                    #F3A8BE,
                    #E98EAC
                );

            color: #ffffff;

            text-decoration: none;

            font-family: inherit;

            cursor: pointer;

            box-shadow:
                0 14px 28px
                rgba(128, 62, 84, 0.18);

            transition:
                background 0.25s ease,
                transform 0.25s ease,
                box-shadow 0.25s ease;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* =====================================================
           BUTTON INNER CONTENT
        ====================================================== */

        .kiosk-button-content {
            display: flex;
            flex-direction: column;

            justify-content: center;
            align-items: center;

            gap: clamp(8px, 1.8vh, 22px);

            padding: clamp(10px, 2vh, 24px);
        }

        /* =====================================================
           BUTTON ICON – scales with the 60vh row
        ====================================================== */

        .kiosk-icon {
            width: clamp(48px, 12vh, 110px);
            height: clamp(48px, 12vh, 110px);

            display: flex;
            justify-content: center;
            align-items: center;

            border:
                3px solid
                rgba(255, 255, 255, 0.9);

            border-radius: 50%;

            font-size: clamp(1.3rem, 4.2vh, 2.8rem);

            color: #ffffff;

            flex-shrink: 0;
        }

        /* =====================================================
           BUTTON TEXT – scales with the 60vh row
        ====================================================== */

        .kiosk-button-text {
            display: block;

            font-size: clamp(1.1rem, 4.8vh, 3rem);
            font-weight: 600;

            letter-spacing: clamp(2px, 0.7vw, 8px);
            line-height: 1;

            text-align: center;
            white-space: nowrap;
        }

        /* =====================================================
           BUTTON HOVER
        ====================================================== */

        .kiosk-box:hover {
            background:
                linear-gradient(
                    135deg,
                    #EC97B2,
                    #DF789B
                );

            color: #ffffff;

            transform: translateY(-5px);

            box-shadow:
                0 22px 38px
                rgba(128, 62, 84, 0.25);
        }

        /* =====================================================
           BUTTON ACTIVE
        ====================================================== */

        .kiosk-box:active {
            transform: scale(0.97);

            box-shadow:
                0 10px 20px
                rgba(128, 62, 84, 0.20);
        }

        /* =====================================================
           BUTTON FOCUS
        ====================================================== */

        .kiosk-box:focus {
            outline:
                4px solid
                rgba(222, 105, 145, 0.25);

            outline-offset: 4px;
        }

        /* =====================================================
           MOBILE – stack buttons, keep 60vh total for the row
        ====================================================== */

        @media (max-width: 640px) {

            .button-row {
                flex-direction: column;

                height: 60vh;
                max-height: 60vh;

                gap: clamp(8px, 1.2vh, 14px);

                max-width: 100%;
            }

            .button-col {
                flex: 1 1 0;             /* each button gets half of 60vh */
                max-width: 100%;
                width: 100%;
                min-width: 0;
            }

            .kiosk-box {
                border-radius: 14px;
            }

            .kiosk-button-content {
                flex-direction: row;     /* icon beside text */
                gap: clamp(8px, 2vw, 16px);
                padding: clamp(8px, 1.5vh, 16px);
            }

            .kiosk-icon {
                width: clamp(34px, 9vh, 60px);
                height: clamp(34px, 9vh, 60px);

                font-size: clamp(0.9rem, 3vh, 1.7rem);

                border-width: 2px;
            }

            .kiosk-button-text {
                font-size: clamp(0.9rem, 4vh, 1.8rem);
                letter-spacing: clamp(2px, 0.7vw, 4px);
            }
        }

        /* =====================================================
           VERY SHORT SCREENS (landscape phones, etc.)
        ====================================================== */

        @media (max-height: 520px) {

            .kiosk-shell {
                padding: 4px 10px;
                gap: 4px;
            }

            .kiosk-logo {
                width: clamp(40px, 8vh, 70px);
                height: clamp(40px, 8vh, 70px);
            }

            .title {
                font-size: clamp(0.8rem, 3vh, 1.4rem);
            }

            .button-row {
                height: 62vh;             /* slightly more on very short screens */
                max-height: 62vh;
            }

            .kiosk-button-content {
                gap: 4px;
                padding: 6px;
            }

            .kiosk-icon {
                width: clamp(28px, 7vh, 44px);
                height: clamp(28px, 7vh, 44px);
                font-size: clamp(0.7rem, 2.2vh, 1.2rem);
                border-width: 2px;
            }

            .kiosk-button-text {
                font-size: clamp(0.75rem, 2.8vh, 1.3rem);
                letter-spacing: clamp(1px, 0.5vw, 3px);
            }
        }
    </style>
</head>

<body>

<div class="kiosk-shell">

    <!-- Logo -->
    <div class="logo-container">
        <img
            src="{{ asset('Image/LC_LOGO.png') }}"
            class="kiosk-logo"
            alt="Lourdes College Logo"
        >
    </div>

    <!-- Title -->
    <h1 class="title">
        Lourdes College
        <br>
        Book Borrow / Return
    </h1>

    <!-- Borrow and Return Buttons – 60% of screen height -->
    <div class="button-row">

        <!-- Borrow -->
        <div class="button-col">
            <form
                action="{{ route('borrow.enter') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="kiosk-box"
                >
                    <div class="kiosk-button-content">

                        <div class="kiosk-icon">
                            &#128214;
                        </div>

                        <span class="kiosk-button-text">
                            Borrow
                        </span>

                    </div>
                </button>
            </form>
        </div>

        <!-- Return -->
        <div class="button-col">
            <form
                action="{{ route('return.enter') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="kiosk-box"
                >
                    <div class="kiosk-button-content">

                        <div class="kiosk-icon">
                            &#8634;
                        </div>

                        <span class="kiosk-button-text">
                            Return
                        </span>

                    </div>
                </button>
            </form>
        </div>

    </div>

</div>

<!-- Bootstrap -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
     * USB RFID readers usually send Enter after the card number. If a kiosk
     * button still has focus, that Enter would submit its form and redirect.
     * Only an intentional mouse/touch click should open Borrow or Return.
     */
    document.querySelectorAll('.kiosk-box').forEach(function (button) {
        button.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                button.blur();
            }
        });
    });

    document.querySelectorAll('form[action]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!event.submitter) {
                event.preventDefault();
            }
        });
    });
});
</script>

</body>
</html>