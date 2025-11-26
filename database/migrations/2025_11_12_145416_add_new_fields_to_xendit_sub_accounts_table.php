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
        Schema::table('xendit_sub_accounts', function (Blueprint $table) {
            $table->string('master_acc_business_id')->nullable()->after('country');
            $table->boolean('payments_enabled')->default(false)->after('master_acc_business_id');
            $table->timestamp('created_xendit')->nullable()->after('payments_enabled');
            $table->timestamp('updated_xendit')->nullable()->after('created_xendit');
            $table->string('suspended_reason')->nullable()->after('updated_xendit');
            $table->timestamp('suspended_at')->nullable()->after('suspended_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xendit_sub_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'master_acc_business_id',
                'payments_enabled',
                'created_xendit',
                'updated_xendit',
                'suspended_reason',
                'suspended_at',
            ]);
        });
    }
};
