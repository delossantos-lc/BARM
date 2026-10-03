<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BookBorrow;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Throwable;

class BookController extends Controller
{
    private const CSV_COLUMNS = ['title', 'author', 'call_number', 'sublocation', 'publisher', 'year', 'edition', 'format', 'content_type', 'media_type', 'carrier_type', 'isbn', 'issn', 'lccn', 'subjects', 'additional_details', 'rfid_tag_uid'];

    private function sendTransactionNotification(string $subject, string $heading, array $details, array $additionalRecipients = []): array
    {
        try {
            $result = StaffTransactionNotifier::send($subject, $heading, $details, $additionalRecipients);

            return is_array($result) ? $result : [
                'sent'    => 0,
                'failed'  => 0,
                'message' => 'Email notification returned no result.',
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return [
                'sent'    => 0,
                'failed'  => 1,
                'message' => 'The transaction was saved, but the email notification could not be sent.',
            ];
        }
    }

    private function getSystemSettings(): SystemSetting
    {
        return SystemSetting::current();
    }

    private function isFingerprintRequiredForTransaction(): bool
    {
        return (bool) (
            LibraryPolicy::current()->require_fingerprint_for_borrowing ?? false
        );
    }

    private function recordBorrowerAudit(
        string $actorName,
        string $action,
        string $module,
        ?string $affectedRecord,
        string $result,
        string $description,
        Request $request
    ): void {
        try {
            AuditLog::create([
                'user_id'         => auth()->id(),
                'actor_name'      => $actorName,
                'user_role'       => 'system',
                'action'          => $action,
                'module'          => $module,
                'affected_record' => $affectedRecord,
                'previous_value'  => null,
                'new_value'       => null,
                'ip_address'      => $request->ip(),
                'result'          => $result,
                'description'     => $description,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function index()
    {
        $books = Book::with('copies')->orderBy('created_at', 'desc')->get();

        return view('book', compact('books'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBook($request);
        $validated['unique_key'] = $this->makeUniqueBookKey();

        $rfid = $request->validate([
            'rfid_tag_uid' => ['nullable', 'string', 'max:191', 'unique:book_copies,rfid_tag_uid'],
        ])['rfid_tag_uid'] ?? null;

        DB::transaction(function () use ($validated, $rfid) {
            $book = new Book();
            $book->forceFill($validated);
            $book->save();

            $this->createBookCopy($book, $rfid);
        });

        return redirect()->route('book')->with('success', 'Book added successfully.');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('fail', 'The CSV file could not be opened.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);

            return back()->with('fail', 'The CSV file is empty.');
        }

        $header = array_map(function ($column) {
            $column = preg_replace('/^\xEF\xBB\xBF/', '', (string) $column);

            return strtolower(trim(str_replace([' ', '-'], '_', $column)));
        }, $header);

        if (!in_array('title', $header, true)) {
            fclose($handle);

            return back()->with('fail', 'The CSV file must contain a title column.');
        }

        if (count($header) !== count(array_unique($header))) {
            fclose($handle);

            return back()->with('fail', 'The CSV file contains duplicate column names.');
        }

        $unknownColumns = array_diff($header, self::CSV_COLUMNS);
        if ($unknownColumns) {
            fclose($handle);

            return back()->with('fail', 'Unknown CSV column(s): ' . implode(', ', $unknownColumns));
        }

        $books = [];
        $skipped = [];
        $seenRfids = [];
        $line = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $line++;

            if ($line > 1001) {
                $skipped[] = 'Only the first 1,000 records were processed.';
                break;
            }

            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            if (count($values) > count($header)) {
                $skipped[] = "Row {$line}: contains more values than the header.";
                continue;
            }

            $values = array_pad($values, count($header), null);
            $row = array_combine($header, $values);
            $row = array_map(fn ($value) => ($value = trim((string) $value)) === '' ? null : $value, $row);

            $validator = Validator::make($row, $this->csvBookRules());
            if ($validator->fails()) {
                $skipped[] = "Row {$line}: " . $validator->errors()->first();
                continue;
            }

            $rfid = $row['rfid_tag_uid'] ?? null;
            if ($rfid !== null) {
                if (isset($seenRfids[$rfid])) {
                    $skipped[] = "Row {$line}: RFID is repeated in this CSV file.";
                    continue;
                }
                $seenRfids[$rfid] = true;
            }

            $emptyBook = array_fill_keys(self::CSV_COLUMNS, null);
            $books[] = array_merge($emptyBook, $validator->validated());
        }

        fclose($handle);

        if (!$books) {
            return back()->with(
                'fail',
                implode(' ', array_slice($skipped, 0, 10)) ?: 'No valid books were found.'
            );
        }

        DB::transaction(function () use ($books) {
            foreach ($books as $book) {
                $rfid = $book['rfid_tag_uid'] ?? null;
                unset($book['rfid_tag_uid']);
                $book['unique_key'] = $this->makeUniqueBookKey();
                $book['is_reservable'] = true;

                $newBook = new Book();
                $newBook->forceFill($book);
                $newBook->save();

                $this->createBookCopy($newBook, $rfid);
            }
        });

        $message = count($books) . ' book(s) imported successfully.';
        if ($skipped) {
            $message .= ' ' . count($skipped) . ' row(s) skipped. ' . implode(' ', array_slice($skipped, 0, 5));
        }

        return redirect()->route('book')->with('success', $message);
    }

    public function downloadCsvTemplate()
    {
        $download = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, self::CSV_COLUMNS);
            fputcsv($file, [
                'Introduction to Programming',
                'Sample Author',
                'QA76.73',
                'Circulation',
                'Sample Publisher',
                '2026',
                '1st Edition',
                'Print',
                'Text',
                'Unmediated',
                'Volume',
                '9780000000000',
                null,
                null,
                'Computer Science; Programming',
                null,
                null,
            ]);
            fclose($file);
        };

        return response()->streamDownload($download, 'book-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function csvBookRules(): array
    {
        return [
            'title'              => ['required', 'string'],
            'author'             => ['nullable', 'string'],
            'call_number'        => ['nullable', 'string', 'max:255'],
            'sublocation'        => ['nullable', 'string', 'max:255'],
            'publisher'          => ['nullable', 'string'],
            'year'               => ['nullable', 'string', 'max:20'],
            'edition'            => ['nullable', 'string', 'max:255'],
            'format'             => ['nullable', 'string'],
            'content_type'       => ['nullable', 'string', 'max:255'],
            'media_type'         => ['nullable', 'string', 'max:255'],
            'carrier_type'       => ['nullable', 'string', 'max:255'],
            'isbn'               => ['nullable', 'string'],
            'issn'               => ['nullable', 'string'],
            'lccn'               => ['nullable', 'string', 'max:255'],
            'subjects'           => ['nullable', 'string'],
            'additional_details' => ['nullable', 'string'],
            'rfid_tag_uid'       => ['nullable', 'string', 'max:191', 'unique:book_copies,rfid_tag_uid'],
        ];
    }

    private function createBookCopy(Book $book, ?string $rfid): void
    {
        $copy = new BookCopy();
        $copy->forceFill([
            'book_id'      => $book->id,
            'barcode'      => 'BC-' . Str::uuid(),
            'rfid_tag_uid' => $rfid,
            'status'       => 'available',
        ]);
        $copy->save();
    }

    private function makeUniqueBookKey(): string
    {
        do {
            $key = 'BOOK-' . strtoupper(bin2hex(random_bytes(4)));
        } while (Book::where('unique_key', $key)->exists());

        return $key;
    }

    public function update(Request $request, Book $book)
    {
        abort_unless(auth()->check() && in_array(auth()->user()->access_level, ['admin', 'staff'], true), 403);

        $validated = $this->validateBook($request);
        $copy = $book->copies()->orderBy('id')->first();

        $rfid = $request->validate([
            'rfid_tag_uid' => [
                'nullable', 'string', 'max:191',
                Rule::unique('book_copies', 'rfid_tag_uid')->ignore($copy->id ?? 0),
            ],
        ])['rfid_tag_uid'] ?? null;

        DB::transaction(function () use ($book, $copy, $validated, $rfid) {
            $book->forceFill($validated);
            $book->save();

            if ($copy) {
                $copy->rfid_tag_uid = $rfid;
                $copy->save();
            } else {
                $this->createBookCopy($book, $rfid);
            }
        });

        return redirect()->route('book')->with('success', 'Book updated successfully.');
    }

    private function validateBook(Request $request): array
    {
        return $request->validate([
            'title'              => ['required', 'string'],
            'author'             => ['nullable', 'string'],
            'call_number'        => ['nullable', 'string', 'max:255'],
            'sublocation'        => ['nullable', 'string', 'max:255'],
            'publisher'          => ['nullable', 'string'],
            'year'               => ['nullable', 'string', 'max:20'],
            'edition'            => ['nullable', 'string', 'max:255'],
            'format'             => ['nullable', 'string'],
            'content_type'       => ['nullable', 'string', 'max:255'],
            'media_type'         => ['nullable', 'string', 'max:255'],
            'carrier_type'       => ['nullable', 'string', 'max:255'],
            'isbn'               => ['nullable', 'string'],
            'issn'               => ['nullable', 'string'],
            'lccn'               => ['nullable', 'string', 'max:255'],
            'subjects'           => ['nullable', 'string'],
            'additional_details' => ['nullable', 'string'],
            'is_reservable'      => ['required', 'boolean'],
        ]);
    }

    public function destroy(Book $book)
    {
        abort_unless(auth()->check() && auth()->user()->access_level === 'admin', 403);

        $hasActiveBorrow = BookBorrow::where('book_id', $book->id)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->exists();

        if ($hasActiveBorrow) {
            return redirect()->route('book')->with(
                'fail',
                'This book cannot be deleted because it is currently borrowed.'
            );
        }

        $hasBorrowHistory = BookBorrow::where('book_id', $book->id)->exists();
        if ($hasBorrowHistory) {
            return redirect()->route('book')->with(
                'fail',
                'This book cannot be deleted because it has borrowing history.'
            );
        }

        $book->delete();

        return redirect()->route('book')->with('success', 'Book deleted successfully.');
    }

    private function getPolicy(): LibraryPolicy
    {
        return LibraryPolicy::current();
    }

    private function getBorrowingLimit(LibraryPolicy $policy, string $borrowerType): int
    {
        if ($borrowerType === 'personnel') {
            return (int) $policy->personnel_borrowing_limit;
        }

        return (int) $policy->student_borrowing_limit;
    }

    private function getBorrowingPeriod(LibraryPolicy $policy, string $borrowerType): string
    {
        if ($borrowerType === 'student') {
            $days = (int) $policy->student_loan_period_days;

            return $days . ($days === 1 ? ' Day' : ' Days');
        }

        $value = (int) $policy->personnel_loan_period_value;
        $unit  = strtolower($policy->personnel_loan_period_unit ?? 'semester');

        if ($unit === 'semester') {
            return $value . ($value === 1 ? ' Semester' : ' Semesters');
        }

        if ($unit === 'months') {
            return $value . ($value === 1 ? ' Month' : ' Months');
        }

        return $value . ($value === 1 ? ' Day' : ' Days');
    }

    private function calculateDueDate(LibraryPolicy $policy, string $borrowerType): Carbon
    {
        $dueDate = Carbon::today();

        if ($borrowerType === 'student') {
            return $dueDate->addDays((int) $policy->student_loan_period_days);
        }

        $value = (int) $policy->personnel_loan_period_value;
        $unit  = strtolower($policy->personnel_loan_period_unit ?? 'semester');

        if ($unit === 'months') {
            return $dueDate->addMonths($value);
        }

        if ($unit === 'semester') {
            return $dueDate->addMonths($value * 5);
        }

        return $dueDate->addDays($value);
    }

    private function getActiveBorrowCount(int $borrowerId, string $borrowerType): int
    {
        return BookBorrow::where('borrower_id', $borrowerId)
            ->where('borrower_type', $borrowerType)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();
    }

    private function attachBorrowerRecord(BookBorrow $borrow): void
    {
        if ($borrow->borrower_type === 'student') {
            $borrow->borrower_record = Student::find($borrow->borrower_id);

            return;
        }

        if ($borrow->borrower_type === 'personnel') {
            $borrow->borrower_record = Personnel::find($borrow->borrower_id);

            return;
        }

        $borrow->borrower_record = null;
    }

    public function borrowForm()
    {
        abort_unless(
            $this->getSystemSettings()->borrowing_enabled,
            503,
            'Borrowing is currently disabled.'
        );

        $unavailableBookIds = BookBorrow::whereIn('status', ['borrowed', 'overdue'])->pluck('book_id');

        $books = Book::with('copies')
            ->whereNotIn('id', $unavailableBookIds)
            ->orderBy('title', 'asc')
            ->get();

        return view('borrowkiosk', compact('books'));
    }

    public function findBorrowerByRfid(string $rfid): JsonResponse
    {
        if (!$this->getSystemSettings()->borrowing_enabled) {
            return response()->json([
                'found'   => false,
                'message' => 'Borrowing is currently disabled.',
            ], 503);
        }

        $rfid = trim($rfid);
        if ($rfid === '') {
            return response()->json([
                'found'   => false,
                'message' => 'Please scan an RFID card.',
            ], 422);
        }

        $borrower = Student::where('rfid_tag_uid', $rfid)->first();
        $borrowerType = 'student';
        $borrowerNumber = null;

        if ($borrower) {
            $borrowerNumber = $borrower->student_number;
        }

        if (!$borrower) {
            $borrower = Personnel::where('rfid_tag_uid', $rfid)->first();
            $borrowerType = 'personnel';

            if ($borrower) {
                $borrowerNumber = $borrower->employee_number;
            }
        }

        if (!$borrower) {
            return response()->json([
                'found'   => false,
                'message' => 'This RFID card is not registered.',
            ], 404);
        }

        $policy           = $this->getPolicy();
        $borrowingLimit   = $this->getBorrowingLimit($policy, $borrowerType);
        $currentlyBorrowed= $this->getActiveBorrowCount($borrower->id, $borrowerType);
        $remainingLimit   = max(0, $borrowingLimit - $currentlyBorrowed);
        $borrowingPeriod  = $this->getBorrowingPeriod($policy, $borrowerType);

        $borrowerName = trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''));

        return response()->json([
            'found'              => true,
            'borrower_id'        => $borrower->id,
            'borrower_type'      => $borrowerType,
            'borrower_number'    => $borrowerNumber,
            'borrower_name'      => $borrowerName,
            'borrowing_period'   => $borrowingPeriod,
            'borrowing_limit'    => $borrowingLimit,
            'currently_borrowed' => $currentlyBorrowed,
            'remaining_limit'    => $remainingLimit,
            'can_borrow'         => $remainingLimit > 0,
            'message'            => $remainingLimit > 0
                ? 'RFID found: ' . $borrowerName
                : 'Borrowing limit reached.',
        ]);
    }

    public function storeBorrow(Request $request)
    {
        if (!$this->getSystemSettings()->borrowing_enabled) {
            return back()->with('error', 'Borrowing is currently disabled.');
        }

        $fingerprintRequired = $this->isFingerprintRequiredForTransaction();

        $rules = [
            'rfid_tag_uid'  => ['required', 'string'],
            'borrower_id'   => ['required', 'integer'],
            'borrower_type' => ['required', Rule::in(['student', 'personnel'])],
            'book_ids'      => ['required', 'array', 'min:1'],
            'book_ids.*'    => ['required', 'integer', 'distinct', 'exists:books,id'],
        ];

        if ($fingerprintRequired) {
            $rules['fingerprint_id'] = ['required', 'integer', 'between:1,127'];
        }

        $validated = $request->validate($rules);

        if ($validated['borrower_type'] === 'student') {
            $borrower = Student::where('rfid_tag_uid', $validated['rfid_tag_uid'])->first();
        } else {
            $borrower = Personnel::where('rfid_tag_uid', $validated['rfid_tag_uid'])->first();
        }

        if (!$borrower || (int) $borrower->id !== (int) $validated['borrower_id']) {
            return back()->withInput()->with('error', 'The borrower does not match the scanned RFID.');
        }

        if ($fingerprintRequired) {
            if ((int) $borrower->fingerprint_id !== (int) $validated['fingerprint_id']) {
                return back()->withInput()->with('error', 'Fingerprint verification does not match the RFID owner.');
            }
        }

        $borrowerType      = $validated['borrower_type'];
        $policy            = $this->getPolicy();
        $borrowingLimit    = $this->getBorrowingLimit($policy, $borrowerType);
        $currentlyBorrowed = $this->getActiveBorrowCount($borrower->id, $borrowerType);
        $remainingLimit    = max(0, $borrowingLimit - $currentlyBorrowed);
        $selectedBookIds   = array_values(array_unique($validated['book_ids']));

        if ($remainingLimit === 0) {
            return back()->withInput()->with('error', 'You have reached your borrowing limit.');
        }

        if (count($selectedBookIds) > $remainingLimit) {
            return back()->withInput()->with('error', 'The number of selected books exceeds your borrowing limit.');
        }

        $unavailableBooks = BookBorrow::whereIn('book_id', $selectedBookIds)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->exists();

        if ($unavailableBooks) {
            return back()->withInput()->with('error', 'One or more selected books are no longer available.');
        }

        $dueDate = $this->calculateDueDate($policy, $borrowerType);

        foreach ($selectedBookIds as $bookId) {
            BookBorrow::create([
                'book_id'       => $bookId,
                'borrower_id'   => $borrower->id,
                'borrower_type' => $borrowerType,
                'borrowed_at'   => Carbon::today(),
                'due_date'      => $dueDate->copy(),
                'returned_at'   => null,
                'status'        => 'borrowed',
                'remarks'       => null,
            ]);
        }

        $borrowerName   = trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''));
        $borrowerNumber = $borrowerType === 'student' ? $borrower->student_number : $borrower->employee_number;
        $bookTitles     = Book::whereIn('id', $selectedBookIds)->pluck('title')->all();

        $emailResult = $this->sendTransactionNotification(
            'New Book Borrowing',
            'Book Borrowed',
            [
                'Borrower'                => $borrowerName,
                'Borrower Type'           => ucfirst($borrowerType),
                'Student/Employee Number' => $borrowerNumber,
                'Books'                   => $bookTitles,
                'Due Date'                => $dueDate->format('F j, Y'),
                'Status'                  => 'Borrowed',
            ],
            [$borrower?->email]
        );

        $this->recordBorrowerAudit(
            $borrowerName,
            'Books Borrowed',
            'Borrowing',
            ucfirst($borrowerType) . ' ' . $borrowerNumber,
            'success',
            ucfirst($borrowerType) . ' borrowed ' . count($selectedBookIds)
                . ' book(s): ' . implode(', ', $bookTitles) . '.',
            $request
        );

        session()->forget('borrow_kiosk_access');

        return redirect()->route('monitor')->with(
            'success',
            count($selectedBookIds) . ' book(s) borrowed successfully. ' . $emailResult['message']
        );
    }

    public function borrowMonitoring()
    {
        $borrows = BookBorrow::with('book')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($borrows as $borrow) {
            $this->attachBorrowerRecord($borrow);
        }

        return view('bookborrow', compact('borrows'));
    }

    public function borrowMonitoringData(): JsonResponse
    {
        $borrows = BookBorrow::with('book')
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $today = Carbon::today();

        $records = $borrows->map(function ($borrow) use ($today) {
            if ($borrow->borrower_type === 'student') {
                $borrower       = Student::find($borrow->borrower_id);
                $borrowerNumber = $borrower->student_number ?? '-';
                $borrowerType   = 'Student';
            } elseif ($borrow->borrower_type === 'personnel') {
                $borrower       = Personnel::find($borrow->borrower_id);
                $borrowerNumber = $borrower->employee_number ?? '-';
                $borrowerType   = 'Personnel';
            } else {
                $borrower       = null;
                $borrowerNumber = '-';
                $borrowerType   = 'Unknown';
            }

            $borrowerName = $borrower
                ? trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''))
                : 'Unknown Borrower';

            $deadline  = Carbon::parse($borrow->due_date);
            $isOverdue = $borrow->status !== 'returned'
                && $deadline->copy()->endOfDay()->isPast();
            $days      = $today->diffInDays($deadline, false);

            return [
                'id'              => $borrow->id,
                'book_title'      => $borrow->book->title ?? 'Unknown Book',
                'book_author'     => $borrow->book->author ?? '',
                'call_number'     => $borrow->book->call_number ?? '-',
                'borrower_name'   => $borrowerName,
                'borrower_number' => $borrowerNumber,
                'borrower_type'   => $borrowerType,
                'borrowed_at'     => $borrow->borrowed_at
                    ? Carbon::parse($borrow->borrowed_at)->format('M d, Y')
                    : '-',
                'deadline'        => $deadline->format('M d, Y'),
                'days'            => $days,
                'is_overdue'      => $isOverdue,
                'status'          => $borrow->status,
            ];
        })->values();

        return response()->json([
            'records' => $records,
            'summary' => [
                'borrowed' => $borrows->where('status', 'borrowed')->count(),
                'overdue'  => $borrows->filter(function ($borrow) {
                    return $borrow->status !== 'returned'
                        && Carbon::parse($borrow->due_date)->endOfDay()->isPast();
                })->count(),
                'returned' => $borrows->where('status', 'returned')->count(),
            ],
        ]);
    }

