<?php

namespace App\Services\Sync;

use App\Models\Activity;
use App\Models\StravaAccount;
use App\Models\SyncLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ActivitySyncService
{
    public function sync(StravaAccount $account, Collection $stravaActivities, SyncLog $syncLog): int
    {
        $count = 0;

        $syncLog->update(['status' => 'running']);

        foreach ($stravaActivities as $data) {
            Activity::updateOrCreate(
                ['strava_activity_id' => $data['id']],
                $this->mapActivityData($account->user_id, $data)
            );

            $count++;
        }

        $syncLog->update(['activities_imported' => $count]);

        return $count;
    }

    public function createSyncLog(StravaAccount $account, string $type): SyncLog
    {
        return SyncLog::create([
            'user_id' => $account->user_id,
            'strava_account_id' => $account->id,
            'sync_type' => $type,
            'status' => 'pending',
            'started_at' => Carbon::now(),
            'activities_imported' => 0,
        ]);
    }

    public function completeSyncLog(SyncLog $log, int $count): void
    {
        $log->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
            'activities_imported' => $count,
        ]);
    }

    public function failSyncLog(SyncLog $log, string $error, string $status = 'failed'): void
    {
        $log->update([
            'status' => $status,
            'error_message' => $error,
        ]);
    }

    private function mapActivityData(int $userId, array $data): array
    {
        $kilojoules = $data['kilojoules'] ?? null;
        $calories = $kilojoules !== null ? (int) round($kilojoules * 0.239) : ($data['calories'] ?? null);

        return [
            'user_id' => $userId,
            'name' => $data['name'] ?? null,
            'sport_type' => $data['sport_type'] ?? null,
            'started_at' => isset($data['start_date']) ? Carbon::parse($data['start_date']) : null,
            'distance' => $data['distance'] ?? null,
            'elapsed_time' => $data['elapsed_time'] ?? null,
            'moving_time' => $data['moving_time'] ?? null,
            'elevation_gain' => $data['total_elevation_gain'] ?? null,
            'average_speed' => $data['average_speed'] ?? null,
            'max_speed' => $data['max_speed'] ?? null,
            'average_heartrate' => $data['average_heartrate'] ?? null,
            'max_heartrate' => $data['max_heartrate'] ?? null,
            'average_watts' => $data['average_watts'] ?? null,
            'calories' => $calories,
            'polyline' => $data['map']['summary_polyline'] ?? null,
            'raw_data' => $data,
        ];
    }
}
