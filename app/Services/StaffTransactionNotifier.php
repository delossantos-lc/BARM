<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class StaffTransactionNotifier
{
    public static function send(
        string $subject,
        string $event,
        array $details = [],
        array $additionalRecipients = []
    ): array {
        $sent = 0;
        $failed = 0;

        try {
            $settings = SystemSetting::current();

            $recipients = User::query()
                ->whereRaw('LOWER(status) = ?', ['active'])
                ->whereIn(
                    DB::raw('LOWER(access_level)'),
                    ['admin', 'staff']
                )
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email')
                ->push($settings->administrator_email)
                ->push($settings->learning_commons_email)
                ->merge($additionalRecipients)
                ->filter(function ($email) {
                    return filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    );
                })
                ->unique()
                ->values();

            if ($recipients->isEmpty()) {
                return [
                    'sent' => 0,
                    'failed' => 0,
                    'message' => 'No notification recipients were found.',
                ];
            }

            $occurredAt = now(
                $settings->timezone ?: 'Asia/Manila'
            );

            foreach ($recipients as $recipient) {
                try {
                    Mail::send(
                        'emails.staff_transaction_notification',
                        compact(
                            'settings',
                            'event',
                            'details',
                            'occurredAt'
                        ),
                        function ($message) use (
                            $recipient,
                            $subject,
                            $settings
                        ) {
                            $message
                                ->to($recipient)
                                ->subject(
                                    '[' .
                                    ($settings->system_short_name ?: 'BARM') .
                                    '] ' .
                                    $subject
                                );
                        }
                    );

                    $sent++;
                } catch (Throwable $exception) {
                    $failed++;
                    report($exception);
                }
            }
        } catch (Throwable $exception) {
            $failed++;
            report($exception);
        }

        if ($sent > 0 && $failed === 0) {
            $message = 'Email notification sent successfully.';
        } elseif ($sent > 0) {
            $message = 'Email notification sent, but some recipients failed.';
        } else {
            $message = 'The transaction was saved, but the email notification could not be sent.';
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'message' => $message,
        ];
    }
}