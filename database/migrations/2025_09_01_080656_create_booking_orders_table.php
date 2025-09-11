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
        Schema::create('booking_orders', function (Blueprint $table) {
            $table->id();

            // relasi (pakai unsignedBigInteger + index; tambah FK nanti jika tabel referensi sudah ada)
            $table->string('booking_order_code')->unique();
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('customer_id')->nullable();

            // data pelanggan
            $table->string('customer_name', 100);

            // status pesanan (pakai string agar fleksibel; default pending)
            $table->string('order_status', 32)->default('pending');

            $table->string('payment_method')->nullable();

            // diskon
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('discount_value', 12, 2)->default(0);

            // total
            $table->decimal('total_order_value', 12, 2)->default(0);

            // catatan
            $table->text('customer_order_note')->nullable();

            // pembayaran
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->boolean('payment_flag')->default(false); // false=belum dibayar

            $table->timestamps();
            // $table->softDeletes(); // aktifkan jika ingin soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_orders');
    }
};
