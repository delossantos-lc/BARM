<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookFine extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_borrow_id',
        'library_policy_id',

        'fine_type',

        'rate_used',
        'quantity',
        'grace_period_used',

        'base_amount',
        'processing_fee',
        'total_amount',
        'amount_paid',
        'remaining_balance',

        'payment_status',

        'replacement_cost',
        'physical_replacement_received',
        'replacement_title',
        'replacement_edition',

        'damage_level',

        'waiver_reason',
        'remarks',

        'assessed_at',
        'paid_at',
        'waived_at',
        'replacement_received_at',

        'processed_by',
    ];

    protected $casts = [
        'rate_used' => 'decimal:2',
        'quantity' => 'integer',
        'grace_period_used' => 'integer',

        'base_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',

        'replacement_cost' => 'decimal:2',

        'physical_replacement_received' =>
            'boolean',

        'assessed_at' => 'datetime',
        'paid_at' => 'datetime',
        'waived_at' => 'datetime',
        'replacement_received_at' =>
            'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | BORROW TRANSACTION
    |--------------------------------------------------------------------------
    */

    public function bookBorrow(): BelongsTo
    {
        return $this->belongsTo(
            BookBorrow::class,
            'book_borrow_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LIBRARY POLICY
    |--------------------------------------------------------------------------
    */

    public function policy(): BelongsTo
    {
        return $this->belongsTo(
            LibraryPolicy::class,
            'library_policy_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN OR STAFF WHO PROCESSED THE FINE
    |--------------------------------------------------------------------------
    */

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function payments(): HasMany
    {
        return $this->hasMany(
            FinePayment::class,
            'book_fine_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OUTSTANDING STATUS
    |--------------------------------------------------------------------------
    */

    public function getIsOutstandingAttribute(): bool
    {
        return in_array(
            $this->payment_status,
            [
                'unpaid',
                'partially_paid',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SETTLED STATUS
    |--------------------------------------------------------------------------
    */

    public function getIsSettledAttribute(): bool
    {
        return in_array(
            $this->payment_status,
            [
                'paid',
                'waived',
                'replaced',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RECALCULATE PAYMENT TOTALS
    |--------------------------------------------------------------------------
    */

    public function recalculatePaymentTotals(): void
    {
        $amountPaid = (float)
            $this->payments()
                ->whereIn(
                    'payment_type',
                    [
                        'fine_payment',
                        'replacement_payment',
                        'processing_fee',
                    ]
                )
                ->sum('amount');

        $totalAmount = (float)
            $this->total_amount;

        $remainingBalance = max(
            0,
            $totalAmount - $amountPaid
        );

        if ($remainingBalance <= 0) {
            $status = 'paid';
            $paidAt = $this->paid_at
                ?? now();
        } elseif ($amountPaid > 0) {
            $status = 'partially_paid';
            $paidAt = null;
        } else {
            $status = 'unpaid';
            $paidAt = null;
        }

        $this->forceFill([
            'amount_paid' =>
                round($amountPaid, 2),

            'remaining_balance' =>
                round($remainingBalance, 2),

            'payment_status' =>
                $status,

            'paid_at' =>
                $paidAt,
        ])->save();
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS WAIVED
    |--------------------------------------------------------------------------
    */

    public function markAsWaived(
        string $reason,
        ?int $processedBy = null
    ): void {
        $this->forceFill([
            'payment_status' =>
                'waived',

            'remaining_balance' =>
                0,

            'waiver_reason' =>
                $reason,

            'waived_at' =>
                now(),

            'processed_by' =>
                $processedBy,
        ])->save();
    }

    /*
    |--------------------------------------------------------------------------
    | MARK PHYSICAL REPLACEMENT RECEIVED
    |--------------------------------------------------------------------------
    */

    public function markReplacementReceived(
        ?string $title = null,
        ?string $edition = null,
        ?int $processedBy = null
    ): void {
        $this->forceFill([
            'payment_status' =>
                'replaced',

            'physical_replacement_received' =>
                true,

            'replacement_title' =>
                $title,

            'replacement_edition' =>
                $edition,

            'remaining_balance' =>
                0,

            'replacement_received_at' =>
                now(),

            'processed_by' =>
                $processedBy,
        ])->save();
    }
}