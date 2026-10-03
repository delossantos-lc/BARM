<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'fine_payments',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | FINE
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('book_fine_id')
                    ->constrained('book_fines')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | PAYMENT DETAILS
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'amount',
                        10,
                        2
                    );

                $table
                    ->enum(
                        'payment_type',
                        [
                            'fine_payment',
                            'replacement_payment',
                            'processing_fee',
                        ]
                    )
                    ->default('fine_payment');

                $table
                    ->enum(
                        'payment_method',
                        [
                            'cash',
                            'bank_transfer',
                            'online_payment',
                            'replacement_book',
                            'other',
                        ]
                    )
                    ->default('cash');

                /*
                |--------------------------------------------------------------------------
                | PAYMENT ACKNOWLEDGEMENT
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('receipt_number')
                    ->unique();

                $table
                    ->timestamp('paid_at');

                $table
                    ->text('remarks')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | ADMIN OR STAFF WHO RECEIVED PAYMENT
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('received_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamps();

                $table->index('payment_type');
                $table->index('paid_at');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'fine_payments'
        );
    }
};