    public function returnBook(Request $request, $id)
    {
        $borrow = BookBorrow::findOrFail($id);

        if ($borrow->status === 'returned') {
            return redirect()->route('bookborrow')->with(
                'error',
                'This book has already been returned.'
            );
        }

        $borrow->update([
            'status'      => 'returned',
            'returned_at' => now(),
        ]);

        $borrow->load('book');
        $this->attachBorrowerRecord($borrow);

        $borrowerRecord = $borrow->borrower_record;

        $emailResult = $this->sendTransactionNotification(
            'Book Returned from Monitoring',
            'Book Returned',
            [
                'Borrower'      => $borrowerRecord
                    ? trim(($borrowerRecord->firstname ?? '') . ' ' . ($borrowerRecord->lastname ?? ''))
                    : 'Unknown Borrower',
                'Borrower Type' => ucfirst((string) $borrow->borrower_type),
                'Book'          => $borrow->book?->title ?? 'Unknown Book',
                'Status'        => 'Returned',
                'Processed By'  => auth()->user()?->full_name ?? 'System User',
            ],
            [$borrowerRecord?->email]
        );

        $borrowerName = $borrowerRecord
            ? trim(($borrowerRecord->firstname ?? '') . ' ' . ($borrowerRecord->lastname ?? ''))
            : 'Unknown Borrower';

        $this->recordBorrowerAudit(
            $borrowerName,
            'Book Returned',
            'Returning',
            'Borrow #' . $borrow->id,
            'success',
            ucfirst((string) $borrow->borrower_type) . ' returned "'
                . ($borrow->book?->title ?? 'Unknown Book') . '".',
            $request
        );

        return redirect()->route('bookborrow')->with(
            'success',
            'Book returned successfully. ' . $emailResult['message']
        );
    }

