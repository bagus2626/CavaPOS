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
            // Tambah kolom owner_id setelah kolom product_code
            $table->unsignedBigInteger('owner_id')->after('product_code')->nullable();

            // Jika ingin ada relasi ke tabel owners
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('partner_products', function (Blueprint $table) {
            // Hapus foreign key dulu kalau tadi ditambahkan
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }
};
