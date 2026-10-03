<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'audit_logs',
            function (Blueprint $table) {
                $table->id();

                /*
                 * Nullable because the staff account
                 * may later be removed.
                 */
                $table
                    ->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                /*
                 * Keep snapshots of the actor's name
                 * and role even if the user changes.
                 */
                $table->string('actor_name');

                $table->enum(
                    'user_role',
                    [
                        'admin',
                        'staff',
                        'system',
                    ]
                );

                $table->string('action');

                $table->string('module');

                $table
                    ->string('affected_record')
                    ->nullable();

                $table
                    ->json('previous_value')
                    ->nullable();

                $table
                    ->json('new_value')
                    ->nullable();

                $table
                    ->string('ip_address', 45)
                    ->nullable();

                $table->enum(
                    'result',
                    [
                        'success',
                        'failed',
                    ]
                )->default('success');

                $table->text('description')->nullable();

                $table->timestamps();

                $table->index('action');
                $table->index('module');
                $table->index('user_role');
                $table->index('result');
                $table->index('created_at');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};