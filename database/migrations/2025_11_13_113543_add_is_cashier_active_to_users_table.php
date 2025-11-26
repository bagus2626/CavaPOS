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
            // Add is_cashier_active column after is_qr_active
            $table->boolean('is_cashier_active')->default(0)->after('is_qr_active');
            
            // Add qr_mode column if not exists
            if (!Schema::hasColumn('users', 'qr_mode')) {
                $table->enum('qr_mode', ['disabled', 'barcode_only', 'cashier_only', 'both'])
                      ->default('disabled')
                      ->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_cashier_active']);
            
            if (Schema::hasColumn('users', 'qr_mode')) {
                $table->dropColumn('qr_mode');
            }
        });
    }
};