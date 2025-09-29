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

            // Tambah kolom partner_product_option_name
            if (!Schema::hasColumn('order_detail_options', 'partner_product_option_name')) {
                $table->string('parent_name')->nullable()->after('option_id');
                $table->string('partner_product_option_name')->nullable()->after('parent_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_detail_options', function (Blueprint $table) {
            if (Schema::hasColumn('order_detail_options', 'partner_product_option_name')) {
                $table->dropColumn('partner_product_option_name');
                $table->dropColumn('parent_name');
            }
        });
    }
};
