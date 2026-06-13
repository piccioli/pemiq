<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('strava_account_id')->constrained()->cascadeOnDelete();
            $table->enum('sync_type', ['historical', 'incremental']);
            $table->enum('status', ['pending', 'running', 'completed', 'failed']);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('activities_imported')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
