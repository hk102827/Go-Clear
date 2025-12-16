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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('admin_commission', 10, 2)->default(0);
            $table->decimal('driver_earning', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'wallet'])->default('card');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
