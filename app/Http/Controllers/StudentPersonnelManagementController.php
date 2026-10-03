<?php
namespace App\Http\Controllers;
use App\Models\Personnel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class StudentPersonnelManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AVAILABLE STUDENT OPTIONS
    |--------------------------------------------------------------------------
    */
    private const YEAR_LEVELS = [
        '1st Year',
        '2nd Year',
        '3rd Year',
        '4th Year',
    ];
    private const COURSE_PROGRAMS = [
        'BSA',
        'BSAIS',
        'BSIT',
        'BSIS',
        'BSN',
        'BSND',
        'BSPHARM',
        'BACOMM',
        'BAEL',
        'BLIS',
        'BM',
        'BSPSYCH',
        'BSBA',
        'BSHM',
        'BSTM',
        'BSSW',
        'BCAED',
        'BECED',
        'BEED',
        'BTLED',
        'BSED',
    ];
    /*
    |--------------------------------------------------------------------------
    | MANAGEMENT PAGE
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $students = Student::orderBy('lastname')
            ->orderBy('firstname')
            ->get();
        $personnel = Personnel::orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        // Determine which tab should be active (defaults to 'students')
        $activeTab = $request->get('tab', 'students');

        return view(
            'studentpersonnelmanagement',
            compact(
                'students',
                'personnel',
                'activeTab'
            )
        );
    }
    /*
    |--------------------------------------------------------------------------
    | ADD STUDENT
    |--------------------------------------------------------------------------
    */
    public function storeStudent(Request $request)
    {
        /*
         * Remove spaces and save emails in lowercase.
         */
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(
                    trim($request->input('email'))
                ),
            ]);
        }
        $validated = $request->validate(
            [
                'firstname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'lastname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'student_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:students,student_number',
                ],
                'year_level' => [
                    'nullable',
                    Rule::in(self::YEAR_LEVELS),
                ],
                'course_program' => [
                    'nullable',
                    Rule::in(self::COURSE_PROGRAMS),
                ],
                'rfid_tag_uid' => [
                    'nullable',
                    'string',
                    'max:255',
                    'unique:students,rfid_tag_uid',
                    'unique:personnel,rfid_tag_uid',
                ],
                'fingerprint_id' => [
                    'required',
                    'integer',
                    'between:1,127',
                    'unique:students,fingerprint_id',
                    'unique:personnel,fingerprint_id',
                ],
                'email' => [
                    'nullable',
                    'email:rfc',
                    'max:255',
                    'ends_with:@lccdo.edu.ph',
                    'unique:students,email',
                ],
            ],
            [
                'year_level.in' =>
                    'Please select a valid year level.',
                'course_program.in' =>
                    'Please select a valid course or program.',
                'email.email' =>
                    'Please enter a valid email address.',
                'email.ends_with' =>
                    'Only @lccdo.edu.ph email addresses are allowed.',
                'email.unique' =>
                    'This email address is already registered.',
                'rfid_tag_uid.unique' =>
                    'This RFID card is already registered.',
                'fingerprint_id.required' =>
                    'Please enroll the student fingerprint before saving.',
                'fingerprint_id.unique' =>
                    'This fingerprint is already assigned to another record.',
            ]
        );
        /*
         * Store NULL when no email was provided.
         */
        $validated['email'] =
            $validated['email'] ?? null;
        Student::create($validated);
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'students'])
            ->with(
                'success',
                'Student added successfully.'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE STUDENT
    |--------------------------------------------------------------------------
    */
    public function updateStudent(
        Request $request,
        Student $student
    ) {
        /*
         * Remove spaces and save emails in lowercase.
         */
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(
                    trim($request->input('email'))
                ),
            ]);
        }
        $validated = $request->validate(
            [
                'firstname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'lastname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'student_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique(
                        'students',
                        'student_number'
                    )->ignore($student->id),
                ],
                'year_level' => [
                    'nullable',
                    Rule::in(self::YEAR_LEVELS),
                ],
                'course_program' => [
                    'nullable',
                    Rule::in(self::COURSE_PROGRAMS),
                ],
                'rfid_tag_uid' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique(
                        'students',
                        'rfid_tag_uid'
                    )->ignore($student->id),
                    Rule::unique(
                        'personnel',
                        'rfid_tag_uid'
                    ),
                ],
                'fingerprint_id' => [
                    'required',
                    'integer',
                    'between:1,127',
                    Rule::unique('students', 'fingerprint_id')->ignore($student->id),
                    Rule::unique('personnel', 'fingerprint_id'),
                ],
                'email' => [
                    'nullable',
                    'email:rfc',
                    'max:255',
                    'ends_with:@lccdo.edu.ph',
                    Rule::unique(
                        'students',
                        'email'
                    )->ignore($student->id),
                ],
            ],
            [
                'year_level.in' =>
                    'Please select a valid year level.',
                'course_program.in' =>
                    'Please select a valid course or program.',
                'email.email' =>
                    'Please enter a valid email address.',
                'email.ends_with' =>
                    'Only @lccdo.edu.ph email addresses are allowed.',
                'email.unique' =>
                    'This email address is already registered.',
                'rfid_tag_uid.unique' =>
                    'This RFID card is already registered.',
                'fingerprint_id.required' =>
                    'Please enroll the student fingerprint before saving.',
                'fingerprint_id.unique' =>
                    'This fingerprint is already assigned to another record.',
            ]
        );
        $validated['email'] =
            $validated['email'] ?? null;
        $student->update($validated);
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'students'])
            ->with(
                'success',
                'Student updated successfully.'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | DELETE STUDENT
    |--------------------------------------------------------------------------
    */
    public function destroyStudent(
        Student $student
    ) {
        $student->delete();
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'students'])
            ->with(
                'success',
                'Student deleted successfully.'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | ADD PERSONNEL
    |--------------------------------------------------------------------------
    */
    public function storePersonnel(Request $request)
    {
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(
                    trim($request->input('email'))
                ),
            ]);
        }
        $validated = $request->validate(
            [
                'firstname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'lastname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'employee_number' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:personnel,employee_number',
                ],
                'department' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'rfid_tag_uid' => [
                    'nullable',
                    'string',
                    'max:255',
                    'unique:personnel,rfid_tag_uid',
                    'unique:students,rfid_tag_uid',
                ],
                'fingerprint_id' => [
                    'required',
                    'integer',
                    'between:1,127',
                    'unique:students,fingerprint_id',
                    'unique:personnel,fingerprint_id',
                ],
                'email' => [
                    'nullable',
                    'max:255',
                    'email:rfc',
                    'ends_with:@lccdo.edu.ph',
                    'unique:personnel,email',
                ],
            ],
            [
                'email.email' =>
                    'Please enter a valid email address.',
                'email.ends_with' =>
                    'Only @lccdo.edu.ph email addresses are allowed.',
                'email.unique' =>
                    'This email address is already registered.',
                'rfid_tag_uid.unique' =>
                    'This RFID card is already registered.',
                'fingerprint_id.required' =>
                    'Please enroll the personnel fingerprint before saving.',
                'fingerprint_id.unique' =>
                    'This fingerprint is already assigned to another record.',
            ]
        );
        $validated['email'] =
            $validated['email'] ?? null;
        Personnel::create($validated);
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'personnel'])
            ->with(
                'success',
                'Personnel added successfully.'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE PERSONNEL
    |--------------------------------------------------------------------------
    */
    public function updatePersonnel(
        Request $request,
        Personnel $personnel
    ) {
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(
                    trim($request->input('email'))
                ),
            ]);
        }
        $validated = $request->validate(
            [
                'firstname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'lastname' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'employee_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique(
                        'personnel',
                        'employee_number'
                    )->ignore($personnel->id),
                ],
                'department' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'rfid_tag_uid' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique(
                        'personnel',
                        'rfid_tag_uid'
                    )->ignore($personnel->id),
                    Rule::unique(
                        'students',
                        'rfid_tag_uid'
                    ),
                ],
                'fingerprint_id' => [
                    'required',
                    'integer',
                    'between:1,127',
                    Rule::unique('personnel', 'fingerprint_id')->ignore($personnel->id),
                    Rule::unique('students', 'fingerprint_id'),
                ],
                'email' => [
                    'nullable',
                    'max:255',
                    'email:rfc',
                    'ends_with:@lccdo.edu.ph',
                    Rule::unique(
                        'personnel',
                        'email'
                    )->ignore($personnel->id),
                ],
            ],
            [
                'email.email' =>
                    'Please enter a valid email address.',
                'email.ends_with' =>
                    'Only @lccdo.edu.ph email addresses are allowed.',
                'email.unique' =>
                    'This email address is already registered.',
                'rfid_tag_uid.unique' =>
                    'This RFID card is already registered.',
                'fingerprint_id.required' =>
                    'Please enroll the personnel fingerprint before saving.',
                'fingerprint_id.unique' =>
                    'This fingerprint is already assigned to another record.',
            ]
        );
        $validated['email'] =
            $validated['email'] ?? null;
        $personnel->update($validated);
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'personnel'])
            ->with(
                'success',
                'Personnel updated successfully.'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | DELETE PERSONNEL
    |--------------------------------------------------------------------------
    */
    public function destroyPersonnel(
        Personnel $personnel
    ) {
        $personnel->delete();
        return redirect()
            ->route('studentpersonnel.management', ['tab' => 'personnel'])
            ->with(
                'success',
                'Personnel deleted successfully.'
            );
    }
}