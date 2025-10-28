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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code')->unique();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('owner_master_product_id')->nullable();
            $table->enum('type', ['master', 'partner'])->default('partner');
            $table->string('stock_name');
            $table->decimal('quantity', 15, 2)->default(0);
            $table->string('unit', 50);
            $table->decimal('last_price_per_unit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            // (opsional) relasi jika kamu punya tabel owners & partners
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
