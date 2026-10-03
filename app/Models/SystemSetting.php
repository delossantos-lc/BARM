<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_title',
        'system_short_name',
        'institution_name',
        'department_name',
        'system_subtitle',
        'logo_path',
        'favicon_path',

        'timezone',
        'date_format',
        'time_format',

        'attendance_kiosk_title',
        'attendance_kiosk_subtitle',
        'attendance_result_duration',
        'student_attendance_enabled',
        'personnel_attendance_enabled',
        'allow_multiple_attendance_sessions',
        'automatic_time_out_enabled',
        'automatic_time_out_at',

        'borrowing_enabled',
        'returning_enabled',
        'reservation_enabled',
        'rfid_required',
        'reservation_pickup_instructions',

        'table_refresh_seconds',
        'records_per_page',
        'academic_year',
        'semester',
        'maintenance_mode',
        'maintenance_message',

        'administrator_email',
        'learning_commons_email',
        'contact_number',
        'office_address',
    ];

    protected $casts = [
        'attendance_result_duration' => 'integer',
        'student_attendance_enabled' => 'boolean',
        'personnel_attendance_enabled' => 'boolean',
        'allow_multiple_attendance_sessions' => 'boolean',
        'automatic_time_out_enabled' => 'boolean',

        'borrowing_enabled' => 'boolean',
        'returning_enabled' => 'boolean',
        'reservation_enabled' => 'boolean',
        'rfid_required' => 'boolean',

        'table_refresh_seconds' => 'integer',
        'records_per_page' => 'integer',
        'maintenance_mode' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            static::defaults()
        );
    }

    public static function defaults(): array
    {
        return [
            'system_title' => 'RFID-BARM System',
            'system_short_name' => 'BARM',
            'institution_name' => 'Lourdes College',
            'department_name' => 'Learning Commons',
            'system_subtitle' => 'Attendance and Resources Processing with RFID Integration',
            'timezone' => 'Asia/Manila',
            'date_format' => 'F j, Y',
            'time_format' => 'h:i:s A',
            'attendance_kiosk_title' => 'Attendance Kiosk',
            'attendance_kiosk_subtitle' => 'Learning Commons Attendance Monitoring',
            'attendance_result_duration' => 3,
            'student_attendance_enabled' => true,
            'personnel_attendance_enabled' => true,
            'allow_multiple_attendance_sessions' => false,
            'automatic_time_out_enabled' => false,
            'borrowing_enabled' => true,
            'returning_enabled' => true,
            'reservation_enabled' => true,
            'rfid_required' => true,
            'reservation_pickup_instructions' => 'Please proceed to the Learning Commons counter to claim your reservation.',
            'table_refresh_seconds' => 3,
            'records_per_page' => 10,
            'maintenance_mode' => false,
            'maintenance_message' => 'The system is temporarily unavailable due to scheduled maintenance.',
        ];
    }

    public function logoUrl(): string
    {
        return $this->logo_path
            ? asset('storage/' . $this->logo_path)
            : asset('Image/LC_LOGO.png');
    }
}
