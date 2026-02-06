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
        Schema::create('owner_manual_payments', function (Blueprint $table) {
            $table->id();

            // Relasi owner (biasanya users / partner / owner)
            $table->unsignedBigInteger('owner_id');

            // Jenis pembayaran manual
            $table->enum('payment_type', [
                'manual_tf',
                'manual_ewallet',
                'manual_qris'
            ]);

            // Nama bank / e-wallet / provider
            $table->string('provider_name', 100);

            // Nama pemilik rekening (untuk transfer), nama qris untuk qris
            $table->string('provider_account_name', 100);

            // Bisa nomor rekening atau nomor HP
            $table->string('provider_account_no', 100)->nullable();

            // QRIS image (nullable, hanya untuk manual_qris)
            $table->string('qris_image_url')->nullable();

            $table->text('additional_info')->nullable();

            $table->timestamps();

            // Indexing (penting untuk performa)
            $table->index('owner_id');
            $table->index('payment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_manual_payments');
    }
};
