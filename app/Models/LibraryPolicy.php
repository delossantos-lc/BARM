<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryPolicy extends Model
{
    protected $fillable = [
        'student_borrowing_limit',
        'student_loan_period_days',

        'personnel_borrowing_limit',
        'personnel_loan_period_value',
        'personnel_loan_period_unit',
        'personnel_standard_return_days',
        'allow_personnel_fine_exemption',

        'require_fingerprint_for_borrowing',

        'fine_per_overdue_day',
        'fine_grace_period_days',

        'reserved_book_loan_period_hours',
        'reserved_book_fine_per_hour',
        'allow_reserved_book_renewal',
        'renew_reserved_only_without_request',

        'reservation_expiration_period_days',
        'maximum_active_reservations',

        'lost_book_same_title_required',
        'lost_book_same_edition_required',
        'use_current_price_for_lost_book',
        'lost_book_processing_fee',

        'damaged_book_penalty',
        'allow_borrowing_with_unpaid_fines',
    ];

    protected $casts = [
        'student_borrowing_limit' => 'integer',
        'student_loan_period_days' => 'integer',

        'personnel_borrowing_limit' => 'integer',
        'personnel_loan_period_value' => 'integer',
        'personnel_standard_return_days' => 'integer',
        'allow_personnel_fine_exemption' => 'boolean',

        'require_fingerprint_for_borrowing' => 'boolean',

        'fine_per_overdue_day' => 'decimal:2',
        'fine_grace_period_days' => 'integer',

        'reserved_book_loan_period_hours' => 'integer',
        'reserved_book_fine_per_hour' => 'decimal:2',
        'allow_reserved_book_renewal' => 'boolean',
        'renew_reserved_only_without_request' => 'boolean',

        'reservation_expiration_period_days' => 'integer',
        'maximum_active_reservations' => 'integer',

        'lost_book_same_title_required' => 'boolean',
        'lost_book_same_edition_required' => 'boolean',
        'use_current_price_for_lost_book' => 'boolean',
        'lost_book_processing_fee' => 'decimal:2',

        'damaged_book_penalty' => 'decimal:2',
        'allow_borrowing_with_unpaid_fines' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | FINES CREATED USING THIS POLICY
    |--------------------------------------------------------------------------
    */

    public function fines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'library_policy_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CURRENT GLOBAL POLICY
    |--------------------------------------------------------------------------
    */

    public static function current(): self
    {
        return static::firstOrCreate(
            [],
            [
                'student_borrowing_limit' => 5,
                'student_loan_period_days' => 5,

                'personnel_borrowing_limit' => 20,
                'personnel_loan_period_value' => 1,
                'personnel_loan_period_unit' => 'semester',
                'personnel_standard_return_days' => 30,
                'allow_personnel_fine_exemption' => true,

                'require_fingerprint_for_borrowing' => false,

                'fine_per_overdue_day' => 3.00,
                'fine_grace_period_days' => 0,

                'reserved_book_loan_period_hours' => 1,
                'reserved_book_fine_per_hour' => 3.00,
                'allow_reserved_book_renewal' => true,
                'renew_reserved_only_without_request' => true,

                'reservation_expiration_period_days' => 1,
                'maximum_active_reservations' => 2,

                'lost_book_same_title_required' => true,
                'lost_book_same_edition_required' => true,
                'use_current_price_for_lost_book' => true,
                'lost_book_processing_fee' => 50.00,

                'damaged_book_penalty' => 0.00,

                'allow_borrowing_with_unpaid_fines' => false,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINGERPRINT REQUIREMENT
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the borrower must scan their fingerprint
     * before borrowing, returning, or reserving a book.
     */
    public function requiresFingerprint(): bool
    {
        return (bool) $this->require_fingerprint_for_borrowing;
    }

    /**
     * Guard a controller action based on the fingerprint policy.
     *
     * Usage inside a borrow/return/reserve controller:
     *
     *     $policy = LibraryPolicy::current();
     *
     *     if ($policy->requiresFingerprint()
     *         && ! session('fingerprint_verified')) {
     *         return redirect()
     *             ->back()
     *             ->with('error', 'Fingerprint scan is required.');
     *     }
     */
    public function fingerprintRequired(): bool
    {
        return $this->requiresFingerprint();
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWING LIMIT
    |--------------------------------------------------------------------------
    */

    public function borrowingLimit(
        string $borrowerType
    ): int {
        if ($borrowerType === 'personnel') {
            return (int)
                $this->personnel_borrowing_limit;
        }

        return (int)
            $this->student_borrowing_limit;
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWING PERIOD LABEL
    |--------------------------------------------------------------------------
    */

    public function borrowingPeriodLabel(
        string $borrowerType
    ): string {
        if ($borrowerType === 'student') {
            $days = (int)
                $this->student_loan_period_days;

            return $days
                . (
                    $days === 1
                        ? ' Day'
                        : ' Days'
                );
        }

        $value = (int)
            $this->personnel_loan_period_value;

        $unit =
            $this->personnel_loan_period_unit;

        if ($unit === 'semester') {
            return $value
                . (
                    $value === 1
                        ? ' Semester'
                        : ' Semesters'
                );
        }

        if ($unit === 'months') {
            return $value
                . (
                    $value === 1
                        ? ' Month'
                        : ' Months'
                );
        }

        return $value
            . (
                $value === 1
                    ? ' Day'
                    : ' Days'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATE DUE DATE
    |--------------------------------------------------------------------------
    */

    public function calculateDueDate(
        string $borrowerType
    ): Carbon {
        $date = Carbon::today();

        if ($borrowerType === 'student') {
            return $date->addDays(
                (int)
                    $this->student_loan_period_days
            );
        }

        $value = (int)
            $this->personnel_loan_period_value;

        $unit =
            $this->personnel_loan_period_unit;

        if ($unit === 'months') {
            return $date->addMonths(
                $value
            );
        }

        if ($unit === 'semester') {
            return $date->addMonths(
                $value * 5
            );
        }

        return $date->addDays(
            $value
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAL OVERDUE FINE
    |--------------------------------------------------------------------------
    */

    public function calculateOverdueFine(
        int $daysOverdue
    ): array {
        $chargeableDays = max(
            0,
            $daysOverdue
            - (int)
                $this->fine_grace_period_days
        );

        $rate = (float)
            $this->fine_per_overdue_day;

        $total =
            $chargeableDays
            * $rate;

        return [
            'rate' => $rate,

            'quantity' =>
                $chargeableDays,

            'grace_period' =>
                (int)
                    $this->fine_grace_period_days,

            'base_amount' =>
                round($total, 2),

            'processing_fee' =>
                0.00,

            'total_amount' =>
                round($total, 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RESERVED BOOK OVERDUE FINE
    |--------------------------------------------------------------------------
    */

    public function calculateReservedFine(
        int $hoursOverdue
    ): array {
        $chargeableHours = max(
            0,
            $hoursOverdue
        );

        $rate = (float)
            $this->reserved_book_fine_per_hour;

        $total =
            $chargeableHours
            * $rate;

        return [
            'rate' => $rate,

            'quantity' =>
                $chargeableHours,

            'grace_period' =>
                0,

            'base_amount' =>
                round($total, 2),

            'processing_fee' =>
                0.00,

            'total_amount' =>
                round($total, 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOST BOOK FINE
    |--------------------------------------------------------------------------
    */

    public function calculateLostBookFine(
        float $replacementCost
    ): array {
        $replacementCost = max(
            0,
            $replacementCost
        );

        $processingFee = (float)
            $this->lost_book_processing_fee;

        $total =
            $replacementCost
            + $processingFee;

        return [
            'rate' =>
                round($replacementCost, 2),

            'quantity' => 1,

            'grace_period' => 0,

            'base_amount' =>
                round($replacementCost, 2),

            'processing_fee' =>
                round($processingFee, 2),

            'total_amount' =>
                round($total, 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DAMAGED BOOK FINE
    |--------------------------------------------------------------------------
    */

    public function calculateDamagedBookFine(): array
    {
        $penalty = (float)
            $this->damaged_book_penalty;

        return [
            'rate' =>
                round($penalty, 2),

            'quantity' => 1,

            'grace_period' => 0,

            'base_amount' =>
                round($penalty, 2),

            'processing_fee' => 0.00,

            'total_amount' =>
                round($penalty, 2),
        ];
    }
}