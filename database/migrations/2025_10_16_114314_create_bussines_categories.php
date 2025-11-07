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
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Kode KBLI (e.g., 56101)
            $table->string('name'); // Nama kategori (e.g., Restoran)
            $table->string('slug')->unique(); // Slug untuk URL friendly
            $table->boolean('is_active')->default(true); // Status aktif/non-aktif
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
        Schema::dropIfExists('business_categories');
    }
};
