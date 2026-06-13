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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('strava_activity_id')->unique();
            $table->string('name', 255);
            $table->string('sport_type', 100);
            $table->timestamp('started_at');
            $table->float('distance')->nullable();
            $table->integer('elapsed_time')->nullable();
            $table->integer('moving_time')->nullable();
            $table->float('elevation_gain')->nullable();
            $table->float('average_speed')->nullable();
            $table->float('max_speed')->nullable();
            $table->float('average_heartrate')->nullable();
            $table->float('max_heartrate')->nullable();
            $table->float('average_watts')->nullable();
            $table->integer('calories')->nullable();
            $table->text('polyline')->nullable();
            $table->json('raw_data');
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
            $table->index(['user_id', 'sport_type']);
            $table->index(['user_id', 'started_at', 'sport_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
