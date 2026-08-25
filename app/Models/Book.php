<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'unique_key',
        'title',
        'author',
        'call_number',
        'sublocation',
        'publisher',
        'year',
        'edition',
        'format',
        'content_type',
        'media_type',
        'carrier_type',
        'isbn',
        'issn',
        'lccn',
        'subjects',
        'additional_details'
    ];

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function borrowings()
    {
        return $this->hasMany(BookBorrow::class);
    }

    public function reservations()
    {
        return $this->hasMany(BookReservation::class);
    }
}