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
        // 1. Tambahkan kolom 'stock_type' ke tabel opsi produk
        // Ini menentukan apakah stok opsi dikelola 'direct' (via tabel stocks)
        // atau 'linked' (via tabel resep)
        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->enum('stock_type', ['direct', 'linked'])
                ->notNull()
                ->default('direct')
                ->after('price');
        });

        // 2. Tambahkan foreign key 'partner_product_option_id' ke tabel stocks
        // Ini memungkinkan satu baris stok untuk terhubung ke satu opsi produk
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('partner_product_option_id')
                ->nullable()
                ->after('partner_product_id')
                ->constrained('partner_product_options')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Lakukan kebalikan dari proses 'up'
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['partner_product_option_id']);
            $table->dropColumn('partner_product_option_id');
        });

        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->dropColumn('stock_type');
        });
    }
};
