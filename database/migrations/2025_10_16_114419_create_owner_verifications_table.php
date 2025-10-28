<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owner_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');

            // Snapshot data pribadi saat pengajuan
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->string('owner_email');
            $table->text('ktp_number');
            $table->string('ktp_photo_path');

            // Snapshot data usaha saat pengajuan
            $table->string('business_name');

            $table->foreignId('business_category_id')->constrained('business_categories')->onDelete('restrict');

            $table->text('business_address');
            $table->string('business_phone');
            $table->string('business_email')->nullable();
            $table->string('business_logo_path')->nullable();

            // Status & review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('owner_verifications');
    }
};
