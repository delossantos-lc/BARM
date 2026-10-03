<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'book_reservations',
            function (Blueprint $table) {
                $table
                    ->foreignId('student_record_id')
                    ->nullable()
                    ->after('book_id')
                    ->constrained('students')
                    ->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'book_reservations',
            function (Blueprint $table) {
                $table->dropForeign([
                    'student_record_id',
                ]);

                $table->dropColumn(
                    'student_record_id'
                );
            }
        );
    }
};