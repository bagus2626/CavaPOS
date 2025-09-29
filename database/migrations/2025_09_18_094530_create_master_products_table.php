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
        Schema::create('master_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('quantity')->default(0)->nullable();
            $table->json('pictures')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->timestamps();

            // index + relasi
            $table->foreign('owner_id')
                ->references('id')
                ->on('owners')
                ->onDelete('cascade');

            // kalau category_id & promo_id juga foreign key, bisa tambahkan:
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            // $table->foreign('promo_id')->references('id')->on('promos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_products');
    }
};
