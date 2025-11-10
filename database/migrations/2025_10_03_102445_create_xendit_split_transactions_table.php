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
        Schema::create('xendit_split_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('xendit_invoice_id')->nullable();
            $table->string('split_rule_id')->nullable();
            $table->string('xendit_split_payment_id')->nullable()->index();
            $table->string('reference_id')->nullable(); // reference dari route
            $table->string('payment_id')->nullable();
            $table->string('payment_reference_id')->nullable();
            $table->string('source_account_id')->nullable();
            $table->string('destination_account_id')->nullable();
            $table->enum('account_type', ['MASTER', 'SUB_ACCOUNT'])->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // misalnya 10.50 (%)
            $table->string('status')->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->json('raw_response')->nullable(); // JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_split_transactions');
    }
};
