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
            $table->unsignedBigInteger('master_product_id')->nullable()->after('id');

            // Kalau mau ada relasi ke tabel master_products
            $table->foreign('master_product_id')
                ->references('id')
                ->on('master_products')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_products', function (Blueprint $table) {
            $table->dropForeign(['master_product_id']);
            $table->dropColumn('master_product_id');
        });
    }
};
