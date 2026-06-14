<?php

namespace Tests\Feature\Strava;

use App\Exceptions\Strava\StravaAuthException;
use App\Exceptions\Strava\StravaApiException;
use App\Jobs\SyncStravaHistoricalActivities;
use App\Jobs\SyncStravaIncrementalActivities;
use App\Mail\SyncFailedNotification;
use App\Models\StravaAccount;
use App\Models\User;
use App\Services\Strava\StravaApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncFailedNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StravaAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);

        $this->user = User::factory()->create(['email' => 'athlete@test.com']);
        $this->account = StravaAccount::create([
            'user_id'           => $this->user->id,
            'strava_athlete_id' => 99999,
            'access_token'      => 'valid-access-token',
            'refresh_token'     => 'valid-refresh-token',
            'token_expires_at'  => now()->addHour(),
            'connection_status' => 'connected',
        ]);
    }

    public function test_critical_auth_failure_sends_notification_and_sets_notified_at(): void
    {
        Mail::fake();

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaAuthException('Strava account deauthorized'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaAuthException) {
            // Expected — job re-throws
        }

        Mail::assertSent(SyncFailedNotification::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        $this->account->refresh();
        $this->assertNotNull($this->account->notified_at);
        $this->assertDatabaseHas('sync_logs', [
            'user_id' => $this->user->id,
            'status'  => 'failed',
        ]);
    }

    public function test_no_duplicate_notification_when_notified_at_already_set(): void
    {
        Mail::fake();

        $this->account->update(['notified_at' => now()->subDay()]);

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaAuthException('Strava account deauthorized'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaAuthException) {
            // Expected — job re-throws
        }

        Mail::assertNothingSent();
    }

    public function test_transitory_failure_does_not_send_notification(): void
    {
        Mail::fake();

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaApiException('Connection refused'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaApiException) {
            // Expected
        }

        Mail::assertNothingSent();
        $this->account->refresh();
        $this->assertNull($this->account->notified_at);
    }

    public function test_successful_sync_resets_notified_at(): void
    {
        Mail::fake();

        $this->account->update(['notified_at' => now()->subDay()]);

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andReturn(collect([]));
        });

        SyncStravaHistoricalActivities::dispatch($this->account);

        $this->account->refresh();
        $this->assertNull($this->account->notified_at);
    }

    public function test_incremental_sync_critical_failure_sends_notification(): void
    {
        Mail::fake();

        $this->account->update(['last_sync_at' => now()->subDay()]);

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaAuthException('Strava account deauthorized'));
        });

        try {
            SyncStravaIncrementalActivities::dispatch($this->account);
        } catch (StravaAuthException) {
            // Expected
        }

        Mail::assertSent(SyncFailedNotification::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        $this->account->refresh();
        $this->assertNotNull($this->account->notified_at);
    }

    public function test_incremental_sync_success_resets_notified_at(): void
    {
        Mail::fake();

        $this->account->update(['last_sync_at' => now()->subDay(), 'notified_at' => now()->subDay()]);

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andReturn(collect([]));
        });

        SyncStravaIncrementalActivities::dispatch($this->account);

        $this->account->refresh();
        $this->assertNull($this->account->notified_at);
    }
}
