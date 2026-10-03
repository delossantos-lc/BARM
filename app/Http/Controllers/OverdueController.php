<?php

namespace App\Http\Controllers;

use App\Models\BookBorrow;
use App\Models\BookFine;
use App\Models\FinePayment;
use App\Models\Personnel;
use App\Models\Student;
use App\Services\StaffTransactionNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OverdueController extends Controller
{
    public function index()
    {
        $overdues = BookBorrow::with('book')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', ['returned', 'lost', 'damaged'])
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($overdues as $borrow) {
            $borrow->borrower_record = $this->findBorrower($borrow);
        }

        return view('overdue', compact('overdues'));
    }

    public function markPaid(Request $request, BookFine $fine)
    {
        if ($fine->payment_status === 'paid') {
            return back()->with('error', 'This fine has already been paid.');
        }

        DB::transaction(function () use ($fine) {
            $amount = (float) ($fine->remaining_balance ?? $fine->total_amount ?? 0);

            if ($amount <= 0) {
                $fine->remaining_balance = 0;
                $fine->payment_status = 'paid';
                $fine->save();
                return;
            }

            FinePayment::create([
                'book_fine_id' => $fine->id,
                'amount' => $amount,
                'payment_date' => now(),
                'remarks' => 'Fine paid in full.',
            ]);

            $fine->remaining_balance = 0;
            $fine->payment_status = 'paid';
            $fine->save();
        });

        $emailResult = $this->notifyFineStatus(
            $fine,
            'Fine Paid',
            'Paid in Full'
        );

        return back()->with(
            'success',
            'Fine marked as paid successfully.' . $this->notificationMessage($emailResult)
        );
    }

    public function waive(Request $request, BookFine $fine)
    {
        if ($fine->payment_status === 'paid') {
            return back()->with('error', 'A paid fine cannot be waived.');
        }

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $fine->remaining_balance = 0;
        $fine->payment_status = 'waived';
        $fine->remarks = $validated['remarks'] ?? 'Fine waived by administrator.';
        $fine->save();

        $emailResult = $this->notifyFineStatus(
            $fine,
            'Fine Waived',
            'Waived'
        );

        return back()->with(
            'success',
            'Fine waived successfully.' . $this->notificationMessage($emailResult)
        );
    }

    public function reportLost(Request $request, BookBorrow $borrow)
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($borrow, $validated) {
            $borrow->status = 'lost';

            if (!empty($validated['remarks'])) {
                $borrow->remarks = $validated['remarks'];
            }

            $borrow->save();

            if ($borrow->book && Schema::hasColumn('books', 'status')) {
                $borrow->book->status = 'lost';
                $borrow->book->save();
            }
        });

        $emailResult = $this->notifyBorrowStatus(
            $borrow,
            'Book Reported Lost',
            'Lost'
        );

        return back()->with(
            'success',
            'Book has been reported as lost.' . $this->notificationMessage($emailResult)
        );
    }

    public function reportDamaged(Request $request, BookBorrow $borrow)
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($borrow, $validated) {
            $borrow->status = 'damaged';

            if (!empty($validated['remarks'])) {
                $borrow->remarks = $validated['remarks'];
            }

            $borrow->save();

            if ($borrow->book && Schema::hasColumn('books', 'status')) {
                $borrow->book->status = 'damaged';
                $borrow->book->save();
            }
        });

        $emailResult = $this->notifyBorrowStatus(
            $borrow,
            'Book Reported Damaged',
            'Damaged'
        );

        return back()->with(
            'success',
            'Book has been reported as damaged.' . $this->notificationMessage($emailResult)
        );
    }

    public function recordReplacementPayment(Request $request, BookFine $fine)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($fine->payment_status === 'paid') {
            return back()->with('error', 'This fine has already been fully paid.');
        }

        if ($fine->payment_status === 'waived') {
            return back()->with('error', 'A waived fine cannot receive a payment.');
        }

        $amount = (float) $validated['amount'];
        $remaining = (float) ($fine->remaining_balance ?? $fine->total_amount ?? 0);

        if ($remaining <= 0) {
            return back()->with('error', 'This fine has no remaining balance.');
        }

        if ($amount > $remaining) {
            return back()->with(
                'error',
                'Payment cannot be greater than the remaining balance.'
            );
        }

        DB::transaction(function () use ($fine, $amount, $validated, $remaining) {
            FinePayment::create([
                'book_fine_id' => $fine->id,
                'amount' => $amount,
                'payment_date' => now(),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $newBalance = max(0, $remaining - $amount);

            $fine->remaining_balance = $newBalance;
            $fine->payment_status = $newBalance <= 0 ? 'paid' : 'partial';

            if (!empty($validated['remarks'])) {
                $fine->remarks = $validated['remarks'];
            }

            $fine->save();
        });

        $fine->refresh();

        $emailResult = $this->notifyFineStatus(
            $fine,
            'Replacement Payment Recorded',
            ucfirst((string) $fine->payment_status),
            $amount
        );

        return back()->with(
            'success',
            'Replacement payment recorded successfully.'
            . $this->notificationMessage($emailResult)
        );
    }

    public function acknowledgement(BookFine $fine)
    {
        $fine->load([
            'bookBorrow.book',
            'payments',
        ]);

        if ($fine->payment_status !== 'paid') {
            return back()->with(
                'error',
                'An acknowledgement can only be printed for a paid fine.'
            );
        }

        return view('fine_acknowledgement', compact('fine'));
    }

    private function findBorrower(BookBorrow $borrow)
    {
        if ($borrow->borrower_type === 'student') {
            return Student::find($borrow->borrower_id);
        }

        if ($borrow->borrower_type === 'personnel') {
            return Personnel::find($borrow->borrower_id);
        }

        return null;
    }

    private function notifyFineStatus(
        BookFine $fine,
        string $event,
        string $status,
        ?float $paymentAmount = null
    ): array {
        $fine->loadMissing('bookBorrow.book');
        $borrow = $fine->bookBorrow;

        if (!$borrow) {
            return $this->sendNotification(
                $event,
                [
                    'Fine ID' => $fine->id,
                    'New Status' => $status,
                    'Payment Amount' => $paymentAmount,
                ]
            );
        }

        return $this->notifyBorrowStatus(
            $borrow,
            $event,
            $status,
            [
                'Fine Amount' => $fine->total_amount,
                'Remaining Balance' => $fine->remaining_balance,
                'Payment Amount' => $paymentAmount,
            ]
        );
    }

    private function notifyBorrowStatus(
        BookBorrow $borrow,
        string $event,
        string $status,
        array $extraDetails = []
    ): array {
        $borrow->loadMissing('book');
        $borrower = $this->findBorrower($borrow);

        $borrowerNumber = null;

        if ($borrower) {
            $borrowerNumber = $borrow->borrower_type === 'student'
                ? $borrower->student_number
                : $borrower->employee_number;
        }

        $borrowerName = $borrower
            ? trim(($borrower->firstname ?? '') . ' ' . ($borrower->lastname ?? ''))
            : 'Unknown Borrower';

        return $this->sendNotification(
            $event,
            array_merge(
                [
                    'Borrower' => $borrowerName,
                    'Borrower Type' => ucfirst((string) $borrow->borrower_type),
                    'Student/Employee Number' => $borrowerNumber ?? '-',
                    'Book' => $borrow->book?->title ?? 'Unknown Book',
                    'New Status' => $status,
                    'Remarks' => $borrow->remarks ?: '-',
                    'Processed By' => auth()->user()?->full_name ?? 'System User',
                ],
                $extraDetails
            ),
            [$borrower?->email]
        );
    }

    private function sendNotification(
        string $event,
        array $details,
        array $recipients = []
    ): array {
        try {
            $result = StaffTransactionNotifier::send(
                $event,
                $event,
                $details,
                array_values(array_filter($recipients))
            );

            return is_array($result)
                ? $result
                : ['message' => ''];
        } catch (\Throwable $exception) {
            report($exception);

            return [
                'message' => 'The record was saved, but the email notification could not be sent.',
            ];
        }
    }

    private function notificationMessage(array $result): string
    {
        $message = trim((string) ($result['message'] ?? ''));

        return $message === '' ? '' : ' ' . $message;
    }
}
