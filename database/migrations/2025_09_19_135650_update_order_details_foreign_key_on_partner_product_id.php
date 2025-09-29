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
        Schema::table('order_details', function (Blueprint $table) {
            // Hapus constraint lama
            $table->dropForeign(['partner_product_id']);

            // Tambahkan constraint baru dengan ON DELETE CASCADE
            $table->foreign('partner_product_id')
                ->references('id')
                ->on('partner_products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Hapus constraint cascade
            $table->dropForeign(['partner_product_id']);

            // Balik lagi ke restrict (default sebelumnya)
            $table->foreign('partner_product_id')
                ->references('id')
                ->on('partner_products')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }
};
