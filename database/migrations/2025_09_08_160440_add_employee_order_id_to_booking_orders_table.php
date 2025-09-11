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
            // Jika ada tabel 'employees' dan ingin FK:
            $table->foreignId('employee_order_id')
                ->nullable()
                ->after('customer_id') // sesuaikan posisi kolom
                ->constrained('employees') // nama tabel referensi
                ->nullOnDelete(); // atau ->cascadeOnDelete() sesuai kebutuhan

            $table->string('order_by')->nullable()->after('employee_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            // Kalau FK ada, drop; kalau tidak ada, biarin
            if (Schema::hasColumn('booking_orders', 'employee_order_id')) {
                try {
                    $table->dropForeign(['employee_order_id']);
                } catch (\Throwable $e) {
                    // FK mungkin sudah tidak ada, abaikan
                }
                $table->dropColumn('employee_order_id');
            }

            if (Schema::hasColumn('booking_orders', 'order_by')) {
                $table->dropColumn('order_by');
            }
        });
    }
};
