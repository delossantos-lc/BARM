<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class UserManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = User::orderBy(
            'created_at',
            'desc'
        )->get();

        $totalStaff = User::where(
            'access_level',
            'staff'
        )->count();

        $activeStaff = User::where(
            'access_level',
            'staff'
        )
            ->where(
                'status',
                'active'
            )
            ->count();

        $inactiveStaff = User::where(
            'access_level',
            'staff'
        )
            ->where(
                'status',
                'inactive'
            )
            ->count();

        $totalAdmins = User::where(
            'access_level',
            'admin'
        )->count();

        return view(
            'usermanagement',
            compact(
                'users',
                'totalStaff',
                'activeStaff',
                'inactiveStaff',
                'totalAdmins'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE STAFF
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
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

            'employeeid' => [
                'required',
                'string',
                'max:255',
                'unique:users,employeeid',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'access_level' => [
                'required',

                Rule::in([
                    'staff',
                    'admin',
                ]),
            ],

            'status' => [
                'required',

                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        try {
            $user = User::create([
                'firstname' =>
                    $validated['firstname'],

                'lastname' =>
                    $validated['lastname'],

                'employeeid' =>
                    $validated['employeeid'],

                'email' =>
                    $validated['email'],

                'password' =>
                    Hash::make(
                        $validated['password']
                    ),

                'access_level' =>
                    $validated['access_level'],

                'status' =>
                    $validated['status'],
            ]);

            /*
             * Do not include the password in
             * the audit log.
             */
            $newValue = [
                'id' =>
                    $user->id,

                'firstname' =>
                    $user->firstname,

                'lastname' =>
                    $user->lastname,

                'employeeid' =>
                    $user->employeeid,

                'email' =>
                    $user->email,

                'access_level' =>
                    $user->access_level,

                'status' =>
                    $user->status,
            ];

            AuditLogger::record(
                action: 'Created',
                module: 'User Management',
                affectedRecord:
                    'User #' . $user->id
                    . ' - '
                    . $user->firstname
                    . ' '
                    . $user->lastname,
                previousValue: null,
                newValue: $newValue,
                result: 'success',
                description:
                    'Created a new user account.',
                request: $request
            );

            return redirect()
                ->route('user.management')
                ->with(
                    'success',
                    'Staff account created successfully.'
                );
        } catch (Throwable $exception) {
            AuditLogger::record(
                action: 'Create',
                module: 'User Management',
                affectedRecord:
                    'New user account',
                previousValue: null,
                newValue: [
                    'firstname' =>
                        $validated['firstname'],

                    'lastname' =>
                        $validated['lastname'],

                    'employeeid' =>
                        $validated['employeeid'],

                    'email' =>
                        $validated['email'],

                    'access_level' =>
                        $validated['access_level'],

                    'status' =>
                        $validated['status'],
                ],
                result: 'failed',
                description:
                    'Failed to create the user account.',
                request: $request
            );

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create the account.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STAFF
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        User $user
    ) {
        $validated = $request->validate([
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

            'employeeid' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'users',
                    'employeeid'
                )->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'access_level' => [
                'required',

                Rule::in([
                    'staff',
                    'admin',
                ]),
            ],

            'status' => [
                'required',

                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        /*
         * Save the values before updating.
         */
        $previousValue = [
            'id' =>
                $user->id,

            'firstname' =>
                $user->firstname,

            'lastname' =>
                $user->lastname,

            'employeeid' =>
                $user->employeeid,

            'email' =>
                $user->email,

            'access_level' =>
                $user->access_level,

            'status' =>
                $user->status,
        ];

        try {
            $user->firstname =
                $validated['firstname'];

            $user->lastname =
                $validated['lastname'];

            $user->employeeid =
                $validated['employeeid'];

            $user->email =
                $validated['email'];

            $user->access_level =
                $validated['access_level'];

            $user->status =
                $validated['status'];

            if (
                !empty(
                    $validated['password']
                )
            ) {
                $user->password =
                    Hash::make(
                        $validated['password']
                    );
            }

            $user->save();

            $newValue = [
                'id' =>
                    $user->id,

                'firstname' =>
                    $user->firstname,

                'lastname' =>
                    $user->lastname,

                'employeeid' =>
                    $user->employeeid,

                'email' =>
                    $user->email,

                'access_level' =>
                    $user->access_level,

                'status' =>
                    $user->status,

                'password_changed' =>
                    !empty(
                        $validated['password']
                    ),
            ];

            AuditLogger::record(
                action: 'Updated',
                module: 'User Management',
                affectedRecord:
                    'User #' . $user->id
                    . ' - '
                    . $user->firstname
                    . ' '
                    . $user->lastname,
                previousValue:
                    $previousValue,
                newValue:
                    $newValue,
                result: 'success',
                description:
                    'Updated a user account.',
                request: $request
            );

            return redirect()
                ->route('user.management')
                ->with(
                    'success',
                    'Staff account updated successfully.'
                );
        } catch (Throwable $exception) {
            AuditLogger::record(
                action: 'Update',
                module: 'User Management',
                affectedRecord:
                    'User #' . $user->id,
                previousValue:
                    $previousValue,
                newValue: [
                    'firstname' =>
                        $validated['firstname'],

                    'lastname' =>
                        $validated['lastname'],

                    'employeeid' =>
                        $validated['employeeid'],

                    'email' =>
                        $validated['email'],

                    'access_level' =>
                        $validated['access_level'],

                    'status' =>
                        $validated['status'],
                ],
                result: 'failed',
                description:
                    'Failed to update the user account.',
                request: $request
            );

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update the account.'
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE STAFF
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        User $user
    ) {
        if (
            auth()->check()
            &&
            auth()->id() === $user->id
        ) {
            AuditLogger::record(
                action: 'Delete',
                module: 'User Management',
                affectedRecord:
                    'User #' . $user->id
                    . ' - '
                    . $user->firstname
                    . ' '
                    . $user->lastname,
                previousValue: [
                    'id' =>
                        $user->id,

                    'firstname' =>
                        $user->firstname,

                    'lastname' =>
                        $user->lastname,

                    'employeeid' =>
                        $user->employeeid,

                    'email' =>
                        $user->email,

                    'access_level' =>
                        $user->access_level,

                    'status' =>
                        $user->status,
                ],
                newValue: null,
                result: 'failed',
                description:
                    'Attempted to delete the currently logged-in account.',
                request: $request
            );

            return redirect()
                ->route('user.management')
                ->with(
                    'error',
                    'You cannot delete your own account.'
                );
        }

        $previousValue = [
            'id' =>
                $user->id,

            'firstname' =>
                $user->firstname,

            'lastname' =>
                $user->lastname,

            'employeeid' =>
                $user->employeeid,

            'email' =>
                $user->email,

            'access_level' =>
                $user->access_level,

            'status' =>
                $user->status,
        ];

        $affectedRecord =
            'User #' . $user->id
            . ' - '
            . $user->firstname
            . ' '
            . $user->lastname;

        try {
            $user->delete();

            AuditLogger::record(
                action: 'Deleted',
                module: 'User Management',
                affectedRecord:
                    $affectedRecord,
                previousValue:
                    $previousValue,
                newValue:
                    null,
                result: 'success',
                description:
                    'Deleted a user account.',
                request: $request
            );

            return redirect()
                ->route('user.management')
                ->with(
                    'success',
                    'Staff account deleted successfully.'
                );
        } catch (Throwable $exception) {
            AuditLogger::record(
                action: 'Delete',
                module: 'User Management',
                affectedRecord:
                    $affectedRecord,
                previousValue:
                    $previousValue,
                newValue:
                    null,
                result: 'failed',
                description:
                    'Failed to delete the user account.',
                request: $request
            );

            report($exception);

            return back()->with(
                'error',
                'Unable to delete the account.'
            );
        }
    }
}