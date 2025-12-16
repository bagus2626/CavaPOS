<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM status dengan menambahkan 'not_available'
        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'occupied', 'reserved', 'not_available') NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM semula (hapus 'not_available')
        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'occupied', 'reserved') NOT NULL DEFAULT 'available'");
    }
};
