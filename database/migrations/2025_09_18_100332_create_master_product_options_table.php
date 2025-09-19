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
        Schema::create('master_product_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('master_product_id')
                ->nullable()
                ->constrained('master_products')
                ->onDelete('cascade');

            $table->foreignId('master_product_parent_option_id')
                ->nullable()
                ->constrained('master_product_parent_options')
                ->onDelete('cascade');

            $table->string('name');
            $table->integer('quantity')->default(0);
            $table->decimal('price', 15, 2);
            $table->json('pictures')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('promo_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_product_options');
    }
};
