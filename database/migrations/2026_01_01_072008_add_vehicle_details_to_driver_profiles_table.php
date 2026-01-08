<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_profiles', function (Blueprint $table) {
            $table->string('vehicle_make_model')->nullable()->after('vehicle_type');
            $table->string('registration_number')->nullable()->after('vehicle_make_model');
            $table->string('manufacture_year')->nullable()->after('registration_number');
            $table->string('fuel_type')->nullable()->after('manufacture_year');

        });
    }

    public function down(): void
    {
        Schema::table('driver_profiles', function (Blueprint $table) {

            $table->dropColumn([
                'vehicle_make_model',
                'registration_number',
                'manufacture_year',
                'fuel_type',
            ]);

        });
    }
};
