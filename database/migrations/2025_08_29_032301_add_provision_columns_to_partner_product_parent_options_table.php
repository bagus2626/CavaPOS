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
            $table->string('provision')->nullable()->after('description');
            $table->integer('provision_value')->nullable()->after('provision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_product_parent_options', function (Blueprint $table) {
            $table->dropColumn(['provision', 'provision_value']);
        });
    }
};
