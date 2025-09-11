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
        Schema::create('order_detail_options', function (Blueprint $table) {
            $table->id();

            // Relasi ke order_details
            $table->foreignId('order_detail_id')
                  ->constrained('order_details')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Relasi ke opsi produk
            $table->foreignId('option_id')
                  ->constrained('partner_product_options') // <- ganti sesuai tabelmu
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Harga opsi saat transaksi (di-freeze)
            $table->decimal('price', 12, 2)->default(0);

            $table->timestamps();

            // Hindari duplikat opsi pada 1 baris order_detail
            $table->unique(['order_detail_id', 'option_id'], 'odo_unique_detail_option');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_detail_options');
    }
};
