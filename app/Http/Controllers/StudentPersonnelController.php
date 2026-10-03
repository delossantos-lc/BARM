<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Personnel;
use App\Models\Attendance;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentPersonnelController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STUDENT MONITORING
    |--------------------------------------------------------------------------
    */

    public function students(Request $request)
    {
        $this->applyAutomaticTimeOut();

        $search = $request->get('search');

        $students = Student::with([
            'attendances' => function ($query) {
                $query->latest('time_in');
            },
        ])
            ->when($search, function ($query, $search) {

                $query->where(function ($q) use ($search) {

                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%")
                        ->orWhere('year_level', 'like', "%{$search}%")
                        ->orWhere('course_program', 'like', "%{$search}%")
                        ->orWhere('rfid_tag_uid', 'like', "%{$search}%");
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get()
            ->flatMap(function (Student $student) {
                /*
                 * The Blade table expects latestAttendance. Create one display
                 * row for every attendance session instead of showing only the
                 * newest session for this student.
                 */
                if ($student->attendances->isEmpty()) {
                    $row = clone $student;
                    $row->setRelation('latestAttendance', null);

                    return [$row];
                }

                return $student->attendances->map(
                    function (Attendance $attendance) use ($student) {
                        $row = clone $student;
                        $row->setRelation('latestAttendance', $attendance);

                        return $row;
                    }
                );
            })
            ->values();

        $totalStudents = Student::count();

        $insideToday = Student::whereHas('attendances', function ($query) {

            $query->whereDate('date', today())
                ->whereNotNull('time_in')
                ->whereNull('time_out');

        })->count();

        // Count visit sessions, not only distinct students.
        $visitedToday = Attendance::whereDate('date', today())
            ->whereHasMorph('attendable', [Student::class])
            ->count();

        return view('student', compact(
            'students',
            'totalStudents',
            'insideToday',
            'visitedToday'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT MONITORING PDF
    |--------------------------------------------------------------------------
    */

    public function studentsPdf(Request $request)
    {
        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $course = trim(
            (string) $request->get(
                'course',
                ''
            )
        );

        $date = $request->get('date');

        $students = Student::with('latestAttendance')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($subQuery) use ($search) {
                            $subQuery
                                ->where(
                                    'firstname',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'lastname',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'student_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'year_level',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'course_program',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'rfid_tag_uid',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $course !== '',
                function ($query) use ($course) {
                    $query->whereRaw(
                        'LOWER(course_program) = ?',
                        [strtolower($course)]
                    );
                }
            )
            ->when(
                !empty($date),
                function ($query) use ($date) {
                    $query->whereHas(
                        'latestAttendance',
                        function ($attendanceQuery) use ($date) {
                            $attendanceQuery->whereDate(
                                'date',
                                $date
                            );
                        }
                    );
                }
            )
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        $filters = [
            'search' => $search,
            'course' => $course,
            'date' => $date,
        ];

        $pdf = Pdf::loadView(
            'student_monitoring_pdf',
            compact(
                'students',
                'filters'
            )
        )->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->download(
            'student-monitoring-'
            . now()->format('Y-m-d-His')
            . '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PERSONNEL MONITORING
    |--------------------------------------------------------------------------
    */

    public function personnel(Request $request)
    {
        $this->applyAutomaticTimeOut();

        $search = $request->get('search');

        $personnel = Personnel::with([
            'attendances' => function ($query) {
                $query->latest('time_in');
            },
        ])
            ->when($search, function ($query, $search) {

                $query->where(function ($q) use ($search) {

                    $q->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('employee_number', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('rfid_tag_uid', 'like', "%{$search}%");
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get()
            ->flatMap(function (Personnel $person) {
                /* Display every personnel visit as a separate table row. */
                if ($person->attendances->isEmpty()) {
                    $row = clone $person;
                    $row->setRelation('latestAttendance', null);

                    return [$row];
                }

                return $person->attendances->map(
                    function (Attendance $attendance) use ($person) {
                        $row = clone $person;
                        $row->setRelation('latestAttendance', $attendance);

                        return $row;
                    }
                );
            })
            ->values();

        $totalPersonnel = Personnel::count();

        $insideToday = Personnel::whereHas('attendances', function ($query) {

            $query->whereDate('date', today())
                ->whereNotNull('time_in')
                ->whereNull('time_out');

        })->count();

        // Count visit sessions, not only distinct personnel.
        $visitedToday = Attendance::whereDate('date', today())
            ->whereHasMorph('attendable', [Personnel::class])
            ->count();

        return view('personnel', compact(
            'personnel',
            'totalPersonnel',
            'insideToday',
            'visitedToday'
        ));
    }

    public function personnelPdf(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $date = $request->get('date');

        $personnel = Personnel::with('latestAttendance')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('employee_number', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('rfid_tag_uid', 'like', "%{$search}%");
                });
            })
            ->when(!empty($date), function ($query) use ($date) {
                $query->whereHas('latestAttendance', function ($attendanceQuery) use ($date) {
                    $attendanceQuery->whereDate('date', $date);
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        $filters = compact('search', 'date');

        return Pdf::loadView(
            'personnel_monitoring_pdf',
            compact('personnel', 'filters')
        )
            ->setPaper('a4', 'landscape')
            ->download('personnel-monitoring-' . now()->format('Y-m-d-His') . '.pdf');
    }

    /**
     * Close today's open attendance records once the configured automatic
     * time-out is reached. Monitoring pages refresh automatically, so this
     * check runs without requiring a full browser reload.
     */
    private function applyAutomaticTimeOut(): void
    {
        $settings = SystemSetting::current();

        if (
            !$settings->automatic_time_out_enabled
            || empty($settings->automatic_time_out_at)
        ) {
            return;
        }

        $timezone = $settings->timezone ?: 'Asia/Manila';
        $now = Carbon::now($timezone);
        $cutoff = Carbon::today($timezone)->setTimeFromTimeString(
            substr((string) $settings->automatic_time_out_at, 0, 8)
        );

        if ($now->lt($cutoff)) {
            return;
        }

        Attendance::whereDate('date', $now->toDateString())
            ->whereNull('time_out')
            ->where('time_in', '<=', $cutoff)
            ->update([
                'time_out' => $cutoff,
                'updated_at' => $now,
            ]);
    }
}
