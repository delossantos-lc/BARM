<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_borrows', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | BOOK
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | BORROWER
            |--------------------------------------------------------------------------
            |
            | borrower_type identifies which table contains borrower_id.
            |
            | student   = students.id
            | personnel = personnel.id
            |
            */

            $table->enum('borrower_type', [
                'student',
                'personnel'
            ]);

            $table->unsignedBigInteger('borrower_id');

            $table->index([
                'borrower_type',
                'borrower_id'
            ]);

            /*
            |--------------------------------------------------------------------------
            | BORROWING DATES
            |--------------------------------------------------------------------------
            */

            $table->date('borrowed_at');

            $table->date('due_date');

            $table->date('returned_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'borrowed',
                'returned',
                'overdue'
            ])->default('borrowed');

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_borrows');
    }
};