@include('layouts.header')
@include('layouts.css')

<link
    href="{{ asset('assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}"
    rel="stylesheet"
>

<style>
    .summary-card,
    .borrow-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
    }

    .summary-number {
        margin-bottom: 0;
        font-size: 32px;
        font-weight: 700;
    }

    .borrower-details {
        font-size: 12px;
        color: #777;
    }

    .refresh-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #777;
        font-size: 12px;
    }

    .refresh-indicator {
        width: 8px;
        height: 8px;
        display: inline-block;
        background: #28a745;
        border-radius: 50%;
    }

    .table td,
    .table th {
        vertical-align: middle !important;
    }
</style>

@include('layouts.top_navbar')
@include('layouts.left_sidebar')

<div class="content-body">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>
                        Borrowed Books/Returned Books
                    </h4>

                    <span>
                        Monitor borrowed books, returned books and deadlines
                    </span>
                </div>
            </div>

            <div
                class="col-sm-6 p-md-0 justify-content-sm-end
                       mt-2 mt-sm-0 d-flex"
            >
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        Borrowed Books
                    </li>
                </ol>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div
                class="alert alert-success
                       alert-dismissible fade show"
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

        {{-- ERROR MESSAGE --}}
        @if(session('error'))
            <div
                class="alert alert-danger
                       alert-dismissible fade show"
            >
                <i class="fa fa-exclamation-circle mr-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                >
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- SUMMARY CARDS --}}
        <div class="row">

            {{-- TOTAL BORROWED --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Total Borrowed</h5>

                        <h2
                            id="totalBorrowed"
                            class="summary-number"
                        >
                            {{
                                $borrows
                                    ->where(
                                        'status',
                                        'borrowed'
                                    )
                                    ->count()
                            }}
                        </h2>

                        <span class="text-muted">
                            Currently borrowed books
                        </span>
                    </div>
                </div>
            </div>

            {{-- OVERDUE --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Overdue</h5>

                        <h2
                            id="totalOverdue"
                            class="summary-number text-danger"
                        >
                            {{
                                $borrows->filter(
                                    function ($borrow) {
                                        return
                                            $borrow->status
                                                !== 'returned'
                                            &&
                                            \Carbon\Carbon::parse(
                                                $borrow->due_date
                                            )->isPast();
                                    }
                                )->count()
                            }}
                        </h2>

                        <span class="text-muted">
                            Books past their deadline
                        </span>
                    </div>
                </div>
            </div>

            {{-- RETURNED --}}
            <div class="col-lg-4 col-sm-6">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5>Returned</h5>

                        <h2
                            id="totalReturned"
                            class="summary-number text-success"
                        >
                            {{
                                $borrows
                                    ->where(
                                        'status',
                                        'returned'
                                    )
                                    ->count()
                            }}
                        </h2>

                        <span class="text-muted">
                            Successfully returned books
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- BORROWED BOOKS TABLE --}}
        <div class="card borrow-card">
            <div class="card-header">
                <div
                    class="row w-100 align-items-center"
                >
                    <div class="col-md-6">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-book mr-2"></i>

                            Borrowed Book Records
                        </h4>
                    </div>

                    <div class="col-md-6 text-md-right">
                        <div class="refresh-status">
                    

        
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table
                        id="borrowTable"
                        class="table table-striped table-hover"
                    >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book</th>
                                <th>Borrower</th>
                                <th>Date Borrowed</th>
                                <th>Deadline</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($borrows as $borrow)
                                @php
                                    $today =
                                        \Carbon\Carbon::today();

                                    $deadline =
                                        \Carbon\Carbon::parse(
                                            $borrow->due_date
                                        );

                                    $isOverdue =
                                        $borrow->status
                                            !== 'returned'
                                        &&
                                        $deadline->isPast();

                                    $days =
                                        $today->diffInDays(
                                            $deadline,
                                            false
                                        );
                                @endphp

                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{
                                                $borrow->book->title
                                                ?? 'Unknown Book'
                                            }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{
                                                $borrow->book->author
                                                ?? ''
                                            }}
                                        </small>

                                        <br>

                                        <small>
                                            Call No:

                                            {{
                                                $borrow->book->call_number
                                                ?? '-'
                                            }}
                                        </small>
                                    </td>

                                    <td>
                                        @if($borrow->borrower_record)
                                            {{
                                                trim(
                                                    (
                                                        $borrow
                                                            ->borrower_record
                                                            ->firstname
                                                        ?? ''
                                                    )
                                                    .
                                                    ' '
                                                    .
                                                    (
                                                        $borrow
                                                            ->borrower_record
                                                            ->lastname
                                                        ?? ''
                                                    )
                                                )
                                            }}

                                            <br>

                                            <span class="borrower-details">
                                                {{
                                                    ucfirst(
                                                        $borrow
                                                            ->borrower_type
                                                    )
                                                }}

                                                -

                                                @if(
                                                    $borrow->borrower_type
                                                    === 'student'
                                                )
                                                    {{
                                                        $borrow
                                                            ->borrower_record
                                                            ->student_number
                                                        ?? '-'
                                                    }}
                                                @else
                                                    {{
                                                        $borrow
                                                            ->borrower_record
                                                            ->employee_number
                                                        ?? '-'
                                                    }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                Unknown Borrower
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $borrow->borrowed_at
                                            )->format('M d, Y')
                                        }}
                                    </td>

                                    <td>
                                        <strong
                                            class="{{
                                                $isOverdue
                                                    ? 'text-danger'
                                                    : ''
                                            }}"
                                        >
                                            {{
                                                $deadline->format(
                                                    'M d, Y'
                                                )
                                            }}
                                        </strong>
                                    </td>

                                    <td>
                                        @if(
                                            $borrow->status
                                            === 'returned'
                                        )
                                            <span class="text-muted">
                                                Completed
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="text-danger">
                                                {{ abs($days) }}
                                                day(s) overdue
                                            </span>
                                        @elseif($days == 0)
                                            <span class="text-warning">
                                                Due Today
                                            </span>
                                        @else
                                            <span class="text-success">
                                                {{ $days }}
                                                day(s) remaining
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if(
                                            $borrow->status
                                            === 'returned'
                                        )
                                            <span
                                                class="badge badge-success"
                                            >
                                                Returned
                                            </span>
                                        @elseif($isOverdue)
                                            <span
                                                class="badge badge-danger"
                                            >
                                                Overdue
                                            </span>
                                        @else
                                            <span
                                                class="badge badge-warning"
                                            >
                                                Borrowed
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if(
                                            $borrow->status
                                            !== 'returned'
                                        )
                                            <form
                                                action="{{
                                                    route(
                                                        'bookborrow.return',
                                                        $borrow->id
                                                    )
                                                }}"
                                                method="POST"
                                                class="return-book-form"
                                            >
                                                @csrf
                                                @method('PUT')

                                                <button
                                                    type="submit"
                                                    class="btn btn-success btn-sm"
                                                >
                                                    <i class="fa fa-check"></i>
                                                    Return
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success">
                                                Returned
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="8"
                                        class="text-center py-4"
                                    >
                                        No borrowing records found.
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

