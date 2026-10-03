<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookBorrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'borrower_id',
        'borrower_type',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'remarks',
        'book_copy_id',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOK
    |--------------------------------------------------------------------------
    */

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINES
    |--------------------------------------------------------------------------
    */

    public function fines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'book_borrow_id'
        );
    }

    /*
     * Unpaid and partially paid fines.
     */
    public function outstandingFines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'book_borrow_id'
        )->whereIn(
            'payment_status',
            [
                'unpaid',
                'partially_paid',
            ]
        );
    }

    /*
     * Overdue fine attached to this transaction.
     */
    public function overdueFines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'book_borrow_id'
        )->where(
            'fine_type',
            'overdue'
        );
    }

    /*
     * Lost-book fine attached to this transaction.
     */
    public function lostFines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'book_borrow_id'
        )->where(
            'fine_type',
            'lost'
        );
    }

    /*
     * Damaged-book fine attached to this transaction.
     */
    public function damagedFines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'book_borrow_id'
        )->where(
            'fine_type',
            'damaged'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWER RECORD
    |--------------------------------------------------------------------------
    */

    public function getBorrowerRecordAttribute()
    {
        if (
            $this->borrower_type
            === 'student'
        ) {
            return Student::find(
                $this->borrower_id
            );
        }

        if (
            $this->borrower_type
            === 'personnel'
        ) {
            return Personnel::find(
                $this->borrower_id
            );
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWER NAME
    |--------------------------------------------------------------------------
    */

    public function getBorrowerNameAttribute(): string
    {
        $borrower =
            $this->borrower_record;

        if (!$borrower) {
            return 'Unknown Borrower';
        }

        return trim(
            ($borrower->firstname ?? '')
            . ' '
            . ($borrower->lastname ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BORROWER NUMBER
    |--------------------------------------------------------------------------
    */

    public function getBorrowerNumberAttribute(): string
    {
        $borrower =
            $this->borrower_record;

        if (!$borrower) {
            return '-';
        }

        if (
            $this->borrower_type
            === 'student'
        ) {
            return (string) (
                $borrower->student_number
                ?? '-'
            );
        }

        return (string) (
            $borrower->employee_number
            ?? '-'
        );
    }



    public function bookCopy(): BelongsTo
{
    return $this->belongsTo(
        BookCopy::class,
        'book_copy_id'
    );
}
    /*
    |--------------------------------------------------------------------------
    | TOTAL OUTSTANDING BALANCE
    |--------------------------------------------------------------------------
    */

    public function getOutstandingBalanceAttribute(): float
    {
        if ($this->relationLoaded('fines')) {
            return (float) $this->fines
                ->whereIn(
                    'payment_status',
                    [
                        'unpaid',
                        'partially_paid',
                    ]
                )
                ->sum('remaining_balance');
        }

        return (float) $this->outstandingFines()
            ->sum('remaining_balance');
    }
}