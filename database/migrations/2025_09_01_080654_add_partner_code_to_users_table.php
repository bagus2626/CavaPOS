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
            // tambah kolom setelah 'slug'
            $table->string('partner_code', 64)
                  ->nullable()              // aman untuk data lama
                  ->after('slug')
                  ->unique();
            $table->string('package')->nullable()->after('partner_code');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn(['partner_code', 'package']);
        });
    }
};
