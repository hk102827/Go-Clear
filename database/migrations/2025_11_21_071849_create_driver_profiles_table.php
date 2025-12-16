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
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Driving License Images
            $table->string('driving_license_front')->nullable();
            $table->string('driving_license_back')->nullable();
            // Other images & details
            $table->string('vehicle_photo')->nullable();
            $table->string('number_plate')->nullable();
            $table->string('insurance_proof')->nullable();
            $table->string('waste_carrier_license')->nullable();
            $table->string('vehicle_type')->nullable();
            // Verification
            $table->enum('verification_status', ['pending', 'in_progress', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            // Driver stats
            $table->boolean('is_online')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_jobs')->default(0);

            $table->timestamps(
                
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};
