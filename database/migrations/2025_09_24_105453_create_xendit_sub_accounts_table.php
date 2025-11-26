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
        Schema::create('xendit_sub_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id')->nullable(); // partner yang punya sub account
            $table->string('xendit_user_id')->unique(); // "for-user-id" dari Xendit
            $table->string('business_name');
            $table->json('raw_response'); // JSON
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('owners')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xendit_sub_accounts');
    }
};
