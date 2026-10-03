<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FingerprintController extends Controller
{
    public function nextAvailable(): JsonResponse
    {
        abort_unless(auth()->check(), 403);

        $usedIds = Student::whereNotNull('fingerprint_id')
            ->pluck('fingerprint_id')
            ->merge(
                Personnel::whereNotNull('fingerprint_id')
                    ->pluck('fingerprint_id')
            )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->all();

        for ($fingerprintId = 1; $fingerprintId <= 127; $fingerprintId++) {
            if (!in_array($fingerprintId, $usedIds, true)) {
                return response()->json([
                    'success' => true,
                    'fingerprint_id' => $fingerprintId,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No available fingerprint slots remain.',
        ], 422);
    }

    public function find(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fingerprint_id' => ['required', 'integer', 'between:1,127'],
        ]);

        $fingerprintId = (int) $validated['fingerprint_id'];

        $student = Student::where('fingerprint_id', $fingerprintId)->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'message' => 'Student fingerprint recognized.',
                'borrower' => $this->studentPayload($student),
            ]);
        }

        $personnel = Personnel::where('fingerprint_id', $fingerprintId)->first();

        if ($personnel) {
            return response()->json([
                'success' => true,
                'message' => 'Personnel fingerprint recognized.',
                'borrower' => $this->personnelPayload($personnel),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'This fingerprint is not assigned to a student or personnel record.',
        ], 404);
    }

    public function assign(Request $request): JsonResponse
    {
        abort_unless(
            auth()->check() && strtolower((string) auth()->user()->access_level) === 'admin',
            403,
            'Administrator only.'
        );

        $validated = $request->validate([
            'person_type' => ['required', Rule::in(['student', 'personnel'])],
            'person_id' => ['required', 'integer', 'min:1'],
            'fingerprint_id' => ['required', 'integer', 'between:1,127'],
        ]);

        $fingerprintId = (int) $validated['fingerprint_id'];

        $alreadyUsedByStudent = Student::where('fingerprint_id', $fingerprintId)
            ->when(
                $validated['person_type'] === 'student',
                fn ($query) => $query->whereKeyNot($validated['person_id'])
            )
            ->exists();

        $alreadyUsedByPersonnel = Personnel::where('fingerprint_id', $fingerprintId)
            ->when(
                $validated['person_type'] === 'personnel',
                fn ($query) => $query->whereKeyNot($validated['person_id'])
            )
            ->exists();

        $alreadyUsed = $alreadyUsedByStudent || $alreadyUsedByPersonnel;

        if ($alreadyUsed) {
            return response()->json([
                'success' => false,
                'message' => 'This fingerprint ID is already assigned.',
            ], 422);
        }

        $model = $validated['person_type'] === 'student'
            ? Student::findOrFail($validated['person_id'])
            : Personnel::findOrFail($validated['person_id']);

        $model->update(['fingerprint_id' => $fingerprintId]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint ID assigned successfully.',
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        abort_unless(
            auth()->check() && strtolower((string) auth()->user()->access_level) === 'admin',
            403,
            'Administrator only.'
        );

        $validated = $request->validate([
            'person_type' => ['required', Rule::in(['student', 'personnel'])],
            'person_id' => ['required', 'integer', 'min:1'],
        ]);

        $model = $validated['person_type'] === 'student'
            ? Student::findOrFail($validated['person_id'])
            : Personnel::findOrFail($validated['person_id']);

        $model->update(['fingerprint_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint assignment removed. Delete the template from the sensor separately.',
        ]);
    }

    private function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'type' => 'student',
            'name' => trim($student->firstname . ' ' . $student->lastname),
            'number' => $student->student_number,
            'email' => $student->email,
            'rfid_tag_uid' => $student->rfid_tag_uid,
            'fingerprint_id' => $student->fingerprint_id,
        ];
    }

    private function personnelPayload(Personnel $personnel): array
    {
        return [
            'id' => $personnel->id,
            'type' => 'personnel',
            'name' => trim($personnel->firstname . ' ' . $personnel->lastname),
            'number' => $personnel->employee_number,
            'email' => $personnel->email,
            'rfid_tag_uid' => $personnel->rfid_tag_uid,
            'fingerprint_id' => $personnel->fingerprint_id,
        ];
    }
}
