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
        Schema::create('xendit_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(); // FK ke orders
            $table->string('xendit_invoice_id')->unique(); // ID dari Xendit
            $table->string('external_id')->unique();
            $table->bigInteger('amount');
            $table->string('status')->default('PENDING');
            $table->string('payment_method')->nullable(); // QRIS, VA, eWallet, dll
            $table->string('invoice_url')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('booking_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_invoices');
    }
};
