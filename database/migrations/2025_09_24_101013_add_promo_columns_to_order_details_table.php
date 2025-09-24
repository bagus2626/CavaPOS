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
        Schema::table('order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('promo_id')->nullable()->after('options_price');
            $table->decimal('promo_amount', 12, 2)->nullable()->after('base_price');

            // Jika promo_id relasi ke promotions
            $table->foreign('promo_id')->references('id')->on('promotions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn(['promo_id', 'promo_amount']);
        });
    }
};
