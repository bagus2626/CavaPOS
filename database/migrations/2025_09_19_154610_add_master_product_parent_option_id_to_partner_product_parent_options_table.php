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
        Schema::table('partner_product_parent_options', function (Blueprint $table) {
            $table->unsignedBigInteger('master_product_parent_option_id')->nullable()->after('id');

            // Jika ingin menambahkan foreign key ke tabel master_product_parent_options:
            // $table->foreign('master_product_parent_option_id')
            //       ->references('id')->on('master_product_parent_options')
            //       ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('partner_product_parent_options', function (Blueprint $table) {
            // Jika ada foreign key, drop dulu
            // $table->dropForeign(['master_product_parent_option_id']);

            $table->dropColumn('master_product_parent_option_id');
        });
    }
};
