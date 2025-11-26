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
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->enum('message_type', ['message', 'popup'])->default('message');
            $table->enum('recipient_target', ['single', 'broadcast']);
            $table->enum('recipient_type', ['all', 'business-partner', 'outlet', 'owner', 'employee', 'end-customer']); // misal: user, role, dll
            $table->foreignId('recipient_id')->nullable();

            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
