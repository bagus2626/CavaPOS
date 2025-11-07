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
        Schema::create('xendit_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event')->nullable(); // contoh: invoice.paid, disbursement.created
            $table->string('xendit_id')->nullable(); // ID dari Xendit (invoice id, disbursement id, dll)
            $table->string('status')->nullable(); // status event (PAID, FAILED, COMPLETED, EXPIRED, dll)
            $table->json('payload'); // JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_webhook_logs');
    }
};
