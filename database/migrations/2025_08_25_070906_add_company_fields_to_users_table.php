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
        Schema::table('users', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('remember_token');
            $table->string('province')->nullable()->after('logo');
            $table->string('province_id')->nullable()->after('province');
            $table->string('city')->nullable()->after('province_id');
            $table->string('city_id')->nullable()->after('city');
            $table->string('subdistrict')->nullable()->after('city_id');
            $table->string('subdistrict_id')->nullable()->after('subdistrict');
            $table->string('urban_village')->nullable()->after('subdistrict_id');
            $table->string('urban_village_id')->nullable()->after('urban_village');
            $table->text('address')->nullable()->after('urban_village_id');
            $table->string('pic_name')->nullable()->after('address');
            $table->string('pic_email')->nullable()->after('pic_name');
            $table->string('pic_phone_number')->nullable()->after('pic_email');
            $table->string('pic_role')->nullable()->after('pic_phone_number');
            $table->boolean('is_active')->default(true)->after('pic_role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'logo',
                'province',
                'city',
                'subdistrict',
                'province_id',
                'city_id',
                'subdistrict_id',
                'urban_village',
                'urban_village_id',
                'address',
                'pic_name',
                'pic_email',
                'pic_phone_number',
                'pic_role',
                'is_active',
            ]);
        });
    }
};
