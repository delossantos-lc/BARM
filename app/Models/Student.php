<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'student_number',
        'year_level',
        'course_program',
        'rfid_tag_uid',
        'fingerprint_id',
        'email',
        
    ];


    protected $casts = [
    'fingerprint_id' => 'integer',
];
    /*
    |--------------------------------------------------------------------------
    | ALL ATTENDANCE RECORDS
    |--------------------------------------------------------------------------
    */

    public function attendances(): MorphMany
    {
        return $this->morphMany(
            Attendance::class,
            'attendable'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LATEST ATTENDANCE
    |--------------------------------------------------------------------------
    */


    public function reservations(): HasMany
{
    return $this->hasMany(
        BookReservation::class,
        'student_record_id'
    );
}
    public function latestAttendance(): MorphOne
    {
        return $this->morphOne(
            Attendance::class,
            'attendable'
        )->latestOfMany();
    }
}