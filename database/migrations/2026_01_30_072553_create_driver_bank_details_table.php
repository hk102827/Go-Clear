
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code'); // or routing_number for other countries
            $table->string('branch_name')->nullable();
            $table->boolean('is_approved')->nullable(); // null=pending, true=approved, false=rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_bank_details');
    }
};