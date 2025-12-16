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
        Schema::create('driver_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->enum('pricing_type', ['fixed', 'per_mile', 'per_hour', 'per_load']);
            $table->decimal('price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_services');
    }
};
