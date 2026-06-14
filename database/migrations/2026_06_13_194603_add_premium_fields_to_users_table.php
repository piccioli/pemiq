<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('remember_token');
            $table->timestamp('premium_started_at')->nullable()->after('is_premium');
            $table->timestamp('premium_expires_at')->nullable()->after('premium_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'premium_started_at', 'premium_expires_at']);
        });
    }
};
