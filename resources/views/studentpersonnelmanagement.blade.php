@include('layouts.header')

@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>

    .management-card,
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

    .management-tabs .nav-link {
        border: none;
        border-radius: 10px;
        padding: 12px 22px;
        font-weight: 600;
        margin-right: 8px;
        color: #555;
    }

    .management-tabs .nav-link.active {
        background: #343a40;
        color: #fff;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }

    .person-name {
        font-weight: 700;
    }

    .person-number {
        color: #777;
        font-size: 12px;
    }

    .rfid-badge {
        background: #f2f2f2;
        border-radius: 7px;
        padding: 6px 9px;
        font-size: 12px;
        color: #555;
        white-space: nowrap;
    }

    .action-button {
        margin: 2px;
    }

    .modal-content {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .modal-header {
        background: #f8f9fa;
    }

    .form-control {
        border-radius: 8px;
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
                        Student & Personnel Management
                    </h4>

                    <span>
                        Manage registered students and personnel
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
                        Student & Personnel Management
                    </li>

                </ol>

            </div>

        </div>


        <!-- ============================================================
             SUCCESS MESSAGE
        ============================================================= -->

        @if(session('success'))

            <div
                class="
                    alert
                    alert-success
                    alert-dismissible
                    fade
                    show
                "
            >

                <i class="fa fa-check-circle mr-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >

                    <span>&times;</span>

                </button>

            </div>

        @endif


        <!-- ============================================================
             VALIDATION ERRORS
        ============================================================= -->

        @if($errors->any())

            <div
                class="
                    alert
                    alert-danger
                    alert-dismissible
                    fade
                    show
                "
            >

                <strong>
                    Please check the following:
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>


                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >

                    <span>&times;</span>

                </button>

            </div>

        @endif


        <!-- ============================================================
             SUMMARY CARDS
        ============================================================= -->

        <div class="row">


            <!-- STUDENTS -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Total Students
                        </h5>

                        <p
                            class="
                                summary-number
                                text-primary
                            "
                        >

                            {{ $students->count() }}

                        </p>

                        <span class="text-muted">
                            Registered student records
                        </span>

                    </div>

                </div>

            </div>


            <!-- PERSONNEL -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Total Personnel
                        </h5>

                        <p
                            class="
                                summary-number
                                text-success
                            "
                        >

                            {{ $personnel->count() }}

                        </p>

                        <span class="text-muted">
                            Registered personnel records
                        </span>

                    </div>

                </div>

            </div>


            <!-- ALL -->

            <div class="col-xl-4 col-lg-6 col-sm-6">

                <div class="card summary-card">

                    <div class="card-body">

                        <h5>
                            Total Records
                        </h5>

                        <p class="summary-number">

                            {{
                                $students->count()
                                +
                                $personnel->count()
                            }}

                        </p>

                        <span class="text-muted">
                            Students and personnel
                        </span>

                    </div>

                </div>

            </div>


        </div>


        <!-- ============================================================
             MANAGEMENT CARD
        ============================================================= -->

        <div class="card management-card">

            <div class="card-header">

                <div
                    class="
                        row
                        w-100
                        align-items-center
                    "
                >


                    <div class="col-md-6">

                        <h4 class="card-title mb-0">

                            <i class="fa fa-users mr-2"></i>

                            Records Management

                        </h4>

                    </div>


                    <!-- =================================================
                         ADD BUTTONS
                         ADMIN + STAFF CAN USE
                    ================================================== -->

                    <div class="col-md-6 text-md-right">


                        <!-- ADD STUDENT -->

                        <button
                            type="button"
                            class="
                                btn
                                btn-primary
                                btn-sm
                                mr-1
                            "
                            data-toggle="modal"
                            data-target="#addStudentModal"
                        >

                            <i class="fa fa-plus mr-1"></i>

                            Add Student

                        </button>


                        <!-- ADD PERSONNEL -->

                        <button
                            type="button"
                            class="
                                btn
                                btn-success
                                btn-sm
                            "
                            data-toggle="modal"
                            data-target="#addPersonnelModal"
                        >

                            <i class="fa fa-plus mr-1"></i>

                            Add Personnel

                        </button>


                    </div>


                </div>

            </div>


            <div class="card-body">


                <!-- ====================================================
                     TABS
                ===================================================== -->

                <ul
                    class="
                        nav
                        nav-pills
                        management-tabs
                        mb-4
                    "
                >


                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            data-toggle="tab"
                            href="#studentsTab"
                        >

                            <i
                                class="
                                    fa
                                    fa-graduation-cap
                                    mr-1
                                "
                            ></i>

                            Students

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            data-toggle="tab"
                            href="#personnelTab"
                        >

                            <i
                                class="
                                    fa
                                    fa-user
                                    mr-1
                                "
                            ></i>

                            Personnel

                        </a>

                    </li>


                </ul>


                <div class="tab-content">


                    <!-- =================================================
                         STUDENT TAB
                    ================================================== -->

                    <div
                        class="
                            tab-pane
                            fade
                            show
                            active
                        "
                        id="studentsTab"
                    >

                        <div class="table-responsive">

                            <table
                                id="studentManagementTable"
                                class="table table-hover"
                            >


                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Student</th>

                                        <th>Year Level</th>

                                        <th>Program</th>

                                        <th>RFID</th>

                                        <th>Contact</th>


                                        <!-- ADMIN ONLY -->

                                        @if(
                                            strtolower(
                                                session('session_access_level')
                                            ) === 'admin'
                                        )

                                            <th>
                                                Action
                                            </th>

                                        @endif


                                    </tr>

                                </thead>


                                <tbody>


                                @foreach($students as $student)


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            {{ $loop->iteration }}

                                        </td>


                                        <!-- STUDENT -->

                                        <td>

                                            <strong class="person-name">

                                                {{ $student->firstname }}

                                                {{ $student->lastname }}

                                            </strong>

                                            <br>

                                            <span class="person-number">

                                                Student No:

                                                {{ $student->student_number }}

                                            </span>

                                        </td>


                                        <!-- YEAR LEVEL -->

                                        <td>

                                            {{
                                                $student->year_level
                                                ?? '-'
                                            }}

                                        </td>


                                        <!-- PROGRAM -->

                                        <td>

                                            {{
                                                $student->course_program
                                                ?? '-'
                                            }}

                                        </td>


                                        <!-- RFID -->

                                        <td>

                                            @if($student->rfid_tag_uid)

                                                <span class="rfid-badge">

                                                    <i
                                                        class="
                                                            fa
                                                            fa-id-card
                                                            mr-1
                                                        "
                                                    ></i>

                                                    {{
                                                        $student
                                                            ->rfid_tag_uid
                                                    }}

                                                </span>

                                            @else

                                                <span class="text-muted">

                                                    No RFID

                                                </span>

                                            @endif

                                        </td>


                                        <!-- CONTACT -->

                                        <td>

                                            {{
                                                $student
                                                    ->contact_information
                                                ?? '-'
                                            }}

                                        </td>


                                        <!-- =================================================
                                             ACTION
                                             ADMIN ONLY
                                        ================================================== -->

                                        @if(
                                            strtolower(
                                                session('session_access_level')
                                            ) === 'admin'
                                        )


                                            <td
                                                style="
                                                    min-width:150px;
                                                "
                                            >


                                                <!-- EDIT -->

                                                <button
                                                    type="button"
                                                    class="
                                                        btn
                                                        btn-info
                                                        btn-sm
                                                        action-button
                                                    "
                                                    data-toggle="modal"
                                                    data-target="#editStudentModal{{ $student->id }}"
                                                >

                                                    <i class="fa fa-pencil"></i>

                                                    Edit

                                                </button>


                                                <!-- DELETE -->

                                                <form
                                                    action="{{
                                                        route(
                                                            'management.students.destroy',
                                                            $student
                                                        )
                                                    }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="
                                                        return confirm(
                                                            'Delete this student?'
                                                        );
                                                    "
                                                >

                                                    @csrf

                                                    @method('DELETE')


                                                    <button
                                                        type="submit"
                                                        class="
                                                            btn
                                                            btn-danger
                                                            btn-sm
                                                            action-button
                                                        "
                                                    >

                                                        <i
                                                            class="
                                                                fa
                                                                fa-trash
                                                            "
                                                        ></i>

                                                    </button>


                                                </form>


                                            </td>


                                        @endif


                                    </tr>


                                    <!-- =================================================
                                         EDIT STUDENT MODAL
                                         ADMIN ONLY
                                    ================================================== -->

                                    @if(
                                        strtolower(
                                            session('session_access_level')
                                        ) === 'admin'
                                    )


                                        <div
                                            class="modal fade"
                                            id="editStudentModal{{ $student->id }}"
                                            tabindex="-1"
                                        >

                                            <div class="modal-dialog modal-lg">

                                                <div class="modal-content">


                                                    <form
                                                        action="{{
                                                            route(
                                                                'management.students.update',
                                                                $student
                                                            )
                                                        }}"
                                                        method="POST"
                                                    >

                                                        @csrf

                                                        @method('PUT')


                                                        <div class="modal-header">

                                                            <h5 class="modal-title">

                                                                Edit Student

                                                            </h5>


                                                            <button
                                                                type="button"
                                                                class="close"
                                                                data-dismiss="modal"
                                                            >

                                                                <span>
                                                                    &times;
                                                                </span>

                                                            </button>

                                                        </div>


                                                        <div class="modal-body">

                                                            <div class="row">


                                                                <!-- FIRST NAME -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            First Name
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="firstname"
                                                                            class="form-control"
                                                                            value="{{ $student->firstname }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- LAST NAME -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Last Name
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="lastname"
                                                                            class="form-control"
                                                                            value="{{ $student->lastname }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- STUDENT NUMBER -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Student Number
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="student_number"
                                                                            class="form-control"
                                                                            value="{{ $student->student_number }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- YEAR LEVEL -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Year Level
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="year_level"
                                                                            class="form-control"
                                                                            value="{{ $student->year_level }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- PROGRAM -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Course / Program
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="course_program"
                                                                            class="form-control"
                                                                            value="{{ $student->course_program }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- RFID -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            RFID UID
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="rfid_tag_uid"
                                                                            class="form-control"
                                                                            value="{{ $student->rfid_tag_uid }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- CONTACT -->

                                                                <div class="col-md-12">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Contact Information
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="contact_information"
                                                                            class="form-control"
                                                                            value="{{ $student->contact_information }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                            </div>

                                                        </div>


                                                        <div class="modal-footer">


                                                            <button
                                                                type="button"
                                                                class="btn btn-secondary"
                                                                data-dismiss="modal"
                                                            >

                                                                Cancel

                                                            </button>


                                                            <button
                                                                type="submit"
                                                                class="btn btn-primary"
                                                            >

                                                                <i class="fa fa-save mr-1"></i>

                                                                Save Changes

                                                            </button>


                                                        </div>


                                                    </form>


                                                </div>

                                            </div>

                                        </div>


                                    @endif


                                @endforeach


                                </tbody>


                            </table>

                        </div>

                    </div>


                    <!-- =================================================
                         PERSONNEL TAB
                    ================================================== -->

                    <div
                        class="
                            tab-pane
                            fade
                        "
                        id="personnelTab"
                    >

                        <div class="table-responsive">

                            <table
                                id="personnelManagementTable"
                                class="table table-hover"
                            >


                                <thead>

                                    <tr>

                                        <th>#</th>

                                        <th>Personnel</th>

                                        <th>Department</th>

                                        <th>RFID</th>

                                        <th>Contact</th>


                                        <!-- ADMIN ONLY -->

                                        @if(
                                            strtolower(
                                                session('session_access_level')
                                            ) === 'admin'
                                        )

                                            <th>
                                                Action
                                            </th>

                                        @endif


                                    </tr>

                                </thead>


                                <tbody>


                                @foreach($personnel as $person)


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            {{ $loop->iteration }}

                                        </td>


                                        <!-- PERSONNEL -->

                                        <td>

                                            <strong class="person-name">

                                                {{ $person->firstname }}

                                                {{ $person->lastname }}

                                            </strong>

                                            <br>


                                            <span class="person-number">

                                                Employee No:

                                                {{
                                                    $person
                                                        ->employee_number
                                                }}

                                            </span>

                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td>

                                            {{
                                                $person->department
                                                ?? '-'
                                            }}

                                        </td>


                                        <!-- RFID -->

                                        <td>

                                            @if($person->rfid_tag_uid)

                                                <span class="rfid-badge">

                                                    <i
                                                        class="
                                                            fa
                                                            fa-id-card
                                                            mr-1
                                                        "
                                                    ></i>

                                                    {{
                                                        $person
                                                            ->rfid_tag_uid
                                                    }}

                                                </span>

                                            @else

                                                <span class="text-muted">

                                                    No RFID

                                                </span>

                                            @endif

                                        </td>


                                        <!-- CONTACT -->

                                        <td>

                                            {{
                                                $person
                                                    ->contact_information
                                                ?? '-'
                                            }}

                                        </td>


                                        <!-- =================================================
                                             ACTION
                                             ADMIN ONLY
                                        ================================================== -->

                                        @if(
                                            strtolower(
                                                session('session_access_level')
                                            ) === 'admin'
                                        )


                                            <td
                                                style="
                                                    min-width:150px;
                                                "
                                            >


                                                <!-- EDIT -->

                                                <button
                                                    type="button"
                                                    class="
                                                        btn
                                                        btn-info
                                                        btn-sm
                                                        action-button
                                                    "
                                                    data-toggle="modal"
                                                    data-target="#editPersonnelModal{{ $person->id }}"
                                                >

                                                    <i class="fa fa-pencil"></i>

                                                    Edit

                                                </button>


                                                <!-- DELETE -->

                                                <form
                                                    action="{{
                                                        route(
                                                            'management.personnel.destroy',
                                                            $person
                                                        )
                                                    }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="
                                                        return confirm(
                                                            'Delete this personnel?'
                                                        );
                                                    "
                                                >

                                                    @csrf

                                                    @method('DELETE')


                                                    <button
                                                        type="submit"
                                                        class="
                                                            btn
                                                            btn-danger
                                                            btn-sm
                                                            action-button
                                                        "
                                                    >

                                                        <i
                                                            class="
                                                                fa
                                                                fa-trash
                                                            "
                                                        ></i>

                                                    </button>


                                                </form>


                                            </td>


                                        @endif


                                    </tr>


                                    <!-- =================================================
                                         EDIT PERSONNEL MODAL
                                         ADMIN ONLY
                                    ================================================== -->

                                    @if(
                                        strtolower(
                                            session('session_access_level')
                                        ) === 'admin'
                                    )


                                        <div
                                            class="modal fade"
                                            id="editPersonnelModal{{ $person->id }}"
                                            tabindex="-1"
                                        >

                                            <div class="modal-dialog modal-lg">

                                                <div class="modal-content">


                                                    <form
                                                        action="{{
                                                            route(
                                                                'management.personnel.update',
                                                                $person
                                                            )
                                                        }}"
                                                        method="POST"
                                                    >

                                                        @csrf

                                                        @method('PUT')


                                                        <div class="modal-header">

                                                            <h5 class="modal-title">

                                                                Edit Personnel

                                                            </h5>


                                                            <button
                                                                type="button"
                                                                class="close"
                                                                data-dismiss="modal"
                                                            >

                                                                <span>
                                                                    &times;
                                                                </span>

                                                            </button>

                                                        </div>


                                                        <div class="modal-body">

                                                            <div class="row">


                                                                <!-- FIRST NAME -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            First Name
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="firstname"
                                                                            class="form-control"
                                                                            value="{{ $person->firstname }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- LAST NAME -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Last Name
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="lastname"
                                                                            class="form-control"
                                                                            value="{{ $person->lastname }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- EMPLOYEE NUMBER -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Employee Number
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="employee_number"
                                                                            class="form-control"
                                                                            value="{{ $person->employee_number }}"
                                                                            required
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- DEPARTMENT -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Department
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="department"
                                                                            class="form-control"
                                                                            value="{{ $person->department }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- RFID -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            RFID UID
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="rfid_tag_uid"
                                                                            class="form-control"
                                                                            value="{{ $person->rfid_tag_uid }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                                <!-- CONTACT -->

                                                                <div class="col-md-6">

                                                                    <div class="form-group">

                                                                        <label>
                                                                            Contact Information
                                                                        </label>

                                                                        <input
                                                                            type="text"
                                                                            name="contact_information"
                                                                            class="form-control"
                                                                            value="{{ $person->contact_information }}"
                                                                        >

                                                                    </div>

                                                                </div>


                                                            </div>

                                                        </div>


                                                        <div class="modal-footer">


                                                            <button
                                                                type="button"
                                                                class="btn btn-secondary"
                                                                data-dismiss="modal"
                                                            >

                                                                Cancel

                                                            </button>


                                                            <button
                                                                type="submit"
                                                                class="btn btn-success"
                                                            >

                                                                <i class="fa fa-save mr-1"></i>

                                                                Save Changes

                                                            </button>


                                                        </div>


                                                    </form>


                                                </div>

                                            </div>

                                        </div>


                                    @endif


                                @endforeach


                                </tbody>


                            </table>

                        </div>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


