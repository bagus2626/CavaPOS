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
            // Lepas foreign key dari partner_product_id (kalau ada)
            $table->dropForeign(['partner_product_id']);

            // Tambah kolom baru
            $table->string('product_code')->nullable()->after('partner_product_id');
            $table->string('product_name')->nullable()->after('product_code');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Hapus kolom baru
            $table->dropColumn(['product_code', 'product_name']);

            // Tambahkan lagi foreign key partner_product_id (jika ingin rollback)
            $table->foreign('partner_product_id')
                ->references('id')->on('partner_products')
                ->onDelete('cascade');
        });
    }
};