<script src="{{ asset('assets/plugins/tables/js/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('assets/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function () {

    const csrfToken = @json(csrf_token());

    const returnRouteTemplate = @json(
        route(
            'bookborrow.return',
            ['id' => '__BORROW_ID__']
        )
    );

    let ajaxIsRunning = false;
    let formIsSubmitting = false;

    const table = $('#borrowTable').DataTable({
        pageLength: 10,

        order: [
            [4, 'asc']
        ],

        stateSave: true,

        language: {
            emptyTable:
                'No borrowing records found.'
        }
    });

    function escapeHtml(value) {
        return $('<div>')
            .text(value ?? '')
            .html();
    }

    function createBookColumn(record) {
        return `
            <strong>
                ${escapeHtml(record.book_title)}
            </strong>

            <br>

            <small class="text-muted">
                ${escapeHtml(record.book_author)}
            </small>

            <br>

            <small>
                Call No:
                ${escapeHtml(record.call_number)}
            </small>
        `;
    }

    function createBorrowerColumn(record) {
        return `
            ${escapeHtml(record.borrower_name)}

            <br>

            <span class="borrower-details">
                ${escapeHtml(record.borrower_type)}
                -
                ${escapeHtml(record.borrower_number)}
            </span>
        `;
    }

    function createDeadlineColumn(record) {
        const deadlineClass =
            record.is_overdue
                ? 'text-danger'
                : '';

        return `
            <strong class="${deadlineClass}">
                ${escapeHtml(record.deadline)}
            </strong>
        `;
    }

    function createDaysColumn(record) {
        if (record.status === 'returned') {
            return `
                <span class="text-muted">
                    Completed
                </span>
            `;
        }

        if (record.is_overdue) {
            return `
                <span class="text-danger">
                    ${Math.abs(record.days)}
                    day(s) overdue
                </span>
            `;
        }

        if (Number(record.days) === 0) {
            return `
                <span class="text-warning">
                    Due Today
                </span>
            `;
        }

        return `
            <span class="text-success">
                ${record.days}
                day(s) remaining
            </span>
        `;
    }

    function createStatusColumn(record) {
        if (record.status === 'returned') {
            return `
                <span class="badge badge-success">
                    Returned
                </span>
            `;
        }

        if (record.is_overdue) {
            return `
                <span class="badge badge-danger">
                    Overdue
                </span>
            `;
        }

        return `
            <span class="badge badge-warning">
                Borrowed
            </span>
        `;
    }

    function createActionColumn(record) {
        if (record.status === 'returned') {
            return `
                <span class="text-success">
                    Returned
                </span>
            `;
        }

        const returnUrl =
            returnRouteTemplate.replace(
                '__BORROW_ID__',
                record.id
            );

        return `
            <form
                action="${returnUrl}"
                method="POST"
                class="return-book-form"
            >
                <input
                    type="hidden"
                    name="_token"
                    value="${csrfToken}"
                >

                <input
                    type="hidden"
                    name="_method"
                    value="PUT"
                >

                <button
                    type="submit"
                    class="btn btn-success btn-sm"
                >
                    <i class="fa fa-check"></i>
                    Return
                </button>
            </form>
        `;
    }

    async function refreshBorrowTable() {
        if (
            ajaxIsRunning ||
            formIsSubmitting
        ) {
            return;
        }

        ajaxIsRunning = true;

        try {
            const response = await fetch(
                @json(route('bookborrow.data')),
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'
                    },

                    cache: 'no-store'
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Unable to retrieve borrowing records.'
                );
            }

            const data =
                await response.json();

            const currentPage =
                table.page();

            const currentSearch =
                table.search();

            const rows = data.records.map(
                function (record, index) {
                    return [
                        index + 1,

                        createBookColumn(
                            record
                        ),

                        createBorrowerColumn(
                            record
                        ),

                        escapeHtml(
                            record.borrowed_at
                        ),

                        createDeadlineColumn(
                            record
                        ),

                        createDaysColumn(
                            record
                        ),

                        createStatusColumn(
                            record
                        ),

                        createActionColumn(
                            record
                        )
                    ];
                }
            );

            /*
             * Silently replace only table data.
             */
            table.clear();
            table.rows.add(rows);

            table.search(
                currentSearch
            );

            table.draw(false);

            /*
             * Preserve the current page.
             */
            const pageInformation =
                table.page.info();

            if (
                currentPage
                < pageInformation.pages
            ) {
                table.page(
                    currentPage
                ).draw(false);
            }

            /*
             * Silently update summary numbers.
             */
            $('#totalBorrowed').text(
                data.summary.borrowed
            );

            $('#totalOverdue').text(
                data.summary.overdue
            );

            $('#totalReturned').text(
                data.summary.returned
            );

        } catch (error) {
            /*
             * Keep errors hidden from the page.
             * Developers can still see them in
             * the browser console.
             */
            console.error(
                'Borrow table refresh error:',
                error
            );
        } finally {
            ajaxIsRunning = false;
        }
    }

    /*
     * Handle dynamically generated return forms.
     */
    $('#borrowTable tbody').on(
        'submit',
        '.return-book-form',
        function (event) {
            const confirmed =
                window.confirm(
                    'Mark this book as returned?'
                );

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            formIsSubmitting = true;

            $(this)
                .find(
                    'button[type="submit"]'
                )
                .prop('disabled', true)
                .html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...'
                );
        }
    );

    /*
     * Load fresh table data immediately.
     */
    refreshBorrowTable();

    /*
     * Silently update only the table and
     * summary numbers every three seconds.
     */
    setInterval(
        refreshBorrowTable,
        3000
    );

});
</script>