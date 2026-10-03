<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'firstname',
        'lastname',
        'employeeid',
        'email',
        'password',
        'access_level',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION USERNAME
    |--------------------------------------------------------------------------
    */

    public function username(): string
    {
        return 'employeeid';
    }

    /*
    |--------------------------------------------------------------------------
    | FULL NAME
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->firstname
            . ' '
            . $this->lastname
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCES
    |--------------------------------------------------------------------------
    */

    public function attendances(): HasMany
    {
        return $this->hasMany(
            Attendance::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINES PROCESSED BY THIS USER
    |--------------------------------------------------------------------------
    */

    public function processedFines(): HasMany
    {
        return $this->hasMany(
            BookFine::class,
            'processed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS RECEIVED BY THIS USER
    |--------------------------------------------------------------------------
    */

    public function receivedFinePayments(): HasMany
    {
        return $this->hasMany(
            FinePayment::class,
            'received_by'
        );
    }
}