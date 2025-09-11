<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('package_specification_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('specification_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_specification_values');
    }
};
