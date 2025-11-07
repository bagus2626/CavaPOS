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
        Schema::create('xendit_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_id')->unique();
            $table->string('reference_id')->nullable()->index();
            $table->string('business_id')->nullable()->index();
            $table->decimal('amount', 20, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->string('channel_code', 50)->nullable();
            $table->string('status', 30)->default('PENDING');
            $table->string('failure_code', 100)->nullable();
            $table->string('account_holder_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('account_type', 50)->nullable();
            $table->json('email_to')->nullable();
            $table->json('email_cc')->nullable();
            $table->json('email_bcc')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('estimated_arrival_time')->nullable();
            $table->timestamp('created_xendit')->nullable();
            $table->timestamp('updated_xendit')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_payouts');
    }
};
