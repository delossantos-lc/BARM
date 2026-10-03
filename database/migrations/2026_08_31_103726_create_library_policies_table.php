<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'library_policies',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | STUDENT / GENERAL BORROWING
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger(
                        'student_borrowing_limit'
                    )
                    ->default(5);

                $table
                    ->unsignedInteger(
                        'student_loan_period_days'
                    )
                    ->default(5);

                /*
                |--------------------------------------------------------------------------
                | PERSONNEL / FACULTY BORROWING
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger(
                        'personnel_borrowing_limit'
                    )
                    ->default(20);

                /*
                 * Faculty loan period:
                 * 1 semester.
                 */
                $table
                    ->unsignedInteger(
                        'personnel_loan_period_value'
                    )
                    ->default(1);

                $table
                    ->enum(
                        'personnel_loan_period_unit',
                        [
                            'days',
                            'months',
                            'semester',
                        ]
                    )
                    ->default('semester');

                /*
                 * Alternative faculty return period
                 * mentioned in the policy.
                 */
                $table
                    ->unsignedInteger(
                        'personnel_standard_return_days'
                    )
                    ->default(30);

                /*
                 * Allows the system to exempt qualified
                 * faculty transactions from overdue fines.
                 */
                $table
                    ->boolean(
                        'allow_personnel_fine_exemption'
                    )
                    ->default(true);

                /*
                |--------------------------------------------------------------------------
                | GENERAL OVERDUE FINE
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'fine_per_overdue_day',
                        10,
                        2
                    )
                    ->default(3.00);

                $table
                    ->unsignedInteger(
                        'fine_grace_period_days'
                    )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | RESERVED BOOK POLICY
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger(
                        'reserved_book_loan_period_hours'
                    )
                    ->default(1);

                $table
                    ->decimal(
                        'reserved_book_fine_per_hour',
                        10,
                        2
                    )
                    ->default(3.00);

                $table
                    ->boolean(
                        'allow_reserved_book_renewal'
                    )
                    ->default(true);

                /*
                 * Renewal is allowed only when there is
                 * no pending request for the reserved book.
                 */
                $table
                    ->boolean(
                        'renew_reserved_only_without_request'
                    )
                    ->default(true);

                /*
                 * Number of days before an unclaimed
                 * reservation expires.
                 */
                $table
                    ->unsignedInteger(
                        'reservation_expiration_period_days'
                    )
                    ->default(1);

                $table
                    ->unsignedInteger(
                        'maximum_active_reservations'
                    )
                    ->default(2);

                /*
                |--------------------------------------------------------------------------
                | LOST BOOK POLICY
                |--------------------------------------------------------------------------
                */

                /*
                 * Require the same title and edition
                 * as the preferred replacement.
                 */
                $table
                    ->boolean(
                        'lost_book_same_title_required'
                    )
                    ->default(true);

                $table
                    ->boolean(
                        'lost_book_same_edition_required'
                    )
                    ->default(true);

                /*
                 * If physical replacement is unavailable,
                 * charge the book's current valid price.
                 */
                $table
                    ->boolean(
                        'use_current_price_for_lost_book'
                    )
                    ->default(true);

                $table
                    ->decimal(
                        'lost_book_processing_fee',
                        10,
                        2
                    )
                    ->default(50.00);

                /*
                |--------------------------------------------------------------------------
                | DAMAGED BOOK POLICY
                |--------------------------------------------------------------------------
                |
                | No exact damaged-book amount was stated,
                | so it remains configurable and defaults to zero.
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'damaged_book_penalty',
                        10,
                        2
                    )
                    ->default(0.00);

                /*
                |--------------------------------------------------------------------------
                | UNPAID FINE RESTRICTION
                |--------------------------------------------------------------------------
                */

                $table
                    ->boolean(
                        'allow_borrowing_with_unpaid_fines'
                    )
                    ->default(false);

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'library_policies'
        );
    }
};