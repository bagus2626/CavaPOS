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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // relasi ke partner (ubah "users" jika Anda punya tabel "partners")
            $table->foreignId('partner_id')
                  ->constrained('users')   // ->constrained('partners') jika pakai tabel partners
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('user_name')->unique();
            $table->string('email')->unique();
            $table->string('role');       // contoh: 'cashier', 'waiter', 'manager'
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
