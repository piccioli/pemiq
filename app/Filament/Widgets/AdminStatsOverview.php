<?php

namespace App\Filament\Widgets;

use App\Models\StravaAccount;
use App\Models\SyncLog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $failedLast24h = SyncLog::where('status', 'failed')
            ->where('created_at', '>', now()->subHours(24))
            ->count();

        return [
            Stat::make('Utenti Totali', User::count()),
            Stat::make('Utenti Premium', User::where('is_premium', true)->count()),
            Stat::make('Account Strava Collegati', StravaAccount::where('connection_status', 'connected')->count()),
            Stat::make('Sync Completate (24h)', SyncLog::where('status', 'completed')
                ->where('created_at', '>', now()->subHours(24))
                ->count()),
            Stat::make('Sync Fallite (24h)', $failedLast24h)
                ->color($failedLast24h > 0 ? 'danger' : 'success'),
        ];
    }
}
