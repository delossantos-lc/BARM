<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotificationController extends Controller
{
    /**
     * Send a test notification to one entered address, or to every active
     * Admin and Staff recipient when test_email is left blank.
     */
    public function test(Request $request): RedirectResponse
    {
        $this->ensureAdministrator($request);

        $validated = $request->validate([
            'test_email' => ['nullable', 'email', 'max:255'],
        ]);

        $settings = SystemSetting::current();
        $testEmail = $validated['test_email'] ?? null;

        if ($testEmail) {
            $recipients = collect([$testEmail]);
        } else {
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
                ->push($settings->learning_commons_email);
        }

        $recipients = $recipients
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return back()->with(
                'error',
                'No valid active Admin or Staff email addresses were found.'
            );
        }

        $event = 'Email Configuration Test';
        $details = [
            'Result' => 'Laravel reached the notification controller.',
            'Requested By' => $request->user()->full_name ?? 'Administrator',
            'Recipients Found' => $recipients->count(),
        ];
        $occurredAt = now($settings->timezone ?: 'Asia/Manila');
        $sent = 0;
        $errors = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::send(
                    'emails.staff_transaction_notification',
                    compact('settings', 'event', 'details', 'occurredAt'),
                    function ($message) use ($recipient, $settings) {
                        $message
                            ->to($recipient)
                            ->subject(
                                '[' . ($settings->system_short_name ?: 'BARM') .
                                '] Email Configuration Test'
                            );
                    }
                );

                $sent++;
            } catch (Throwable $exception) {
                report($exception);
                $errors[] = $recipient . ': ' . $exception->getMessage();
            }
        }

        if ($errors !== []) {
            return back()
                ->with('error', 'Email test failed: ' . $errors[0])
                ->with('email_test_sent', $sent);
        }

        return back()->with(
            'success',
            "Test email sent successfully to {$sent} recipient(s)."
        );
    }

    private function ensureAdministrator(Request $request): void
    {
        $roles = [
            $request->session()->get('access_level'),
            $request->session()->get('session_access_level'),
            $request->user()->access_level ?? null,
        ];

        $isAdministrator = collect($roles)->contains(
            fn ($role) => strtolower(trim((string) $role)) === 'admin'
        );

        abort_unless($isAdministrator, 403, 'Administrators only.');
    }
}
