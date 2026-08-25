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

    .personnel-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    }

    .personnel-name {
        font-weight: 700;
    }

    .employee-number {
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
                        Personnel Monitoring
                    </h4>

                    <span>
                        Monitor personnel information and library attendance
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
                        Personnel
                    </li>

                </ol>

            </div>

        </div>


        <!-- ============================================================
             SUMMARY CARDS
        ============================================================= -->

        <div class="row">


            <!-- ========================================================
                 TOTAL PERSONNEL
            ========================================================= -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Total Personnel
                        </h5>


                        <p
                            class="summary-number"
                            id="totalPersonnelCount"
                        >

                            {{ $totalPersonnel }}

                        </p>


                        <span class="text-muted">
                            Registered personnel
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
                            Personnel who entered today
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
                            Personnel without time out
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ============================================================
             PERSONNEL TABLE
        ============================================================= -->

        <div class="card personnel-card">

            <div class="card-header">

                <div class="row w-100 align-items-center">

                    <div class="col-md-6">

                        <h4 class="card-title mb-0">

                            <i class="fa fa-users mr-2"></i>

                            Personnel Records

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
                        id="personnelTable"
                        class="table table-hover"
                    >

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Personnel</th>

                                <th>Department</th>

                                <th>RFID</th>

                                <th>Contact</th>

                                <th>Date</th>

                                <th>Time In</th>

                                <th>Time Out</th>

                                <th>Status</th>

                            </tr>

                        </thead>


                        <tbody id="personnelTableBody">

                        @forelse($personnel as $person)

                            @php

                                $attendance =
                                    $person->latestAttendance;


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
                                     PERSONNEL
                                ================================================== -->

                                <td>

                                    <strong class="personnel-name">

                                        {{ $person->firstname }}
                                        {{ $person->lastname }}

                                    </strong>

                                    <br>


                                    <span class="employee-number">

                                        Employee No:

                                        {{ $person->employee_number }}

                                    </span>

                                </td>


                                <!-- =================================================
                                     DEPARTMENT
                                ================================================== -->

                                <td>

                                    {{ $person->department ?? '-' }}

                                </td>


                                <!-- =================================================
                                     RFID
                                ================================================== -->

                                <td>

                                    @if($person->rfid_tag_uid)

                                        <span class="rfid-text">

                                            <i class="fa fa-id-card mr-1"></i>

                                            {{ $person->rfid_tag_uid }}

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

                                    {{ $person->contact_information ?? '-' }}

                                </td>


                                <!-- =================================================
                                     DATE
                                ================================================== -->

                                <td>

                                    @if(
                                        $attendance &&
                                        $attendance->date
                                    )

                                        {{
                                            $attendance
                                                ->date
                                                ->format('M d, Y')
                                        }}

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
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="fa fa-users"
                                        style="
                                            font-size:45px;
                                            color:#ccc;
                                        "
                                    >
                                    </i>


                                    <h5 class="mt-3">

                                        No personnel found

                                    </h5>


                                    <p class="text-muted">

                                        Personnel records will appear here.

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

    let personnelTable =
        $('#personnelTable').DataTable({

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
    | REFRESH PERSONNEL MONITORING
    |--------------------------------------------------------------------------
    */

    async function refreshPersonnelMonitoring() {


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
                personnelTable.search();


            const currentPage =
                personnelTable.page();


            const currentOrder =
                personnelTable.order();


            const currentLength =
                personnelTable.page.len();


            /*
            |--------------------------------------------------------------------------
            | LOAD FRESH LARAVEL PAGE
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
            | CHECK REQUEST
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    'Unable to load the latest personnel monitoring data.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | READ HTML
            |--------------------------------------------------------------------------
            */

            const html =
                await response.text();


            /*
            |--------------------------------------------------------------------------
            | CREATE TEMPORARY DOCUMENT
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

            const newTotalPersonnel =
                newDocument.getElementById(
                    'totalPersonnelCount'
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
            | CURRENT SUMMARY COUNTS
            |--------------------------------------------------------------------------
            */

            const totalPersonnelElement =
                document.getElementById(
                    'totalPersonnelCount'
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
            | UPDATE TOTAL PERSONNEL
            |--------------------------------------------------------------------------
            */

            if (
                newTotalPersonnel &&
                totalPersonnelElement
            ) {

                const oldValue =
                    totalPersonnelElement
                        .textContent
                        .trim();


                const newValue =
                    newTotalPersonnel
                        .textContent
                        .trim();


                if (oldValue !== newValue) {

                    totalPersonnelElement.textContent =
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
                    'personnelTableBody'
                );


            /*
            |--------------------------------------------------------------------------
            | CURRENT TABLE BODY
            |--------------------------------------------------------------------------
            */

            const currentTableBody =
                document.getElementById(
                    'personnelTableBody'
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
                 * Compare rows first.
                 * If there is no change,
                 * leave DataTable alone.
                 */

                const oldRows =
                    currentTableBody
                        .innerHTML
                        .trim();


                const newRows =
                    newTableBody
                        .innerHTML
                        .trim();


                if (oldRows !== newRows) {


                    /*
                    |--------------------------------------------------------------------------
                    | DESTROY EXISTING DATATABLE
                    |--------------------------------------------------------------------------
                    */

                    personnelTable.destroy();


                    /*
                    |--------------------------------------------------------------------------
                    | REPLACE PERSONNEL ROWS
                    |--------------------------------------------------------------------------
                    */

                    currentTableBody.innerHTML =
                        newTableBody.innerHTML;


                    /*
                    |--------------------------------------------------------------------------
                    | REINITIALIZE DATATABLE
                    |--------------------------------------------------------------------------
                    */

                    personnelTable =
                        $('#personnelTable')
                            .DataTable({

                                pageLength:
                                    currentLength,

                                order:
                                    currentOrder

                            });


                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE SEARCH
                    |--------------------------------------------------------------------------
                    */

                    personnelTable
                        .search(
                            currentSearch
                        )
                        .draw();


                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE PAGE NUMBER
                    |--------------------------------------------------------------------------
                    */

                    const pageInfo =
                        personnelTable
                            .page
                            .info();


                    if (
                        currentPage <
                        pageInfo.pages
                    ) {

                        personnelTable
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
            | REFRESH SUCCESSFUL
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
            | ERROR
            |--------------------------------------------------------------------------
            */

            console.error(
                'PERSONNEL AUTO REFRESH ERROR:',
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
            | RESET REQUEST
            |--------------------------------------------------------------------------
            */

            isRefreshing = false;


            /*
            |--------------------------------------------------------------------------
            | STOP ANIMATION
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
    | AUTOMATIC REFRESH EVERY 3 SECONDS
    |--------------------------------------------------------------------------
    */

    setInterval(

        refreshPersonnelMonitoring,

        refreshInterval

    );


    /*
    |--------------------------------------------------------------------------
    | REFRESH IMMEDIATELY WHEN TAB BECOMES ACTIVE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'visibilitychange',
        function () {

            if (
                document.visibilityState
                ===
                'visible'
            ) {

                refreshPersonnelMonitoring();

            }

        }
    );


});

</script>