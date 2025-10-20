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
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('cashier_process_id')->nullable()->after('employee_order_note');
            $table->unsignedBigInteger('kitchen_process_id')->nullable()->after('cashier_process_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->dropColumn(['cashier_process_id', 'kitchen_process_id']);
        });
    }
};
