<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('pickup_postcode')->nullable()->change();
            $table->string('delivery_postcode')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('pickup_postcode')->nullable(false)->change();
            $table->string('delivery_postcode')->nullable(false)->change();
        });
    }
};