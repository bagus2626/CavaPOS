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
        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->boolean('always_available_flag')
                ->default(false)
                ->after('quantity'); // ganti dengan kolom yang sesuai
        });
    }

    public function down(): void
    {
        Schema::table('partner_product_options', function (Blueprint $table) {
            $table->dropColumn('always_available_flag');
        });
    }
};
