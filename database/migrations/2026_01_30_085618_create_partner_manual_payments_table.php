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
        Schema::create('partner_manual_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('partner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('owner_manual_payment_id')
                ->constrained('owner_manual_payments') // <-- pastikan nama tabel ini benar
                ->cascadeOnDelete();

            $table->timestamps();

            // opsional tapi bagus: cegah duplikasi pasangan partner-payment
            $table->unique(['partner_id', 'owner_manual_payment_id'], 'pmp_partner_owner_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_manual_payments');
    }
};
