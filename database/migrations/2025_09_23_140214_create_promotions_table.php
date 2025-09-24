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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promotion_code')->unique();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->string('promotion_name');
            $table->enum('promotion_type', ['percentage', 'amount']); // contoh tipe
            $table->decimal('promotion_value', 10, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('active_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('uses_expiry')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            // foreign keys (optional, kalau ada tabel owners & partners)
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
