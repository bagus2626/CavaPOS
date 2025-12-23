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
        Schema::table('partner_product_options', function (Blueprint $table) {
            // Kolom untuk menyimpan hasil perhitungan linked stock (cache)
            $table->decimal('available_linked_quantity', 15, 2)->default(0.00)->after('stock_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->dropColumn('available_linked_quantity');
        });
    }
};