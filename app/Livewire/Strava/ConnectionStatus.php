<?php

namespace App\Livewire\Strava;

use App\Jobs\SyncStravaHistoricalActivities;
use App\Models\StravaAccount;
use App\Models\SyncLog;
use Illuminate\View\View;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Poll('5s')]
class ConnectionStatus extends Component
{
    public ?string $syncMessage = null;

    public function startHistoricalSync(): void
    {
        $account = $this->getConnectedAccount();

        if (! $account) {
            return;
        }

        if ($this->isSyncRunning($account)) {
            return;
        }

        SyncStravaHistoricalActivities::dispatch($account);

        $this->syncMessage = 'Sincronizzazione avviata';
    }

    public function render(): View
    {
        $account = $this->getConnectedAccount();
        $syncRunning = $account ? $this->isSyncRunning($account) : false;

        return view('livewire.strava.connection-status', [
            'stravaAccount' => $account,
            'syncRunning' => $syncRunning,
        ]);
    }

    private function getConnectedAccount(): ?StravaAccount
    {
        return auth()->user()->stravaAccount?->connection_status === 'connected'
            ? auth()->user()->stravaAccount
            : null;
    }

    private function isSyncRunning(StravaAccount $account): bool
    {
        return SyncLog::where('strava_account_id', $account->id)
            ->where('status', 'running')
            ->exists();
    }
}
