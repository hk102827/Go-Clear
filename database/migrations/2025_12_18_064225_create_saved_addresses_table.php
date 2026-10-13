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
        Schema::create('saved_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('booking_id')->nullable(); // ✅ Added
            $table->string('label'); // Home, Office, Service Pickup, Service Delivery
            $table->string('address');
            $table->string('postcode')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('distance_meters')->nullable(); // ✅ Added
            $table->integer('duration_seconds')->nullable(); // ✅ Added
            $table->timestamps();
            
            // Optional: Add index for better query performance
            $table->index(['user_id', 'booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_addresses');
    }
};