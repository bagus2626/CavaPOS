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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_no');
            $table->string('table_code')->unique()->nullable();
            $table->unsignedBigInteger('partner_id');
            $table->string('table_class')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('seat_layout_id')->nullable();
            $table->json('images')->nullable();
            $table->string('table_url')->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->timestamps();

            // Jika partner_id relasi ke tabel partners, tambahkan foreign key:
            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
