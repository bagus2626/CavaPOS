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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();

            $table->string('file_path');      // path di storage/public
            $table->string('file_name');      // nama file untuk ditampilkan
            // $table->enum('attachment_type', ['image', 'attachment_image', 'document', 'video', 'audio', 'other'])->default('other');            
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // dalam byte

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
