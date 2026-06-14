<?php

namespace App\Services\Dashboard;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrendAnalysisService
{
    public function weeklyVolume(User $user, ?string $sportType, Carbon $from, Carbon $to): Collection
    {
        $weekExpr = $this->weekExpr('started_at');

        $query = Activity::where('user_id', $user->id)
            ->where('started_at', '>=', $from)
            ->where('started_at', '<=', $to)
            ->selectRaw("
                {$weekExpr} as period,
                COUNT(*) as activities,
                ROUND(SUM(distance) / 1000.0, 1) as distance_km,
                ROUND(SUM(elapsed_time) / 3600.0, 2) as hours
            ")
            ->groupByRaw($weekExpr)
            ->orderByRaw($weekExpr);

        if ($sportType !== null) {
            $query->where('sport_type', $sportType);
        }

        return $query->get();
    }

    public function monthlyVolume(User $user, ?string $sportType, Carbon $from, Carbon $to): Collection
    {
        $monthExpr = $this->monthLabelExpr('started_at');

        $query = Activity::where('user_id', $user->id)
            ->where('started_at', '>=', $from)
            ->where('started_at', '<=', $to)
            ->selectRaw("
                {$monthExpr} as period,
                COUNT(*) as activities,
                ROUND(SUM(distance) / 1000.0, 1) as distance_km,
                ROUND(SUM(elapsed_time) / 3600.0, 2) as hours
            ")
            ->groupByRaw($monthExpr)
            ->orderByRaw($monthExpr);

        if ($sportType !== null) {
            $query->where('sport_type', $sportType);
        }

        return $query->get();
    }

    private function weekExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "strftime('%Y-%W', {$column})";
        }

        return "DATE_FORMAT({$column}, '%Y-%u')";
    }

    private function monthLabelExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "strftime('%Y-%m', {$column})";
        }

        return "DATE_FORMAT({$column}, '%Y-%m')";
    }
}
