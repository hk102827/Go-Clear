<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->decimal('fixed_price', 10, 2)->nullable();
            $table->decimal('price_per_mile', 10, 2)->nullable();
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_load', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_pricings');
    }
};
