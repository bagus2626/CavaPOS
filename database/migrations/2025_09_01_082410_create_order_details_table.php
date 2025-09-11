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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            // relasi
            $table->foreignId('booking_order_id')
                  ->constrained('booking_orders')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete(); // hapus detail saat booking_order dihapus

            $table->foreignId('partner_product_id')
                  ->constrained('partner_products') // ganti nama tabel jika berbeda
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->integer('quantity')->default(0);

            // harga
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('options_price', 12, 2)->default(0);

            // catatan
            $table->text('customer_note')->nullable();

            $table->string('status')->nullable();
            $table->boolean('done_flag')->nullable();

            //pegawai yang mengerjakan orderan menu ini
            $table->unsignedBigInteger('kitchen_employee_id')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
