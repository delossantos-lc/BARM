<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'actor_name',
        'user_role',
        'action',
        'module',
        'affected_record',
        'previous_value',
        'new_value',
        'ip_address',
        'result',
        'description',
    ];

    protected $casts = [
        'previous_value' => 'array',
        'new_value' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }
}