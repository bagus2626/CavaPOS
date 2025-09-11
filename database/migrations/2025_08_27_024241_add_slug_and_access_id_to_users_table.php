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
            $table->string('slug')->after('role')->nullable()->unique();
            $table->unsignedBigInteger('access_id')->after('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // hapus foreign key dulu kalau ada
            // $table->dropForeign(['access_id']);

            $table->dropColumn(['slug', 'access_id']);
        });
    }
};
