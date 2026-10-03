<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->string('rfid_tag_uid', 191)
                ->nullable()
                ->unique()
                ->after('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->dropUnique(['rfid_tag_uid']);
            $table->dropColumn('rfid_tag_uid');
        });
    }
};
