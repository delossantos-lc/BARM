<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\BookFine;
use App\Models\BookReservation;
use App\Models\FinePayment;
use App\Models\Personnel;
use App\Models\Student;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'report_type' => [
                'nullable',
                'in:attendance,borrowing,return,overdue,reservation,inventory,fine_collection,lost_damaged,most_borrowed,active_users',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'course' => [
                'nullable',
                'string',
            ],

            'user_type' => [
                'nullable',
                'in:student,personnel',
            ],

            'book_status' => [
                'nullable',
                'string',
            ],

            'generated' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | DEFAULT REPORT
        |--------------------------------------------------------------------------
        |
        | Attendance loads automatically when opening the page.
        |
        */

        $reportType = $request->input(
            'report_type',
            'attendance'
        );

        $reportGenerated = $request->boolean('generated');

        // Do not execute any report query when the page first opens. Reports
        // are generated only after the user submits the filter form.
        $report = $reportGenerated ? match ($reportType) {
            'attendance' =>
                $this->attendanceReport($request),

            'borrowing' =>
                $this->borrowingReport($request),

            'return' =>
                $this->returnReport($request),

            'overdue' =>
                $this->overdueReport($request),

            'reservation' =>
                $this->reservationReport($request),

            'inventory' =>
                $this->inventoryReport($request),

            'fine_collection' =>
                $this->fineCollectionReport($request),

            'lost_damaged' =>
                $this->lostDamagedReport($request),

            'most_borrowed' =>
                $this->mostBorrowedReport($request),

            'active_users' =>
                $this->mostActiveUsersReport($request),

            default =>
                $this->attendanceReport($request),
        } : [
            'columns' => [],
            'rows' => collect(),
            'title' => 'Select a report to begin',
            'summary' => [],
        ];

        $columns =
            $report['columns'];

        $rows =
            $report['rows'];

        $reportTitle =
            $report['title'];

        $summary =
            $report['summary'];

        $courses = [
            'BSA',
            'BSAIS',
            'BSIT',
            'BSIS',
            'BSN',
            'BSND',
            'BSPHARM',
            'BACOMM',
            'BAEL',
            'BLIS',
            'BM',
            'BSPSYCH',
            'BSBA',
            'BSHM',
            'BSTM',
            'BSSW',
            'BCAED',
            'BECED',
            'BEED',
            'BTLED',
            'BSED',
        ];

        return view(
            'report',
            compact(
                'reportType',
                'columns',
                'rows',
                'reportTitle',
                'summary',
                'courses',
                'reportGenerated'
            )
        );
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'report_type' => [
                'required',
                'in:attendance,borrowing,return,overdue,reservation,inventory,fine_collection,lost_damaged,most_borrowed,active_users',
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'course' => ['nullable', 'string'],
            'user_type' => ['nullable', 'in:student,personnel'],
            'book_status' => ['nullable', 'string'],
        ]);

        $reportType = $request->input('report_type');

        $report = match ($reportType) {
            'attendance' => $this->attendanceReport($request),
            'borrowing' => $this->borrowingReport($request),
            'return' => $this->returnReport($request),
            'overdue' => $this->overdueReport($request),
            'reservation' => $this->reservationReport($request),
            'inventory' => $this->inventoryReport($request),
            'fine_collection' => $this->fineCollectionReport($request),
            'lost_damaged' => $this->lostDamagedReport($request),
            'most_borrowed' => $this->mostBorrowedReport($request),
            'active_users' => $this->mostActiveUsersReport($request),
        };

        $columns = $report['columns'];
        $rows = $report['rows'];
        $reportTitle = $report['title'];
        $summary = $report['summary'];
        $filters = $request->only([
            'start_date',
            'end_date',
            'course',
            'user_type',
            'book_status',
        ]);

        $filename = str($reportTitle)
            ->slug()
            ->append('-', now()->format('Y-m-d-His'), '.pdf')
            ->toString();

        return Pdf::loadView(
            'report_pdf',
            compact(
                'columns',
                'rows',
                'reportTitle',
                'summary',
                'filters'
            )
        )
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE REPORT
    |--------------------------------------------------------------------------
    */

    private function attendanceReport(
        Request $request
    ): array {
        $query =
            Attendance::query()
                ->orderByDesc('date')
                ->orderByDesc('time_in');

        $this->applyDateFilter(
            $query,
            $request,
            'date'
        );

        $records =
            $query->get();

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $records =
                $records
                    ->filter(
                        function (
                            $attendance
                        ) use (
                            $request
                        ) {
                            $user =
                                $this
                                    ->resolveAttendanceUser(
                                        $attendance
                                    );

                            if (!$user) {
                                return false;
                            }

                            if (
                                $request
                                    ->user_type
                                === 'student'
                            ) {
                                return
                                    $user
                                    instanceof Student;
                            }

                            return
                                $user
                                instanceof Personnel;
                        }
                    )
                    ->values();
        }

        if (
            $request->filled(
                'course'
            )
        ) {
            $records =
                $records
                    ->filter(
                        function (
                            $attendance
                        ) use (
                            $request
                        ) {
                            $user =
                                $this
                                    ->resolveAttendanceUser(
                                        $attendance
                                    );

                            if (
                                !$user
                                ||
                                !(
                                    $user
                                    instanceof Student
                                )
                            ) {
                                return false;
                            }

                            return (
                                $user
                                    ->course_program
                                ?? null
                            )
                            ===
                            $request->course;
                        }
                    )
                    ->values();
        }

        $rows =
            $records
                ->map(
                    function (
                        $attendance
                    ) {
                        $user =
                            $this
                                ->resolveAttendanceUser(
                                    $attendance
                                );

                        if (!$user) {
                            return [
                                'User ID' =>
                                    '-',

                                'Name' =>
                                    'Unknown User',

                                'User Type' =>
                                    '-',

                                'Course / Program' =>
                                    '-',

                                'Date' =>
                                    $this
                                        ->formatDate(
                                            $attendance
                                                ->date
                                        ),

                                'Time In' =>
                                    $this
                                        ->formatTime(
                                            $attendance
                                                ->time_in
                                        ),

                                'Time Out' =>
                                    $this
                                        ->formatTime(
                                            $attendance
                                                ->time_out
                                        ),
                            ];
                        }

                        $isStudent =
                            $user
                            instanceof Student;

                        $number =
                            $isStudent
                                ? (
                                    $user
                                        ->student_number
                                    ?? '-'
                                )
                                : (
                                    $user
                                        ->employee_number
                                    ?? '-'
                                );

                        return [
                            'User ID' =>
                                $number,

                            'Name' =>
                                trim(
                                    (
                                        $user
                                            ->firstname
                                        ?? ''
                                    )
                                    . ' '
                                    . (
                                        $user
                                            ->lastname
                                        ?? ''
                                    )
                                )
                                ?: 'Unknown User',

                            'User Type' =>
                                $isStudent
                                    ? 'Student'
                                    : 'Personnel',

                            'Course / Program' =>
                                $isStudent
                                    ? (
                                        $user
                                            ->course_program
                                        ?? '-'
                                    )
                                    : '-',

                            'Date' =>
                                $this
                                    ->formatDate(
                                        $attendance
                                            ->date
                                    ),

                            'Time In' =>
                                $this
                                    ->formatTime(
                                        $attendance
                                            ->time_in
                                    ),

                            'Time Out' =>
                                $this
                                    ->formatTime(
                                        $attendance
                                            ->time_out
                                    ),
                        ];
                    }
                )
                ->values();

        $studentCount =
            $records
                ->filter(
                    fn ($attendance) =>
                        $this->resolveAttendanceUser($attendance)
                        instanceof Student
                )
                ->count();

        $personnelCount =
            $records
                ->filter(
                    fn ($attendance) =>
                        $this->resolveAttendanceUser($attendance)
                        instanceof Personnel
                )
                ->count();

        return [
            'title' =>
                'Attendance Report',

            'columns' => [
                'User ID',
                'Name',
                'User Type',
                'Course / Program',
                'Date',
                'Time In',
                'Time Out',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Total Attendance',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Students',

                    'value' =>
                        $studentCount,

                    'class' =>
                        'text-primary',
                ],

                [
                    'label' =>
                        'Personnel',

                    'value' =>
                        $personnelCount,

                    'class' =>
                        'text-success',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWING REPORT
    |--------------------------------------------------------------------------
    */

    private function borrowingReport(
        Request $request
    ): array {
        $query =
            BookBorrow::with([
                'book',
                'bookCopy',
                'fines',
            ])
                ->orderByDesc(
                    'borrowed_at'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'borrowed_at'
        );

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $query->where(
                'borrower_type',
                $request->user_type
            );
        }

        if (
            $request->filled(
                'book_status'
            )
        ) {
            switch (
                $request->book_status
            ) {
                case 'borrowed':
                    $query
                        ->whereNull(
                            'returned_at'
                        )
                        ->where(
                            function (
                                $q
                            ) {
                                $q
                                    ->whereNull(
                                        'due_date'
                                    )
                                    ->orWhereDate(
                                        'due_date',
                                        '>=',
                                        today()
                                    );
                            }
                        );
                    break;

                case 'returned':
                    $query
                        ->whereNotNull(
                            'returned_at'
                        );
                    break;

                case 'overdue':
                    $query
                        ->whereNull(
                            'returned_at'
                        )
                        ->whereNotNull(
                            'due_date'
                        )
                        ->whereDate(
                            'due_date',
                            '<',
                            today()
                        );
                    break;
            }
        }

        $records =
            $query->get();

        $records =
            $this
                ->filterBorrowRecordsByCourse(
                    $records,
                    $request
                );

        $rows =
            $records
                ->map(
                    function (
                        $borrow
                    ) {
                        $borrower =
                            $borrow
                                ->borrower_record;

                        return [
                            'Borrower ID' =>
                                $borrow
                                    ->borrower_number,

                            'Borrower' =>
                                $borrow
                                    ->borrower_name,

                            'User Type' =>
                                ucfirst(
                                    $borrow
                                        ->borrower_type
                                ),

                            'Course / Program' =>
                                $borrower
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $borrow
                                    ->book
                                    ->title
                                ?? '-',

                            'Call Number' =>
                                $borrow
                                    ->book
                                    ->call_number
                                ?? '-',

                            'Borrowed Date' =>
                                $this
                                    ->formatDateTime(
                                        $borrow
                                            ->borrowed_at
                                    ),

                            'Due Date' =>
                                $this
                                    ->formatDate(
                                        $borrow
                                            ->due_date
                                    ),

                            'Returned Date' =>
                                $this
                                    ->formatDateTime(
                                        $borrow
                                            ->returned_at
                                    ),

                            'Status' =>
                                $this
                                    ->getBorrowStatus(
                                        $borrow
                                    ),
                        ];
                    }
                )
                ->values();

        $borrowedCount =
            $records
                ->filter(
                    function (
                        $borrow
                    ) {
                        return
                            !$borrow
                                ->returned_at
                            &&
                            (
                                !$borrow
                                    ->due_date
                                ||
                                !$borrow
                                    ->due_date
                                    ->isPast()
                            );
                    }
                )
                ->count();

        $returnedCount =
            $records
                ->whereNotNull(
                    'returned_at'
                )
                ->count();

        $overdueCount =
            $records
                ->filter(
                    function (
                        $borrow
                    ) {
                        return
                            !$borrow
                                ->returned_at
                            &&
                            $borrow
                                ->due_date
                            &&
                            $borrow
                                ->due_date
                                ->isPast();
                    }
                )
                ->count();

        return [
            'title' =>
                'Borrowing Report',

            'columns' => [
                'Borrower ID',
                'Borrower',
                'User Type',
                'Course / Program',
                'Book',
                'Call Number',
                'Borrowed Date',
                'Due Date',
                'Returned Date',
                'Status',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Transactions',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Currently Borrowed',

                    'value' =>
                        $borrowedCount,

                    'class' =>
                        'text-primary',
                ],

                [
                    'label' =>
                        'Returned',

                    'value' =>
                        $returnedCount,

                    'class' =>
                        'text-success',
                ],

                [
                    'label' =>
                        'Overdue',

                    'value' =>
                        $overdueCount,

                    'class' =>
                        'text-danger',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN REPORT
    |--------------------------------------------------------------------------
    */

    private function returnReport(
        Request $request
    ): array {
        $query =
            BookBorrow::with([
                'book',
                'fines',
            ])
                ->whereNotNull(
                    'returned_at'
                )
                ->orderByDesc(
                    'returned_at'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'returned_at'
        );

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $query->where(
                'borrower_type',
                $request->user_type
            );
        }

        $records =
            $query->get();

        $records =
            $this
                ->filterBorrowRecordsByCourse(
                    $records,
                    $request
                );

        $lateCount = 0;

        $rows =
            $records
                ->map(
                    function (
                        $borrow
                    ) use (
                        &$lateCount
                    ) {
                        $borrower =
                            $borrow
                                ->borrower_record;

                        $wasLate =
                            $borrow
                                ->due_date
                            &&
                            $borrow
                                ->returned_at
                            &&
                            $borrow
                                ->returned_at
                                ->greaterThan(
                                    $borrow
                                        ->due_date
                                );

                        if (
                            $wasLate
                        ) {
                            $lateCount++;
                        }

                        return [
                            'Borrower ID' =>
                                $borrow
                                    ->borrower_number,

                            'Borrower' =>
                                $borrow
                                    ->borrower_name,

                            'User Type' =>
                                ucfirst(
                                    $borrow
                                        ->borrower_type
                                ),

                            'Course / Program' =>
                                $borrower
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $borrow
                                    ->book
                                    ->title
                                ?? '-',

                            'Call Number' =>
                                $borrow
                                    ->book
                                    ->call_number
                                ?? '-',

                            'Borrowed Date' =>
                                $this
                                    ->formatDateTime(
                                        $borrow
                                            ->borrowed_at
                                    ),

                            'Due Date' =>
                                $this
                                    ->formatDate(
                                        $borrow
                                            ->due_date
                                    ),

                            'Returned Date' =>
                                $this
                                    ->formatDateTime(
                                        $borrow
                                            ->returned_at
                                    ),

                            'Return Status' =>
                                $wasLate
                                    ? 'Returned Late'
                                    : 'Returned On Time',
                        ];
                    }
                )
                ->values();

        return [
            'title' =>
                'Return Report',

            'columns' => [
                'Borrower ID',
                'Borrower',
                'User Type',
                'Course / Program',
                'Book',
                'Call Number',
                'Borrowed Date',
                'Due Date',
                'Returned Date',
                'Return Status',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Total Returns',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'On Time',

                    'value' =>
                        $rows->count()
                        - $lateCount,

                    'class' =>
                        'text-success',
                ],

                [
                    'label' =>
                        'Returned Late',

                    'value' =>
                        $lateCount,

                    'class' =>
                        'text-danger',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OVERDUE REPORT
    |--------------------------------------------------------------------------
    */

    private function overdueReport(
        Request $request
    ): array {
        $query =
            BookBorrow::with([
                'book',
                'fines',
            ])
                ->whereNull(
                    'returned_at'
                )
                ->whereNotNull(
                    'due_date'
                )
                ->whereDate(
                    'due_date',
                    '<',
                    today()
                )
                ->orderBy(
                    'due_date'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'borrowed_at'
        );

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $query->where(
                'borrower_type',
                $request->user_type
            );
        }

        $records =
            $query->get();

        $records =
            $this
                ->filterBorrowRecordsByCourse(
                    $records,
                    $request
                );

        $totalFine = 0;
        $totalBalance = 0;

        $rows =
            $records
                ->map(
                    function (
                        $borrow
                    ) use (
                        &$totalFine,
                        &$totalBalance
                    ) {
                        $borrower =
                            $borrow
                                ->borrower_record;

                        $overdueFines =
                            $borrow
                                ->fines
                                ->where(
                                    'fine_type',
                                    'overdue'
                                );

                        $fineAmount =
                            (float)
                            $overdueFines
                                ->sum(
                                    'total_amount'
                                );

                        $balance =
                            (float)
                            $overdueFines
                                ->sum(
                                    'remaining_balance'
                                );

                        $totalFine +=
                            $fineAmount;

                        $totalBalance +=
                            $balance;

                        $daysOverdue =
                            $borrow
                                ->due_date
                                ? Carbon::parse(
                                    $borrow
                                        ->due_date
                                )
                                    ->startOfDay()
                                    ->diffInDays(
                                        today()
                                    )
                                : 0;

                        $paymentStatus =
                            $overdueFines
                                ->pluck(
                                    'payment_status'
                                )
                                ->filter()
                                ->unique()
                                ->map(
                                    fn ($status) =>
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $status
                                            )
                                        )
                                )
                                ->implode(', ');

                        return [
                            'Borrower ID' =>
                                $borrow
                                    ->borrower_number,

                            'Borrower' =>
                                $borrow
                                    ->borrower_name,

                            'User Type' =>
                                ucfirst(
                                    $borrow
                                        ->borrower_type
                                ),

                            'Course / Program' =>
                                $borrower
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $borrow
                                    ->book
                                    ->title
                                ?? '-',

                            'Borrowed Date' =>
                                $this
                                    ->formatDate(
                                        $borrow
                                            ->borrowed_at
                                    ),

                            'Due Date' =>
                                $this
                                    ->formatDate(
                                        $borrow
                                            ->due_date
                                    ),

                            'Days Overdue' =>
                                $daysOverdue,

                            'Total Fine' =>
                                $this
                                    ->money(
                                        $fineAmount
                                    ),

                            'Balance' =>
                                $this
                                    ->money(
                                        $balance
                                    ),

                            'Payment Status' =>
                                $paymentStatus
                                ?: 'No Fine Record',

                            'Remarks' =>
                                $borrow
                                    ->remarks
                                ?? '-',
                        ];
                    }
                )
                ->values();

        return [
            'title' =>
                'Overdue Report',

            'columns' => [
                'Borrower ID',
                'Borrower',
                'User Type',
                'Course / Program',
                'Book',
                'Borrowed Date',
                'Due Date',
                'Days Overdue',
                'Total Fine',
                'Balance',
                'Payment Status',
                'Remarks',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Overdue Transactions',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        'text-danger',
                ],

                [
                    'label' =>
                        'Total Fine',

                    'value' =>
                        $this
                            ->money(
                                $totalFine
                            ),

                    'class' =>
                        'text-warning',
                ],

                [
                    'label' =>
                        'Outstanding Balance',

                    'value' =>
                        $this
                            ->money(
                                $totalBalance
                            ),

                    'class' =>
                        'text-danger',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVATION REPORT
    |--------------------------------------------------------------------------
    */

    private function reservationReport(
        Request $request
    ): array {
        $query =
            BookReservation::with(
                'book'
            )
                ->orderByDesc(
                    'borrow_date'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'borrow_date'
        );

        if (
            $request->filled(
                'user_type'
            )
            &&
            $request
                ->user_type
            === 'personnel'
        ) {
            $query
                ->whereRaw(
                    '1 = 0'
                );
        }

        if (
            $request->filled(
                'book_status'
            )
        ) {
            $query->where(
                'status',
                $request->book_status
            );
        }

        $records =
            $query->get();

        $rows =
            $records
                ->map(
                    function (
                        $reservation
                    ) {
                        $student =
                            $this
                                ->findReservationStudent(
                                    $reservation
                                        ->student_id
                                );

                        return [
                            'Student ID' =>
                                $student
                                    ->student_number
                                ?? $reservation
                                    ->student_id
                                ?? '-',

                            'Student Name' =>
                                $reservation
                                    ->student_name
                                ?: (
                                    $student
                                        ? trim(
                                            (
                                                $student
                                                    ->firstname
                                                ?? ''
                                            )
                                            . ' '
                                            . (
                                                $student
                                                    ->lastname
                                                ?? ''
                                            )
                                        )
                                        : '-'
                                ),

                            'Course / Program' =>
                                $student
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $reservation
                                    ->book
                                    ->title
                                ?? '-',

                            'Call Number' =>
                                $reservation
                                    ->book
                                    ->call_number
                                ?? '-',

                            'Reservation Date' =>
                                $this
                                    ->formatDate(
                                        $reservation
                                            ->borrow_date
                                    ),

                            'Pickup Time' =>
                                $this
                                    ->formatTime(
                                        $reservation
                                            ->pickup_time
                                    ),

                            'Status' =>
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $reservation
                                            ->status
                                        ?? 'pending'
                                    )
                                ),

                            'Remarks' =>
                                $reservation
                                    ->remarks
                                ?? '-',
                        ];
                    }
                );

        if (
            $request->filled(
                'course'
            )
        ) {
            $rows =
                $rows
                    ->filter(
                        fn ($row) =>
                            (
                                $row[
                                    'Course / Program'
                                ]
                                ?? null
                            )
                            ===
                            $request->course
                    );
        }

        $rows =
            $rows->values();

        $pendingCount =
            $rows
                ->filter(
                    fn ($row) =>
                        in_array(
                            strtolower(
                                $row['Status']
                            ),
                            [
                                'pending',
                                'reserved',
                            ],
                            true
                        )
                )
                ->count();

        $borrowedCount =
            $rows
                ->filter(
                    fn ($row) =>
                        strtolower(
                            $row['Status']
                        )
                        === 'borrowed'
                )
                ->count();

        return [
            'title' =>
                'Reservation Report',

            'columns' => [
                'Student ID',
                'Student Name',
                'Course / Program',
                'Book',
                'Call Number',
                'Reservation Date',
                'Pickup Time',
                'Status',
                'Remarks',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Total Reservations',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Pending / Reserved',

                    'value' =>
                        $pendingCount,

                    'class' =>
                        'text-primary',
                ],

                [
                    'label' =>
                        'Borrowed',

                    'value' =>
                        $borrowedCount,

                    'class' =>
                        'text-success',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | INVENTORY REPORT
    |--------------------------------------------------------------------------
    */

    private function inventoryReport(
        Request $request
    ): array {
        $books =
            Book::with([
                'copies',
                'borrowings',
                'reservations',
            ])
                ->orderBy(
                    'title'
                )
                ->get();

        $rows =
            $books
                ->map(
                    function (
                        $book
                    ) {
                        $status =
                            $this
                                ->determineBookStatus(
                                    $book
                                );

                        return [
                            'Unique Key' =>
                                $book
                                    ->unique_key
                                ?? '-',

                            'Title' =>
                                $book
                                    ->title
                                ?? '-',

                            'Author' =>
                                $book
                                    ->author
                                ?? '-',

                            'Call Number' =>
                                $book
                                    ->call_number
                                ?? '-',

                            'Sublocation' =>
                                $book
                                    ->sublocation
                                ?? '-',

                            'Publisher' =>
                                $book
                                    ->publisher
                                ?? '-',

                            'Year' =>
                                $book
                                    ->year
                                ?? '-',

                            'Edition' =>
                                $book
                                    ->edition
                                ?? '-',

                            'ISBN' =>
                                $book
                                    ->isbn
                                ?? '-',

                            'Copies' =>
                                $book
                                    ->copies
                                    ->count(),

                            'Status' =>
                                $status,
                        ];
                    }
                );

        if (
            $request->filled(
                'book_status'
            )
        ) {
            $rows =
                $rows
                    ->filter(
                        fn ($row) =>
                            strtolower(
                                $row['Status']
                            )
                            ===
                            strtolower(
                                $request
                                    ->book_status
                            )
                    );
        }

        $rows =
            $rows->values();

        return [
            'title' =>
                'Inventory Report',

            'columns' => [
                'Unique Key',
                'Title',
                'Author',
                'Call Number',
                'Sublocation',
                'Publisher',
                'Year',
                'Edition',
                'ISBN',
                'Copies',
                'Status',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Total Titles',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Available',

                    'value' =>
                        $rows
                            ->where(
                                'Status',
                                'Available'
                            )
                            ->count(),

                    'class' =>
                        'text-success',
                ],

                [
                    'label' =>
                        'Borrowed',

                    'value' =>
                        $rows
                            ->where(
                                'Status',
                                'Borrowed'
                            )
                            ->count(),

                    'class' =>
                        'text-primary',
                ],

                [
                    'label' =>
                        'Overdue',

                    'value' =>
                        $rows
                            ->where(
                                'Status',
                                'Overdue'
                            )
                            ->count(),

                    'class' =>
                        'text-danger',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FINE COLLECTION REPORT
    |--------------------------------------------------------------------------
    */

    private function fineCollectionReport(
        Request $request
    ): array {
        $query =
            FinePayment::with([
                'fine.bookBorrow.book',
                'receivedBy',
            ])
                ->orderByDesc(
                    'paid_at'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'paid_at'
        );

        $records =
            $query->get();

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $records =
                $records
                    ->filter(
                        function (
                            $payment
                        ) use (
                            $request
                        ) {
                            $borrow =
                                $payment
                                    ->fine
                                    ?->bookBorrow;

                            return
                                $borrow
                                &&
                                $borrow
                                    ->borrower_type
                                ===
                                $request
                                    ->user_type;
                        }
                    )
                    ->values();
        }

        if (
            $request->filled(
                'course'
            )
        ) {
            $records =
                $records
                    ->filter(
                        function (
                            $payment
                        ) use (
                            $request
                        ) {
                            $borrow =
                                $payment
                                    ->fine
                                    ?->bookBorrow;

                            if (
                                !$borrow
                                ||
                                $borrow
                                    ->borrower_type
                                !== 'student'
                            ) {
                                return false;
                            }

                            $student =
                                $borrow
                                    ->borrower_record;

                            return (
                                $student
                                    ->course_program
                                ?? null
                            )
                            ===
                            $request->course;
                        }
                    )
                    ->values();
        }

        $rows =
            $records
                ->map(
                    function (
                        $payment
                    ) {
                        $fine =
                            $payment
                                ->fine;

                        $borrow =
                            $fine
                                ?->bookBorrow;

                        $borrower =
                            $borrow
                                ?->borrower_record;

                        $receiver =
                            $payment
                                ->receivedBy;

                        return [
                            'Receipt No.' =>
                                $payment
                                    ->receipt_number
                                ?? '-',

                            'Borrower' =>
                                $borrow
                                    ? $borrow
                                        ->borrower_name
                                    : '-',

                            'User Type' =>
                                $borrow
                                    ? ucfirst(
                                        $borrow
                                            ->borrower_type
                                    )
                                    : '-',

                            'Course / Program' =>
                                $borrower
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $borrow
                                    ?->book
                                    ?->title
                                ?? '-',

                            'Fine Type' =>
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $fine
                                            ?->fine_type
                                        ?? '-'
                                    )
                                ),

                            'Payment Type' =>
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $payment
                                            ->payment_type
                                        ?? '-'
                                    )
                                ),

                            'Amount Paid' =>
                                $this
                                    ->money(
                                        $payment
                                            ->amount
                                    ),

                            'Payment Method' =>
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $payment
                                            ->payment_method
                                        ?? '-'
                                    )
                                ),

                            'Date Paid' =>
                                $this
                                    ->formatDateTime(
                                        $payment
                                            ->paid_at
                                    ),

                            'Received By' =>
                                $receiver
                                    ->name
                                ?? '-',
                        ];
                    }
                )
                ->values();

        $totalCollected =
            (float)
            $records
                ->sum(
                    'amount'
                );

        return [
            'title' =>
                'Fine Collection Report',

            'columns' => [
                'Receipt No.',
                'Borrower',
                'User Type',
                'Course / Program',
                'Book',
                'Fine Type',
                'Payment Type',
                'Amount Paid',
                'Payment Method',
                'Date Paid',
                'Received By',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Payments',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Total Collected',

                    'value' =>
                        $this
                            ->money(
                                $totalCollected
                            ),

                    'class' =>
                        'text-success',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOST AND DAMAGED BOOKS
    |--------------------------------------------------------------------------
    */

    private function lostDamagedReport(
        Request $request
    ): array {
        $query =
            BookFine::with([
                'bookBorrow.book',
            ])
                ->whereIn(
                    'fine_type',
                    [
                        'lost',
                        'damaged',
                    ]
                )
                ->orderByDesc(
                    'assessed_at'
                );

        $this->applyDateFilter(
            $query,
            $request,
            'assessed_at'
        );

        if (
            $request->filled(
                'book_status'
            )
            &&
            in_array(
                $request->book_status,
                [
                    'lost',
                    'damaged',
                ],
                true
            )
        ) {
            $query->where(
                'fine_type',
                $request->book_status
            );
        }

        $records =
            $query->get();

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $records =
                $records
                    ->filter(
                        fn ($fine) =>
                            $fine
                                ->bookBorrow
                            &&
                            $fine
                                ->bookBorrow
                                ->borrower_type
                            ===
                            $request
                                ->user_type
                    )
                    ->values();
        }

        if (
            $request->filled(
                'course'
            )
        ) {
            $records =
                $this
                    ->filterFineRecordsByCourse(
                        $records,
                        $request
                    );
        }

        $rows =
            $records
                ->map(
                    function (
                        $fine
                    ) {
                        $borrow =
                            $fine
                                ->bookBorrow;

                        $book =
                            $borrow
                                ?->book;

                        $borrower =
                            $borrow
                                ?->borrower_record;

                        return [
                            'Borrower' =>
                                $borrow
                                    ?->borrower_name
                                ?? '-',

                            'User Type' =>
                                $borrow
                                    ? ucfirst(
                                        $borrow
                                            ->borrower_type
                                    )
                                    : '-',

                            'Course / Program' =>
                                $borrower
                                    ->course_program
                                ?? '-',

                            'Book' =>
                                $book
                                    ->title
                                ?? '-',

                            'Call Number' =>
                                $book
                                    ->call_number
                                ?? '-',

                            'Status' =>
                                ucfirst(
                                    $fine
                                        ->fine_type
                                ),

                            'Damage Level' =>
                                $fine
                                    ->damage_level
                                    ? ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $fine
                                                ->damage_level
                                        )
                                    )
                                    : '-',

                            'Replacement Cost' =>
                                $this
                                    ->money(
                                        $fine
                                            ->replacement_cost
                                        ?? 0
                                    ),

                            'Total Amount' =>
                                $this
                                    ->money(
                                        $fine
                                            ->total_amount
                                    ),

                            'Payment Status' =>
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $fine
                                            ->payment_status
                                        ?? '-'
                                    )
                                ),

                            'Assessed Date' =>
                                $this
                                    ->formatDate(
                                        $fine
                                            ->assessed_at
                                    ),

                            'Remarks' =>
                                $fine
                                    ->remarks
                                ?? '-',
                        ];
                    }
                )
                ->values();

        $lost =
            $records
                ->where(
                    'fine_type',
                    'lost'
                )
                ->count();

        $damaged =
            $records
                ->where(
                    'fine_type',
                    'damaged'
                )
                ->count();

        return [
            'title' =>
                'Lost and Damaged Books',

            'columns' => [
                'Borrower',
                'User Type',
                'Course / Program',
                'Book',
                'Call Number',
                'Status',
                'Damage Level',
                'Replacement Cost',
                'Total Amount',
                'Payment Status',
                'Assessed Date',
                'Remarks',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Total Records',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Lost Books',

                    'value' =>
                        $lost,

                    'class' =>
                        'text-danger',
                ],

                [
                    'label' =>
                        'Damaged Books',

                    'value' =>
                        $damaged,

                    'class' =>
                        'text-warning',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MOST BORROWED BOOKS
    |--------------------------------------------------------------------------
    */

    private function mostBorrowedReport(
        Request $request
    ): array {
        $query =
            BookBorrow::with(
                'book'
            );

        $this->applyDateFilter(
            $query,
            $request,
            'borrowed_at'
        );

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $query->where(
                'borrower_type',
                $request->user_type
            );
        }

        $records =
            $query->get();

        $records =
            $this
                ->filterBorrowRecordsByCourse(
                    $records,
                    $request
                );

        $grouped =
            $records
                ->filter(
                    fn ($borrow) =>
                        $borrow
                            ->book
                        !== null
                )
                ->groupBy(
                    'book_id'
                )
                ->map(
                    function (
                        $items
                    ) {
                        return [
                            'book' =>
                                $items
                                    ->first()
                                    ->book,

                            'total' =>
                                $items
                                    ->count(),
                        ];
                    }
                )
                ->sortByDesc(
                    'total'
                )
                ->values();

        $rank = 1;

        $rows =
            $grouped
                ->map(
                    function (
                        $item
                    ) use (
                        &$rank
                    ) {
                        $book =
                            $item['book'];

                        return [
                            'Rank' =>
                                $rank++,

                            'Book' =>
                                $book
                                    ->title
                                ?? '-',

                            'Author' =>
                                $book
                                    ->author
                                ?? '-',

                            'Call Number' =>
                                $book
                                    ->call_number
                                ?? '-',

                            'Sublocation' =>
                                $book
                                    ->sublocation
                                ?? '-',

                            'Times Borrowed' =>
                                $item[
                                    'total'
                                ],
                        ];
                    }
                );

        return [
            'title' =>
                'Most Borrowed Books',

            'columns' => [
                'Rank',
                'Book',
                'Author',
                'Call Number',
                'Sublocation',
                'Times Borrowed',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Books Ranked',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Borrow Transactions',

                    'value' =>
                        $records->count(),

                    'class' =>
                        'text-primary',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MOST ACTIVE LIBRARY USERS
    |--------------------------------------------------------------------------
    */

    private function mostActiveUsersReport(
        Request $request
    ): array {
        $query =
            BookBorrow::query();

        $this->applyDateFilter(
            $query,
            $request,
            'borrowed_at'
        );

        if (
            $request->filled(
                'user_type'
            )
        ) {
            $query->where(
                'borrower_type',
                $request->user_type
            );
        }

        $records =
            $query->get();

        $records =
            $this
                ->filterBorrowRecordsByCourse(
                    $records,
                    $request
                );

        $grouped =
            $records
                ->groupBy(
                    function (
                        $borrow
                    ) {
                        return
                            $borrow
                                ->borrower_type
                            . ':'
                            . $borrow
                                ->borrower_id;
                    }
                )
                ->map(
                    function (
                        $items
                    ) {
                        $first =
                            $items
                                ->first();

                        return [
                            'borrower' =>
                                $first
                                    ->borrower_record,

                            'name' =>
                                $first
                                    ->borrower_name,

                            'number' =>
                                $first
                                    ->borrower_number,

                            'type' =>
                                $first
                                    ->borrower_type,

                            'total' =>
                                $items
                                    ->count(),
                        ];
                    }
                )
                ->sortByDesc(
                    'total'
                )
                ->values();

        $rank = 1;

        $rows =
            $grouped
                ->map(
                    function (
                        $item
                    ) use (
                        &$rank
                    ) {
                        return [
                            'Rank' =>
                                $rank++,

                            'User ID' =>
                                $item[
                                    'number'
                                ],

                            'Name' =>
                                $item[
                                    'name'
                                ],

                            'User Type' =>
                                ucfirst(
                                    $item[
                                        'type'
                                    ]
                                ),

                            'Course / Program' =>
                                $item[
                                    'borrower'
                                ]
                                    ->course_program
                                ?? '-',

                            'Total Books Borrowed' =>
                                $item[
                                    'total'
                                ],
                        ];
                    }
                );

        return [
            'title' =>
                'Most Active Library Users',

            'columns' => [
                'Rank',
                'User ID',
                'Name',
                'User Type',
                'Course / Program',
                'Total Books Borrowed',
            ],

            'rows' =>
                $rows,

            'summary' => [
                [
                    'label' =>
                        'Active Users',

                    'value' =>
                        $rows->count(),

                    'class' =>
                        '',
                ],

                [
                    'label' =>
                        'Borrow Transactions',

                    'value' =>
                        $records->count(),

                    'class' =>
                        'text-primary',
                ],
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve the Student or Personnel connected to an attendance row.
     *
     * Current rows use attendable_id + attendable_type. The fallbacks below
     * also support older rows created before the polymorphic relationship was
     * standardized. An ID without a type is used only when it exists in one
     * table and not the other, preventing the report from guessing incorrectly.
     */
    private function resolveAttendanceUser(
        Attendance $attendance
    ): Student|Personnel|null {
        $attributes = $attendance->getAttributes();

        $attendableId = $attributes['attendable_id'] ?? null;
        $type = strtolower(trim((string) ($attributes['attendable_type'] ?? '')));

        // Resolve the stored type first. This works for the short morph-map
        // values and for records that still contain a full model class name.
        if ($attendableId) {
            if (
                $type === 'student'
                || $type === strtolower(Student::class)
                || str_ends_with($type, '\\student')
            ) {
                return Student::query()->find($attendableId);
            }

            if (
                $type === 'personnel'
                || $type === 'employee'
                || $type === strtolower(Personnel::class)
                || str_ends_with($type, '\\personnel')
            ) {
                return Personnel::query()->find($attendableId);
            }
        }

        // Use the relation only after checking the raw database values.
        $related = $attendance->attendable()->first();

        if ($related instanceof Student || $related instanceof Personnel) {
            return $related;
        }

        $studentId = $attributes['student_id'] ?? null;
        if ($studentId) {
            $student = Student::find($studentId);

            if ($student) {
                return $student;
            }
        }

        $personnelId = $attributes['personnel_id'] ?? null;
        if ($personnelId) {
            $personnel = Personnel::find($personnelId);

            if ($personnel) {
                return $personnel;
            }
        }

        $rfid = trim((string) ($attributes['rfid_tag_uid'] ?? ''));
        if ($rfid !== '') {
            $student = Student::where('rfid_tag_uid', $rfid)->first();

            if ($student) {
                return $student;
            }

            $personnel = Personnel::where('rfid_tag_uid', $rfid)->first();

            if ($personnel) {
                return $personnel;
            }
        }

        if (!$attendableId) {
            return null;
        }

        // Legacy row with an ID but no usable type. Recover it only when the
        // ID belongs exclusively to one table; student/personnel IDs may overlap.
        $student = Student::find($attendableId);
        $personnel = Personnel::find($attendableId);

        if ($student && !$personnel) {
            return $student;
        }

        if ($personnel && !$student) {
            return $personnel;
        }

        return null;
    }

    private function applyDateFilter(
        $query,
        Request $request,
        string $column
    ): void {
        if (
            $request->filled(
                'start_date'
            )
        ) {
            $query->whereDate(
                $column,
                '>=',
                $request->start_date
            );
        }

        if (
            $request->filled(
                'end_date'
            )
        ) {
            $query->whereDate(
                $column,
                '<=',
                $request->end_date
            );
        }
    }

    private function filterBorrowRecordsByCourse(
        Collection $records,
        Request $request
    ): Collection {
        if (
            !$request->filled(
                'course'
            )
        ) {
            return $records;
        }

        return $records
            ->filter(
                function (
                    $borrow
                ) use (
                    $request
                ) {
                    if (
                        $borrow
                            ->borrower_type
                        !== 'student'
                    ) {
                        return false;
                    }

                    $student =
                        $borrow
                            ->borrower_record;

                    return (
                        $student
                            ->course_program
                        ?? null
                    )
                    ===
                    $request->course;
                }
            )
            ->values();
    }

    private function filterFineRecordsByCourse(
        Collection $records,
        Request $request
    ): Collection {
        return $records
            ->filter(
                function (
                    $fine
                ) use (
                    $request
                ) {
                    $borrow =
                        $fine
                            ->bookBorrow;

                    if (
                        !$borrow
                        ||
                        $borrow
                            ->borrower_type
                        !== 'student'
                    ) {
                        return false;
                    }

                    $student =
                        $borrow
                            ->borrower_record;

                    return (
                        $student
                            ->course_program
                        ?? null
                    )
                    ===
                    $request->course;
                }
            )
            ->values();
    }

    private function findReservationStudent(
        $studentId
    ): ?Student {
        if (
            !$studentId
        ) {
            return null;
        }

        return Student::where(
            'id',
            $studentId
        )
            ->orWhere(
                'student_number',
                $studentId
            )
            ->first();
    }

    private function getBorrowStatus(
        BookBorrow $borrow
    ): string {
        if (
            $borrow
                ->returned_at
        ) {
            return 'Returned';
        }

        if (
            $borrow
                ->due_date
            &&
            $borrow
                ->due_date
                ->isPast()
        ) {
            return 'Overdue';
        }

        return 'Borrowed';
    }

    private function determineBookStatus(
        Book $book
    ): string {
        $problemFine =
            BookFine::whereHas(
                'bookBorrow',
                function (
                    $query
                ) use (
                    $book
                ) {
                    $query->where(
                        'book_id',
                        $book->id
                    );
                }
            )
                ->whereIn(
                    'fine_type',
                    [
                        'lost',
                        'damaged',
                    ]
                )
                ->whereIn(
                    'payment_status',
                    [
                        'unpaid',
                        'partially_paid',
                    ]
                )
                ->latest(
                    'assessed_at'
                )
                ->first();

        if (
            $problemFine
        ) {
            return
                $problemFine
                    ->fine_type
                === 'lost'
                    ? 'Lost'
                    : 'Damaged';
        }

        $hasOverdue =
            $book
                ->borrowings
                ->contains(
                    function (
                        $borrow
                    ) {
                        return
                            !$borrow
                                ->returned_at
                            &&
                            $borrow
                                ->due_date
                            &&
                            $borrow
                                ->due_date
                                ->isPast();
                    }
                );

        if (
            $hasOverdue
        ) {
            return 'Overdue';
        }

        $hasBorrowed =
            $book
                ->borrowings
                ->contains(
                    fn ($borrow) =>
                        !$borrow
                            ->returned_at
                );

        if (
            $hasBorrowed
        ) {
            return 'Borrowed';
        }

        $hasReservation =
            $book
                ->reservations
                ->contains(
                    function (
                        $reservation
                    ) {
                        return
                            in_array(
                                strtolower(
                                    $reservation
                                        ->status
                                    ?? ''
                                ),
                                [
                                    'pending',
                                    'reserved',
                                ],
                                true
                            );
                    }
                );

        if (
            $hasReservation
        ) {
            return 'Reserved';
        }

        return 'Available';
    }

    private function money(
        $amount
    ): string {
        return
            '₱'
            . number_format(
                (float)
                $amount,
                2
            );
    }

    private function formatDate(
        $value
    ): string {
        if (!$value) {
            return '-';
        }

        try {
            return
                Carbon::parse(
                    $value
                )->format(
                    'M d, Y'
                );
        } catch (
            \Throwable $e
        ) {
            return
                (string)
                $value;
        }
    }

    private function formatDateTime(
        $value
    ): string {
        if (!$value) {
            return '-';
        }

        try {
            return
                Carbon::parse(
                    $value
                )->format(
                    'M d, Y h:i A'
                );
        } catch (
            \Throwable $e
        ) {
            return
                (string)
                $value;
        }
    }

    private function formatTime(
        $value
    ): string {
        if (!$value) {
            return '-';
        }

        try {
            return
                Carbon::parse(
                    $value
                )->format(
                    'h:i A'
                );
        } catch (
            \Throwable $e
        ) {
            return
                (string)
                $value;
        }
    }
}
