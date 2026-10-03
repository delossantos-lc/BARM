<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance and Resources Processing with RFID System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ===== RESET & BASE ===== */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            background: #1a0b1a;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ===== MAIN CONTAINER ===== */
        .login-page {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(16px, 4vw, 48px) clamp(16px, 4vw, 60px);
            background:
                radial-gradient(circle at 15% 30%, rgba(255, 235, 245, 0.4), transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(235, 190, 220, 0.35), transparent 50%),
                linear-gradient(145deg, #f5e9f0 0%, #ecc8de 40%, #d99cb8 100%);
        }

        .login-page::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(255, 215, 235, 0.06);
            pointer-events: none;
        }

        .page-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1560px;
        }

        /* ===== LEFT SECTION (MOTTO) ===== */
        .left-section {
            min-height: min(650px, 70vh);
            display: flex;
            align-items: flex-end;
            padding: clamp(20px, 3vw, 40px);
        }

        .school-message {
            color: #ffffff;
            text-shadow: 0 6px 28px rgba(100, 30, 70, 0.45);
        }

        .school-message h1 {
            margin: 0;
            font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
            font-size: clamp(3rem, 6.5vw, 7rem);
            font-style: italic;
            font-weight: 700;
            line-height: 0.95;
            letter-spacing: -3px;
        }

        .message-line {
            display: block;
        }

        .established {
            margin-top: clamp(16px, 2.5vw, 32px);
            margin-left: clamp(30px, 6vw, 100px);
            font-size: clamp(0.7rem, 1vw, 1rem);
            font-weight: 600;
            letter-spacing: clamp(6px, 1.2vw, 13px);
            text-transform: lowercase;
            opacity: 0.95;
        }

        /* ===== RIGHT CARD SECTION ===== */
        .login-card-section {
            min-height: min(650px, 70vh);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: clamp(340px, 32vw, 460px);
            padding: clamp(24px, 3vw, 44px) clamp(20px, 2.8vw, 40px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: clamp(20px, 2.5vw, 32px);
            background: rgba(255, 255, 255, 0.78);
            box-shadow:
                0 30px 60px rgba(70, 20, 50, 0.25),
                0 10px 30px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            transition: transform 0.25s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 35px 70px rgba(70, 20, 50, 0.3);
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-bottom: clamp(4px, 1vw, 10px);
        }

        .school-logo {
            display: block;
            width: clamp(72px, 8vw, 100px);
            height: clamp(72px, 8vw, 100px);
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(180, 60, 120, 0.2));
        }

        .login-title {
            margin-bottom: clamp(18px, 2.5vw, 30px);
            color: #2a1a2a;
            font-size: clamp(1.05rem, 1.6vw, 1.45rem);
            font-weight: 600;
            letter-spacing: 0.5px;
            text-align: center;
            line-height: 1.45;
            padding: 0 4px;
        }

        /* ===== FORM ===== */
        .form-group-custom {
            position: relative;
            margin-bottom: clamp(14px, 1.8vw, 20px);
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: clamp(14px, 1.5vw, 18px);
            z-index: 2;
            color: #a06a86;
            font-size: clamp(0.95rem, 1.2vw, 1.1rem);
            transform: translateY(-50%);
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control.login-input {
            height: clamp(46px, 5vw, 56px);
            padding: 10px clamp(42px, 4vw, 48px);
            border: 1.5px solid rgba(200, 170, 190, 0.6);
            border-radius: clamp(10px, 1.2vw, 14px);
            background: rgba(255, 255, 255, 0.92);
            color: #2a1a2a;
            font-size: clamp(0.85rem, 1vw, 0.95rem);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .form-control.login-input:focus {
            border-color: #c74b8a;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(199, 75, 138, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.02);
            outline: none;
        }

        .form-control.login-input::placeholder {
            color: #b294a8;
            font-weight: 400;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: 48px;
            background-image: none;
        }

        .password-input {
            padding-right: clamp(42px, 4vw, 52px) !important;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: clamp(12px, 1.4vw, 18px);
            z-index: 3;
            border: 0;
            padding: 6px;
            color: #a06a86;
            background: transparent;
            font-size: clamp(0.95rem, 1.1vw, 1.15rem);
            transform: translateY(-50%);
            cursor: pointer;
            border-radius: 50%;
            transition: color 0.2s, background 0.2s;
        }

        .password-toggle:hover {
            color: #b12f6b;
            background: rgba(199, 75, 138, 0.08);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 clamp(18px, 2.2vw, 28px);
            font-size: clamp(0.8rem, 0.95vw, 0.9rem);
        }

        .form-check-input {
            border-color: #c9a3b8;
            width: 1.1em;
            height: 1.1em;
            margin-top: 0.15em;
        }

        .form-check-input:checked {
            border-color: #b12f6b;
            background-color: #b12f6b;
        }

        .form-check-input:focus {
            border-color: #b12f6b;
            box-shadow: 0 0 0 3px rgba(177, 47, 107, 0.2);
        }

        .form-check-label {
            color: #4a3040;
            font-weight: 500;
            padding-left: 6px;
        }

        /* ===== BUTTON ===== */
        .login-button {
            width: 100%;
            height: clamp(46px, 5vw, 56px);
            border: none;
            border-radius: clamp(10px, 1.3vw, 16px);
            background: linear-gradient(135deg, #3a9e4a 0%, #2a7e38 100%);
            color: #ffffff;
            font-size: clamp(0.85rem, 1vw, 1rem);
            font-weight: 600;
            letter-spacing: 0.8px;
            box-shadow: 0 8px 20px rgba(46, 135, 55, 0.3);
            transition: background 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-button i {
            font-size: clamp(0.95rem, 1.1vw, 1.1rem);
        }

        .login-button:hover {
            background: linear-gradient(135deg, #2f8a3e 0%, #1f6a2c 100%);
            box-shadow: 0 12px 26px rgba(46, 135, 55, 0.4);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .login-button:active {
            transform: translateY(1px);
            box-shadow: 0 6px 14px rgba(46, 135, 55, 0.3);
        }

        /* ===== ALERTS ===== */
        .alert {
            font-size: clamp(0.78rem, 0.95vw, 0.88rem);
            text-align: left;
            border-radius: 12px;
            border: none;
            padding: clamp(10px, 1.2vw, 14px) clamp(14px, 1.6vw, 18px);
            margin-bottom: clamp(14px, 1.8vw, 20px);
        }

        .alert-danger {
            background: #fce4ec;
            color: #8a1e4a;
        }

        .alert-success {
            background: #e8f5e9;
            color: #1b5e20;
        }

        .invalid-feedback {
            margin-top: 6px;
            font-size: clamp(0.72rem, 0.85vw, 0.8rem);
            text-align: left;
            color: #b02a37;
        }

        /* ===== RESPONSIVE BREAKPOINTS ===== */

        /* Large tablets & small desktops */
        @media (max-width: 1199.98px) {
            .login-card {
                max-width: clamp(320px, 38vw, 420px);
            }

            .left-section {
                min-height: min(550px, 65vh);
            }
        }

        /* Tablets */
        @media (max-width: 991.98px) {
            .login-page {
                padding: clamp(20px, 4vw, 40px) clamp(16px, 3vw, 32px);
            }

            .left-section {
                min-height: auto;
                justify-content: center;
                padding: clamp(16px, 2vw, 24px) 10px clamp(28px, 4vw, 48px);
                text-align: center;
            }

            .school-message h1 {
                font-size: clamp(2.8rem, 11vw, 5.5rem);
                letter-spacing: -2px;
            }

            .established {
                margin-left: 0;
                letter-spacing: clamp(5px, 1.5vw, 8px);
            }

            .login-card-section {
                min-height: auto;
            }

            .login-card {
                max-width: clamp(360px, 60vw, 480px);
            }
        }

        /* Small tablets & large phones */
        @media (max-width: 767.98px) {
            .school-message h1 {
                font-size: clamp(2.5rem, 13vw, 4.5rem);
                letter-spacing: -1.5px;
            }

            .login-card {
                max-width: 100%;
                margin: 0 auto;
            }

            .login-card-section {
                padding: 0 10px;
            }
        }

        /* Phones */
        @media (max-width: 575.98px) {
            .login-page {
                padding: clamp(14px, 3vw, 24px) clamp(12px, 3vw, 18px);
            }

            .left-section {
                padding-bottom: clamp(20px, 4vw, 36px);
            }

            .school-message h1 {
                font-size: clamp(2.2rem, 15vw, 3.8rem);
                letter-spacing: -1px;
            }

            .established {
                font-size: clamp(0.65rem, 2.5vw, 0.8rem);
                letter-spacing: clamp(3px, 1.5vw, 5px);
            }

            .login-card {
                padding: clamp(22px, 6vw, 34px) clamp(16px, 5vw, 24px);
                border-radius: clamp(16px, 4vw, 24px);
            }

            .school-logo {
                width: clamp(64px, 20vw, 88px);
                height: clamp(64px, 20vw, 88px);
            }

            .login-title {
                font-size: clamp(0.95rem, 4vw, 1.15rem);
                margin-bottom: clamp(14px, 4vw, 22px);
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: clamp(8px, 2vw, 12px);
            }

            .login-button {
                height: clamp(44px, 12vw, 52px);
                font-size: clamp(0.85rem, 3.5vw, 0.95rem);
            }

            .form-control.login-input {
                height: clamp(44px, 11vw, 52px);
                font-size: clamp(0.82rem, 3.5vw, 0.9rem);
            }

            .input-icon {
                font-size: clamp(0.9rem, 3.5vw, 1rem);
            }

            .password-toggle {
                font-size: clamp(0.9rem, 3.5vw, 1rem);
            }
        }

        /* Very small phones */
        @media (max-width: 359.98px) {
            .school-message h1 {
                font-size: clamp(1.9rem, 14vw, 2.8rem);
            }

            .login-card {
                padding: 18px 14px;
                border-radius: 16px;
            }

            .login-title {
                font-size: 0.9rem;
            }
        }

        /* Landscape phones & short screens */
        @media (max-height: 600px) and (orientation: landscape) {
            .login-page {
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .left-section {
                min-height: auto;
                padding: 10px 20px;
                align-items: center;
            }

            .school-message h1 {
                font-size: clamp(2rem, 5vw, 3rem);
            }

            .established {
                margin-top: 8px;
                margin-left: 20px;
            }

            .login-card-section {
                min-height: auto;
            }

            .login-card {
                padding: 20px 24px;
            }
        }
    </style>
</head>

<body>

<section class="login-page">
    <div class="page-content">
        <div class="row align-items-center g-0">

            <!-- Left side: motto -->
            <div class="col-lg-7">
                <div class="left-section">
                    <div class="school-message">
                        <h1>
                            <span class="message-line">Faith.</span>
                            <span class="message-line">Excellence.</span>
                            <span class="message-line">Service.</span>
                        </h1>
                        <div class="established">est. 1928</div>
                    </div>
                </div>
            </div>

            <!-- Right side: login card -->
            <div class="col-lg-5">
                <div class="login-card-section">
                    <div class="login-card">

                        <div class="logo-container">
                            <img src="{{ asset('Image/LC_LOGO.png') }}" alt="Lourdes College Logo" class="school-logo">
                        </div>

                        <h2 class="login-title">
                            Attendance and Resources Processing with Integration of RFID System
                        </h2>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('login.authenticate') }}" method="POST">
                            @csrf

                            <div class="form-group-custom">
                                <i class="bi bi-person-circle input-icon"></i>
                                <input type="text" name="employeeid" id="employeeid"
                                    class="form-control login-input @error('employeeid') is-invalid @enderror"
                                    placeholder="Employee ID Number" value="{{ old('employeeid') }}"
                                    autocomplete="username" required autofocus>

                                @error('employeeid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group-custom">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" name="password" id="password"
                                    class="form-control login-input password-input @error('password') is-invalid @enderror"
                                    placeholder="Password" autocomplete="current-password" required>

                                <button type="button" class="password-toggle" id="togglePassword"
                                    aria-label="Show or hide password">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                        id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                            </div>

                            <button type="submit" class="btn login-button">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Log In
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (togglePassword && passwordInput && passwordIcon) {
            togglePassword.addEventListener('click', function () {
                const passwordIsHidden =
                    passwordInput.getAttribute('type') === 'password';

                passwordInput.setAttribute(
                    'type',
                    passwordIsHidden ? 'text' : 'password'
                );

                passwordIcon.classList.toggle(
                    'bi-eye',
                    !passwordIsHidden
                );

                passwordIcon.classList.toggle(
                    'bi-eye-slash',
                    passwordIsHidden
                );
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>