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
        Schema::table('stocks', function (Blueprint $table) {

            // 1. HAPUS kolom-kolom lama DULU
            $table->dropColumn('unit');
            $table->dropColumn('quantity');

            // 2. TAMBAHKAN kolom 'quantity' BARU (dengan arti baru)
            // Kolom ini sekarang akan menyimpan 'quantity_in_base_unit'
            $table->decimal('quantity', 15, 2)->default(0.00)->after('stock_name');

            // 3. TAMBAHKAN kolom 'display_unit_id' (FK ke master_units)
            $table->unsignedBigInteger('display_unit_id')->nullable()->after('partner_product_id');

            // 4. BUAT Foreign Key untuk unit tampilan
            $table->foreign('display_unit_id')
                ->references('id')
                ->on('master_units')
                ->onDelete('set null');

            // 5. BUAT ULANG Foreign Key 'partner_product_id' agar 'cascade'
            $table->dropForeign('stocks_partner_product_id_foreign');
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
        Schema::table('stocks', function (Blueprint $table) {

            // 1. Hapus FK dan kolom baru
            $table->dropForeign(['display_unit_id']);
            $table->dropColumn('display_unit_id');

            // 2. Hapus FK 'partner_product_id' yang baru
            $table->dropForeign('stocks_partner_product_id_foreign');

            // 3. Hapus kolom 'quantity' baru
            $table->dropColumn('quantity');

            // 4. Tambahkan kembali kolom-kolom lama
            $table->string('unit', 50)->nullable();
            $table->decimal('quantity', 15, 2)->default(0.00);

            // 5. Buat kembali FK 'partner_product_id' yang lama (onDelete set null)
            $table->foreign('partner_product_id', 'stocks_partner_product_id_foreign')
                ->references('id')
                ->on('partner_products')
                ->onDelete('set null');
        });
    }
};
