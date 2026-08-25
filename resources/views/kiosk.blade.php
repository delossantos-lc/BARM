<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!-- =====================================================
         CSRF TOKEN
    ====================================================== -->

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >


    <title>
        Book Borrow / Return
    </title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        /* =====================================================
           BODY
        ====================================================== */

        body {

            min-height: 100vh;

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

        }


        /* =====================================================
           TITLE
        ====================================================== */

        .title {

            font-size: 3.8rem;

            font-weight: 500;

            letter-spacing: 5px;

            line-height: 1.4;

            color: #252525;

            margin-bottom: 60px;

            text-transform: uppercase;

        }


        /* =====================================================
           LOGO
        ====================================================== */

        .kiosk-logo {

            width: 220px;

            height: auto;

            display: block;

            margin:
                0 auto
                30px auto;

        }


        /* =====================================================
           KIOSK BUTTON
        ====================================================== */

        .kiosk-box {

            width: 100%;

            height: 280px;

            border: 2px solid
                rgba(255, 255, 255, 0.65);

            border-radius: 25px;

            background:
                linear-gradient(
                    135deg,
                    #F3A8BE,
                    #E98EAC
                );

            color: #ffffff;

            text-decoration: none;

            font-family:
                'Montserrat',
                Arial,
                Helvetica,
                sans-serif;

            font-size: 3rem;

            font-weight: 600;

            letter-spacing: 8px;

            text-transform: uppercase;

            cursor: pointer;

            box-shadow:
                0 18px 35px
                rgba(128, 62, 84, 0.18);

            transition:
                background 0.25s ease,
                transform 0.25s ease,
                box-shadow 0.25s ease;

        }


        /* =====================================================
           BUTTON INNER CONTENT
        ====================================================== */

        .kiosk-button-content {

            display: flex;

            flex-direction: column;

            justify-content: center;

            align-items: center;

            gap: 25px;

        }


        /* =====================================================
           BUTTON ICON
        ====================================================== */

        .kiosk-icon {

            width: 85px;

            height: 85px;

            display: flex;

            justify-content: center;

            align-items: center;

            border:

                3px solid
                rgba(255, 255, 255, 0.9);

            border-radius: 50%;

            font-size: 2.5rem;

            color: #ffffff;

        }


        /* =====================================================
           BUTTON TEXT
        ====================================================== */

        .kiosk-button-text {

            display: block;

            font-size: 3rem;

            font-weight: 600;

            letter-spacing: 8px;

            line-height: 1;

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

            transform:
                translateY(-7px);

            box-shadow:
                0 25px 45px
                rgba(128, 62, 84, 0.25);

        }


        /* =====================================================
           BUTTON ACTIVE
        ====================================================== */

        .kiosk-box:active {

            transform:
                scale(0.97);

            box-shadow:
                0 10px 20px
                rgba(128, 62, 84, 0.20);

        }


        /* =====================================================
           BUTTON FOCUS
        ====================================================== */

        .kiosk-box:focus {

            outline:
                5px solid
                rgba(222, 105, 145, 0.25);

            outline-offset: 6px;

        }


        /* =====================================================
           INVISIBLE RFID SCANNER
        ====================================================== */

        #rfidScanner {

            position: fixed;

            left: -9999px;

            top: -9999px;

            width: 1px;

            height: 1px;

            opacity: 0;

            border: 0;

            padding: 0;

            margin: 0;

            outline: none;

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media(max-width: 992px) {

            .title {

                font-size: 2.7rem;

                letter-spacing: 3px;

                margin-bottom: 40px;

            }


            .kiosk-logo {

                width: 150px;

            }


            .kiosk-box {

                height: 210px;

                border-radius: 20px;

            }


            .kiosk-icon {

                width: 65px;

                height: 65px;

                font-size: 2rem;

            }


            .kiosk-button-text {

                font-size: 2.2rem;

                letter-spacing: 5px;

            }

        }


        /* =====================================================
           SMALL MOBILE
        ====================================================== */

        @media(max-width: 576px) {

            .title {

                font-size: 2rem;

                letter-spacing: 2px;

            }


            .kiosk-logo {

                width: 120px;

            }


            .kiosk-box {

                height: 170px;

            }


            .kiosk-button-content {

                gap: 15px;

            }


            .kiosk-icon {

                width: 55px;

                height: 55px;

                font-size: 1.6rem;

            }


            .kiosk-button-text {

                font-size: 1.7rem;

                letter-spacing: 4px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     INVISIBLE RFID SCANNER INPUT
====================================================== -->

<input
    type="text"
    id="rfidScanner"
    autocomplete="off"
    autofocus
>


<div
    class="
        container-fluid
        min-vh-100
        d-flex
        justify-content-center
        align-items-center
    "
>


    <div class="container-xxl text-center">


        <!-- =====================================================
             LOGO
        ====================================================== -->

        <img
            src="{{ asset('images/lclogo.png') }}"
            class="kiosk-logo"
            alt="Library Logo"
        >


        <!-- =====================================================
             TITLE
        ====================================================== -->

        <h1 class="title">

            LOURDES COLLEGE

            <br>

            BOOK BORROW / RETURN

        </h1>


        <!-- =====================================================
             BUTTONS
        ====================================================== -->

        <div
            class="
                row
                justify-content-center
                gx-5
                gy-4
            "
        >


            <!-- =================================================
                 BORROW
            ================================================== -->

            <div class="col-lg-6">


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


                            <!-- Book Icon -->

                            <div class="kiosk-icon">

                                &#128214;

                            </div>


                            <!-- Text -->

                            <span class="kiosk-button-text">

                                BORROW

                            </span>


                        </div>

                    </button>


                </form>


            </div>


            <!-- =================================================
                 RETURN
            ================================================== -->

            <div class="col-lg-6">


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


                            <!-- Return Icon -->

                            <div class="kiosk-icon">

                                &#8634;

                            </div>


                            <!-- Text -->

                            <span class="kiosk-button-text">

                                RETURN

                            </span>


                        </div>

                    </button>


                </form>


            </div>


        </div>


    </div>


</div>


<!-- =====================================================
     BOOTSTRAP
====================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
></script>


<!-- =====================================================
     RFID SCANNER
====================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const scanner =
        document.getElementById('rfidScanner');


    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');


    let scanTimer = null;

    let processing = false;


    /*
    |--------------------------------------------------------------------------
    | Focus Scanner
    |--------------------------------------------------------------------------
    */

    function focusScanner() {

        if (!processing) {

            scanner.focus();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Focus
    |--------------------------------------------------------------------------
    */

    setTimeout(function () {

        focusScanner();

    }, 300);


    /*
    |--------------------------------------------------------------------------
    | Refocus When Browser Window Becomes Active
    |--------------------------------------------------------------------------
    */

    window.addEventListener('focus', function () {

        setTimeout(function () {

            focusScanner();

        }, 200);

    });


    /*
    |--------------------------------------------------------------------------
    | Refocus After Clicking Empty Area
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        if (
            event.target.tagName !== 'BUTTON' &&
            event.target.tagName !== 'A'
        ) {

            setTimeout(function () {

                focusScanner();

            }, 200);

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Detect RFID Typing
    |--------------------------------------------------------------------------
    */

    scanner.addEventListener('input', function () {

        clearTimeout(scanTimer);


        /*
         * Wait briefly until scanner
         * finishes typing the RFID.
         */

        scanTimer = setTimeout(function () {

            processRFID();

        }, 200);

    });


    /*
    |--------------------------------------------------------------------------
    | RFID Reader ENTER Key
    |--------------------------------------------------------------------------
    */

    scanner.addEventListener('keydown', function (event) {

        if (event.key === 'Enter') {

            event.preventDefault();

            clearTimeout(scanTimer);

            processRFID();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Process RFID
    |--------------------------------------------------------------------------
    */

    async function processRFID() {


        /*
         * Prevent double scan/request
         */

        if (processing) {

            return;

        }


        const rfid =
            scanner.value.trim();


        /*
         * Clear scanner immediately
         */

        scanner.value = '';


        /*
         * Ignore empty values
         */

        if (!rfid) {

            focusScanner();

            return;

        }


        processing = true;


        console.log(
            'RFID SCANNED:',
            rfid
        );


        try {


            /*
            |--------------------------------------------------------------------------
            | Send RFID To Laravel
            |--------------------------------------------------------------------------
            */

            const response = await fetch(

                '{{ route("attendance.scan") }}',

                {

                    method: 'POST',

                    credentials: 'same-origin',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            csrfToken,

                        'X-Requested-With':
                            'XMLHttpRequest'

                    },

                    body: JSON.stringify({

                        rfid_tag_uid: rfid

                    })

                }

            );


            /*
            |--------------------------------------------------------------------------
            | Read Laravel Response
            |--------------------------------------------------------------------------
            */

            let data;


            try {

                data =
                    await response.json();

            }

            catch (jsonError) {

                console.error(
                    'Invalid JSON:',
                    jsonError
                );


                throw new Error(
                    'Laravel returned an invalid response.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Laravel Error
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(

                    data.message ??
                    'Unable to record attendance.'

                );

            }


            console.log(
                'ATTENDANCE RESPONSE:',
                data
            );


            /*
            |--------------------------------------------------------------------------
            | CLOCK IN
            |--------------------------------------------------------------------------
            */

            if (data.action === 'clock_in') {

                alert(

                    data.name +

                    '\n\nTIME IN SUCCESSFUL' +

                    '\n\nRFID: ' +

                    data.rfid_tag_uid

                );

            }


            /*
            |--------------------------------------------------------------------------
            | CLOCK OUT
            |--------------------------------------------------------------------------
            */

            else if (data.action === 'clock_out') {

                alert(

                    data.name +

                    '\n\nTIME OUT SUCCESSFUL' +

                    '\n\nRFID: ' +

                    data.rfid_tag_uid

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Other Successful Response
            |--------------------------------------------------------------------------
            */

            else {

                alert(

                    data.message ??
                    'Attendance recorded successfully.'

                );

            }

        }


        catch (error) {


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            console.error(
                'RFID ERROR:',
                error
            );


            alert(
                error.message
            );

        }


        finally {


            /*
            |--------------------------------------------------------------------------
            | Reset Scanner
            |--------------------------------------------------------------------------
            */

            processing = false;

            scanner.value = '';


            setTimeout(function () {

                focusScanner();

            }, 300);

        }

    }

});

</script>


</body>

</html>