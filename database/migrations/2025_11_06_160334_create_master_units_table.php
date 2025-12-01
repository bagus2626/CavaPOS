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
        Schema::create('master_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('unit_name');
            $table->boolean('is_base_unit');
            $table->decimal('base_unit_conversion_value', 15, 2);
            $table->string('group_label');
            $table->timestamps();

            $table->foreign('owner_id')
                ->references('id')
                ->on('owners')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_units');
    }
};
