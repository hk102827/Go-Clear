<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saved_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // numeric booking id (001 style will come from id or jobs table)
                        $table->string('booking_id')->nullable()->change();


            $table->string('label'); // Pickup / Delivery
            $table->string('address');
            $table->string('postcode')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->unsignedInteger('distance_meters')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->timestamps();

            // optional safety index
            $table->index(['user_id', 'booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addresses');
    }
};
