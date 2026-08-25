<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getBorrowerRecordAttribute()
    {
        if ($this->borrower_type === 'student') {
            return Student::find(
                $this->borrower_id
            );
        }

        if ($this->borrower_type === 'personnel') {
            return Personnel::find(
                $this->borrower_id
            );
        }

        return null;
    }
}