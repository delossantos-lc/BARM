<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel';

    protected $fillable = [
        'firstname',
        'lastname',
        'employee_number',
        'department',
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

    public function latestAttendance(): MorphOne
    {
        return $this->morphOne(
            Attendance::class,
            'attendable'
        )->latestOfMany();
    }
}