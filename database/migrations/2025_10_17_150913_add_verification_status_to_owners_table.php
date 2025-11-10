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
        Schema::table('owners', function (Blueprint $table) {
            // Tambahkan kolom verification_status setelah kolom id
            $table->enum('verification_status', ['unverified', 'pending', 'approved', 'rejected'])
                ->default('unverified')
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            // Hapus kolom verification_status jika rollback
            $table->dropColumn('verification_status');
        });
    }
};
