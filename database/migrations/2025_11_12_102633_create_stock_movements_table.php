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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignid('owner_id')->constrained('owners')->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->string('category');
            $table->text('notes')->nullable();


            $table->timestamps();
            $table->index('category');
        });

        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');

            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement_items');
        Schema::dropIfExists('stock_movements');
    }
};
