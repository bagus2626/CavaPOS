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
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_manual_payment_id')
                ->nullable()
                ->after('payment_type');

            $table->string('manual_provider_name')
                ->nullable()
                ->after('owner_manual_payment_id');

            $table->string('manual_provider_account_name')
                ->nullable()
                ->after('manual_provider_name');

            $table->string('manual_provider_account_no')
                ->nullable()
                ->after('manual_provider_account_name');
            $table->string('manual_payment_image')
                ->nullable()
                ->after('manual_provider_account_no');
        });
    }

    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn([
                'owner_manual_payment_id',
                'manual_provider_name',
                'manual_provider_account_name',
                'manual_provider_account_no',
            ]);
        });
    }
};
