<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Book extends Model
{
    use HasFactory, SoftDeletes;
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
        'additional_details',
        'replacement_price'
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