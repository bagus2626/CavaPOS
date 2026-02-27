<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Hanya digunakan oleh role manager & supervisor
            // null = belum diset = semua menu aktif (default)
            $table->json('menu_permissions')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('menu_permissions');
        });
    }
};
