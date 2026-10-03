<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'book_fines',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | BORROW TRANSACTION
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('book_borrow_id')
                    ->constrained('book_borrows')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | POLICY USED
                |--------------------------------------------------------------------------
                |
                | The policy can later be changed or deleted,
                | but this fine keeps a snapshot of the rate
                | and amounts that were originally used.
                |
                */

                $table
                    ->foreignId('library_policy_id')
                    ->nullable()
                    ->constrained('library_policies')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | FINE TYPE
                |--------------------------------------------------------------------------
                */

                $table
                    ->enum(
                        'fine_type',
                        [
                            'overdue',
                            'reserved_overdue',
                            'lost',
                            'damaged',
                        ]
                    );

                /*
                |--------------------------------------------------------------------------
                | POLICY SNAPSHOT
                |--------------------------------------------------------------------------
                |
                | rate_used:
                | - Overdue: fine per day
                | - Reserved: fine per hour
                | - Damaged: damaged-book penalty
                | - Lost: current replacement price
                |
                | quantity:
                | - Number of overdue days
                | - Number of overdue hours
                | - One damaged/lost book
                |
                */

                $table
                    ->decimal(
                        'rate_used',
                        10,
                        2
                    )
                    ->default(0);

                $table
                    ->unsignedInteger('quantity')
                    ->default(1);

                $table
                    ->unsignedInteger(
                        'grace_period_used'
                    )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | FINE AMOUNTS
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'base_amount',
                        10,
                        2
                    )
                    ->default(0);

                $table
                    ->decimal(
                        'processing_fee',
                        10,
                        2
                    )
                    ->default(0);

                $table
                    ->decimal(
                        'total_amount',
                        10,
                        2
                    )
                    ->default(0);

                $table
                    ->decimal(
                        'amount_paid',
                        10,
                        2
                    )
                    ->default(0);

                $table
                    ->decimal(
                        'remaining_balance',
                        10,
                        2
                    )
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | PAYMENT STATUS
                |--------------------------------------------------------------------------
                */

                $table
                    ->enum(
                        'payment_status',
                        [
                            'unpaid',
                            'partially_paid',
                            'paid',
                            'waived',
                            'replaced',
                        ]
                    )
                    ->default('unpaid');

                /*
                |--------------------------------------------------------------------------
                | LOST BOOK DETAILS
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'replacement_cost',
                        10,
                        2
                    )
                    ->nullable();

                $table
                    ->boolean(
                        'physical_replacement_received'
                    )
                    ->default(false);

                $table
                    ->string(
                        'replacement_title'
                    )
                    ->nullable();

                $table
                    ->string(
                        'replacement_edition'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | DAMAGED BOOK DETAILS
                |--------------------------------------------------------------------------
                */

                $table
                    ->enum(
                        'damage_level',
                        [
                            'minor',
                            'moderate',
                            'severe',
                        ]
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | WAIVER DETAILS
                |--------------------------------------------------------------------------
                */

                $table
                    ->text('waiver_reason')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | REMARKS
                |--------------------------------------------------------------------------
                */

                $table
                    ->text('remarks')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT DATES
                |--------------------------------------------------------------------------
                */

                $table
                    ->timestamp('assessed_at')
                    ->nullable();

                $table
                    ->timestamp('paid_at')
                    ->nullable();

                $table
                    ->timestamp('waived_at')
                    ->nullable();

                $table
                    ->timestamp('replacement_received_at')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | ADMIN OR STAFF WHO PROCESSED THE FINE
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('processed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamps();

                /*
                 * Prevent duplicate fine types for
                 * the same borrowing transaction.
                 */
                $table->unique(
                    [
                        'book_borrow_id',
                        'fine_type',
                    ],
                    'book_fines_borrow_type_unique'
                );

                $table->index('payment_status');
                $table->index('fine_type');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'book_fines'
        );
    }
};