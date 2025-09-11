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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_order_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('payment_type'); // contoh: cash, transfer, e-wallet
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->string('payment_status')->default('PENDING'); // pending, paid, failed
            $table->text('note')->nullable();
            $table->timestamps();

            // Opsional: tambahkan foreign key jika tabel terkait ada
            $table->foreign('booking_order_id')->references('id')->on('booking_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
