<?php

namespace App\Console\Commands;

use App\Jobs\SyncStravaIncrementalActivities;
use App\Models\StravaAccount;
use Illuminate\Console\Command;

class SyncAllStravaAccounts extends Command
{
    protected $signature = 'strava:sync-all';

    protected $description = 'Dispatch incremental Strava sync for all connected accounts';

    public function handle(): int
    {
        $accounts = StravaAccount::where('connection_status', 'connected')->get();

        foreach ($accounts as $account) {
            SyncStravaIncrementalActivities::dispatch($account);
        }

        $this->info("Dispatched sync for {$accounts->count()} account(s).");

        return self::SUCCESS;
    }
}
