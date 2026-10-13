<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('nearby_notification_sent')
                  ->default(false)
                  ->after('status');

            $table->boolean('arrived_notification_sent')
                  ->default(false)
                  ->after('nearby_notification_sent');

            $table->timestamp('started_at')
                  ->nullable()
                  ->after('status');

            $table->timestamp('arrived_at')
                  ->nullable()
                  ->after('started_at');

            $table->timestamp('completed_at')
                  ->nullable()
                  ->after('arrived_at');
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'nearby_notification_sent',
                'arrived_notification_sent',
                'started_at',
                'arrived_at',
                'completed_at',
            ]);
        });
    }
};