    public function returnKiosk()
    {
        abort_unless(
            $this->getSystemSettings()->returning_enabled,
            503,
            'Returning is currently disabled.'
        );

        return view('return');
    }

    public function findReturnBorrowerByRfid(string $rfid): JsonResponse
    {
        if (!$this->getSystemSettings()->returning_enabled) {
            return response()->json([
                'found'   => false,
                'message' => 'Returning is currently disabled.',
            ], 503);
        }

        $rfid = trim($rfid);
        if ($rfid === '') {
            return response()->json([
                'found'   => false,
                'message' => 'Please scan an RFID card.',
            ], 422);
        }

        $borrower = Student::where('rfid_tag_uid', $rfid)->first();
        $borrowerType = 'student';
        $borrowerNumber = null;

        if ($borrower) {
            $borrowerNumber = $borrower->student_number;
        }

        if (!$borrower) {
            $borrower = Personnel::where('rfid_tag_uid', $rfid)->first();
            $borrowerType = 'personnel';

            if ($borrower) {
                $borrowerNumber = $borrower->employee_number;
            }
        }

        if (!$borrower) {
            return response()->json([
                'found'   => false,
                'message' => 'This RFID card is not registered.',
            ], 404);
        }

        $borrows = BookBorrow::with('book')
            ->where('borrower_id', $borrower->id)
            ->where('borrower_type', $borrowerType)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->get();

        $copyTags = BookCopy::whereIn('book_id', $borrows->pluck('book_id'))
            ->whereNotNull('rfid_tag_uid')
            ->get(['book_id', 'rfid_tag_uid'])
            ->groupBy('book_id');

        $borrowedBooks = $borrows->map(function ($borrow) use ($copyTags) {
            $deadline = Carbon::parse($borrow->due_date);

            return [
                'borrow_id'   => $borrow->id,
                'book_id'     => $borrow->book_id,
                'rfid_tags'   => ($copyTags->get($borrow->book_id) ?? collect())
                    ->pluck('rfid_tag_uid')->values()->all(),
                'title'       => $borrow->book->title ?? 'Unknown Book',
                'author'      => $borrow->book->author ?? 'Unknown Author',
                'call_number' => $borrow->book->call_number ?? '-',
                'isbn'        => $borrow->book->isbn ?? '-',
                'borrowed_at' => $borrow->borrowed_at
                    ? Carbon::parse($borrow->borrowed_at)->format('M d, Y')
                    : '-',
                'due_date'    => $deadline->format('M d, Y'),
                'is_overdue'  => $deadline->copy()->endOfDay()->isPast(),
            ];
        })->values();

        return response()->json([
            'found'           => true,
            'borrower_id'     => $borrower->id,
            'borrower_type'   => $borrowerType,
            'borrower_number' => $borrowerNumber,
            'borrower_name'   => trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? '')),
            'borrowed_books'  => $borrowedBooks,
        ]);
    }

    public function storeReturn(Request $request)
    {
        if (!$this->getSystemSettings()->returning_enabled) {
            return back()->with('error', 'Returning is currently disabled.');
        }

        $fingerprintRequired = $this->isFingerprintRequiredForTransaction();

        $rules = [
            'rfid_tag_uid'  => ['required', 'string'],
            'borrower_id'   => ['required', 'integer'],
            'borrower_type' => ['required', Rule::in(['student', 'personnel'])],
            'borrow_ids'    => ['required', 'array', 'min:1'],
            'borrow_ids.*'  => ['required', 'integer', 'distinct', 'exists:book_borrows,id'],
        ];

        if ($fingerprintRequired) {
            $rules['fingerprint_id'] = ['required', 'integer', 'between:1,127'];
        }

        $validated = $request->validate($rules);

        if ($validated['borrower_type'] === 'student') {
            $borrower = Student::where('rfid_tag_uid', $validated['rfid_tag_uid'])->first();
        } else {
            $borrower = Personnel::where('rfid_tag_uid', $validated['rfid_tag_uid'])->first();
        }

        if (!$borrower || (int) $borrower->id !== (int) $validated['borrower_id']) {
            return back()->withInput()->with('error', 'The borrower does not match the scanned RFID.');
        }

        if ($fingerprintRequired) {
            if ((int) $borrower->fingerprint_id !== (int) $validated['fingerprint_id']) {
                return back()->withInput()->with('error', 'Fingerprint verification does not match the RFID owner.');
            }
        }

        $borrows = BookBorrow::whereIn('id', $validated['borrow_ids'])
            ->where('borrower_id', $borrower->id)
            ->where('borrower_type', $validated['borrower_type'])
            ->whereIn('status', ['borrowed', 'overdue'])
            ->get();

        if ($borrows->isEmpty()) {
            return back()->withInput()->with('error', 'No valid borrowed books were selected.');
        }

        foreach ($borrows as $borrow) {
            $borrow->update([
                'status'      => 'returned',
                'returned_at' => now(),
            ]);
        }

        $borrows->load('book');

        $borrowerName   = trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''));
        $borrowerNumber = $validated['borrower_type'] === 'student'
            ? $borrower->student_number
            : $borrower->employee_number;

        $emailResult = $this->sendTransactionNotification(
            'Book Return Completed',
            'Book Returned',
            [
                'Borrower'                => $borrowerName,
                'Borrower Type'           => ucfirst($validated['borrower_type']),
                'Student/Employee Number' => $borrowerNumber,
                'Books'                   => $borrows->pluck('book.title')->filter()->all(),
                'Status'                  => 'Returned',
            ],
            [$borrower?->email]
        );

        $returnedTitles = $borrows->pluck('book.title')->filter()->all();

        $this->recordBorrowerAudit(
            $borrowerName,
            'Books Returned',
            'Returning',
            ucfirst($validated['borrower_type']) . ' ' . $borrowerNumber,
            'success',
            ucfirst($validated['borrower_type']) . ' returned '
                . $borrows->count() . ' book(s): '
                . implode(', ', $returnedTitles) . '.',
            $request
        );

        session()->forget('return_kiosk_access');

        return redirect()->route('monitor')->with(
            'success',
            $borrows->count() . ' book(s) returned successfully. ' . $emailResult->message
        );
    }
}