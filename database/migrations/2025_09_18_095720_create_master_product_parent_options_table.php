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
        Schema::create('master_product_parent_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_product_id')
                ->constrained('master_products')
                ->onDelete('cascade');

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('provision');
            $table->integer('provision_value');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_product_parent_options');
    }
};
