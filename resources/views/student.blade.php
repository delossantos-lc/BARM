@include('layouts.header')

@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>

    .summary-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    }

    .summary-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .student-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    }

    .student-name {
        font-weight: 700;
    }

    .student-number {
        font-size: 12px;
        color: #777;
    }

    .rfid-text {
        font-size: 12px;
        color: #666;
    }

    .status-badge {
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .inside-row {
        background-color: #eaffef !important;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }


    /* ============================================================
       AUTO REFRESH INDICATOR
    ============================================================ */

    .auto-refresh-status {

        display: inline-flex;
        align-items: center;
        gap: 7px;

        font-size: 12px;
        color: #777;

        margin-left: 12px;

    }


    .auto-refresh-dot {

        width: 8px;
        height: 8px;

        border-radius: 50%;

        background: #28a745;

        display: inline-block;

    }


    .auto-refresh-dot.refreshing {

        animation: refreshPulse 0.8s infinite alternate;

    }


    @keyframes refreshPulse {

        from {
            opacity: 0.3;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1.2);
        }

    }

</style>


@include('layouts.top_navbar')

@include('layouts.left_sidebar')


<div class="content-body">

    <div class="container-fluid">


        <!-- ============================================================
             PAGE TITLE
        ============================================================= -->

        <div class="row page-titles mx-0">

            <div class="col-sm-6 p-md-0">

                <div class="welcome-text">

                    <h4>
                        Student Monitoring
                    </h4>

                    <span>
                        Monitor student information and library attendance
                    </span>

                </div>

            </div>


            <div
                class="
                    col-sm-6
                    p-md-0
                    justify-content-sm-end
                    mt-2
                    mt-sm-0
                    d-flex
                "
            >

                <ol class="breadcrumb">

                    <li class="breadcrumb-item">

                        <a href="{{ route('dashboard') }}">
                            Dashboard
                        </a>

                    </li>

                    <li class="breadcrumb-item active">
                        Students
                    </li>

                </ol>

            </div>

        </div>


        <!-- ============================================================
             SUMMARY CARDS
        ============================================================= -->

        <div class="row">


            <!-- ========================================================
                 TOTAL STUDENTS
            ========================================================= -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Total Students
                        </h5>


                        <p
                            class="summary-number"
                            id="totalStudentsCount"
                        >

                            {{ $totalStudents }}

                        </p>


                        <span class="text-muted">
                            Registered students
                        </span>

                    </div>

                </div>

            </div>


            <!-- ========================================================
                 VISITED TODAY
            ========================================================= -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Visited Today
                        </h5>


                        <p
                            class="summary-number text-primary"
                            id="visitedTodayCount"
                        >

                            {{ $visitedToday }}

                        </p>


                        <span class="text-muted">
                            Students who entered today
                        </span>

                    </div>

                </div>

            </div>


            <!-- ========================================================
                 CURRENTLY INSIDE
            ========================================================= -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Currently Inside
                        </h5>


                        <p
                            class="summary-number text-success"
                            id="insideTodayCount"
                        >

                            {{ $insideToday }}

                        </p>


                        <span class="text-muted">
                            Students without time out
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ============================================================
             STUDENT TABLE
        ============================================================= -->

        <div class="card student-card">

            <div class="card-header">

                <div class="row w-100 align-items-center">

                    <div class="col-md-6">

                        <h4 class="card-title mb-0">

                            <i class="fa fa-graduation-cap mr-2"></i>

                            Student Records

                        </h4>

                    </div>


                    <div class="col-md-6 text-md-right">

                        <small class="text-muted">

                            {{ now()->format('F d, Y') }}

                        </small>


                        <span class="auto-refresh-status">

                            <span
                                class="auto-refresh-dot"
                                id="autoRefreshDot"
                            ></span>

                            <span id="autoRefreshText">
                                Auto Refresh
                            </span>

                        </span>

                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table
                        id="studentTable"
                        class="table table-hover"
                    >

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Student</th>

                                <th>Year Level</th>

                                <th>Course / Program</th>

                                <th>RFID</th>

                                <th>Contact</th>

                                <th>Date</th>

                                <th>Time In</th>

                                <th>Time Out</th>

                                <th>Status</th>

                            </tr>

                        </thead>


                        <tbody id="studentTableBody">

                        @forelse($students as $student)

                            @php

                                $attendance =
                                    $student->latestAttendance;


                                $isInside =
                                    $attendance &&
                                    $attendance->date &&
                                    $attendance->date->isToday() &&
                                    $attendance->time_in &&
                                    !$attendance->time_out;

                            @endphp


                            <tr
                                class="{{ $isInside ? 'inside-row' : '' }}"
                            >


                                <!-- =================================================
                                     NUMBER
                                ================================================== -->

                                <td>

                                    {{ $loop->iteration }}

                                </td>


                                <!-- =================================================
                                     STUDENT
                                ================================================== -->

                                <td>

                                    <strong class="student-name">

                                        {{ $student->firstname }}
                                        {{ $student->lastname }}

                                    </strong>

                                    <br>


                                    <span class="student-number">

                                        Student No:

                                        {{ $student->student_number }}

                                    </span>

                                </td>


                                <!-- =================================================
                                     YEAR LEVEL
                                ================================================== -->

                                <td>

                                    {{ $student->year_level ?? '-' }}

                                </td>


                                <!-- =================================================
                                     COURSE
                                ================================================== -->

                                <td>

                                    {{ $student->course_program ?? '-' }}

                                </td>


                                <!-- =================================================
                                     RFID
                                ================================================== -->

                                <td>

                                    @if($student->rfid_tag_uid)

                                        <span class="rfid-text">

                                            <i class="fa fa-id-card mr-1"></i>

                                            {{ $student->rfid_tag_uid }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            No RFID
                                        </span>

                                    @endif

                                </td>


                                <!-- =================================================
                                     CONTACT
                                ================================================== -->

                                <td>

                                    {{ $student->contact_information ?? '-' }}

                                </td>


                                <!-- =================================================
                                     ATTENDANCE DATE
                                ================================================== -->

                                <td>

                                    @if(
                                        $attendance &&
                                        $attendance->date
                                    )

                                        {{ $attendance->date->format('M d, Y') }}

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <!-- =================================================
                                     TIME IN
                                ================================================== -->

                                <td>

                                    @if(
                                        $attendance &&
                                        $attendance->time_in
                                    )

                                        <i
                                            class="
                                                fa
                                                fa-sign-in
                                                mr-1
                                                text-success
                                            "
                                        ></i>


                                        <strong>

                                            {{
                                                $attendance
                                                    ->time_in
                                                    ->format('h:i A')
                                            }}

                                        </strong>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <!-- =================================================
                                     TIME OUT
                                ================================================== -->

                                <td>

                                    @if(
                                        $attendance &&
                                        $attendance->time_out
                                    )

                                        <i
                                            class="
                                                fa
                                                fa-sign-out
                                                mr-1
                                                text-danger
                                            "
                                        ></i>


                                        <strong>

                                            {{
                                                $attendance
                                                    ->time_out
                                                    ->format('h:i A')
                                            }}

                                        </strong>


                                    @elseif($isInside)

                                        <span class="text-warning">

                                            <i
                                                class="
                                                    fa
                                                    fa-clock-o
                                                    mr-1
                                                "
                                            ></i>

                                            Still Inside

                                        </span>


                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <!-- =================================================
                                     STATUS
                                ================================================== -->

                                <td>


                                    @if($isInside)

                                        <span
                                            class="
                                                badge
                                                badge-success
                                                status-badge
                                            "
                                        >

                                            Inside

                                        </span>


                                    @elseif(
                                        $attendance &&
                                        $attendance->time_out
                                    )

                                        <span
                                            class="
                                                badge
                                                badge-secondary
                                                status-badge
                                            "
                                        >

                                            Time Out

                                        </span>


                                    @elseif(
                                        $attendance &&
                                        $attendance->time_in
                                    )

                                        <span
                                            class="
                                                badge
                                                badge-warning
                                                status-badge
                                            "
                                        >

                                            No Time Out

                                        </span>


                                    @else

                                        <span
                                            class="
                                                badge
                                                badge-light
                                                status-badge
                                            "
                                        >

                                            No Attendance

                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty


                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="fa fa-graduation-cap"
                                        style="
                                            font-size:45px;
                                            color:#ccc;
                                        "
                                    >
                                    </i>


                                    <h5 class="mt-3">

                                        No students found

                                    </h5>


                                    <p class="text-muted">

                                        Student records will appear here.

                                    </p>

                                </td>

                            </tr>


                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    </div>

</div>


@include('layouts.footer')


<!-- ================================================================
     DATATABLE
================================================================ -->

<script
    src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"
></script>

<script
    src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"
></script>


<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    let studentTable =
        $('#studentTable').DataTable({

            pageLength: 10,

            order: [
                [1, 'asc']
            ]

        });


    /*
    |--------------------------------------------------------------------------
    | AUTO REFRESH SETTINGS
    |--------------------------------------------------------------------------
    |
    | 1000  = 1 second
    | 3000  = 3 seconds
    | 5000  = 5 seconds
    | 10000 = 10 seconds
    |
    */

    const refreshInterval = 3000;


    /*
    |--------------------------------------------------------------------------
    | PREVENT MULTIPLE REQUESTS
    |--------------------------------------------------------------------------
    */

    let isRefreshing = false;


    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const autoRefreshDot =
        document.getElementById(
            'autoRefreshDot'
        );


    const autoRefreshText =
        document.getElementById(
            'autoRefreshText'
        );


    /*
    |--------------------------------------------------------------------------
    | REFRESH STUDENT MONITORING
    |--------------------------------------------------------------------------
    */

    async function refreshStudentMonitoring() {


        /*
         * Do not start another request
         * if the previous one is still running.
         */

        if (isRefreshing) {

            return;

        }


        isRefreshing = true;


        /*
        |--------------------------------------------------------------------------
        | Refresh Indicator
        |--------------------------------------------------------------------------
        */

        if (autoRefreshDot) {

            autoRefreshDot
                .classList
                .add('refreshing');

        }


        if (autoRefreshText) {

            autoRefreshText.textContent =
                'Updating...';

        }


        try {


            /*
            |--------------------------------------------------------------------------
            | Save Current DataTable State
            |--------------------------------------------------------------------------
            */

            const currentSearch =
                studentTable.search();


            const currentPage =
                studentTable.page();


            const currentOrder =
                studentTable.order();


            const currentLength =
                studentTable.page.len();


            /*
            |--------------------------------------------------------------------------
            | Load Fresh Laravel Page
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(

                    window.location.href,

                    {

                        method: 'GET',

                        headers: {

                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Cache-Control':
                                'no-cache',

                            'Pragma':
                                'no-cache'

                        },

                        cache: 'no-store'

                    }

                );


            /*
            |--------------------------------------------------------------------------
            | Check Request
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    'Unable to load the latest monitoring data.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Read HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();


            /*
            |--------------------------------------------------------------------------
            | Convert HTML Into Temporary Document
            |--------------------------------------------------------------------------
            */

            const parser =
                new DOMParser();


            const newDocument =
                parser.parseFromString(
                    html,
                    'text/html'
                );


            /*
            |--------------------------------------------------------------------------
            | NEW SUMMARY COUNTS
            |--------------------------------------------------------------------------
            */

            const newTotalStudents =
                newDocument.getElementById(
                    'totalStudentsCount'
                );


            const newVisitedToday =
                newDocument.getElementById(
                    'visitedTodayCount'
                );


            const newInsideToday =
                newDocument.getElementById(
                    'insideTodayCount'
                );


            /*
            |--------------------------------------------------------------------------
            | CURRENT SUMMARY ELEMENTS
            |--------------------------------------------------------------------------
            */

            const totalStudentsElement =
                document.getElementById(
                    'totalStudentsCount'
                );


            const visitedTodayElement =
                document.getElementById(
                    'visitedTodayCount'
                );


            const insideTodayElement =
                document.getElementById(
                    'insideTodayCount'
                );


            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL STUDENTS
            |--------------------------------------------------------------------------
            */

            if (
                newTotalStudents &&
                totalStudentsElement
            ) {

                const oldValue =
                    totalStudentsElement
                        .textContent
                        .trim();


                const newValue =
                    newTotalStudents
                        .textContent
                        .trim();


                if (oldValue !== newValue) {

                    totalStudentsElement.textContent =
                        newValue;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE VISITED TODAY
            |--------------------------------------------------------------------------
            */

            if (
                newVisitedToday &&
                visitedTodayElement
            ) {

                const oldValue =
                    visitedTodayElement
                        .textContent
                        .trim();


                const newValue =
                    newVisitedToday
                        .textContent
                        .trim();


                if (oldValue !== newValue) {

                    visitedTodayElement.textContent =
                        newValue;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE CURRENTLY INSIDE
            |--------------------------------------------------------------------------
            */

            if (
                newInsideToday &&
                insideTodayElement
            ) {

                const oldValue =
                    insideTodayElement
                        .textContent
                        .trim();


                const newValue =
                    newInsideToday
                        .textContent
                        .trim();


                if (oldValue !== newValue) {

                    insideTodayElement.textContent =
                        newValue;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | NEW TABLE BODY
            |--------------------------------------------------------------------------
            */

            const newTableBody =
                newDocument.getElementById(
                    'studentTableBody'
                );


            /*
            |--------------------------------------------------------------------------
            | CURRENT TABLE BODY
            |--------------------------------------------------------------------------
            */

            const currentTableBody =
                document.getElementById(
                    'studentTableBody'
                );


            /*
            |--------------------------------------------------------------------------
            | UPDATE TABLE
            |--------------------------------------------------------------------------
            */

            if (
                newTableBody &&
                currentTableBody
            ) {


                /*
                 * Compare table contents first.
                 *
                 * This prevents DataTable from
                 * being destroyed every 3 seconds
                 * when nothing has changed.
                 */

                const oldRows =
                    currentTableBody
                        .innerHTML
                        .trim();


                const newRows =
                    newTableBody
                        .innerHTML
                        .trim();


                /*
                 * Only rebuild the table if
                 * attendance records changed.
                 */

                if (oldRows !== newRows) {


                    /*
                    |--------------------------------------------------------------------------
                    | Destroy Existing DataTable
                    |--------------------------------------------------------------------------
                    */

                    studentTable.destroy();


                    /*
                    |--------------------------------------------------------------------------
                    | Replace Rows
                    |--------------------------------------------------------------------------
                    */

                    currentTableBody.innerHTML =
                        newTableBody.innerHTML;


                    /*
                    |--------------------------------------------------------------------------
                    | Reinitialize DataTable
                    |--------------------------------------------------------------------------
                    */

                    studentTable =
                        $('#studentTable')
                            .DataTable({

                                pageLength:
                                    currentLength,

                                order:
                                    currentOrder

                            });


                    /*
                    |--------------------------------------------------------------------------
                    | Restore Search
                    |--------------------------------------------------------------------------
                    */

                    studentTable
                        .search(
                            currentSearch
                        )
                        .draw();


                    /*
                    |--------------------------------------------------------------------------
                    | Restore Page
                    |--------------------------------------------------------------------------
                    */

                    const pageInfo =
                        studentTable
                            .page
                            .info();


                    if (
                        currentPage <
                        pageInfo.pages
                    ) {

                        studentTable
                            .page(
                                currentPage
                            )
                            .draw(
                                'page'
                            );

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Refresh Successful
            |--------------------------------------------------------------------------
            */

            if (autoRefreshText) {

                autoRefreshText.textContent =
                    'Auto Refresh';

            }


        }


        catch (error) {


            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            console.error(
                'AUTO REFRESH ERROR:',
                error
            );


            if (autoRefreshText) {

                autoRefreshText.textContent =
                    'Refresh Error';

            }

        }


        finally {


            /*
            |--------------------------------------------------------------------------
            | Reset Request Status
            |--------------------------------------------------------------------------
            */

            isRefreshing = false;


            /*
            |--------------------------------------------------------------------------
            | Stop Refresh Animation
            |--------------------------------------------------------------------------
            */

            if (autoRefreshDot) {

                autoRefreshDot
                    .classList
                    .remove('refreshing');

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | AUTOMATIC REFRESH
    |--------------------------------------------------------------------------
    |
    | Every 3 seconds Laravel is checked
    | for updated student attendance data.
    |
    */

    setInterval(

        refreshStudentMonitoring,

        refreshInterval

    );


    /*
    |--------------------------------------------------------------------------
    | REFRESH WHEN USER RETURNS TO TAB
    |--------------------------------------------------------------------------
    |
    | If the administrator switches tabs
    | and comes back, refresh immediately.
    |
    */

    document.addEventListener(
        'visibilitychange',
        function () {

            if (
                document.visibilityState
                ===
                'visible'
            ) {

                refreshStudentMonitoring();

            }

        }
    );


});

</script>