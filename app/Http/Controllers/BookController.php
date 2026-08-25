<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Personnel;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Display all books.
     */
    public function index()
    {
        $books = Book::orderBy(
            'created_at',
            'desc'
        )->get();

        return view(
            'book',
            compact('books')
        );
    }

    /**
     * Store a new book.
     */
    public function store(Request $request)
    {
        $validated = $this->validateBook(
            $request
        );

        /*
         * Generate a unique book key.
         */
        do {
            $uniqueKey =
                'BOOK-' .
                strtoupper(
                    substr(
                        md5(
                            uniqid(
                                (string) mt_rand(),
                                true
                            )
                        ),
                        0,
                        8
                    )
                );
        } while (
            Book::where(
                'unique_key',
                $uniqueKey
            )->exists()
        );

        $validated['unique_key'] =
            $uniqueKey;

        Book::create($validated);

        return redirect()
            ->route('book')
            ->with(
                'success',
                'Book added successfully.'
            );
    }

    /**
     * Update an existing book.
     */
    public function update(
        Request $request,
        Book $book
    ) {
        $validated = $this->validateBook(
            $request
        );

        $book->update($validated);

        return redirect()
            ->route('book')
            ->with(
                'success',
                'Book updated successfully.'
            );
    }

    /**
     * Validate book information.
     */
    private function validateBook(
        Request $request
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
            ],

            'author' => [
                'nullable',
                'string',
            ],

            'call_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sublocation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'publisher' => [
                'nullable',
                'string',
            ],

            'year' => [
                'nullable',
                'string',
                'max:20',
            ],

            'edition' => [
                'nullable',
                'string',
                'max:255',
            ],

            'format' => [
                'nullable',
                'string',
            ],

            'content_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'media_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'carrier_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'isbn' => [
                'nullable',
                'string',
            ],

            'issn' => [
                'nullable',
                'string',
            ],

            'lccn' => [
                'nullable',
                'string',
                'max:255',
            ],

            'subjects' => [
                'nullable',
                'string',
            ],

            'additional_details' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book)
    {
        $currentlyBorrowed =
            BookBorrow::where(
                'book_id',
                $book->id
            )
                ->where(
                    'status',
                    'borrowed'
                )
                ->exists();

        if ($currentlyBorrowed) {
            return redirect()
                ->route('book')
                ->with(
                    'fail',
                    'This book cannot be deleted because it is currently borrowed.'
                );
        }

        $hasBorrowHistory =
            BookBorrow::where(
                'book_id',
                $book->id
            )->exists();

        if ($hasBorrowHistory) {
            return redirect()
                ->route('book')
                ->with(
                    'fail',
                    'This book cannot be deleted because it has borrowing history.'
                );
        }

        $book->delete();

        return redirect()
            ->route('book')
            ->with(
                'success',
                'Book deleted successfully.'
            );
    }

    /**
     * Display the borrowing kiosk.
     */
    public function borrowForm()
    {
        $borrowedBookIds =
            BookBorrow::where(
                'status',
                'borrowed'
            )->pluck('book_id');

        $books =
            Book::whereNotIn(
                'id',
                $borrowedBookIds
            )
                ->orderBy(
                    'title',
                    'asc'
                )
                ->get();

        return view(
            'borrowkiosk',
            compact('books')
        );
    }

    /**
     * Find a student or personnel using RFID.
     */
    public function findBorrowerByRfid(
        string $rfid
    ): JsonResponse {
        $rfid = trim($rfid);

        if ($rfid === '') {
            return response()->json([
                'found' => false,

                'message' =>
                    'Please scan an RFID card.',
            ], 422);
        }

        /*
         * Search students.
         */
        $student =
            Student::where(
                'rfid_tag_uid',
                $rfid
            )->first();

        if ($student) {
            return response()->json([
                'found' =>
                    true,

                'borrower_id' =>
                    $student->id,

                'borrower_type' =>
                    'student',

                'borrower_number' =>
                    $student->student_number,

                'borrower_name' =>
                    trim(
                        ($student->firstname ?? '')
                        .
                        ' '
                        .
                        ($student->lastname ?? '')
                    ),
            ]);
        }

        /*
         * Search personnel.
         */
        $personnel =
            Personnel::where(
                'rfid_tag_uid',
                $rfid
            )->first();

        if ($personnel) {
            return response()->json([
                'found' =>
                    true,

                'borrower_id' =>
                    $personnel->id,

                'borrower_type' =>
                    'personnel',

                'borrower_number' =>
                    $personnel->employee_number,

                'borrower_name' =>
                    trim(
                        ($personnel->firstname ?? '')
                        .
                        ' '
                        .
                        ($personnel->lastname ?? '')
                    ),
            ]);
        }

        return response()->json([
            'found' => false,

            'message' =>
                'This RFID card is not registered.',
        ], 404);
    }

    /**
     * Store borrowing transactions.
     */
    public function storeBorrow(Request $request)
    {
        $validated =
            $request->validate([
                'rfid_tag_uid' => [
                    'required',
                    'string',
                ],

                'borrower_id' => [
                    'required',
                    'integer',
                ],

                'borrower_type' => [
                    'required',

                    Rule::in([
                        'student',
                        'personnel',
                    ]),
                ],

                'book_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'book_ids.*' => [
                    'required',
                    'integer',
                    'exists:books,id',
                ],
            ]);

        /*
         * Verify the scanned RFID again.
         */
        if (
            $validated['borrower_type']
            === 'student'
        ) {
            $borrower =
                Student::where(
                    'rfid_tag_uid',
                    $validated['rfid_tag_uid']
                )->first();
        } else {
            $borrower =
                Personnel::where(
                    'rfid_tag_uid',
                    $validated['rfid_tag_uid']
                )->first();
        }

        if (!$borrower) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The scanned RFID card is not registered.'
                );
        }

        if (
            (int) $validated['borrower_id']
            !== (int) $borrower->id
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The borrower does not match the scanned RFID.'
                );
        }

        $borrowedCount = 0;

        foreach (
            $validated['book_ids']
            as $bookId
        ) {
            $alreadyBorrowed =
                BookBorrow::where(
                    'book_id',
                    $bookId
                )
                    ->where(
                        'status',
                        'borrowed'
                    )
                    ->exists();

            if ($alreadyBorrowed) {
                continue;
            }

            BookBorrow::create([
                'book_id' =>
                    $bookId,

                'borrower_id' =>
                    $borrower->id,

                'borrower_type' =>
                    $validated['borrower_type'],

                'borrowed_at' =>
                    now(),

                'due_date' =>
                    now()->addDays(7),

                'returned_at' =>
                    null,

                'status' =>
                    'borrowed',

                'remarks' =>
                    null,
            ]);

            $borrowedCount++;
        }

        if ($borrowedCount === 0) {
            return redirect()
                ->route('borrow.kiosk')
                ->with(
                    'error',
                    'The selected book or books are already borrowed.'
                );
        }

        return redirect()
            ->route('borrow.kiosk')
            ->with(
                'success',
                $borrowedCount .
                ' book(s) borrowed successfully.'
            );
    }

    /**
     * Display borrowing-monitoring page.
     */
    public function borrowMonitoring()
    {
        $borrows =
            BookBorrow::with('book')
                ->orderBy(
                    'due_date',
                    'asc'
                )
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->get();

        /*
         * Attach the correct borrower record
         * for the initial page load.
         */
        foreach ($borrows as $borrow) {
            if (
                $borrow->borrower_type
                === 'student'
            ) {
                $borrow->borrower_record =
                    Student::find(
                        $borrow->borrower_id
                    );
            } elseif (
                $borrow->borrower_type
                === 'personnel'
            ) {
                $borrow->borrower_record =
                    Personnel::find(
                        $borrow->borrower_id
                    );
            } else {
                $borrow->borrower_record =
                    null;
            }
        }

        return view(
            'bookborrow',
            compact('borrows')
        );
    }

    /**
     * Provide fresh table records through AJAX.
     */
    public function borrowMonitoringData(): JsonResponse
    {
        $borrows =
            BookBorrow::with('book')
                ->orderBy(
                    'due_date',
                    'asc'
                )
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->get();

        $today = Carbon::today();

        $records = $borrows->map(
            function ($borrow) use ($today) {
                /*
                 * Find borrower information.
                 */
                if (
                    $borrow->borrower_type
                    === 'student'
                ) {
                    $borrower =
                        Student::find(
                            $borrow->borrower_id
                        );

                    $borrowerNumber =
                        $borrower->student_number
                        ?? '-';

                    $borrowerType =
                        'Student';
                } elseif (
                    $borrow->borrower_type
                    === 'personnel'
                ) {
                    $borrower =
                        Personnel::find(
                            $borrow->borrower_id
                        );

                    $borrowerNumber =
                        $borrower->employee_number
                        ?? '-';

                    $borrowerType =
                        'Personnel';
                } else {
                    $borrower = null;

                    $borrowerNumber =
                        '-';

                    $borrowerType =
                        'Unknown';
                }

                if ($borrower) {
                    $borrowerName = trim(
                        ($borrower->firstname ?? '')
                        .
                        ' '
                        .
                        ($borrower->lastname ?? '')
                    );
                } else {
                    $borrowerName =
                        'Unknown Borrower';
                }

                $deadline =
                    Carbon::parse(
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

                return [
                    'id' =>
                        $borrow->id,

                    'book_title' =>
                        $borrow->book->title
                        ?? 'Unknown Book',

                    'book_author' =>
                        $borrow->book->author
                        ?? '',

                    'call_number' =>
                        $borrow->book->call_number
                        ?? '-',

                    'borrower_name' =>
                        $borrowerName,

                    'borrower_number' =>
                        $borrowerNumber,

                    'borrower_type' =>
                        $borrowerType,

                    'borrowed_at' =>
                        $borrow->borrowed_at
                            ? Carbon::parse(
                                $borrow->borrowed_at
                            )->format(
                                'M d, Y'
                            )
                            : '-',

                    'deadline' =>
                        $deadline->format(
                            'M d, Y'
                        ),

                    'days' =>
                        $days,

                    'is_overdue' =>
                        $isOverdue,

                    'status' =>
                        $borrow->status,
                ];
            }
        )->values();

        return response()->json([
            'records' =>
                $records,

            'summary' => [
                'borrowed' =>
                    $borrows
                        ->where(
                            'status',
                            'borrowed'
                        )
                        ->count(),

                'overdue' =>
                    $borrows
                        ->filter(
                            function ($borrow) {
                                return
                                    $borrow->status
                                        !== 'returned'
                                    &&
                                    Carbon::parse(
                                        $borrow->due_date
                                    )->isPast();
                            }
                        )
                        ->count(),

                'returned' =>
                    $borrows
                        ->where(
                            'status',
                            'returned'
                        )
                        ->count(),
            ],
        ]);
    }

    /**
     * Mark a book as returned.
     */
    public function returnBook($id)
    {
        $borrow =
            BookBorrow::findOrFail($id);

        if (
            $borrow->status
            === 'returned'
        ) {
            return redirect()
                ->route('bookborrow')
                ->with(
                    'error',
                    'This book has already been returned.'
                );
        }

        $borrow->update([
            'status' =>
                'returned',

            'returned_at' =>
                now(),
        ]);

        return redirect()
            ->route('bookborrow')
            ->with(
                'success',
                'Book returned successfully.'
            );
    }

    /**
 * Display the public return kiosk.
 */
public function returnKiosk()
{
    return view('return');
}

/**
 * Find a student or personnel through RFID
 * and retrieve their active borrowed books.
 */
public function findReturnBorrowerByRfid(
    string $rfid
): JsonResponse {
    $rfid = trim($rfid);

    if ($rfid === '') {
        return response()->json([
            'found' => false,
            'message' => 'Please scan an RFID card.',
        ], 422);
    }

    /*
     * Look for a registered student.
     */
    $borrower = Student::where(
        'rfid_tag_uid',
        $rfid
    )->first();

    $borrowerType = 'student';
    $borrowerNumber = null;

    if ($borrower) {
        $borrowerNumber =
            $borrower->student_number;
    }

    /*
     * Look for registered personnel if
     * no student was found.
     */
    if (!$borrower) {
        $borrower = Personnel::where(
            'rfid_tag_uid',
            $rfid
        )->first();

        $borrowerType = 'personnel';

        if ($borrower) {
            $borrowerNumber =
                $borrower->employee_number;
        }
    }

    if (!$borrower) {
        return response()->json([
            'found' => false,
            'message' => 'This RFID card is not registered.',
        ], 404);
    }

    /*
     * Retrieve the borrower's active loans.
     */
    $borrows = BookBorrow::with('book')
        ->where(
            'borrower_id',
            $borrower->id
        )
        ->where(
            'borrower_type',
            $borrowerType
        )
        ->where(
            'status',
            'borrowed'
        )
        ->orderBy(
            'due_date',
            'asc'
        )
        ->get();

    $borrowedBooks = $borrows->map(
        function ($borrow) {
            $deadline = Carbon::parse(
                $borrow->due_date
            );

            return [
                /*
                 * The form must submit the borrowing
                 * transaction ID, not the book ID.
                 */
                'borrow_id' =>
                    $borrow->id,

                'book_id' =>
                    $borrow->book_id,

                'title' =>
                    $borrow->book->title
                    ?? 'Unknown Book',

                'author' =>
                    $borrow->book->author
                    ?? 'Unknown Author',

                'call_number' =>
                    $borrow->book->call_number
                    ?? '-',

                'isbn' =>
                    $borrow->book->isbn
                    ?? '-',

                'borrowed_at' =>
                    $borrow->borrowed_at
                        ? Carbon::parse(
                            $borrow->borrowed_at
                        )->format('M d, Y')
                        : '-',

                'due_date' =>
                    $deadline->format(
                        'M d, Y'
                    ),

                'is_overdue' =>
                    $deadline->isPast(),
            ];
        }
    )->values();

    return response()->json([
        'found' =>
            true,

        'borrower_id' =>
            $borrower->id,

        'borrower_type' =>
            $borrowerType,

        'borrower_number' =>
            $borrowerNumber,

        'borrower_name' =>
            trim(
                ($borrower->firstname ?? '')
                .
                ' '
                .
                ($borrower->lastname ?? '')
            ),

        'borrowed_books' =>
            $borrowedBooks,
    ]);
}

/**
 * Return the selected borrowing records.
 */
public function storeReturn(Request $request)
{
    $validated = $request->validate([
        'rfid_tag_uid' => [
            'required',
            'string',
        ],

        'borrower_id' => [
            'required',
            'integer',
        ],

        'borrower_type' => [
            'required',

            Rule::in([
                'student',
                'personnel',
            ]),
        ],

        'borrow_ids' => [
            'required',
            'array',
            'min:1',
        ],

        'borrow_ids.*' => [
            'required',
            'integer',
            'exists:book_borrows,id',
        ],
    ]);

    /*
     * Verify the RFID owner again on the server.
     */
    if (
        $validated['borrower_type']
        === 'student'
    ) {
        $borrower = Student::where(
            'rfid_tag_uid',
            $validated['rfid_tag_uid']
        )->first();
    } else {
        $borrower = Personnel::where(
            'rfid_tag_uid',
            $validated['rfid_tag_uid']
        )->first();
    }

    if (
        !$borrower
        ||
        (int) $borrower->id
            !== (int) $validated['borrower_id']
    ) {
        return back()
            ->withInput()
            ->with(
                'error',
                'The borrower does not match the scanned RFID.'
            );
    }

    /*
     * Retrieve only active records belonging
     * to this exact borrower.
     */
    $borrows = BookBorrow::whereIn(
        'id',
        $validated['borrow_ids']
    )
        ->where(
            'borrower_id',
            $borrower->id
        )
        ->where(
            'borrower_type',
            $validated['borrower_type']
        )
        ->where(
            'status',
            'borrowed'
        )
        ->get();

    if ($borrows->isEmpty()) {
        return back()
            ->withInput()
            ->with(
                'error',
                'No valid borrowed books were selected.'
            );
    }

    foreach ($borrows as $borrow) {
        $borrow->update([
            'status' =>
                'returned',

            'returned_at' =>
                now(),
        ]);
    }

    return redirect()
        ->route('return')
        ->with(
            'success',
            $borrows->count() .
            ' book(s) returned successfully.'
        );
}
}