<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't enforce enum constraints and doesn't support MODIFY COLUMN
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE sync_logs MODIFY COLUMN status ENUM('pending', 'running', 'completed', 'failed', 'rate_limited') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE sync_logs MODIFY COLUMN status ENUM('pending', 'running', 'completed', 'failed') NOT NULL");
    }
};
