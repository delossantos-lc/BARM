<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BookReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'student_id',
        'student_name',
        'borrow_date',
        'pickup_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'borrow_date' => 'date',
    ];


    public function student(): BelongsTo
{
    return $this->belongsTo(
        Student::class,
        'student_record_id'
    );
}
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}