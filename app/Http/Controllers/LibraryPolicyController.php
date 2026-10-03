<?php

namespace App\Http\Controllers;

use App\Models\LibraryPolicy;
use Illuminate\Http\Request;

class LibraryPolicyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | POLICY PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
         * The policy table should contain only
         * one global library policy record.
         */
        $policy = LibraryPolicy::firstOrCreate(
            [],
            [
                /*
                 * Student/general borrowing.
                 */
                'student_borrowing_limit' => 5,
                'student_loan_period_days' => 5,

                /*
                 * Personnel/faculty borrowing.
                 */
                'personnel_borrowing_limit' => 20,
                'personnel_loan_period_value' => 1,
                'personnel_loan_period_unit' => 'semester',
                'personnel_standard_return_days' => 30,
                'allow_personnel_fine_exemption' => true,

                /*
                 * Fingerprint requirement.
                 */
                'require_fingerprint_for_borrowing' => false,

                /*
                 * General overdue fine.
                 */
                'fine_per_overdue_day' => 3.00,
                'fine_grace_period_days' => 0,

                /*
                 * Reserved books.
                 */
                'reserved_book_loan_period_hours' => 1,
                'reserved_book_fine_per_hour' => 3.00,
                'allow_reserved_book_renewal' => true,
                'renew_reserved_only_without_request' => true,

                /*
                 * Reservations.
                 */
                'reservation_expiration_period_days' => 1,
                'maximum_active_reservations' => 2,

                /*
                 * Lost books.
                 */
                'lost_book_same_title_required' => true,
                'lost_book_same_edition_required' => true,
                'use_current_price_for_lost_book' => true,
                'lost_book_processing_fee' => 50.00,

                /*
                 * Damaged books.
                 */
                'damaged_book_penalty' => 0.00,

                /*
                 * Unpaid fines.
                 */
                'allow_borrowing_with_unpaid_fines' => false,
            ]
        );

        return view(
            'policy',
            compact('policy')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE POLICY
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | STUDENT BORROWING
            |--------------------------------------------------------------------------
            */

            'student_borrowing_limit' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'student_loan_period_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSONNEL BORROWING
            |--------------------------------------------------------------------------
            */

            'personnel_borrowing_limit' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'personnel_loan_period_value' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'personnel_loan_period_unit' => [
                'required',
                'in:days,months,semester',
            ],

            'personnel_standard_return_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'allow_personnel_fine_exemption' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | FINGERPRINT REQUIREMENT
            |--------------------------------------------------------------------------
            */

            'require_fingerprint_for_borrowing' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | GENERAL OVERDUE FINE
            |--------------------------------------------------------------------------
            */

            'fine_per_overdue_day' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'fine_grace_period_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],

            /*
            |--------------------------------------------------------------------------
            | RESERVED BOOK POLICY
            |--------------------------------------------------------------------------
            */

            'reserved_book_loan_period_hours' => [
                'required',
                'integer',
                'min:1',
                'max:168',
            ],

            'reserved_book_fine_per_hour' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'allow_reserved_book_renewal' => [
                'required',
                'boolean',
            ],

            'renew_reserved_only_without_request' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | RESERVATION POLICY
            |--------------------------------------------------------------------------
            */

            'reservation_expiration_period_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],

            'maximum_active_reservations' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | LOST BOOK POLICY
            |--------------------------------------------------------------------------
            */

            'lost_book_same_title_required' => [
                'required',
                'boolean',
            ],

            'lost_book_same_edition_required' => [
                'required',
                'boolean',
            ],

            'use_current_price_for_lost_book' => [
                'required',
                'boolean',
            ],

            'lost_book_processing_fee' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            /*
            |--------------------------------------------------------------------------
            | DAMAGED BOOK POLICY
            |--------------------------------------------------------------------------
            */

            'damaged_book_penalty' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            /*
            |--------------------------------------------------------------------------
            | UNPAID FINE POLICY
            |--------------------------------------------------------------------------
            */

            'allow_borrowing_with_unpaid_fines' => [
                'required',
                'boolean',
            ],
        ]);

        /*
         * Retrieve or create the single
         * global policy record.
         */
        $policy = LibraryPolicy::firstOrCreate(
            [],
            [
                'student_borrowing_limit' => 5,
                'student_loan_period_days' => 5,
                'personnel_borrowing_limit' => 20,
                'personnel_loan_period_value' => 1,
                'personnel_loan_period_unit' => 'semester',
                'personnel_standard_return_days' => 30,
                'allow_personnel_fine_exemption' => true,
                'require_fingerprint_for_borrowing' => false,
                'fine_per_overdue_day' => 3.00,
                'fine_grace_period_days' => 0,
                'reserved_book_loan_period_hours' => 1,
                'reserved_book_fine_per_hour' => 3.00,
                'allow_reserved_book_renewal' => true,
                'renew_reserved_only_without_request' => true,
                'reservation_expiration_period_days' => 1,
                'maximum_active_reservations' => 2,
                'lost_book_same_title_required' => true,
                'lost_book_same_edition_required' => true,
                'use_current_price_for_lost_book' => true,
                'lost_book_processing_fee' => 50.00,
                'damaged_book_penalty' => 0.00,
                'allow_borrowing_with_unpaid_fines' => false,
            ]
        );

        /*
         * Explicitly normalize every boolean switch
         * so that "0"/"1" strings are stored as
         * real true/false values in the database.
         */
        $validated['allow_personnel_fine_exemption']      = $request->boolean('allow_personnel_fine_exemption');
        $validated['require_fingerprint_for_borrowing']   = $request->boolean('require_fingerprint_for_borrowing');
        $validated['allow_reserved_book_renewal']         = $request->boolean('allow_reserved_book_renewal');
        $validated['renew_reserved_only_without_request'] = $request->boolean('renew_reserved_only_without_request');
        $validated['lost_book_same_title_required']       = $request->boolean('lost_book_same_title_required');
        $validated['lost_book_same_edition_required']     = $request->boolean('lost_book_same_edition_required');
        $validated['use_current_price_for_lost_book']     = $request->boolean('use_current_price_for_lost_book');
        $validated['allow_borrowing_with_unpaid_fines']   = $request->boolean('allow_borrowing_with_unpaid_fines');

        $policy->update($validated);

        return redirect()
            ->route('policy.index')
            ->with(
                'success',
                'Library policies updated successfully.'
            );
    }
}