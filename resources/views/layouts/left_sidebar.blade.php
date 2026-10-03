@php
    $accessLevel = strtolower(Session::get('session_access_level', ''));
    $userName = Session::get('session_name');
    $isAdmin = $accessLevel === 'admin';

    $isActive = function ($patterns) {
        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) return true;
        }
        return false;
    };
@endphp

<style>
    :root {
        --lc-pink: #EC6FA5;
        --lc-pink-soft: #F9A8C8;
        --lc-blush: #FDE7F0;
        --lc-pale: #FFF5F9;
        --lc-pink-deep: #D64C86;
        --lc-text: #5A3B48;
        --lc-muted: #A88294;

        --sidebar-width: 250px;
        --header-height: 70px;
    }

    @media (max-width: 1400px) { :root { --sidebar-width: 220px; --header-height: 70px; } }
    @media (max-width: 1200px) { :root { --sidebar-width: 200px; --header-height: 66px; } }
    @media (max-width: 1024px) { :root { --sidebar-width: 180px; --header-height: 62px; } }
    @media (max-width: 768px)  { :root { --sidebar-width: 160px; --header-height: 58px; } }
    @media (max-width: 576px)  { :root { --sidebar-width: 150px; --header-height: 56px; } }

    /* ---------- SIDEBAR SHELL ---------- */
    .nk-sidebar {
        position: fixed;
        top: var(--header-height);
        left: 0;
        width: var(--sidebar-width);
        height: calc(100vh - var(--header-height));
        background: #FFFFFF !important;
        box-shadow: 2px 0 20px rgba(236, 111, 165, 0.08) !important;
        border-right: 1px solid rgba(236, 111, 165, 0.10) !important;
        font-family: 'Poppins', 'Segoe UI', sans-serif;
        z-index: 1040;
        overflow: hidden;
        transition: width 0.25s ease;
        display: flex;
        flex-direction: column;
        min-height: 0;
        box-sizing: border-box;
        transform: none !important;
        padding: 0 !important;      /* ⬅ remove any padding */
        margin: 0 !important;       /* ⬅ remove any margin */
    }

    .nk-sidebar * { box-sizing: border-box; }

    /* ---------- SCROLLABLE MENU AREA ----------
       Zero padding all around. The list itself will have no whitespace.
    */
    .nk-sidebar .nk-nav-scroll {
        padding: 0 !important;      /* ⬅ no padding at all */
        margin: 0 !important;       /* ⬅ no margin at all */
        background: #FFFFFF;
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(236, 111, 165, 0.22) #FFF5F9;
    }

    /* Push main content right so it isn't hidden behind the fixed sidebar */
    .nk-main {
        margin-left: var(--sidebar-width) !important;
        transition: margin-left 0.25s ease;
        min-height: 100vh;
    }

    /* ---------- MENU ---------- */
    .metismenu {
        list-style: none;
        margin: 0 !important;       /* ⬅ no default browser margin */
        padding: 0 !important;      /* ⬅ no default browser padding */
        background: #FFFFFF;
        width: 100%;
    }

    .metismenu > li {
        position: relative;
        background: transparent;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .metismenu .nav-label {
        padding: 18px 18px 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--lc-pink-deep);
        background: linear-gradient(90deg, rgba(253, 231, 240, 0.85), rgba(255, 245, 249, 0.4)) !important;
        border-bottom: 1px solid rgba(236, 111, 165, 0.12);
        margin-bottom: 0;           /* ⬅ no extra bottom margin */
        display: block;
    }

    .metismenu .nav-label i { color: var(--lc-pink); }

    .metismenu > li > a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        color: var(--lc-text);
        font-size: 13.5px;
        font-weight: 500;
        text-decoration: none;
        border-left: 4px solid transparent;
        background: #FFFFFF;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        position: relative;
        width: 100%;
    }

    .metismenu > li > a:hover {
        background: var(--lc-pale) !important;
        color: var(--lc-pink-deep);
        border-left-color: var(--lc-pink);
        text-decoration: none;
    }

    .metismenu > li > a:focus { text-decoration: none; }

    .metismenu .menu-icon {
        width: 20px;
        text-align: center;
        font-size: 15px;
        color: var(--lc-pink);
        transition: color 0.2s ease, transform 0.2s ease;
        flex-shrink: 0;
    }

    .metismenu > li > a:hover .menu-icon {
        color: var(--lc-pink-deep);
        transform: scale(1.08);
    }

    .metismenu .nav-text {
        flex-grow: 1;
        letter-spacing: 0.2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }

    .metismenu .has-arrow::after {
        content: "\f107";
        font-family: "FontAwesome";
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: var(--lc-muted);
        transition: transform 0.25s ease, color 0.25s ease;
    }

    .metismenu .has-arrow[aria-expanded="true"]::after {
        transform: translateY(-50%) rotate(180deg);
        color: var(--lc-pink-deep);
    }

    .metismenu > li > a.active,
    .metismenu > li.mm-active > a {
        background: linear-gradient(90deg, rgba(253, 231, 240, 0.95), rgba(255, 245, 249, 0.6)) !important;
        color: var(--lc-pink-deep);
        border-left-color: var(--lc-pink);
        font-weight: 600;
    }

    .metismenu > li.mm-active > a .menu-icon { color: var(--lc-pink-deep); }

    /* ---------- SUBMENU ----------
       Zero bottom margin and padding. Items sit flush.
    */
    .metismenu ul {
        list-style: none;
        margin: 0 !important;       /* ⬅ no margin at all */
        padding: 0 !important;      /* ⬅ no padding at all */
        background: rgba(255, 245, 249, 0.9) !important;
        border-left: 2px solid rgba(236, 111, 165, 0.15);
    }

    .metismenu ul li {
        position: relative;
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .metismenu ul li a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px 9px 20px;
        color: var(--lc-muted);
        font-size: 12.5px;
        font-weight: 500;
        text-decoration: none;
        background: transparent;
        border-left: 3px solid transparent;
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease, padding-left 0.18s ease;
    }

    .metismenu ul li a i {
        width: 16px;
        text-align: center;
        font-size: 12px;
        color: var(--lc-pink-soft);
        transition: color 0.18s ease;
        flex-shrink: 0;
    }

    .metismenu ul li a:hover {
        background: rgba(253, 231, 240, 0.85) !important;
        color: var(--lc-pink-deep);
        border-left-color: var(--lc-pink);
        padding-left: 24px;
        text-decoration: none;
    }

    .metismenu ul li a:hover i { color: var(--lc-pink-deep); }

    .metismenu ul li a.active {
        background: rgba(249, 168, 200, 0.30) !important;
        color: var(--lc-pink-deep);
        font-weight: 600;
        border-left-color: var(--lc-pink);
    }
    .metismenu ul li a.active i { color: var(--lc-pink-deep); }

    .metismenu ul li a::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--lc-pink-soft);
        opacity: 0;
        transition: opacity 0.18s ease;
    }

    .metismenu ul li a:hover::before,
    .metismenu ul li a.active::before { opacity: 1; }

    /* Kill submenu's default padding when it's the last child (no trailing gap) */
    .metismenu > li:last-child ul,
    .metismenu > li:last-child > ul > li:last-child {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    /* ---------- SCROLLBAR ---------- */
    .nk-nav-scroll::-webkit-scrollbar { width: 6px; }
    .nk-nav-scroll::-webkit-scrollbar-track { background: #FFF5F9; }
    .nk-nav-scroll::-webkit-scrollbar-thumb {
        background: rgba(236, 111, 165, 0.22);
        border-radius: 3px;
    }
    .nk-nav-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(236, 111, 165, 0.38);
    }

    /* ---------- FORCE LIGHT BACKGROUNDS ---------- */
    .nk-sidebar,
    .nk-sidebar .nk-nav-scroll,
    .nk-sidebar .metismenu,
    .nk-sidebar .metismenu > li,
    .nk-sidebar .metismenu > li > a { background-color: #FFFFFF; }

    .nk-sidebar .metismenu > li > a:hover,
    .nk-sidebar .metismenu > li > a.active,
    .nk-sidebar .metismenu > li.mm-active > a { background-color: #FFF5F9; }

    @media (max-width: 1024px) {
        .metismenu > li > a { padding: 11px 12px; font-size: 12.5px; }
        .metismenu ul li a { padding: 8px 12px 8px 18px; font-size: 11.5px; }
        .metismenu .menu-icon { font-size: 14px; width: 18px; }
        .metismenu .has-arrow::after { right: 10px; }
    }

    @media (max-width: 768px) {
        .metismenu > li > a { padding: 10px 10px; font-size: 12px; gap: 8px; }
        .metismenu ul li a { padding: 7px 10px 7px 16px; font-size: 11px; }
        .metismenu ul { margin-left: 0; }
    }
</style>

<div class="nk-sidebar" id="lc-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">

            {{-- Dashboard --}}
            <li class="{{ $isActive('index') ? 'mm-active' : '' }}">
                <a href="{{ route('index') }}"
                   class="{{ $isActive('index') ? 'active' : '' }}">
                    <i class="fa fa-tachometer menu-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Time In / Check Out --}}
            <li class="mega-menu mega-menu-sm {{ $isActive(['students.monitoring', 'personnel.monitoring']) ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fas fa-clock menu-icon"></i>
                    <span class="nav-text">Time In/Check Out</span>
                </a>

                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('students.monitoring') }}"
                           class="{{ $isActive('students.monitoring') ? 'active' : '' }}">
                            <i class="fa fa-graduation-cap"></i>
                            Student
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('personnel.monitoring') }}"
                           class="{{ $isActive('personnel.monitoring') ? 'active' : '' }}">
                            <i class="fa fa-id-badge"></i>
                            Personnel
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Resource Management --}}
            <li class="mega-menu mega-menu-sm {{ $isActive(['book', 'bookborrow', 'reserve.monitoring', 'overdue']) ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-book menu-icon"></i>
                    <span class="nav-text">Resource Management</span>
                </a>

                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('book') }}"
                           class="{{ $isActive('book') ? 'active' : '' }}">
                            <i class="fa fa-book"></i>
                            Books
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bookborrow') }}"
                           class="{{ $isActive('bookborrow') ? 'active' : '' }}">
                            <i class="fa fa-exchange"></i>
                            Borrowed/Returned
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reserve.monitoring') }}"
                           class="{{ $isActive('reserve.monitoring') ? 'active' : '' }}">
                            <i class="fa fa-calendar-check-o"></i>
                            Books Reserved
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('overdue') }}"
                           class="{{ $isActive('overdue') ? 'active' : '' }}">
                            <i class="fa fa-exclamation-triangle"></i>
                            Overdues
                        </a>
                    </li>
                </ul>
            </li>

            {{-- User Management --}}
            <li class="mega-menu mega-menu-sm {{ $isActive(['user.management', 'studentpersonnel.management']) ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa fa-users menu-icon"></i>
                    <span class="nav-text">User Management</span>
                </a>

                <ul aria-expanded="false">
                    @if($isAdmin)
                        <li>
                            <a href="{{ route('user.management') }}"
                               class="{{ $isActive('user.management') ? 'active' : '' }}">
                                <i class="fa fa-user-secret"></i>
                                Staff
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('studentpersonnel.management', ['tab' => 'students']) }}"
                           class="{{ $isActive('studentpersonnel.management') && request('tab', 'students') === 'students' ? 'active' : '' }}">
                            <i class="fa fa-graduation-cap"></i>
                            Student
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('studentpersonnel.management', ['tab' => 'personnel']) }}"
                           class="{{ $isActive('studentpersonnel.management') && request('tab') === 'personnel' ? 'active' : '' }}">
                            <i class="fa fa-id-badge"></i>
                            Personnel
                        </a>
                    </li>
                </ul>
            </li>

            @if($isAdmin)
                {{-- System Settings --}}
                <li class="mega-menu mega-menu-sm {{ $isActive(['policy.index', 'settings.index']) ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-cogs menu-icon"></i>
                        <span class="nav-text">System Settings</span>
                    </a>

                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('policy.index') }}"
                               class="{{ $isActive('policy.index') ? 'active' : '' }}">
                                <i class="fa fa-file-text-o"></i>
                                Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}"
                               class="{{ $isActive('settings.index') ? 'active' : '' }}">
                                <i class="fa fa-cog"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Reports and Analytics --}}
                <li class="mega-menu mega-menu-sm {{ $isActive(['reports.index', 'audit.log']) ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-line-chart menu-icon"></i>
                        <span class="nav-text">Reports and Analytics</span>
                    </a>

                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('reports.index') }}"
                               class="{{ $isActive('reports.index') ? 'active' : '' }}">
                                <i class="fa fa-bar-chart"></i>
                                Reports
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('audit.log') }}"
                               class="{{ $isActive('audit.log') ? 'active' : '' }}">
                                <i class="fa fa-history"></i>
                                Audit Log
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Backup and Restore --}}
                <li class="mega-menu mega-menu-sm {{ $isActive(['database.backup.*', 'database.restore.*']) ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa fa-database menu-icon"></i>
                        <span class="nav-text">Backup and Restore</span>
                    </a>

                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('database.backup.page') }}"
                               class="{{ $isActive('database.backup.*') ? 'active' : '' }}">
                                <i class="fa fa-cloud-download"></i>
                                Database Backup
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('database.restore.page') }}"
                               class="{{ $isActive('database.restore.*') ? 'active' : '' }}">
                                <i class="fa fa-cloud-upload"></i>
                                Restore Database
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</div>