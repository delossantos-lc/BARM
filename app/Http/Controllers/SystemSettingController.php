<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SystemSettingController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index(Request $request): View
    {
        $this->ensureAdministrator($request);

        $settings = SystemSetting::current();

        return view('settings', compact('settings'));
    }

    /**
     * Update the single global system settings record.
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $this->ensureAdministrator($request);

        $validated = $request->validate(
            [
                // System identity
                'system_title' => ['required', 'string', 'max:150'],
                'system_short_name' => ['required', 'string', 'max:30'],
                'institution_name' => ['required', 'string', 'max:150'],
                'department_name' => ['nullable', 'string', 'max:150'],
                'system_subtitle' => ['nullable', 'string', 'max:255'],
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],

                // Localization
                'timezone' => ['required', 'in:Asia/Manila'],
                'date_format' => [
                    'required',
                    Rule::in([
                        'F j, Y',
                        'M d, Y',
                        'm/d/Y',
                        'd/m/Y',
                    ]),
                ],
                'time_format' => [
                    'required',
                    Rule::in(['h:i:s A']),
                ],

                // Attendance kiosk
                'attendance_kiosk_title' => ['required', 'string', 'max:150'],
                'attendance_kiosk_subtitle' => ['nullable', 'string', 'max:255'],
                'attendance_result_duration' => ['required', 'integer', 'min:1', 'max:30'],
                'student_attendance_enabled' => ['required', 'boolean'],
                'personnel_attendance_enabled' => ['required', 'boolean'],
                'allow_multiple_attendance_sessions' => ['required', 'boolean'],
                'automatic_time_out_enabled' => ['required', 'boolean'],
                'automatic_time_out_at' => [
                    'nullable',
                    'required_if:automatic_time_out_enabled,1',
                    'regex:/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',
                ],

                // Library module switches
                'borrowing_enabled' => ['required', 'boolean'],
                'returning_enabled' => ['required', 'boolean'],
                'reservation_enabled' => ['required', 'boolean'],
                'rfid_required' => ['required', 'boolean'],
                'reservation_pickup_instructions' => ['nullable', 'string', 'max:1000'],

                // General system behavior
                'table_refresh_seconds' => ['required', 'integer', 'min:1', 'max:300'],
                'records_per_page' => ['required', 'integer', 'in:10,25,50,100'],
                'academic_year' => [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^\d{4}-\d{4}$/',
                ],
                'semester' => ['nullable', 'in:First Semester,Second Semester,Summer'],
                'maintenance_mode' => ['required', 'boolean'],
                'maintenance_message' => [
                    'nullable',
                    'string',
                    'max:1000',
                    'required_if:maintenance_mode,1',
                ],

                // Contact information
                'administrator_email' => ['nullable', 'email', 'max:255'],
                'learning_commons_email' => ['nullable', 'email', 'max:255'],
                'contact_number' => ['nullable', 'string', 'max:50'],
                'office_address' => ['nullable', 'string', 'max:500'],
            ],
            [
                'academic_year.regex' => 'The academic year must use the format 2026-2027.',
                'automatic_time_out_at.required_if' => 'Select an automatic time-out time.',
                'automatic_time_out_at.regex' => 'Enter a valid time, such as 05:30 PM.',
                'maintenance_message.required_if' => 'Enter a maintenance message when maintenance mode is enabled.',
                'logo.max' => 'The logo must not be larger than 2 MB.',
            ]
        );

        // Convert every switch to a real boolean value. This also safely
        // handles unchecked checkboxes if the Blade form is changed later.
        foreach ($this->booleanFields() as $field) {
            $validated[$field] = $request->boolean($field);
        }

        // Disabled form controls are not submitted. Clear dependent values
        // when their corresponding feature is switched off.
        if (!$validated['automatic_time_out_enabled']) {
            $validated['automatic_time_out_at'] = null;
        } elseif (!empty($validated['automatic_time_out_at'])) {
            // HTML time inputs normally submit HH:MM, while a MySQL TIME
            // column may return HH:MM:SS. Store one consistent format.
            $validated['automatic_time_out_at'] = strlen(
                $validated['automatic_time_out_at']
            ) === 5
                ? $validated['automatic_time_out_at'] . ':00'
                : $validated['automatic_time_out_at'];
        }

        if (!$validated['maintenance_mode']) {
            $validated['maintenance_message'] = $request->input(
                'maintenance_message',
                'The system is temporarily unavailable due to scheduled maintenance.'
            );
        }

        $settings = SystemSetting::current();

        $oldLogoPath = $settings->logo_path;
        $newLogoPath = null;

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                $settings,
                &$newLogoPath
            ): void {
                if ($request->hasFile('logo')) {
                    $newLogoPath = $request
                        ->file('logo')
                        ->store('system/logos', 'public');

                    $validated['logo_path'] = $newLogoPath;
                }

                // The uploaded file itself is not a database column.
                unset($validated['logo']);

                $settings->fill($validated);
                $settings->save();
            });

            // Remove the previous uploaded logo only after the database update
            // succeeds. The default public/Image/LC_LOGO.png is never removed.
            if (
                $newLogoPath !== null &&
                !empty($oldLogoPath) &&
                $oldLogoPath !== $newLogoPath &&
                Storage::disk('public')->exists($oldLogoPath)
            ) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            $this->recordAudit(
                request: $request,
                result: 'success',
                description: 'Updated the global system settings.'
            );

            $settings->refresh();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'System settings updated successfully.',
                    'settings' => $this->settingsPayload($settings),
                ]);
            }

            return redirect()
                ->route('settings.index')
                ->with('success', 'System settings updated successfully.');
        } catch (Throwable $exception) {
            // If the file was stored but the database update failed, remove
            // only that newly uploaded file to avoid leaving an unused file.
            if (
                $newLogoPath !== null &&
                Storage::disk('public')->exists($newLogoPath)
            ) {
                Storage::disk('public')->delete($newLogoPath);
            }

            report($exception);

            $this->recordAudit(
                request: $request,
                result: 'failed',
                description: 'Failed to update the global system settings.'
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to update the settings. Please try again.',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Unable to update the settings. Please try again.');
        }
    }

    /**
     * Permit administrators only.
     *
     * The project has used different role keys in different login versions,
     * including access_level and user_type. Check the active session first,
     * then fall back to the authenticated user model when Laravel Auth is used.
     */
    private function ensureAdministrator(Request $request): void
    {
        $sessionRoles = [
            $request->session()->get('access_level'),
            $request->session()->get('session_access_level'),
            $request->session()->get('user_type'),
            $request->session()->get('role'),
            $request->session()->get('accessLevel'),
        ];

        $authenticatedUser = $request->user();

        $userRoles = $authenticatedUser
            ? [
                $authenticatedUser->access_level ?? null,
                $authenticatedUser->user_type ?? null,
                $authenticatedUser->role ?? null,
            ]
            : [];

        $roles = array_merge($sessionRoles, $userRoles);

        $isAdministrator = collect($roles)->contains(function ($role) {
            return strtolower(trim((string) $role)) === 'admin';
        });

        abort_unless(
            $isAdministrator,
            403,
            'Administrators only. Please log out and sign in again.'
        );
    }

    /**
     * Checkbox/boolean columns accepted by the settings page.
     */
    private function booleanFields(): array
    {
        return [
            'student_attendance_enabled',
            'personnel_attendance_enabled',
            'allow_multiple_attendance_sessions',
            'automatic_time_out_enabled',
            'borrowing_enabled',
            'returning_enabled',
            'reservation_enabled',
            'rfid_required',
            'maintenance_mode',
        ];
    }

    /**
     * Safe values sent to the browser after an AJAX settings update.
     */
    private function settingsPayload(SystemSetting $settings): array
    {
        return [
            'system_title' => $settings->system_title,
            'system_short_name' => $settings->system_short_name,
            'institution_name' => $settings->institution_name,
            'department_name' => $settings->department_name,
            'system_subtitle' => $settings->system_subtitle,
            // A version query forces every open page to request the new file
            // instead of displaying a browser-cached copy of the old logo.
            'logo_url' => $this->versionedLogoUrl($settings),
            'timezone' => $settings->timezone,
            'academic_year' => $settings->academic_year,
            'table_refresh_seconds' => (int) $settings->table_refresh_seconds,
            'attendance_kiosk_title' => $settings->attendance_kiosk_title,
            'attendance_kiosk_subtitle' => $settings->attendance_kiosk_subtitle,
            'reservation_pickup_instructions' => $settings->reservation_pickup_instructions,
        ];
    }

    private function versionedLogoUrl(SystemSetting $settings): string
    {
        $url = $settings->logoUrl();
        $version = $settings->updated_at?->getTimestamp() ?? time();
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . 'v=' . $version;
    }

    /**
     * Defaults used when the settings table has no record yet.
     */
    private function defaultSettings(): array
    {
        return SystemSetting::defaults();
    }

    /**
     * Record the settings change without storing previous/new setting values.
     * Audit failures must not prevent administrators from saving settings.
     */
    private function recordAudit(
        Request $request,
        string $result,
        string $description
    ): void {
        try {
            AuditLogger::record(
                action: 'Update',
                module: 'System Settings',
                affectedRecord: 'Global System Settings',
                previousValue: null,
                newValue: null,
                result: $result,
                description: $description,
                request: $request
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
