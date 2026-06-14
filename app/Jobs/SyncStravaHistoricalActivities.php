<?php

namespace App\Jobs;

use App\Exceptions\Strava\StravaRateLimitException;
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

class SyncStravaHistoricalActivities implements ShouldQueue
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
        $syncLog = $syncService->createSyncLog($this->account, 'historical');

        try {
            $activities = $apiService->fetchActivities($this->account);
            $count = $syncService->sync($this->account, $activities, $syncLog);
            $syncService->completeSyncLog($syncLog, $count);
            $this->account->update(['last_sync_at' => now()]);
        } catch (StravaTokenExpiredException $e) {
            // Force token refresh before Laravel retries the job
            try {
                $tokenService->ensureValidToken($this->account->fresh());
            } catch (\Exception) {
                // Refresh failed — next retry will attempt again
            }
            $syncService->failSyncLog($syncLog, 'Token scaduto, verrà riprovato');
            throw $e;
        } catch (StravaRateLimitException $e) {
            $syncService->failSyncLog($syncLog, $e->getMessage(), 'rate_limited');
            throw $e;
        } catch (\Exception $e) {
            $syncService->failSyncLog($syncLog, $e->getMessage());
            throw $e;
        }
    }
}
