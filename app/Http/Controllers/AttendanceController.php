<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Personnel;
use App\Models\Attendance;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Throwable;

class AttendanceController extends Controller
{
    private function recordAttendanceAudit(
        string $actorName,
        string $action,
        ?string $affectedRecord,
        string $result,
        string $description,
        Request $request
    ): void {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'actor_name' => $actorName,
                'user_role' => 'system',
                'action' => $action,
                'module' => 'Attendance',
                'affected_record' => $affectedRecord,
                'previous_value' => null,
                'new_value' => null,
                'ip_address' => $request->ip(),
                'result' => $result,
                'description' => $description,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Check if the current time is within allowed working hours.
     */
    private function isWithinWorkingHours($now, $settings)
    {
        // If working hours are not configured, allow all times
        if (empty($settings->working_hours_start) || empty($settings->working_hours_end)) {
            return ['allowed' => true, 'message' => ''];
        }

        $start = Carbon::parse($settings->working_hours_start);
        $end = Carbon::parse($settings->working_hours_end);

        // If the current time is before start or after end
        if ($now->lt($start) || $now->gt($end)) {
            return [
                'allowed' => false,
                'message' => 'Attendance is only allowed between ' . $start->format('h:i A') . ' and ' . $end->format('h:i A') . '.'
            ];
        }

        return ['allowed' => true, 'message' => ''];
    }

    /**
     * Auto time-out students who forgot to time out.
     */
    public function autoCutoffForgottenAttendance()
    {
        $settings = SystemSetting::current();
        $timezone = $settings->timezone ?: 'Asia/Manila';
        $today = Carbon::now($timezone)->toDateString();
        
        // Find all attendance records from PREVIOUS days that have a time_in but no time_out
        $forgotten = Attendance::whereNull('time_out')
            ->whereDate('date', '<', $today) 
            ->get();

        foreach ($forgotten as $record) {
            // Set time_out to 11:59:59 PM of that specific date
            $record->time_out = Carbon::parse($record->date)->setTime(23, 59, 59);
            
            // If you have a status column, uncomment this line:
            // $record->status = 'cut_off'; 
            
            $record->save();

            $this->recordAttendanceAudit(
                'System Auto-Cutoff',
                'Auto Time Out',
                'Attendance #' . $record->id,
                'success',
                'Student forgot to time out. Automatically timed out.',
                request()
            );
        }

        return response()->json(['message' => 'Auto cutoff completed.', 'count' => $forgotten->count()]);
    }

    /**
     * RFID Scanner
     */
    public function scanRfid(Request $request)
    {
        $settings = SystemSetting::current();
        $timezone = $settings->timezone ?: 'Asia/Manila';
        $now = Carbon::now($timezone);

        // 1. Check Working Hours
        $workingHoursCheck = $this->isWithinWorkingHours($now, $settings);
        if (!$workingHoursCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $workingHoursCheck['message'],
                'rfid_tag_uid' => $request->input('rfid_tag_uid'),
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate RFID
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'rfid_tag_uid' => 'required|string',
        ]);

        $rfid = trim((string) $request->input('rfid_tag_uid'));

        /*
        |--------------------------------------------------------------------------
        | Search Student
        |--------------------------------------------------------------------------
        */
        $person = Student::where('rfid_tag_uid', $rfid)->first();

        /*
        |--------------------------------------------------------------------------
        | If not Student, search Personnel
        |--------------------------------------------------------------------------
        */
        if (!$person) {
            $person = Personnel::where('rfid_tag_uid', $rfid)->first();
        }

        /*
        |--------------------------------------------------------------------------
        | RFID Not Registered
        |--------------------------------------------------------------------------
        */
        if (!$person) {
            $this->recordAttendanceAudit(
                'Unknown RFID',
                'RFID Scan Failed',
                $rfid,
                'failed',
                'Attendance scan failed because the RFID card is not registered.',
                $request
            );

            return response()->json([
                'success' => false,
                'message' => 'RFID card is not registered.',
                'rfid_tag_uid' => $rfid,
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Person Type
        |--------------------------------------------------------------------------
        */
        $personType = $person instanceof Student ? 'Student' : 'Personnel';

        if ($person instanceof Student && !$settings->student_attendance_enabled) {
            $this->recordAttendanceAudit(
                trim($person->firstname . ' ' . $person->lastname),
                'Clock In Failed',
                'Student #' . $person->id,
                'failed',
                'Student attendance is currently disabled.',
                $request
            );

            return response()->json([
                'success' => false,
                'message' => 'Student attendance is currently disabled.',
            ], 403);
        }

        if ($person instanceof Personnel && !$settings->personnel_attendance_enabled) {
            $this->recordAttendanceAudit(
                trim($person->firstname . ' ' . $person->lastname),
                'Clock In Failed',
                'Personnel #' . $person->id,
                'failed',
                'Personnel attendance is currently disabled.',
                $request
            );

            return response()->json([
                'success' => false,
                'message' => 'Personnel attendance is currently disabled.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Find Open Attendance
        |--------------------------------------------------------------------------
        */
        $openAttendance = $person
            ->attendances()
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | CLOCK OUT
        |--------------------------------------------------------------------------
        */
        if ($openAttendance) {
            $openAttendance->update([
                'time_out' => $now,
                // 'status' => 'completed' // Uncomment if using status column
            ]);

            $this->recordAttendanceAudit(
                trim($person->firstname . ' ' . $person->lastname),
                'Clock Out',
                'Attendance #' . $openAttendance->id,
                'success',
                $personType . ' clocked out successfully.',
                $request
            );

            return response()->json([
                'success' => true,
                'action' => 'clock_out',
                'message' => $person->firstname . ' ' . $person->lastname . ' clocked out successfully.',
                'person_type' => $personType,
                'person_id' => $person->id,
                'name' => $person->firstname . ' ' . $person->lastname,
                'rfid_tag_uid' => $person->rfid_tag_uid,
                'student_number' => $person instanceof Student ? $person->student_number : null,
                'year_level' => $person instanceof Student ? $person->year_level : null,
                'course_program' => $person instanceof Student ? $person->course_program : null,
                'employee_number' => $person instanceof Personnel ? $person->employee_number : null,
                'department' => $person instanceof Personnel ? $person->department : null,
                'attendance_id' => $openAttendance->id,
                'date' => $now->toDateString(),
                'time_in' => Carbon::parse($openAttendance->time_in)->timezone($timezone)->toIso8601String(),
                'time_out' => $now->toIso8601String(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CLOCK IN
        |--------------------------------------------------------------------------
        */
        $attendance = Attendance::create([
            'attendable_id' => $person->getKey(),
            'attendable_type' => $person instanceof Student ? 'student' : 'personnel',
            'date' => $now->toDateString(),
            'time_in' => $now,
            'time_out' => null,
            // 'status' => 'ongoing' // Uncomment if using status column
        ]);

        $this->recordAttendanceAudit(
            trim($person->firstname . ' ' . $person->lastname),
            'Clock In',
            'Attendance #' . $attendance->id,
            'success',
            $personType . ' clocked in successfully.',
            $request
        );

        return response()->json([
            'success' => true,
            'action' => 'clock_in',
            'message' => $person->firstname . ' ' . $person->lastname . ' clocked in successfully.',
            'person_type' => $personType,
            'person_id' => $person->id,
            'name' => $person->firstname . ' ' . $person->lastname,
            'rfid_tag_uid' => $person->rfid_tag_uid,
            'student_number' => $person instanceof Student ? $person->student_number : null,
            'year_level' => $person instanceof Student ? $person->year_level : null,
            'course_program' => $person instanceof Student ? $person->course_program : null,
            'employee_number' => $person instanceof Personnel ? $person->employee_number : null,
            'department' => $person instanceof Personnel ? $person->department : null,
            'attendance_id' => $attendance->id,
            'date' => $now->toDateString(),
            'time_in' => $now->toIso8601String(),
            'time_out' => null,
        ]);
    }
}