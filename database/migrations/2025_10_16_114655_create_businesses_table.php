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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            // Terhubung one-to-one dengan tabel owners (satu owner punya satu bisnis inti)
            $table->foreignId('owner_id')->unique()->constrained('owners')->onDelete('cascade');
            // Terhubung dengan business_categories
            $table->foreignId('business_category_id')
                ->constrained('business_categories')
                ->onDelete('restrict'); // Prevent delete jika masih digunakan

            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
};
