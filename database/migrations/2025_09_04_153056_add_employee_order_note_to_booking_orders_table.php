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
            $table->text('employee_order_note')->nullable()->after('customer_order_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            $table->dropColumn('employee_order_note');
        });
    }
};
