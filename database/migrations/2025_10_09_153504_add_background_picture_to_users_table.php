<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // string path/URL gambar, boleh null
            $table->string('background_picture')->nullable()->after('logo');
            // Catatan: ->after('logo') hanya didukung MySQL/MariaDB.
            // Untuk PostgreSQL/SQLite, hapus ->after('logo').
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('background_picture');
        });
    }
};
