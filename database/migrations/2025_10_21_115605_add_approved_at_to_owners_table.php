<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            // Menambahkan kolom 'approved_at' setelah 'verification_status'
            // Kolom ini akan menyimpan tanggal dan waktu saat verifikasi disetujui
            $table->timestamp('approved_at')->nullable()->after('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            // Menghapus kolom 'approved_at' jika migrasi di-rollback
            $table->dropColumn('approved_at');
        });
    }
};
