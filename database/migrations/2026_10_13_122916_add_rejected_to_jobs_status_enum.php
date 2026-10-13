<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE jobs
            MODIFY COLUMN status ENUM(
                'pending',
                'accepted',
                'in_progress',
                'completed',
                'cancelled',
                'rejected'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert to original enum — will fail if any row has status='rejected'
        DB::table('jobs')->where('status', 'rejected')->update(['status' => 'cancelled']);

        DB::statement("
            ALTER TABLE jobs
            MODIFY COLUMN status ENUM(
                'pending',
                'accepted',
                'in_progress',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};