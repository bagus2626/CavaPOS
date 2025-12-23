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
        Schema::table('partner_products', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan kolom 'quantity' jika diperlukan rollback
        Schema::table('partner_products', function (Blueprint $table) {
            $table->integer('quantity')->default(0);
        });

        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->integer('quantity')->default(0);
        });
    }
};
