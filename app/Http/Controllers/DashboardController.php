<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY CARDS
        |--------------------------------------------------------------------------
        */

        $borrowedToday = DB::table('book_borrows')
            ->whereDate('borrowed_at', $today)
            ->count();

        $returnedToday = DB::table('book_borrows')
            ->whereDate('returned_at', $today)
            ->count();

        $currentlyBorrowed = DB::table('book_borrows')
            ->whereNull('returned_at')
            ->whereIn('status', [
                'borrowed',
                'overdue'
            ])
            ->count();

        $overdueBooks = DB::table('book_borrows')
            ->whereNull('returned_at')
            ->whereDate('due_date', '<', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | GENERAL BORROWING ANALYTICS
        |--------------------------------------------------------------------------
        */

        $totalBorrowings = DB::table('book_borrows')
            ->count();

        $uniqueBorrowers = DB::table('book_borrows')
            ->select(
                'borrower_type',
                'borrower_id'
            )
            ->distinct()
            ->get()
            ->count();

        $studentBorrowings = DB::table('book_borrows')
            ->where('borrower_type', 'student')
            ->count();

        $personnelBorrowings = DB::table('book_borrows')
            ->where('borrower_type', 'personnel')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | STUDENT BORROWING TOTALS
        |--------------------------------------------------------------------------
        */

        $studentTotals = DB::table('book_borrows as bb')
            ->join('students as s', function ($join) {
                $join->on(
                    's.id',
                    '=',
                    'bb.borrower_id'
                );

                $join->where(
                    'bb.borrower_type',
                    '=',
                    'student'
                );
            })
            ->selectRaw("
                bb.borrower_id,
                CONCAT(
                    s.firstname,
                    ' ',
                    s.lastname
                ) AS borrower_name,
                'student' AS borrower_type,
                COUNT(bb.id) AS total_borrowed
            ")
            ->groupBy(
                'bb.borrower_id',
                's.firstname',
                's.lastname'
            );

        /*
        |--------------------------------------------------------------------------
        | PERSONNEL BORROWING TOTALS
        |--------------------------------------------------------------------------
        */

        $personnelTotals = DB::table('book_borrows as bb')
            ->join('personnel as p', function ($join) {
                $join->on(
                    'p.id',
                    '=',
                    'bb.borrower_id'
                );

                $join->where(
                    'bb.borrower_type',
                    '=',
                    'personnel'
                );
            })
            ->selectRaw("
                bb.borrower_id,
                CONCAT(
                    p.firstname,
                    ' ',
                    p.lastname
                ) AS borrower_name,
                'personnel' AS borrower_type,
                COUNT(bb.id) AS total_borrowed
            ")
            ->groupBy(
                'bb.borrower_id',
                'p.firstname',
                'p.lastname'
            );

        /*
        |--------------------------------------------------------------------------
        | TOP BORROWERS
        |--------------------------------------------------------------------------
        */

        $combinedBorrowers = $studentTotals
            ->unionAll($personnelTotals);

        $topBorrowers = DB::query()
            ->fromSub(
                $combinedBorrowers,
                'combined_borrowers'
            )
            ->orderByDesc('total_borrowed')
            ->orderBy('borrower_name')
            ->limit(10)
            ->get();

        $topBorrower = $topBorrowers->first();

        /*
        |--------------------------------------------------------------------------
        | TOP BORROWER CHART DATA
        |--------------------------------------------------------------------------
        */

        $borrowerNames = $topBorrowers
            ->pluck('borrower_name')
            ->values();

        $borrowerCounts = $topBorrowers
            ->pluck('total_borrowed')
            ->map(function ($value) {
                return (int) $value;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | MOST BORROWED BOOKS
        |--------------------------------------------------------------------------
        */

        $mostBorrowedBooks = DB::table('book_borrows as bb')
            ->join(
                'books as b',
                'b.id',
                '=',
                'bb.book_id'
            )
            ->select(
                'b.id',
                'b.title as book_title',
                DB::raw(
                    'COUNT(bb.id) as total_borrowed'
                )
            )
            ->groupBy(
                'b.id',
                'b.title'
            )
            ->orderByDesc('total_borrowed')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SEVEN-DAY BORROWING ACTIVITY
        |--------------------------------------------------------------------------
        */

        $startDate = $today
            ->copy()
            ->subDays(6);

        $sevenDayActivity = DB::table('book_borrows')
            ->selectRaw(
                'DATE(borrowed_at) as borrow_day'
            )
            ->selectRaw(
                'COUNT(*) as total'
            )
            ->whereDate(
                'borrowed_at',
                '>=',
                $startDate
            )
            ->whereDate(
                'borrowed_at',
                '<=',
                $today
            )
            ->groupByRaw(
                'DATE(borrowed_at)'
            )
            ->pluck(
                'total',
                'borrow_day'
            );

        $dailyLabels = collect();
        $dailyValues = collect();

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = $today
                ->copy()
                ->subDays($daysAgo);

            $dateKey = $date->toDateString();

            $dailyLabels->push(
                $date->format('M d')
            );

            $dailyValues->push(
                (int) (
                    $sevenDayActivity[$dateKey] ?? 0
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OTHER DATABASE COUNTS
        |--------------------------------------------------------------------------
        |
        | These values connect the other tables to the dashboard. They can be
        | displayed in additional dashboard cards later.
        |
        */

        $totalBooks = DB::table('books')
            ->count();

        $totalBookCopies = DB::table('book_copies')
            ->count();

        $availableCopies = DB::table('book_copies')
            ->where('status', 'available')
            ->count();

        $borrowedCopies = DB::table('book_copies')
            ->where('status', 'borrowed')
            ->count();

        $reservedCopies = DB::table('book_copies')
            ->where('status', 'reserved')
            ->count();

        $lostCopies = DB::table('book_copies')
            ->where('status', 'lost')
            ->count();

        $damagedCopies = DB::table('book_copies')
            ->where('status', 'damaged')
            ->count();

        $totalStudents = DB::table('students')
            ->count();

        $totalPersonnel = DB::table('personnel')
            ->count();

        $pendingReservations = DB::table('book_reservations')
            ->where('status', 'pending')
            ->count();

        $readyReservations = DB::table('book_reservations')
            ->where('status', 'ready')
            ->count();

        $pickedUpReservations = DB::table('book_reservations')
            ->where('status', 'picked_up')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD VIEW
        |--------------------------------------------------------------------------
        |
        | Your current routes use view('index'), so this controller also loads
        | resources/views/index.blade.php.
        |
        */

        return view('index', compact(
            'borrowedToday',
            'returnedToday',
            'currentlyBorrowed',
            'overdueBooks',

            'totalBorrowings',
            'uniqueBorrowers',
            'studentBorrowings',
            'personnelBorrowings',

            'topBorrowers',
            'topBorrower',
            'borrowerNames',
            'borrowerCounts',

            'mostBorrowedBooks',

            'dailyLabels',
            'dailyValues',

            'totalBooks',
            'totalBookCopies',
            'availableCopies',
            'borrowedCopies',
            'reservedCopies',
            'lostCopies',
            'damagedCopies',

            'totalStudents',
            'totalPersonnel',

            'pendingReservations',
            'readyReservations',
            'pickedUpReservations'
        ));
    }
}