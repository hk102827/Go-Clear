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
 Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('pickup_address');
            $table->string('pickup_postcode');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_postcode')->nullable();
            $table->decimal('delivery_latitude', 10, 8)->nullable();
            $table->decimal('delivery_longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('distance', 10, 2)->nullable(); // in miles
            $table->integer('duration')->nullable(); // in minutes
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
   
    }
};
