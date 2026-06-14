<?php

namespace App\Jobs;

use App\Exceptions\Strava\StravaTokenExpiredException;
use App\Models\StravaAccount;
use App\Services\Strava\StravaApiService;
use App\Services\Strava\StravaTokenService;
use App\Services\Sync\ActivitySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncStravaIncrementalActivities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 30, 60];

    public function __construct(public readonly StravaAccount $account) {}

    public function handle(
        StravaApiService $apiService,
        ActivitySyncService $syncService,
        StravaTokenService $tokenService,
    ): void {
        $syncType = $this->account->last_sync_at ? 'incremental' : 'historical';
        $after = $this->account->last_sync_at;

        $syncLog = $syncService->createSyncLog($this->account, $syncType);

        try {
            $activities = $apiService->fetchActivities($this->account, $after);
            $count = $syncService->sync($this->account, $activities, $syncLog);
            $syncService->completeSyncLog($syncLog, $count);
            $this->account->update(['last_sync_at' => now()]);
        } catch (StravaTokenExpiredException $e) {
            try {
                $tokenService->ensureValidToken($this->account->fresh());
            } catch (\Exception) {
                // Refresh failed — next retry will attempt again
            }
            $syncService->failSyncLog($syncLog, 'Token scaduto, verrà riprovato');
            throw $e;
        } catch (\Exception $e) {
            $syncService->failSyncLog($syncLog, $e->getMessage());
            throw $e;
        }
    }
}
