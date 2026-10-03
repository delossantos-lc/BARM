<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_fine_id',
        'amount',
        'payment_type',
        'payment_method',
        'receipt_number',
        'paid_at',
        'remarks',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | FINE
    |--------------------------------------------------------------------------
    */

    public function fine(): BelongsTo
    {
        return $this->belongsTo(
            BookFine::class,
            'book_fine_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN OR STAFF WHO RECEIVED PAYMENT
    |--------------------------------------------------------------------------
    */

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'received_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE RECEIPT NUMBER
    |--------------------------------------------------------------------------
    */

    public static function generateReceiptNumber(): string
    {
        do {
            $receiptNumber =
                'RFID-BARM-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(
                    substr(
                        bin2hex(
                            random_bytes(3)
                        ),
                        0,
                        6
                    )
                );
        } while (
            static::where(
                'receipt_number',
                $receiptNumber
            )->exists()
        );

        return $receiptNumber;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE FINE AFTER PAYMENT
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::created(
            function (
                FinePayment $payment
            ) {
                $payment->fine
                    ?->recalculatePaymentTotals();
            }
        );

        static::updated(
            function (
                FinePayment $payment
            ) {
                $payment->fine
                    ?->recalculatePaymentTotals();
            }
        );

        static::deleted(
            function (
                FinePayment $payment
            ) {
                $payment->fine
                    ?->recalculatePaymentTotals();
            }
        );
    }
}