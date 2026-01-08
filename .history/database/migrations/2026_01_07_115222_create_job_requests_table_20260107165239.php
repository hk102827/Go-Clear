<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');

            $table->enum('status', ['sent','accepted','rejected','expired','cancelled'])->default('sent');

            $table->dateTime('sent_at')->nullable();
            $table->dateTime('responded_at')->nullable();

            $table->timestamps();

            $table->unique(['job_id','driver_id']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
