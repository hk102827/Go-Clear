<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saved_addresses', function (Blueprint $table) {

            // booking_id numeric + nullable
            $table->unsignedBigInteger('booking_id')
                  ->nullable()
                  ->after('user_id');

            $table->unsignedInteger('distance_meters')
                  ->nullable()
                  ->after('longitude');

            $table->unsignedInteger('duration_seconds')
                  ->nullable()
                  ->after('distance_meters');

            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('saved_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'booking_id',
                'distance_meters',
                'duration_seconds'
            ]);
        });
    }
};
