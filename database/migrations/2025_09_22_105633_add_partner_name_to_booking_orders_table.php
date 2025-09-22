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
        Schema::table('booking_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_orders', 'partner_name')) {
                $table->string('partner_name')->nullable()->after('partner_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_orders', function (Blueprint $table) {
            if (Schema::hasColumn('booking_orders', 'partner_name')) {
                $table->dropColumn('partner_name');
            }
        });
    }
};
