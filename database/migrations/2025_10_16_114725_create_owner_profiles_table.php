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
        Schema::create('owner_profiles', function (Blueprint $table) {
            $table->id();
            // Terhubung one-to-one dengan tabel owners
            $table->foreignId('owner_id')->unique()->constrained('owners')->onDelete('cascade');
            $table->string('ktp_number')->unique();
            $table->string('ktp_photo_path');
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
        Schema::dropIfExists('owner_profiles');
    }
};
