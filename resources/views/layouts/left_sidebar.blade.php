<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">

            {{-- LOGGED-IN USER --}}
            <li class="nav-label">
                {{ ucfirst(Session::get('session_access_level')) }}
                -
                {{ Session::get('session_name') }}
            </li>

            {{-- DASHBOARD --}}
            <li>
                <a href="{{ route('index') }}" aria-expanded="false">
                    <i class="fa fa-tachometer menu-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-label">Apps</li>

            {{-- TIME IN / CHECK OUT --}}
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow"
                   href="javascript:void(0)"
                   aria-expanded="false">

                    <i class="fas fa-boxes menu-icon"></i>
                    <span class="nav-text">Time In/Check Out</span>
                </a>

                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('students.monitoring') }}">
                            Student
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('personnel.monitoring') }}">
                            Personnel
                        </a>
                    </li>
                </ul>
            </li>

            {{-- BOOKS BORROWED / RESERVED --}}
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow"
                   href="javascript:void(0)"
                   aria-expanded="false">

                    <i class="fa fa-tags menu-icon"></i>
                    <span class="nav-text">
                        Books Borrowed/Reserved
                    </span>
                </a>

                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('book') }}">
                            Books
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('bookborrow') }}">
                            Books Borrowed/Books Returned
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reserve.monitoring') }}">
                            Books Reserved
                        </a>
                    </li>
                </ul>
            </li>

            {{-- USER MANAGEMENT --}}
            <li class="mega-menu mega-menu-sm">
                <a class="has-arrow"
                   href="javascript:void(0)"
                   aria-expanded="false">

                    <i class="fa fa-group menu-icon"></i>
                    <span class="nav-text">User Management</span>
                </a>

                <ul aria-expanded="false">

                    {{-- ADMIN ONLY --}}
                    @if(strtolower(Session::get('session_access_level', '')) === 'admin')
                        <li>
                            <a href="{{ route('user.management') }}">
                                Staff
                            </a>
                        </li>
                    @endif

                    {{-- ADMIN AND STAFF --}}
                    <li>
                        <a href="{{ route('studentpersonnel.management') }}">
                            Student/Personnel
                        </a>
                    </li>
                </ul>
            </li>

            

            {{-- LOGOUT --}}
            <li>
                <a href="#"
                   onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">

                    <i class="icon-key"></i>
                    <span class="nav-text">Logout</span>
                </a>

                <form id="logout-form"
                      action="{{ route('logout') }}"
                      method="POST"
                      style="display: none;">

                    @csrf
                </form>
            </li>

        </ul>
    </div>
</div>