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
        Schema::table('order_detail_options', function (Blueprint $table) {
            // Nama constraint biasanya {table}_{column}_foreign
            $table->dropForeign(['option_id']);
        });
    }

    public function down(): void
    {
        Schema::table('order_detail_options', function (Blueprint $table) {
            $table->foreign('option_id')
                ->references('id')
                ->on('partner_product_options')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }
};
