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
        Schema::table('owner_manual_payments', function (Blueprint $table) {
            $table->boolean('is_active')
                ->default(false)
                ->after('additional_info');
        });
    }

    public function down(): void
    {
        Schema::table('owner_manual_payments', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
