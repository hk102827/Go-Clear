<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('driver_profiles')->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedSmallInteger('heading')->nullable(); // 0-360
            $table->unsignedSmallInteger('speed')->nullable();   // km/h optional
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->unique('driver_id'); // 1 driver = 1 latest row
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};
