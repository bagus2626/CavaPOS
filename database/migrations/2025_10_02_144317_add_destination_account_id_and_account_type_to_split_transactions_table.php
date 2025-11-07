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
        Schema::table('split_transactions', function (Blueprint $table) {
            $table->string('destination_account_id')->nullable()->after('sub_account_id');
            $table->string('reference_id')->nullable()->after('destination_account_id');
            $table->enum('account_type', ['MASTER', 'SUB_ACCOUNT'])->nullable()->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('split_transactions', function (Blueprint $table) {
            $table->dropColumn(['destination_account_id', 'reference_id', 'account_type']);
        });
    }
};
