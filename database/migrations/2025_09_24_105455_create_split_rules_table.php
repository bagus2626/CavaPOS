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
        Schema::create('split_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->string('split_rule_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('routes'); // JSON
            $table->json('raw_response')->nullable(); // JSON
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('owners')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_rules');
    }
};
