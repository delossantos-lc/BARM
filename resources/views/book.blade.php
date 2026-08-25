@include('layouts.header')

<!-- CSS -->
@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>

    .book-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .book-card-header {
        background: #ffffff;
        padding: 25px;
        border-bottom: 1px solid #eeeeee;
    }

    .book-card-header h3 {
        margin: 0;
        font-weight: 700;
        color: #333;
    }

    .book-form-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .form-section-title {
        font-weight: 700;
        color: #555;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eeeeee;
    }

    .form-control {
        border-radius: 10px;
        min-height: 45px;
    }

    textarea.form-control {
        min-height: 90px;
    }

    .btn {
        border-radius: 10px;
    }

    .btn-add-book {
        min-width: 140px;
        height: 45px;
        font-weight: 600;
    }

    .table thead th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
    }

    .book-title {
        font-weight: 600;
        color: #333;
    }

    .book-author {
        font-size: 13px;
        color: #777;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin: 1px;
    }

    .badge-book {
        padding: 7px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .book-code {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .5px;
    }

    .modal-content {
        border: none;
        border-radius: 18px;
    }

    .modal-header {
        border-bottom: 1px solid #eeeeee;
    }

    .modal-footer {
        border-top: 1px solid #eeeeee;
    }

</style>


@include('layouts.top_navbar')
@include('layouts.left_sidebar')


<!--**********************************
    Content body start
***********************************-->

<div class="content-body">

    <div class="container-fluid">


        <!-- =====================================================
             PAGE TITLE
        ====================================================== -->

        <div class="row page-titles mx-0">

            <div class="col-sm-6 p-md-0">

                <div class="welcome-text">

                    <h4>
                        Book Management
                    </h4>

                    <span>
                        Manage library book records
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
                        Books
                    </li>

                </ol>

            </div>

        </div>


        <!-- =====================================================
             SUCCESS MESSAGE
        ====================================================== -->

        @if(Session::has('success'))

            <div
                class="
                    alert
                    alert-success
                    alert-dismissible
                    fade
                    show
                "
            >

                {{ Session::get('success') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >
                    <span>&times;</span>
                </button>

            </div>

        @endif


        <!-- =====================================================
             ERROR MESSAGE
        ====================================================== -->

        @if(Session::has('fail'))

            <div
                class="
                    alert
                    alert-danger
                    alert-dismissible
                    fade
                    show
                "
            >

                {{ Session::get('fail') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >
                    <span>&times;</span>
                </button>

            </div>

        @endif


        <!-- =====================================================
             VALIDATION ERRORS
        ====================================================== -->

        @if($errors->any())

            <div class="alert alert-danger">

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

            </div>

        @endif


        <!-- =====================================================
             ADD BOOK
        ====================================================== -->

        <div class="card book-form-card mb-4">

            <div class="card-body">


                <div
                    class="
                        d-flex
                        justify-content-between
                        align-items-center
                    "
                >

                    <div>

                        <h4 class="mb-1">

                            <i class="fa fa-book mr-2"></i>

                            Add New Book

                        </h4>

                        <p class="text-muted mb-0">

                            Enter the bibliographic information
                            of the book.

                        </p>

                    </div>


                    <button
                        class="btn btn-primary btn-add-book"
                        type="button"
                        data-toggle="collapse"
                        data-target="#bookForm"
                    >

                        <i class="fa fa-plus mr-1"></i>

                        Add Book

                    </button>

                </div>


                <!-- Automatically open if validation failed -->

                <div
                    class="
                        collapse
                        mt-4
                        {{ $errors->any() ? 'show' : '' }}
                    "
                    id="bookForm"
                >

                    <hr>


                    <form
                        action="{{ route('books.store') }}"
                        method="POST"
                    >

                        @csrf


                        <!-- =================================================
                             BASIC INFORMATION
                        ================================================== -->

                        <h5 class="form-section-title">

                            Basic Information

                        </h5>


                        <div class="row">


                            <!-- TITLE -->

                            <div class="col-lg-6 col-md-12 mb-3">

                                <label>

                                    Book Title

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <textarea
                                    name="title"
                                    class="form-control"
                                    placeholder="Enter book title"
                                    required
                                >{{ old('title') }}</textarea>

                            </div>


                            <!-- AUTHOR -->

                            <div class="col-lg-6 col-md-12 mb-3">

                                <label>
                                    Author
                                </label>

                                <textarea
                                    name="author"
                                    class="form-control"
                                    placeholder="Enter author name"
                                >{{ old('author') }}</textarea>

                            </div>


                            <!-- CALL NUMBER -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    Call Number
                                </label>

                                <input
                                    type="text"
                                    name="call_number"
                                    value="{{ old('call_number') }}"
                                    class="form-control"
                                    placeholder="Example: QA76.73"
                                >

                            </div>


                            <!-- SUBLOCATION -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    Sublocation
                                </label>

                                <input
                                    type="text"
                                    name="sublocation"
                                    value="{{ old('sublocation') }}"
                                    class="form-control"
                                    placeholder="Example: Filipiniana"
                                >

                            </div>


                            <!-- YEAR -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    Publication Year
                                </label>

                                <input
                                    type="text"
                                    name="year"
                                    value="{{ old('year') }}"
                                    class="form-control"
                                    placeholder="Example: 2026"
                                >

                            </div>

                        </div>


                        <!-- =================================================
                             PUBLICATION INFORMATION
                        ================================================== -->

                        <h5 class="form-section-title mt-4">

                            Publication Information

                        </h5>


                        <div class="row">


                            <!-- PUBLISHER -->

                            <div class="col-lg-6 mb-3">

                                <label>
                                    Publisher
                                </label>

                                <textarea
                                    name="publisher"
                                    class="form-control"
                                    placeholder="Enter publisher"
                                >{{ old('publisher') }}</textarea>

                            </div>


                            <!-- FORMAT -->

                            <div class="col-lg-6 mb-3">

                                <label>
                                    Format
                                </label>

                                <textarea
                                    name="format"
                                    class="form-control"
                                    placeholder="Example: Print, Electronic Resource"
                                >{{ old('format') }}</textarea>

                            </div>


                            <!-- EDITION -->

                            <div class="col-lg-3 col-md-6 mb-3">

                                <label>
                                    Edition
                                </label>

                                <input
                                    type="text"
                                    name="edition"
                                    value="{{ old('edition') }}"
                                    class="form-control"
                                    placeholder="Example: 3rd Edition"
                                >

                            </div>


                            <!-- CONTENT TYPE -->

                            <div class="col-lg-3 col-md-6 mb-3">

                                <label>
                                    Content Type
                                </label>

                                <input
                                    type="text"
                                    name="content_type"
                                    value="{{ old('content_type') }}"
                                    class="form-control"
                                    placeholder="Example: Text"
                                >

                            </div>


                            <!-- MEDIA TYPE -->

                            <div class="col-lg-3 col-md-6 mb-3">

                                <label>
                                    Media Type
                                </label>

                                <input
                                    type="text"
                                    name="media_type"
                                    value="{{ old('media_type') }}"
                                    class="form-control"
                                    placeholder="Example: Unmediated"
                                >

                            </div>


                            <!-- CARRIER TYPE -->

                            <div class="col-lg-3 col-md-6 mb-3">

                                <label>
                                    Carrier Type
                                </label>

                                <input
                                    type="text"
                                    name="carrier_type"
                                    value="{{ old('carrier_type') }}"
                                    class="form-control"
                                    placeholder="Example: Volume"
                                >

                            </div>

                        </div>


                        <!-- =================================================
                             IDENTIFIERS
                        ================================================== -->

                        <h5 class="form-section-title mt-4">

                            Book Identifiers

                        </h5>


                        <div class="row">


                            <!-- ISBN -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    ISBN
                                </label>

                                <textarea
                                    name="isbn"
                                    class="form-control"
                                    placeholder="Enter ISBN"
                                >{{ old('isbn') }}</textarea>

                            </div>


                            <!-- ISSN -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    ISSN
                                </label>

                                <textarea
                                    name="issn"
                                    class="form-control"
                                    placeholder="Enter ISSN"
                                >{{ old('issn') }}</textarea>

                            </div>


                            <!-- LCCN -->

                            <div class="col-lg-4 col-md-6 mb-3">

                                <label>
                                    LCCN
                                </label>

                                <input
                                    type="text"
                                    name="lccn"
                                    value="{{ old('lccn') }}"
                                    class="form-control"
                                    placeholder="Enter LCCN"
                                >

                            </div>

                        </div>


                        <!-- =================================================
                             SUBJECTS / DETAILS
                        ================================================== -->

                        <h5 class="form-section-title mt-4">

                            Subjects & Additional Details

                        </h5>


                        <div class="row">


                            <!-- SUBJECTS -->

                            <div class="col-lg-6 mb-3">

                                <label>
                                    Subjects
                                </label>

                                <textarea
                                    name="subjects"
                                    rows="4"
                                    class="form-control"
                                    placeholder="Example: Computer Science; Programming; Information Technology"
                                >{{ old('subjects') }}</textarea>

                            </div>


                            <!-- DETAILS -->

                            <div class="col-lg-6 mb-3">

                                <label>
                                    Additional Details
                                </label>

                                <textarea
                                    name="additional_details"
                                    rows="4"
                                    class="form-control"
                                    placeholder="Enter other bibliographic details"
                                >{{ old('additional_details') }}</textarea>

                            </div>

                        </div>


                        <!-- =================================================
                             BUTTONS
                        ================================================== -->

                        <div class="text-right mt-3">


                            <button
                                type="button"
                                class="btn btn-light"
                                data-toggle="collapse"
                                data-target="#bookForm"
                            >

                                Cancel

                            </button>


                            <button
                                type="reset"
                                class="btn btn-secondary"
                            >

                                <i class="fa fa-refresh mr-1"></i>

                                Clear

                            </button>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >

                                <i class="fa fa-save mr-1"></i>

                                Save Book

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =====================================================
             BOOK LIST
        ====================================================== -->

        <div class="card book-card">


            <div class="book-card-header">


                <div class="row align-items-center">


                    <div class="col-md-6">

                        <h3>

                            <i class="fa fa-book mr-2"></i>

                            Books

                        </h3>

                        <small class="text-muted">

                            List of books currently registered
                            in the system.

                        </small>

                    </div>


                    <div
                        class="
                            col-md-6
                            text-md-right
                            mt-3
                            mt-md-0
                        "
                    >

                        <span
                            class="
                                badge
                                badge-primary
                                badge-book
                            "
                        >

                            Total Books:

                            {{ isset($books) ? $books->count() : 0 }}

                        </span>

                    </div>

                </div>

            </div>


            <div class="card-body">


                <div class="table-responsive">


                    <table
                        id="booksTable"
                        class="table table-hover table-striped"
                    >


                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Book Code</th>

                                <th>Book</th>

                                <th>Call Number</th>

                                <th>Sublocation</th>

                                <th>Publisher</th>

                                <th>Year</th>

                                <th>ISBN</th>

                                <th>Subject</th>

                                <th width="150">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            @forelse($books as $book)


                                <tr>


                                    <!-- NUMBER -->

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    <!-- BOOK CODE -->

                                    <td>

                                        <span
                                            class="
                                                badge
                                                badge-dark
                                                book-code
                                            "
                                        >

                                            {{ $book->unique_key ?? '-' }}

                                        </span>

                                    </td>


                                    <!-- BOOK -->

                                    <td style="min-width:250px;">

                                        <div class="book-title">

                                            {{ $book->title ?? 'No Title' }}

                                        </div>

                                        <div class="book-author">

                                            <i class="fa fa-user mr-1"></i>

                                            {{ $book->author ?? 'Unknown Author' }}

                                        </div>

                                    </td>


                                    <!-- CALL NUMBER -->

                                    <td>

                                        {{ $book->call_number ?? '-' }}

                                    </td>


                                    <!-- SUBLOCATION -->

                                    <td>

                                        {{ $book->sublocation ?? '-' }}

                                    </td>


                                    <!-- PUBLISHER -->

                                    <td>

                                        {{
                                            $book->publisher
                                                ? \Illuminate\Support\Str::limit(
                                                    $book->publisher,
                                                    30
                                                )
                                                : '-'
                                        }}

                                    </td>


                                    <!-- YEAR -->

                                    <td>

                                        {{ $book->year ?? '-' }}

                                    </td>


                                    <!-- ISBN -->

                                    <td>

                                        {{
                                            $book->isbn
                                                ? \Illuminate\Support\Str::limit(
                                                    $book->isbn,
                                                    25
                                                )
                                                : '-'
                                        }}

                                    </td>


                                    <!-- SUBJECT -->

                                    <td>

                                        {{
                                            $book->subjects
                                                ? \Illuminate\Support\Str::limit(
                                                    $book->subjects,
                                                    35
                                                )
                                                : '-'
                                        }}

                                    </td>


                                    <!-- =================================================
                                         ACTIONS
                                    ================================================== -->

                                    <td>


                                        <!-- =================================================
                                             VIEW
                                             ADMIN + STAFF
                                        ================================================== -->

                                        <button
                                            type="button"
                                            class="
                                                btn
                                                btn-info
                                                action-btn
                                            "
                                            title="View"
                                            data-toggle="modal"
                                            data-target="#viewBook{{ $book->id }}"
                                        >

                                            <i class="fa fa-eye"></i>

                                        </button>


                                        <!-- =================================================
                                             ADMIN ONLY
                                        ================================================== -->

                                        @if(
                                            auth()->check() &&
                                            auth()->user()->access_level === 'admin'
                                        )


                                            <!-- EDIT -->

                                            <button
                                                type="button"
                                                class="
                                                    btn
                                                    btn-warning
                                                    action-btn
                                                "
                                                title="Edit"
                                                data-toggle="modal"
                                                data-target="#editBook{{ $book->id }}"
                                            >

                                                <i class="fa fa-pencil"></i>

                                            </button>


                                            <!-- DELETE -->

                                            <form
                                                action="{{ route('books.destroy', $book->id) }}"
                                                method="POST"
                                                class="d-inline"
                                            >

                                                @csrf

                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    class="
                                                        btn
                                                        btn-danger
                                                        action-btn
                                                    "
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this book?')"
                                                >

                                                    <i class="fa fa-trash"></i>

                                                </button>

                                            </form>


                                        @endif


                                    </td>

                                </tr>


                                <!-- =================================================
                                     VIEW MODAL
                                     ADMIN + STAFF
                                ================================================== -->

                                <div
                                    class="modal fade"
                                    id="viewBook{{ $book->id }}"
                                    tabindex="-1"
                                    role="dialog"
                                >


                                    <div
                                        class="
                                            modal-dialog
                                            modal-lg
                                        "
                                    >


                                        <div class="modal-content">


                                            <div class="modal-header">


                                                <h5 class="modal-title">

                                                    <i class="fa fa-book mr-2"></i>

                                                    Book Information

                                                </h5>


                                                <button
                                                    type="button"
                                                    class="close"
                                                    data-dismiss="modal"
                                                >

                                                    <span>&times;</span>

                                                </button>

                                            </div>


                                            <div class="modal-body">


                                                <div class="row">


                                                    <!-- BOOK CODE -->

                                                    <div class="col-md-12 mb-3">

                                                        <strong>
                                                            Book Code
                                                        </strong>

                                                        <p>

                                                            <span class="badge badge-dark">

                                                                {{ $book->unique_key ?? '-' }}

                                                            </span>

                                                        </p>

                                                    </div>


                                                    <!-- TITLE -->

                                                    <div class="col-md-12 mb-3">

                                                        <small class="text-muted">
                                                            Title
                                                        </small>

                                                        <h4>
                                                            {{ $book->title ?? 'No Title' }}
                                                        </h4>

                                                    </div>


                                                    <!-- AUTHOR -->

                                                    <div class="col-md-6 mb-3">

                                                        <strong>
                                                            Author
                                                        </strong>

                                                        <p>
                                                            {{ $book->author ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- CALL NUMBER -->

                                                    <div class="col-md-3 mb-3">

                                                        <strong>
                                                            Call Number
                                                        </strong>

                                                        <p>
                                                            {{ $book->call_number ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- SUBLOCATION -->

                                                    <div class="col-md-3 mb-3">

                                                        <strong>
                                                            Sublocation
                                                        </strong>

                                                        <p>
                                                            {{ $book->sublocation ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- PUBLISHER -->

                                                    <div class="col-md-6 mb-3">

                                                        <strong>
                                                            Publisher
                                                        </strong>

                                                        <p>
                                                            {{ $book->publisher ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- YEAR -->

                                                    <div class="col-md-3 mb-3">

                                                        <strong>
                                                            Year
                                                        </strong>

                                                        <p>
                                                            {{ $book->year ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- EDITION -->

                                                    <div class="col-md-3 mb-3">

                                                        <strong>
                                                            Edition
                                                        </strong>

                                                        <p>
                                                            {{ $book->edition ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- FORMAT -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            Format
                                                        </strong>

                                                        <p>
                                                            {{ $book->format ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- CONTENT -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            Content Type
                                                        </strong>

                                                        <p>
                                                            {{ $book->content_type ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- MEDIA -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            Media Type
                                                        </strong>

                                                        <p>
                                                            {{ $book->media_type ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- CARRIER -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            Carrier Type
                                                        </strong>

                                                        <p>
                                                            {{ $book->carrier_type ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- ISBN -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            ISBN
                                                        </strong>

                                                        <p>
                                                            {{ $book->isbn ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- ISSN -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            ISSN
                                                        </strong>

                                                        <p>
                                                            {{ $book->issn ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- LCCN -->

                                                    <div class="col-md-4 mb-3">

                                                        <strong>
                                                            LCCN
                                                        </strong>

                                                        <p>
                                                            {{ $book->lccn ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- SUBJECTS -->

                                                    <div class="col-md-12 mb-3">

                                                        <strong>
                                                            Subjects
                                                        </strong>

                                                        <p>
                                                            {{ $book->subjects ?? '-' }}
                                                        </p>

                                                    </div>


                                                    <!-- DETAILS -->

                                                    <div class="col-md-12">

                                                        <strong>
                                                            Additional Details
                                                        </strong>

                                                        <div
                                                            class="
                                                                bg-light
                                                                p-3
                                                                rounded
                                                                mt-2
                                                            "
                                                        >

                                                            {!! nl2br(
                                                                e(
                                                                    $book->additional_details
                                                                    ?? '-'
                                                                )
                                                            ) !!}

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

                                                    Close

                                                </button>

                                            </div>


                                        </div>

                                    </div>

                                </div>


                                <!-- =================================================
                                     EDIT MODAL
                                     ADMIN ONLY
                                ================================================== -->

                                @if(
                                    auth()->check() &&
                                    auth()->user()->access_level === 'admin'
                                )


                                    <div
                                        class="modal fade"
                                        id="editBook{{ $book->id }}"
                                        tabindex="-1"
                                        role="dialog"
                                    >


                                        <div
                                            class="
                                                modal-dialog
                                                modal-xl
                                            "
                                        >


                                            <div class="modal-content">


                                                <form
                                                    action="{{ route('books.update', $book->id) }}"
                                                    method="POST"
                                                >

                                                    @csrf

                                                    @method('PUT')


                                                    <div class="modal-header">


                                                        <h5 class="modal-title">

                                                            <i class="fa fa-pencil mr-2"></i>

                                                            Edit Book

                                                        </h5>


                                                        <button
                                                            type="button"
                                                            class="close"
                                                            data-dismiss="modal"
                                                        >

                                                            <span>&times;</span>

                                                        </button>

                                                    </div>


                                                    <div class="modal-body">


                                                        <div class="row">


                                                            <!-- TITLE -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>

                                                                    Title

                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>

                                                                </label>

                                                                <textarea
                                                                    name="title"
                                                                    class="form-control"
                                                                    required
                                                                >{{ $book->title }}</textarea>

                                                            </div>


                                                            <!-- AUTHOR -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>
                                                                    Author
                                                                </label>

                                                                <textarea
                                                                    name="author"
                                                                    class="form-control"
                                                                >{{ $book->author }}</textarea>

                                                            </div>


                                                            <!-- CALL NUMBER -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    Call Number
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="call_number"
                                                                    value="{{ $book->call_number }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- SUBLOCATION -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    Sublocation
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="sublocation"
                                                                    value="{{ $book->sublocation }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- YEAR -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    Year
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="year"
                                                                    value="{{ $book->year }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- PUBLISHER -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>
                                                                    Publisher
                                                                </label>

                                                                <textarea
                                                                    name="publisher"
                                                                    class="form-control"
                                                                >{{ $book->publisher }}</textarea>

                                                            </div>


                                                            <!-- FORMAT -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>
                                                                    Format
                                                                </label>

                                                                <textarea
                                                                    name="format"
                                                                    class="form-control"
                                                                >{{ $book->format }}</textarea>

                                                            </div>


                                                            <!-- EDITION -->

                                                            <div class="col-lg-3 mb-3">

                                                                <label>
                                                                    Edition
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="edition"
                                                                    value="{{ $book->edition }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- CONTENT TYPE -->

                                                            <div class="col-lg-3 mb-3">

                                                                <label>
                                                                    Content Type
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="content_type"
                                                                    value="{{ $book->content_type }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- MEDIA TYPE -->

                                                            <div class="col-lg-3 mb-3">

                                                                <label>
                                                                    Media Type
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="media_type"
                                                                    value="{{ $book->media_type }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- CARRIER TYPE -->

                                                            <div class="col-lg-3 mb-3">

                                                                <label>
                                                                    Carrier Type
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="carrier_type"
                                                                    value="{{ $book->carrier_type }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- ISBN -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    ISBN
                                                                </label>

                                                                <textarea
                                                                    name="isbn"
                                                                    class="form-control"
                                                                >{{ $book->isbn }}</textarea>

                                                            </div>


                                                            <!-- ISSN -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    ISSN
                                                                </label>

                                                                <textarea
                                                                    name="issn"
                                                                    class="form-control"
                                                                >{{ $book->issn }}</textarea>

                                                            </div>


                                                            <!-- LCCN -->

                                                            <div class="col-lg-4 mb-3">

                                                                <label>
                                                                    LCCN
                                                                </label>

                                                                <input
                                                                    type="text"
                                                                    name="lccn"
                                                                    value="{{ $book->lccn }}"
                                                                    class="form-control"
                                                                >

                                                            </div>


                                                            <!-- SUBJECTS -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>
                                                                    Subjects
                                                                </label>

                                                                <textarea
                                                                    name="subjects"
                                                                    rows="4"
                                                                    class="form-control"
                                                                >{{ $book->subjects }}</textarea>

                                                            </div>


                                                            <!-- DETAILS -->

                                                            <div class="col-lg-6 mb-3">

                                                                <label>
                                                                    Additional Details
                                                                </label>

                                                                <textarea
                                                                    name="additional_details"
                                                                    rows="4"
                                                                    class="form-control"
                                                                >{{ $book->additional_details }}</textarea>

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


                            @empty


                                <tr>

                                    <td
                                        colspan="10"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="fa fa-book"
                                            style="
                                                font-size:45px;
                                                color:#ccc;
                                            "
                                        ></i>

                                        <h5 class="mt-3">

                                            No books found

                                        </h5>

                                        <p class="text-muted">

                                            Add your first book
                                            to the library database.

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

<!--**********************************
    Content body end
***********************************-->


@include('layouts.footer')


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

        $('#booksTable').DataTable({

            pageLength: 10,

            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],

            order: [
                [0, 'desc']
            ]

        });

    });

</script>