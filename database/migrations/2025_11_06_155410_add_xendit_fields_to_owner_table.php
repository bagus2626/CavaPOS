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
        Schema::table('owners', function (Blueprint $table) {
            $table->string('xendit_registration_status')
                ->nullable()
                ->after('is_active');

            $table->dateTime('xendit_registered_at')
                ->nullable()
                ->after('xendit_registration_status');

            $table->enum('xendit_split_rule_status', ['none', 'created'])
                ->default('none')
                ->after('xendit_registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_registration_status',
                'xendit_registered_at',
                'xendit_split_rule_status'
            ]);
        });
    }
};
