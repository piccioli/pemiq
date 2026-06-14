<?php

namespace App\Livewire\Strava;

use App\Jobs\SyncStravaHistoricalActivities;
use App\Models\StravaAccount;
use App\Models\SyncLog;
use Illuminate\View\View;
use Livewire\Component;

class ConnectionStatus extends Component
{
    public ?string $syncMessage = null;

    public function startHistoricalSync(): void
    {
        $account = $this->getConnectedAccount();

        if (! $account) {
            return;
        }

        $latestSync = $this->getLatestSync($account);
        if ($latestSync?->status === 'running') {
            return;
        }

        SyncStravaHistoricalActivities::dispatch($account);

        $this->syncMessage = 'Sincronizzazione avviata';
    }

    public function render(): View
    {
        $account = $this->getConnectedAccount();
        $latestSync = $account ? $this->getLatestSync($account) : null;
        $syncStatus = $latestSync?->status;
        $syncActivitiesImported = (int) ($latestSync?->activities_imported ?? 0);
        $syncErrorMessage = $latestSync?->error_message;

        return view('livewire.strava.connection-status', [
            'stravaAccount' => $account,
            'syncStatus' => $syncStatus,
            'syncActivitiesImported' => $syncActivitiesImported,
            'syncErrorMessage' => $syncErrorMessage,
        ]);
    }

    private function getConnectedAccount(): ?StravaAccount
    {
        return auth()->user()->stravaAccount?->connection_status === 'connected'
            ? auth()->user()->stravaAccount
            : null;
    }

    private function getLatestSync(StravaAccount $account): ?SyncLog
    {
        return SyncLog::where('strava_account_id', $account->id)
            ->latest('started_at')
            ->first();
    }
}
