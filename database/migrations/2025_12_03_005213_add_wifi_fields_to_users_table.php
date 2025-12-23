<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom WiFi sebelum is_qr_active
            $table->string('user_wifi')->nullable()->after('address');
            $table->string('pass_wifi')->nullable()->after('user_wifi');
            
            // Menambahkan kolom is_wifi_shown setelah is_active
            $table->boolean('is_wifi_shown')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_wifi', 'pass_wifi', 'is_wifi_shown']);
        });
    }
};