<!-- ================================================================
     ADD STUDENT MODAL
     ADMIN + STAFF
================================================================ -->

<div
    class="modal fade"
    id="addStudentModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">


            <form
                action="{{
                    route(
                        'management.students.store'
                    )
                }}"
                method="POST"
            >

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">

                        <i
                            class="
                                fa
                                fa-graduation-cap
                                mr-2
                            "
                        ></i>

                        Add New Student

                    </h5>


                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >

                        <span>
                            &times;
                        </span>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="row">


                        <!-- FIRST NAME -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="firstname"
                                    class="form-control"
                                    value="{{ old('firstname') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- LAST NAME -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="lastname"
                                    class="form-control"
                                    value="{{ old('lastname') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- STUDENT NUMBER -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Student Number
                                </label>

                                <input
                                    type="text"
                                    name="student_number"
                                    class="form-control"
                                    value="{{ old('student_number') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- YEAR LEVEL -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Year Level
                                </label>

                                <select
                                    name="year_level"
                                    class="form-control"
                                >

                                    <option value="">
                                        Select Year Level
                                    </option>


                                    <option
                                        value="1st Year"
                                        {{ old('year_level') === '1st Year' ? 'selected' : '' }}
                                    >
                                        1st Year
                                    </option>


                                    <option
                                        value="2nd Year"
                                        {{ old('year_level') === '2nd Year' ? 'selected' : '' }}
                                    >
                                        2nd Year
                                    </option>


                                    <option
                                        value="3rd Year"
                                        {{ old('year_level') === '3rd Year' ? 'selected' : '' }}
                                    >
                                        3rd Year
                                    </option>


                                    <option
                                        value="4th Year"
                                        {{ old('year_level') === '4th Year' ? 'selected' : '' }}
                                    >
                                        4th Year
                                    </option>


                                </select>

                            </div>

                        </div>


                        <!-- COURSE -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Course / Program
                                </label>

                                <input
                                    type="text"
                                    name="course_program"
                                    class="form-control"
                                    value="{{ old('course_program') }}"
                                    placeholder="Example: BSIT"
                                >

                            </div>

                        </div>


                        <!-- RFID -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    RFID UID
                                </label>

                                <input
                                    type="text"
                                    name="rfid_tag_uid"
                                    class="form-control"
                                    value="{{ old('rfid_tag_uid') }}"
                                    placeholder="Scan or enter RFID"
                                >

                            </div>

                        </div>


                        <!-- CONTACT -->

                        <div class="col-md-12">

                            <div class="form-group">

                                <label>
                                    Contact Information
                                </label>

                                <input
                                    type="text"
                                    name="contact_information"
                                    class="form-control"
                                    value="{{ old('contact_information') }}"
                                    placeholder="Phone number or email"
                                >

                            </div>

                        </div>


                    </div>

                </div>


                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i class="fa fa-save mr-1"></i>

                        Add Student

                    </button>


                </div>


            </form>


        </div>

    </div>

