<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(
        string $action,
        string $module,
        ?string $affectedRecord = null,
        ?array $previousValue = null,
        ?array $newValue = null,
        string $result = 'success',
        ?string $description = null,
        ?Request $request = null
    ): AuditLog {
        $user = Auth::user();

        $request = $request
            ?? request();

        $actorName = $user
            ? trim(
                ($user->firstname ?? '')
                . ' ' .
                ($user->lastname ?? '')
            )
            : 'System';

        $userRole = $user
            ? strtolower(
                $user->access_level
                ?? 'staff'
            )
            : 'system';

        if (
            !in_array(
                $userRole,
                [
                    'admin',
                    'staff',
                    'system',
                ],
                true
            )
        ) {
            $userRole = 'staff';
        }

        /*
         * Never save passwords or tokens
         * inside previous/new values.
         */
        $previousValue =
            self::removeSensitiveValues(
                $previousValue
            );

        $newValue =
            self::removeSensitiveValues(
                $newValue
            );

        return AuditLog::create([
            'user_id' =>
                $user?->id,

            'actor_name' =>
                $actorName,

            'user_role' =>
                $userRole,

            'action' =>
                $action,

            'module' =>
                $module,

            'affected_record' =>
                $affectedRecord,

            'previous_value' =>
                $previousValue,

            'new_value' =>
                $newValue,

            'ip_address' =>
                $request?->ip(),

            'result' =>
                $result === 'failed'
                    ? 'failed'
                    : 'success',

            'description' =>
                $description,
        ]);
    }

    private static function removeSensitiveValues(
        ?array $values
    ): ?array {
        if ($values === null) {
            return null;
        }

        $sensitiveFields = [
            'password',
            'password_confirmation',
            'remember_token',
            'token',
            '_token',
        ];

        foreach ($sensitiveFields as $field) {
            if (
                array_key_exists(
                    $field,
                    $values
                )
            ) {
                $values[$field] =
                    '[REDACTED]';
            }
        }

        return $values;
    }
}