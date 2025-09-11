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
        Schema::create('partner_product_parent_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_product_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key ke partner_products
            $table->foreign('partner_product_id')
                ->references('id')
                ->on('partner_products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_product_parent_options');
    }
};