</div>


<!-- ================================================================
     ADD PERSONNEL MODAL
     ADMIN + STAFF
================================================================ -->

<div
    class="modal fade"
    id="addPersonnelModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">


            <form
                action="{{
                    route(
                        'management.personnel.store'
                    )
                }}"
                method="POST"
            >

                @csrf


                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="fa fa-user mr-2"></i>

                        Add New Personnel

                    </h5>


                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >

                        <span>
                            &times;
                        </span>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="row">


                        <!-- FIRST NAME -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="firstname"
                                    class="form-control"
                                    value="{{ old('firstname') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- LAST NAME -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="lastname"
                                    class="form-control"
                                    value="{{ old('lastname') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- EMPLOYEE NUMBER -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Employee Number
                                </label>

                                <input
                                    type="text"
                                    name="employee_number"
                                    class="form-control"
                                    value="{{ old('employee_number') }}"
                                    required
                                >

                            </div>

                        </div>


                        <!-- DEPARTMENT -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Department
                                </label>

                                <input
                                    type="text"
                                    name="department"
                                    class="form-control"
                                    value="{{ old('department') }}"
                                >

                            </div>

                        </div>


                        <!-- RFID -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    RFID UID
                                </label>

                                <input
                                    type="text"
                                    name="rfid_tag_uid"
                                    class="form-control"
                                    value="{{ old('rfid_tag_uid') }}"
                                    placeholder="Scan or enter RFID"
                                >

                            </div>

                        </div>


                        <!-- CONTACT -->

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>
                                    Contact Information
                                </label>

                                <input
                                    type="text"
                                    name="contact_information"
                                    class="form-control"
                                    value="{{ old('contact_information') }}"
                                >

                            </div>

                        </div>


                    </div>

                </div>


                <div class="modal-footer">


                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="fa fa-save mr-1"></i>

                        Add Personnel

                    </button>


                </div>


            </form>


        </div>

    </div>

</div>


@include('layouts.footer')


<!-- ================================================================
     DATATABLES
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
    | STUDENT DATATABLE
    |--------------------------------------------------------------------------
    */

    $('#studentManagementTable').DataTable({

        pageLength: 10,

        order: [
            [1, 'asc']
        ]

    });


    /*
    |--------------------------------------------------------------------------
    | PERSONNEL DATATABLE
    |--------------------------------------------------------------------------
    */

    $('#personnelManagementTable').DataTable({

        pageLength: 10,

        order: [
            [1, 'asc']
        ]

    });


});

</script>