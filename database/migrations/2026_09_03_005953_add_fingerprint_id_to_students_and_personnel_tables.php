<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedSmallInteger('fingerprint_id')
                ->nullable()
                ->unique()
                ->after('rfid_tag_uid');
        });

        Schema::table('personnel', function (Blueprint $table) {
            $table->unsignedSmallInteger('fingerprint_id')
                ->nullable()
                ->unique()
                ->after('rfid_tag_uid');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['fingerprint_id']);
            $table->dropColumn('fingerprint_id');
        });

        Schema::table('personnel', function (Blueprint $table) {
            $table->dropUnique(['fingerprint_id']);
            $table->dropColumn('fingerprint_id');
        });
    }
};