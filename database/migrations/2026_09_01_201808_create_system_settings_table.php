<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // System identity
            $table->string('system_title')
                ->default('RFID-BARM System');

            $table->string('system_short_name')
                ->default('BARM');

            $table->string('institution_name')
                ->default('Lourdes College');

            $table->string('department_name')
                ->default('Learning Commons');

            $table->string('system_subtitle')
                ->nullable();

            $table->string('logo_path')
                ->nullable();

            $table->string('favicon_path')
                ->nullable();

            // Localization
            $table->string('timezone')
                ->default('Asia/Manila');

            $table->string('date_format')
                ->default('F j, Y');

            $table->string('time_format')
                ->default('h:i:s A');

            // Attendance kiosk
            $table->string('attendance_kiosk_title')
                ->default('Attendance Kiosk');

            $table->string('attendance_kiosk_subtitle')
                ->default('Learning Commons Attendance Monitoring');

            $table->unsignedInteger('attendance_result_duration')
                ->default(3);

            $table->boolean('student_attendance_enabled')
                ->default(true);

            $table->boolean('personnel_attendance_enabled')
                ->default(true);

            $table->boolean('allow_multiple_attendance_sessions')
                ->default(false);

            $table->boolean('automatic_time_out_enabled')
                ->default(false);

            $table->time('automatic_time_out_at')
                ->nullable();

            // Library modules
            $table->boolean('borrowing_enabled')
                ->default(true);

            $table->boolean('returning_enabled')
                ->default(true);

            $table->boolean('reservation_enabled')
                ->default(true);

            $table->boolean('rfid_required')
                ->default(true);

            $table->text('reservation_pickup_instructions')
                ->nullable();

            // General system behavior
            $table->unsignedInteger('table_refresh_seconds')
                ->default(3);

            $table->unsignedInteger('records_per_page')
                ->default(10);

            $table->string('academic_year')
                ->nullable();

            $table->string('semester')
                ->nullable();

            $table->boolean('maintenance_mode')
                ->default(false);

            $table->text('maintenance_message')
                ->nullable();

            // Contact information
            $table->string('administrator_email')
                ->nullable();

            $table->string('learning_commons_email')
                ->nullable();

            $table->string('contact_number')
                ->nullable();

            $table->text('office_address')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};