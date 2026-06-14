<?php

namespace App\Services\Dashboard;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    public function getOverviewStats(User $user): array
    {
        $stats = Activity::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_activities,
                SUM(distance) as total_distance_m,
                SUM(elevation_gain) as total_elevation_m,
                SUM(elapsed_time) as total_elapsed_seconds,
                SUM(moving_time) as total_moving_seconds
            ')
            ->first();

        return [
            'total_activities'     => (int) ($stats->total_activities ?? 0),
            'total_distance_km'    => round(($stats->total_distance_m ?? 0) / 1000, 1),
            'total_elevation_m'    => round($stats->total_elevation_m ?? 0, 1),
            'total_elapsed_seconds' => (int) ($stats->total_elapsed_seconds ?? 0),
            'total_moving_seconds'  => (int) ($stats->total_moving_seconds ?? 0),
        ];
    }

    public function getAnnualStats(User $user): Collection
    {
        $yearExpr = $this->yearExpr('started_at');

        return Activity::where('user_id', $user->id)
            ->selectRaw("
                {$yearExpr} as year,
                COUNT(*) as activities,
                ROUND(SUM(distance) / 1000, 1) as distance_km,
                ROUND(SUM(elevation_gain), 1) as elevation_m,
                ROUND(SUM(elapsed_time) / 3600, 2) as hours
            ")
            ->groupByRaw($yearExpr)
            ->orderByRaw("{$yearExpr} DESC")
            ->get();
    }

    public function getMonthlyStats(User $user, int $year): Collection
    {
        $yearExpr  = $this->yearExpr('started_at');
        $monthExpr = $this->monthExpr('started_at');

        $rows = Activity::where('user_id', $user->id)
            ->whereRaw("{$yearExpr} = ?", [$year])
            ->selectRaw("
                {$monthExpr} as month,
                COUNT(*) as activities,
                ROUND(SUM(distance) / 1000, 1) as distance_km,
                ROUND(SUM(elevation_gain), 1) as elevation_m,
                ROUND(SUM(elapsed_time) / 3600, 2) as hours
            ")
            ->groupByRaw($monthExpr)
            ->get()
            ->keyBy('month');

        return collect(range(1, 12))->map(fn ($m) => (object) [
            'month'       => $m,
            'activities'  => (int) ($rows[$m]->activities ?? 0),
            'distance_km' => (float) ($rows[$m]->distance_km ?? 0),
            'elevation_m' => (float) ($rows[$m]->elevation_m ?? 0),
            'hours'       => (float) ($rows[$m]->hours ?? 0),
        ]);
    }

    public function getSportDistribution(User $user): Collection
    {
        return Activity::where('user_id', $user->id)
            ->selectRaw('
                sport_type,
                COUNT(*) as activities,
                ROUND(SUM(distance) / 1000, 1) as distance_km,
                ROUND(SUM(elapsed_time) / 3600, 2) as hours
            ')
            ->groupBy('sport_type')
            ->orderByRaw('COUNT(*) DESC')
            ->get();
    }

    private function yearExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%Y', {$column}) AS INTEGER)";
        }

        return "YEAR({$column})";
    }

    private function monthExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%m', {$column}) AS INTEGER)";
        }

        return "MONTH({$column})";
    }
}
