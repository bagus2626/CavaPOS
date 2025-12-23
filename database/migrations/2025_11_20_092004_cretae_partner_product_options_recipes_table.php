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
        Schema::create('partner_product_options_recipes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('partner_product_option_id');
            $table->unsignedBigInteger('stock_id');
            $table->decimal('quantity_used', 15, 2); 

            $table->unsignedBigInteger('display_unit_id')->nullable();
            $table->timestamps();

            $table->foreign('partner_product_option_id', 'ppor_option_id_foreign')
                ->references('id')
                ->on('partner_product_options')
                ->onDelete('cascade');

            $table->foreign('stock_id', 'ppor_stock_id_foreign')
                ->references('id')
                ->on('stocks')
                ->onDelete('cascade');

            $table->foreign('display_unit_id', 'ppor_display_unit_id_foreign')
                ->references('id')
                ->on('master_units')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_product_options_recipes');
    }
};
