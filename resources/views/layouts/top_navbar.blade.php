@php
    $systemSettings = $systemSettings ?? \App\Models\SystemSetting::current();

    $lourdesLogo = asset('assets/images/lourdes-logo.png');
    $systemLogo = $systemSettings->logoUrl()
        ?? asset('assets/images/favicon.png');

    // Pull the logged-in user info from the session (consistent with the rest of the app)
    $sessionName = Session::get('session_name', 'User');
    $sessionAccessLevel = Session::get('session_access_level', 'Staff');
    $sessionEmployeeId = Session::get('session_employeeid') ?? Session::get('session_employee_id') ?? '-';
    $sessionEmail = Session::get('session_email') ?? '-';
    $sessionStatus = Session::get('session_status', 'active');

    // Try to split the full name into first / last for the dropdown
    $nameParts = preg_split('/\s+/', trim((string) $sessionName), 2);
    $firstName = $nameParts[0] ?? $sessionName;
    $lastName  = $nameParts[1] ?? '';
@endphp

<style>
    :root {
        --lc-pink: #EC6FA5;
        --lc-pink-deep: #D64C86;
        --lc-pale: #FFF5F9;
        --lc-text: #4A2B38;
        --lc-muted: #9B7285;

        --sidebar-width: 250px;
        --lc-header-height: 70px;
    }

    @media (max-width: 1200px) { :root { --sidebar-width: 200px; --lc-header-height: 66px; } }
    @media (max-width: 1024px) { :root { --sidebar-width: 180px; --lc-header-height: 62px; } }
    @media (max-width: 768px)  { :root { --sidebar-width: 160px; --lc-header-height: 58px; } }
    @media (max-width: 576px)  { :root { --sidebar-width: 150px; --lc-header-height: 56px; } }

    /* ============================================================
       FIX — Push page content below the fixed header
       so it never covers page layouts.
       ============================================================ */
    html body {
        padding-top: var(--lc-header-height) !important;
        margin-top: 0 !important;
    }

    /* Anchor links respect the header height */
    html {
        scroll-padding-top: var(--lc-header-height);
    }

    /* Prevent content wrappers from double-padding */
    .content-body,
    .page-wrapper,
    .nk-main {
        margin-top: 0;
    }

    /* ---------- FIXED HEADER — no outer spacing ---------- */
    .lc-sticky-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: var(--lc-header-height);
        z-index: 1035;
        background: #FFFFFF;
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 !important;
    }

    .lc-header-row {
        display: flex;
        flex-direction: row;
        align-items: stretch;
        width: 100%;
        height: var(--lc-header-height);
        box-sizing: border-box;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ============================================================
       BRAND BLOCK — logo flush at x=0
       ============================================================ */
    .lc-header-brand {
        flex: 0 0 var(--sidebar-width);
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        max-width: var(--sidebar-width);
        height: 100%;

        display: flex;
        flex-direction: row;
        align-items: center;

        padding: 0 8px 0 0;
        margin: 0 !important;
        gap: 6px;
        box-sizing: border-box;

        background: #FFFFFF;
        border: none !important;
        box-shadow: none !important;
        overflow: hidden;
    }

    .lc-header-brand .lourdes-logo {
        height: 40px;
        width: auto;
        max-width: 44px;
        object-fit: contain;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        flex: 0 0 auto;
        display: block;
        align-self: center;
    }

    .lc-header-brand .logo-divider {
        display: none !important;
    }

    .lc-header-brand .logo-abbr {
        height: 34px;
        width: auto;
        max-width: 40px;
        object-fit: contain;
        flex: 0 0 auto;
        display: block;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
    }

    .lc-header-brand .brand-text {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.15;
        min-width: 0;
        flex: 1 1 auto;
        overflow: hidden;
        padding: 0;
        margin: 0;
    }

    .lc-header-brand .brand-title {
        color: var(--lc-pink-deep);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
        padding: 0;
        line-height: 1.1;
    }

    .lc-header-brand .brand-subtitle {
        color: var(--lc-muted);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.1px;
        margin: 1px 0 0 0;
        padding: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.1;
    }

    /* ============================================================
       PINK BAR
       ============================================================ */
    .lc-header-pink {
        flex: 1 1 auto;
        min-width: 0;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0 20px;
        box-sizing: border-box;
        margin: 0;

        background: linear-gradient(135deg, var(--lc-pink) 0%, var(--lc-pink-deep) 100%);
        border-left: none !important;
        box-shadow: 0 2px 12px rgba(236, 111, 165, 0.15);
    }

    /* ---------- PROFILE ---------- */
    .header-profile .nav-link {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 12px;
        text-decoration: none;
    }

    .header-profile .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .header-profile .profile-icon-circle {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.45);
    }

    .header-profile .profile-icon-circle i {
        color: var(--lc-pink-deep);
        font-size: 20px;
    }

    .header-profile .profile-name {
        display: block;
        color: #FFFFFF !important;
        font-weight: 600;
        font-size: 14px;
        line-height: 1.2;
    }

    .header-profile .profile-role {
        display: block;
        margin-top: 2px;
        color: rgba(255, 255, 255, 0.85) !important;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .header-profile .fa-angle-down {
        color: #FFFFFF;
        font-size: 14px;
    }

    /* ---------- DROPDOWN ---------- */
    .header-profile .dropdown-menu {
        width: 300px;
        padding: 0;
        overflow: hidden;
        background: #FFFFFF !important;
        border: 1px solid rgba(236, 111, 165, 0.25);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(214, 76, 134, 0.30);
        margin-top: 14px;
        z-index: 1060;
        right: 0;
        left: auto;
    }

    .header-profile .dropdown-user-info {
        padding: 20px;
        background: linear-gradient(135deg, #FDE7F0 0%, #F9C6DC 100%) !important;
        border-bottom: 1px solid rgba(236, 111, 165, 0.20);
    }

    .header-profile .dropdown-avatar {
        width: 55px;
        height: 55px;
        min-width: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF !important;
        border-radius: 50%;
        border: 2px solid rgba(236, 111, 165, 0.30);
    }

    .header-profile .dropdown-avatar i {
        color: var(--lc-pink-deep);
        font-size: 26px;
    }

    .header-profile .dropdown-fullname {
        display: block;
        color: #6B2E48;
        font-size: 15px;
        font-weight: 700;
    }

    .header-profile .dropdown-meta,
    .header-profile .dropdown-email {
        display: block;
        color: #9B5A78;
        font-size: 12px;
        margin-top: 2px;
    }

    .header-profile .dropdown-email {
        max-width: 175px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .header-profile .dropdown-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 14px;
    }

    .header-profile .badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .header-profile .badge-admin,
    .header-profile .badge-active {
        background: #FFFFFF !important;
        color: var(--lc-pink-deep) !important;
        border: 1px solid rgba(236, 111, 165, 0.40);
    }

    .header-profile .badge-staff {
        background: #FFF5F9 !important;
        color: var(--lc-pink-deep) !important;
        border: 1px solid rgba(249, 168, 200, 0.60);
    }

    .header-profile .badge-inactive {
        background: #EFE3E9 !important;
        color: #7A5666 !important;
        border: 1px solid rgba(155, 114, 133, 0.40);
    }

    .header-profile .dropdown-item {
        padding: 14px 20px;
        color: var(--lc-text);
        font-size: 14px;
        display: flex;
        align-items: center;
        background-color: #FFFFFF;
        text-decoration: none;
    }

    .header-profile .dropdown-item:hover {
        background: var(--lc-pale);
        color: var(--lc-pink-deep);
    }

    .header-profile .dropdown-item i {
        width: 18px;
        text-align: center;
        color: var(--lc-pink);
    }

    .header-profile .dropdown-item.text-danger {
        color: var(--lc-pink-deep) !important;
    }

    .header-profile .dropdown-divider {
        margin: 0;
        border-top: 1px solid rgba(236, 111, 165, 0.10);
    }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 1200px) {
        .lc-header-brand { gap: 5px; padding-right: 6px; }
        .lc-header-brand .lourdes-logo { height: 34px; max-width: 36px; }
        .lc-header-brand .logo-abbr   { height: 28px; max-width: 32px; }
        .lc-header-brand .brand-title { font-size: 14px; }
        .lc-header-brand .brand-subtitle { font-size: 9px; }
    }

    @media (max-width: 991px) {
        .lc-header-brand { gap: 4px; padding-right: 5px; }
        .lc-header-brand .lourdes-logo { height: 30px; max-width: 32px; }
        .lc-header-brand .logo-abbr   { height: 24px; max-width: 28px; }
        .lc-header-brand .brand-title { font-size: 13px; }
        .lc-header-brand .brand-subtitle { font-size: 8.5px; }
    }

    @media (max-width: 768px) {
        .lc-header-brand .brand-text { display: none; }
        .lc-header-brand { justify-content: flex-start; padding: 0 4px 0 0; gap: 4px; }
    }
</style>

<div class="lc-sticky-header">
    <div class="lc-header-row">

        <div class="lc-header-brand">
            <img
                class="lourdes-logo"
                src="{{ $lourdesLogo }}"
                alt="Lourdes College Logo"
                onerror="this.style.visibility='hidden';"
            >
            <span class="logo-divider"></span>
            <img
                class="logo-abbr"
                data-system-logo
                src="{{ $systemLogo }}"
                alt="{{ $systemSettings->system_short_name ?? 'BARM' }} Logo"
                onerror="this.style.visibility='hidden';"
            >
            <div class="brand-text">
                <span class="brand-title" data-system-short-name>
                    {{ $systemSettings->system_short_name ?? 'BARM' }}
                </span>
                <span class="brand-subtitle">
                    Learning Commons
                </span>
            </div>
        </div>

        <div class="lc-header-pink">
            <ul class="navbar-nav header-right" style="list-style:none; margin:0; padding:0;">
                @if(Session::has('session_name'))
                    <li class="nav-item dropdown header-profile">
                        <a
                            class="nav-link"
                            href="javascript:void(0);"
                            role="button"
                            data-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="profile-icon-circle">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="d-none d-md-block" style="text-align: left; line-height: 1.2;">
                                    <span class="profile-name">
                                        {{ $firstName }}
                                    </span>
                                    <small class="profile-role">
                                        {{ $sessionAccessLevel }}
                                    </small>
                                </div>
                                <i class="fa fa-angle-down d-none d-md-inline"></i>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-user-info">
                                <div style="display: flex; align-items: center;">
                                    <div class="dropdown-avatar">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div style="margin-left: 12px; overflow: hidden;">
                                        <strong class="dropdown-fullname">
                                            {{ $firstName }} {{ $lastName }}
                                        </strong>
                                        <small class="dropdown-meta">
                                            Employee ID: {{ $sessionEmployeeId }}
                                        </small>
                                        <small class="dropdown-email" title="{{ $sessionEmail }}">
                                            {{ $sessionEmail }}
                                        </small>
                                    </div>
                                </div>
                                <div class="dropdown-badges">
                                    @if(strtolower($sessionAccessLevel) === 'admin')
                                        <span class="badge badge-admin">
                                            <i class="fa fa-shield mr-1"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge badge-staff">
                                            <i class="fa fa-user mr-1"></i> Staff
                                        </span>
                                    @endif
                                    @if(strtolower($sessionStatus) === 'active')
                                        <span class="badge badge-active">
                                            <i class="fa fa-check-circle mr-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-inactive">
                                            <i class="fa fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button
                                    type="submit"
                                    class="dropdown-item text-danger"
                                    style="width: 100%; border: none; background: #ffffff; text-align: left; cursor: pointer;"
                                >
                                    <i class="fa fa-sign-out mr-3"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endif
            </ul>
        </div>

    </div>
</div>