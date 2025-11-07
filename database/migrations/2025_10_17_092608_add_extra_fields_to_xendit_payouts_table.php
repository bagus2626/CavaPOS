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
        Schema::table('xendit_payouts', function (Blueprint $table) {
            $table->text('description')->nullable()->after('status');
            $table->string('idempotency_key')->nullable()->after('reference_id');
            $table->string('channel_category', 50)->nullable()->after('channel_code');
            $table->string('connector_reference')->nullable()->after('channel_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xendit_payouts', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'idempotency_key',
                'channel_category',
                'connector_reference',
            ]);
        });
    }
};
