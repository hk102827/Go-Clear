<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            // withdrawal_id add
            $table->foreignId('withdrawal_id')
                ->nullable()
                ->after('driver_id')
                ->constrained()
                ->onDelete('cascade');

    $table->enum('payment_method', [
        'cash',
        'card',
        'wallet',
        'bank_transfer'
    ])->default('card')->change(); // 👈 default card

        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->dropForeign(['withdrawal_id']);
            $table->dropColumn('withdrawal_id');

            // enum rollback (bank_transfer remove)
    $table->enum('payment_method', [
        'cash',
        'card',
        'wallet'
    ])->default('card')->change();
        });
    }
};
