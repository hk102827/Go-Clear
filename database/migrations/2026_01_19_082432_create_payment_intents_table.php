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
    Schema::create('payment_intents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->foreignId('customer_id')->constrained('users');
        $table->string('stripe_payment_intent_id')->unique();
        $table->decimal('amount', 10, 2);
        $table->string('currency', 3)->default('usd');
        $table->string('status')->default('pending'); // pending, succeeded, failed, canceled
        $table->string('client_secret')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};
