<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\BookReservation;
use App\Models\LibraryPolicy;
use App\Models\Personnel;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use App\Services\StaffTransactionNotifier;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class BookReservationController extends Controller
{
    private function recordReservationAudit(
        string $actorName,
        string $action,
        ?string $affectedRecord,
        string $result,
        string $description,
        ?Request $request = null
    ): void {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'actor_name' => $actorName,
                'user_role' => 'system',
                'action' => $action,
                'module' => 'Reservation',
                'affected_record' => $affectedRecord,
                'previous_value' => null,
                'new_value' => null,
                'ip_address' => ($request ?? request())->ip(),
                'result' => $result,
                'description' => $description,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function calculateDueDate(
        LibraryPolicy $policy,
        string $borrowerType
    ): Carbon {
        $dueDate = Carbon::today();
        if ($borrowerType === 'student') {
            return $dueDate->addDays(
                max(1, (int) $policy->student_loan_period_days)
            );
        }
        $value = max(1, (int) $policy->personnel_loan_period_value);
        $unit = strtolower(
            (string) ($policy->personnel_loan_period_unit ?? 'semester')
        );
        return match ($unit) {
            'months' => $dueDate->addMonths($value),
            'semester' => $dueDate->addMonths($value * 5),
            default => $dueDate->addDays($value),
        };
    }

    private function notificationMessage(mixed $result): string
    {
        if (!is_array($result)) {
            return '';
        }
        $message = trim((string) ($result['message'] ?? ''));
        return $message === '' ? '' : ' ' . $message;
    }

    private function getReservationLimit(): int
    {
        $policy = LibraryPolicy::current();
        return max(
            1,
            (int) (
                $policy->maximum_active_reservations
                ?? $policy->max_active_reservations
                ?? 1
            )
        );
    }

    /**
     * Whether the library policy currently requires a fingerprint for
     * borrowing-related actions (which includes reservations).
     */
    private function requiresFingerprint(): bool
    {
        $policy = LibraryPolicy::current();
        return (bool) ($policy?->require_fingerprint_for_borrowing ?? false);
    }

    /**
     * Borrowing limit for the given borrower type.
     */
    private function getBorrowingLimit(LibraryPolicy $policy, string $borrowerType): int
    {
        if ($borrowerType === 'personnel') {
            return max(0, (int) $policy->personnel_borrowing_limit);
        }

        return max(0, (int) $policy->student_borrowing_limit);
    }

    /**
     * Count of currently active (borrowed / overdue) books for a borrower.
     */
    private function getActiveBorrowCount(int $borrowerId, string $borrowerType): int
    {
        return BookBorrow::where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();
    }

    private function getActiveReservationCount(string $borrowerNumber): int
    {
        /*
         * Expire old reservations before counting. A pending or admin-
         * accepted (ready) reservation counts until it is cancelled,
         * expired, or picked up.
         */
        BookReservation::where('student_id', $borrowerNumber)
            ->whereIn('status', ['pending', 'ready'])
            ->whereDate('borrow_date', '<', Carbon::today())
            ->update([
                'status' => 'expired',
            ]);

        return BookReservation::where('student_id', $borrowerNumber)
            ->whereIn('status', ['pending', 'ready'])
            ->count();
    }

    public function findStudentByRfid(string $rfid): JsonResponse
    {
        if (!SystemSetting::current()->reservation_enabled) {
            return response()->json([
                'found' => false,
                'message' => 'Reservations are currently disabled.',
            ], 503);
        }

        $rfid = trim($rfid);
        if ($rfid === '') {
            return response()->json([
                'found' => false,
                'message' => 'Please scan a Student or Personnel RFID card.',
            ], 422);
        }

        $borrower = Student::where('rfid_tag_uid', $rfid)->first();
        $borrowerType = 'student';

        if (!$borrower) {
            $borrower = Personnel::where('rfid_tag_uid', $rfid)->first();
            $borrowerType = 'personnel';
        }

        if (!$borrower) {
            return response()->json([
                'found' => false,
                'message' => 'This RFID card is not registered.',
            ], 404);
        }

        /*
         * The "no enrolled fingerprint" rejection is ONLY enforced when
         * the library policy actually requires a fingerprint.
         */
        $requiresFingerprint = $this->requiresFingerprint();
        if ($requiresFingerprint && !$borrower->fingerprint_id) {
            return response()->json([
                'found' => false,
                'message' => 'This account does not have an enrolled fingerprint.',
            ], 422);
        }

        $borrowerName = trim(
            ($borrower->firstname ?? '') . ' ' .
            ($borrower->lastname ?? '')
        );

        $borrowerNumber = $borrowerType === 'student'
            ? $borrower->student_number
            : $borrower->employee_number;

        $reservationLimit = $this->getReservationLimit();
        $activeReservations = $this->getActiveReservationCount(
            $borrowerNumber
        );

        if ($activeReservations >= $reservationLimit) {
            return response()->json([
                'found' => true,
                'reservation_limit_reached' => true,
                'message' => 'Reservation limit reached. You have '
                    . $activeReservations . ' of ' . $reservationLimit
                    . ' active reservations. Pending and admin-accepted reservations both count toward this limit.',
                'active_reservations' => $activeReservations,
                'reservation_limit' => $reservationLimit,
                'remaining_reservations' => 0,
            ], 409);
        }

        $remainingReservations = max(
            0,
            $reservationLimit - $activeReservations
        );

        return response()->json([
            'found' => true,
            'reservation_limit_reached' => false,
            'message' => ucfirst($borrowerType) . ' RFID accepted. You have '
                . $activeReservations . ' of ' . $reservationLimit
                . ' active reservations and can reserve '
                . $remainingReservations . ' more.',
            'borrower_type' => $borrowerType,
            'borrower_database_id' => $borrower->id,
            'borrower_number' => $borrowerNumber,
            'borrower_name' => $borrowerName,
            'rfid_tag_uid' => $borrower->rfid_tag_uid,
            'student_database_id' => $borrower->id,
            'student_number' => $borrowerNumber,
            'student_name' => $borrowerName,
            'fingerprint_id' => $borrower->fingerprint_id,
            'active_reservations' => $activeReservations,
            'reservation_limit' => $reservationLimit,
            'remaining_reservations' => $remainingReservations,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVATION PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        abort_unless(
            SystemSetting::current()->reservation_enabled,
            503,
            'Reservations are currently disabled.'
        );

        // Books currently borrowed cannot be reserved.
        $borrowedBookIds = BookBorrow::whereIn(
            'status',
            ['borrowed', 'overdue']
        )->pluck('book_id');

        $books = Book::with('copies')->where('is_reservable', true)
            ->whereNotIn('id', $borrowedBookIds)
            ->orderBy('title', 'asc')
            ->get();

        return view(
            'reserve',
            compact('books')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE RESERVATION
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!SystemSetting::current()->reservation_enabled) {
            return back()->with('error', 'Reservations are currently disabled.');
        }

        $requiresFingerprint = $this->requiresFingerprint();

        $rules = [
            'student_id' => [
                'required',
                'string',
            ],
            'student_name' => [
                'required',
                'string',
                'max:255',
            ],
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
            ],
            'borrow_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'pickup_time' => [
                'required',
                'date_format:H:i',
                'after_or_equal:08:00',
                'before_or_equal:18:00',
            ],
            'borrower_type' => [
                'required',
                Rule::in(['student', 'personnel']),
            ],
        ];

        if ($requiresFingerprint) {
            $rules['fingerprint_id'] = [
                'required',
                'integer',
                'between:1,127',
            ];
        }

        $validated = $request->validate($rules);

        $requester = $validated['borrower_type'] === 'student'
            ? Student::where('student_number', $validated['student_id'])->first()
            : Personnel::where('employee_number', $validated['student_id'])->first();

        if ($requiresFingerprint) {
            if (
                !$requester
                || (int) $requester->fingerprint_id !== (int) $validated['fingerprint_id']
            ) {
                return back()
                    ->withInput()
                    ->with('error', 'Fingerprint verification does not match the RFID owner.');
            }
        } elseif (!$requester) {
            return back()
                ->withInput()
                ->with('error', 'The borrower could not be found.');
        }

        $reservationLimit = $this->getReservationLimit();
        $activeReservations = $this->getActiveReservationCount(
            $validated['student_id']
        );

        if ($activeReservations >= $reservationLimit) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Reservation limit reached. You have '
                    . $activeReservations . ' of ' . $reservationLimit
                    . ' active reservations. Pending and admin-accepted reservations both count toward this limit.'
                );
        }

        $selectedBook = Book::find($validated['book_id']);
        if (!$selectedBook || !$selectedBook->is_reservable) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This book is not available for reservation.'
                );
        }

        $bookIsBorrowed = BookBorrow::where(
            'book_id',
            $validated['book_id']
        )
            ->where('status', 'borrowed')
            ->exists();

        if ($bookIsBorrowed) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This book is currently borrowed and cannot be reserved.'
                );
        }

        $existingReservation = BookReservation::where(
            'student_id',
            $validated['student_id']
        )
            ->where(
                'book_id',
                $validated['book_id']
            )
            ->whereIn(
                'status',
                [
                    'pending',
                    'ready',
                ]
            )
            ->exists();

        if ($existingReservation) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You already have an active reservation for this book.'
                );
        }

        $reservation = BookReservation::create([
            'student_id' => $validated['student_id'],
            'student_name' => $validated['student_name'],
            'book_id' => $validated['book_id'],
            'borrow_date' => $validated['borrow_date'],
            'pickup_time' => $validated['pickup_time'],
            'status' => 'pending',
            'remarks' => null,
        ]);

        $reservation->load('book');

        $emailResult = StaffTransactionNotifier::send(
            'New Book Reservation',
            'Book Reserved',
            [
                'Borrower' => $validated['student_name'],
                'Borrower Type' => ucfirst($validated['borrower_type']),
                'Student/Employee Number' => $validated['student_id'],
                'Book' => $reservation->book->title ?? 'Unknown Book',
                'Reservation Date' => Carbon::parse($validated['borrow_date'])->format('F j, Y'),
                'Pickup Time' => $validated['pickup_time'],
                'Status' => 'Pending',
            ],
            [$requester->email ?? null]
        );

        $this->recordReservationAudit(
            $validated['student_name'],
            'Book Reserved',
            'Reservation #' . $reservation->id,
            'success',
            ucfirst($validated['borrower_type']) . ' reserved "'
                . ($reservation->book?->title ?? 'Unknown Book')
                . '" for ' . Carbon::parse($validated['borrow_date'])->format('F j, Y')
                . ' at ' . $validated['pickup_time'] . '.',
            $request
        );

        return redirect()
            ->route('reserve')
            ->with(
                'success',
                'Book reserved successfully.'
                . $this->notificationMessage($emailResult)
            )
            ->with(
                'reservation_popup',
                [
                    'book_title' => $reservation->book?->title ?? 'Unknown Book',
                    'status' => 'Pending',
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVATION AND BORROW MONITORING
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Automatically expire reservations from previous dates.
        BookReservation::whereIn(
            'status',
            [
                'pending',
                'ready',
            ]
        )
            ->whereDate(
                'borrow_date',
                '<',
                Carbon::today()
            )
            ->update([
                'status' => 'expired',
            ]);

        $reservations = BookReservation::with('book')
            ->orderBy('borrow_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        /*
         * Pre-load borrowing limits and active borrow counts for every
         * borrower referenced by the current reservations. This lets the
         * Blade show a "Limit Reached" indicator without hitting the
         * database inside the loop.
         */
        $policy = LibraryPolicy::current();

        $reservationStudentIds = $reservations
            ->pluck('student_id')
            ->unique()
            ->values();

        // Map student_number / employee_number → borrower id + type
        $studentMap = Student::whereIn('student_number', $reservationStudentIds)
            ->get()
            ->keyBy('student_number');

        $personnelMap = Personnel::whereIn('employee_number', $reservationStudentIds)
            ->get()
            ->keyBy('employee_number');

        // Count active borrows for those borrowers.
        $studentIds   = $studentMap->pluck('id')->all();
        $personnelIds = $personnelMap->pluck('id')->all();

        $studentBorrowCounts = BookBorrow::where('borrower_type', 'student')
            ->whereIn('borrower_id', $studentIds ?: [0])
            ->whereIn('status', ['borrowed', 'overdue'])
            ->selectRaw('borrower_id, COUNT(*) as total')
            ->groupBy('borrower_id')
            ->pluck('total', 'borrower_id');

        $personnelBorrowCounts = BookBorrow::where('borrower_type', 'personnel')
            ->whereIn('borrower_id', $personnelIds ?: [0])
            ->whereIn('status', ['borrowed', 'overdue'])
            ->selectRaw('borrower_id, COUNT(*) as total')
            ->groupBy('borrower_id')
            ->pluck('total', 'borrower_id');

        /*
         * Attach dynamic limit info to each reservation for the Blade.
         */
        $reservations->each(function ($reservation) use (
            $policy,
            $studentMap,
            $personnelMap,
            $studentBorrowCounts,
            $personnelBorrowCounts
        ) {
            $student   = $studentMap->get($reservation->student_id);
            $personnel = $personnelMap->get($reservation->student_id);

            if ($student) {
                $borrowerType = 'student';
                $borrowerId   = $student->id;
                $limit        = $this->getBorrowingLimit($policy, 'student');
                $active       = (int) ($studentBorrowCounts[$borrowerId] ?? 0);
            } elseif ($personnel) {
                $borrowerType = 'personnel';
                $borrowerId   = $personnel->id;
                $limit        = $this->getBorrowingLimit($policy, 'personnel');
                $active       = (int) ($personnelBorrowCounts[$borrowerId] ?? 0);
            } else {
                $borrowerType = 'student';
                $borrowerId   = null;
                $limit        = $this->getBorrowingLimit($policy, 'student');
                $active       = 0;
            }

            $reservation->borrower_type           = $borrowerType;
            $reservation->borrower_database_id    = $borrowerId;
            $reservation->borrowing_limit         = $limit;
            $reservation->active_borrows          = $active;
            $reservation->remaining_borrow_slots  = max(0, $limit - $active);
            $reservation->borrowing_limit_reached = $limit > 0 && $active >= $limit;

            /*
             * Human-readable reason shown to the admin/staff when they press
             * "Picked Up" on a reservation whose borrower is at the limit.
             */
            $reservation->pickup_block_reason = $reservation->borrowing_limit_reached
                ? 'This borrower has reached the borrowing limit of '
                    . $limit . ' book(s) and is currently borrowing '
                    . $active . ' book(s). They must return at least one book '
                    . 'before this reservation can be picked up.'
                : null;
        });

        /*
         * Convert reservations into the common monitoring format.
         */
        $reservationRecords = $reservations->map(
            function ($reservation) {
                return [
                    'record_id' => $reservation->id,
                    'record_type' => 'reservation',
                    'borrower_name' =>
                        $reservation->student_name,
                    'borrower_number' =>
                        $reservation->student_id,
                    'borrower_type' => 'Student',
                    'book_title' =>
                        $reservation->book->title
                        ?? 'Unknown Book',
                    'book_author' =>
                        $reservation->book->author
                        ?? 'Unknown Author',
                    'call_number' =>
                        $reservation->book->call_number
                        ?? '-',
                    'transaction_date' =>
                        $reservation->borrow_date,
                    'pickup_time' =>
                        $reservation->pickup_time,
                    'due_date' => null,
                    'status' =>
                        $reservation->status,
                    'remarks' =>
                        $reservation->remarks,
                    'created_at' =>
                        $reservation->created_at,
                ];
            }
        );

        /*
         * Get normal borrowing transactions.
         */
        $borrows = BookBorrow::with('book')
            ->orderBy('borrowed_at', 'desc')
            ->get();

        $studentIds = $borrows
            ->where('borrower_type', 'student')
            ->pluck('borrower_id')
            ->unique()
            ->values();

        $personnelIds = $borrows
            ->where('borrower_type', 'personnel')
            ->pluck('borrower_id')
            ->unique()
            ->values();

        $students = Student::whereIn(
            'id',
            $studentIds
        )
            ->get()
            ->keyBy('id');

        $personnel = Personnel::whereIn(
            'id',
            $personnelIds
        )
            ->get()
            ->keyBy('id');

        $borrowRecords = $borrows->map(
            function ($borrow) use (
                $students,
                $personnel
            ) {
                if ($borrow->borrower_type === 'student') {
                    $borrower = $students->get(
                        $borrow->borrower_id
                    );
                    $borrowerName = $borrower
                        ? trim(
                            ($borrower->firstname ?? '')
                            . ' ' .
                            ($borrower->lastname ?? '')
                        )
                        : 'Unknown Student';
                    $borrowerNumber =
                        $borrower->student_number
                        ?? '-';
                    $borrowerType = 'Student';
                } else {
                    $borrower = $personnel->get(
                        $borrow->borrower_id
                    );
                    $borrowerName = $borrower
                        ? trim(
                            ($borrower->firstname ?? '')
                            . ' ' .
                            ($borrower->lastname ?? '')
                        )
                        : 'Unknown Personnel';
                    $borrowerNumber =
                        $borrower->employee_number
                        ?? '-';
                    $borrowerType = 'Personnel';
                }

                return [
                    'record_id' => $borrow->id,
                    'record_type' => 'borrow',
                    'borrower_name' =>
                        $borrowerName,
                    'borrower_number' =>
                        $borrowerNumber,
                    'borrower_type' =>
                        $borrowerType,
                    'book_title' =>
                        $borrow->book->title
                        ?? 'Unknown Book',
                    'book_author' =>
                        $borrow->book->author
                        ?? 'Unknown Author',
                    'call_number' =>
                        $borrow->book->call_number
                        ?? '-',
                    'transaction_date' =>
                        $borrow->borrowed_at,
                    'pickup_time' => null,
                    'due_date' =>
                        $borrow->due_date,
                    'status' =>
                        $borrow->status === 'returned'
                            ? 'returned'
                            : 'borrowed',
                    'remarks' =>
                        $borrow->remarks,
                    'created_at' =>
                        $borrow->created_at,
                ];
            }
        );

        $monitoringRecords = $reservationRecords
            ->concat($borrowRecords)
            ->sortByDesc('created_at')
            ->values();

        return view(
            'reserve_monitoring',
            compact(
                'reservations',
                'monitoringRecords',
                'policy'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MARK RESERVATION AS READY
    |--------------------------------------------------------------------------
    */

    public function ready($id)
    {
        $reservation = BookReservation::findOrFail($id);

        if (
            !in_array(
                $reservation->status,
                [
                    'pending',
                    'ready',
                ],
                true
            )
        ) {
            return redirect()
                ->route('reserve.monitoring')
                ->with(
                    'error',
                    'This reservation can no longer be marked as ready.'
                );
        }

        $reservation->update([
            'status' => 'ready',
        ]);

        $emailResult = $this->sendReservationStatusEmail(
            $reservation,
            'Ready for Pickup'
        );

        return redirect()
            ->route('reserve.monitoring')
            ->with(
                'success',
                'Reservation marked as ready for pickup.'
                . $this->notificationMessage($emailResult)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MARK RESERVATION AS PICKED UP
    |--------------------------------------------------------------------------
    */

    public function pickup(Request $request, $id)
    {
        try {
            $reservation = DB::transaction(function () use ($id) {
                $reservation = BookReservation::with('book')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if (!in_array($reservation->status, ['pending', 'ready'], true)) {
                    throw new \RuntimeException(
                        'This reservation can no longer be picked up.'
                    );
                }

                $student = Student::where(
                    'student_number',
                    $reservation->student_id
                )->first();

                $borrower = $student ?: Personnel::where(
                    'employee_number',
                    $reservation->student_id
                )->first();

                if (!$borrower) {
                    throw new \RuntimeException(
                        'The reservation borrower could not be found.'
                    );
                }

                $borrowerType = $student ? 'student' : 'personnel';

                /*
                 * ----------------------------------------------------------
                 * BORROWING LIMIT GUARD
                 * ----------------------------------------------------------
                 * The borrower is allowed to *reserve* even at the limit,
                 * but they are NOT allowed to pick up (convert to a
                 * borrowing transaction) until they return a book.
                 */
                $policy         = LibraryPolicy::current();
                $borrowingLimit = $this->getBorrowingLimit($policy, $borrowerType);
                $activeBorrows  = $this->getActiveBorrowCount($borrower->id, $borrowerType);

                if ($borrowingLimit > 0 && $activeBorrows >= $borrowingLimit) {
                    throw new \RuntimeException(
                        'This borrower has reached the borrowing limit of '
                        . $borrowingLimit . ' book(s) (currently borrowing '
                        . $activeBorrows . '). They must return a book before '
                        . 'this reservation can be picked up.'
                    );
                }

                $alreadyBorrowed = BookBorrow::where(
                    'book_id',
                    $reservation->book_id
                )
                    ->whereIn('status', ['borrowed', 'overdue'])
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyBorrowed) {
                    throw new \RuntimeException(
                        'This book is already borrowed and cannot be picked up again.'
                    );
                }

                $dueDate = $this->calculateDueDate(
                    $policy,
                    $borrowerType
                );

                BookBorrow::create([
                    'book_id' => $reservation->book_id,
                    'borrower_id' => $borrower->id,
                    'borrower_type' => $borrowerType,
                    'borrowed_at' => now(),
                    'due_date' => $dueDate,
                    'returned_at' => null,
                    'status' => 'borrowed',
                    'remarks' => 'Created from reservation #' . $reservation->id,
                ]);

                $reservation->update([
                    'status' => 'picked_up',
                ]);

                return $reservation->fresh('book');
            });
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('reserve.monitoring')
                ->with('error', $exception->getMessage());
        }

        $emailResult = $this->sendReservationStatusEmail(
            $reservation,
            'Picked Up'
        );

        $this->recordReservationAudit(
            $reservation->student_name ?: 'Unknown Borrower',
            'Reserved Book Picked Up',
            'Reservation #' . $reservation->id,
            'success',
            'Reserved book "' . ($reservation->book?->title ?? 'Unknown Book')
                . '" was picked up and converted to a borrowing transaction.',
            $request
        );

        return redirect()
            ->route('reserve.monitoring')
            ->with(
                'success',
                'Reservation marked as picked up.'
                . $this->notificationMessage($emailResult)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL RESERVATION
    |--------------------------------------------------------------------------
    */

    public function cancel(Request $request, $id)
    {
        $validated = $request->validate([
            'remarks' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $reservation = BookReservation::findOrFail($id);

        if (
            in_array(
                $reservation->status,
                [
                    'picked_up',
                    'cancelled',
                    'expired',
                ],
                true
            )
        ) {
            return redirect()
                ->route('reserve.monitoring')
                ->with(
                    'error',
                    'This reservation can no longer be cancelled.'
                );
        }

        $reservation->update([
            'status' => 'cancelled',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $this->recordReservationAudit(
            $reservation->student_name ?: 'Unknown Borrower',
            'Reservation Cancelled',
            'Reservation #' . $reservation->id,
            'success',
            'Reservation was cancelled.'
                . ($reservation->remarks ? ' Remarks: ' . $reservation->remarks : ''),
            $request
        );

        $emailResult = $this->sendReservationStatusEmail(
            $reservation,
            'Cancelled'
        );

        return redirect()
            ->route('reserve.monitoring')
            ->with(
                'success',
                'Reservation cancelled successfully.'
                . $this->notificationMessage($emailResult)
            );
    }

    private function sendReservationStatusEmail(
        BookReservation $reservation,
        string $status
    ): array {
        $reservation->loadMissing('book');

        $student = Student::where(
            'student_number',
            $reservation->student_id
        )->first();

        $requester = $student ?: Personnel::where(
            'employee_number',
            $reservation->student_id
        )->first();

        try {
            $result = StaffTransactionNotifier::send(
                'Reservation ' . $status,
                'Reservation Status Updated',
                [
                    'Borrower' => $reservation->student_name ?: 'Unknown Borrower',
                    'Borrower Type' => $student ? 'Student' : 'Personnel',
                    'Student/Employee Number' => $reservation->student_id ?: '-',
                    'Book' => $reservation->book?->title ?? 'Unknown Book',
                    'New Status' => $status,
                    'Remarks' => $reservation->remarks ?: '-',
                    'Processed By' => auth()->user()?->full_name ?? 'System User',
                ],
                [$requester?->email]
            );

            return is_array($result)
                ? $result
                : [
                    'sent' => 0,
                    'failed' => 0,
                    'message' => 'Email notification returned no result.',
                ];
        } catch (\Throwable $exception) {
            report($exception);

            return [
                'sent' => 0,
                'failed' => 1,
                'message' => 'The status was updated, but the email notification could not be sent.',
            ];
        }
    }
}