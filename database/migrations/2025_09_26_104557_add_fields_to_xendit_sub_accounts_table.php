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
            $table->string('email')->nullable()->after('business_name');
            $table->string('type')->nullable()->after('email');
            $table->string('status')->nullable()->after('type');
            $table->string('country', 5)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('xendit_sub_accounts', function (Blueprint $table) {
            $table->dropColumn(['type', 'email', 'status', 'country']);
        });
    }
};
