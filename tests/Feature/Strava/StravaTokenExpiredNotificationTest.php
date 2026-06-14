<?php

namespace Tests\Feature\Strava;

use App\Exceptions\Strava\StravaAuthException;
use App\Exceptions\Strava\StravaTokenExpiredException;
use App\Exceptions\Strava\StravaTokenRefreshException;
use App\Jobs\SyncStravaHistoricalActivities;
use App\Jobs\SyncStravaIncrementalActivities;
use App\Mail\StravaTokenExpiredNotification;
use App\Models\StravaAccount;
use App\Models\User;
use App\Services\Strava\StravaApiService;
use App\Services\Strava\StravaTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StravaTokenExpiredNotificationTest extends TestCase
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
            'access_token'      => 'expired-access-token',
            'refresh_token'     => 'invalid-refresh-token',
            'token_expires_at'  => now()->subHour(),
            'connection_status' => 'connected',
        ]);
    }

    public function test_historical_sync_sends_notification_when_token_refresh_permanently_fails(): void
    {
        Mail::fake();

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaTokenExpiredException('Access token expired'));
        });

        $this->mock(StravaTokenService::class, function ($mock) {
            $mock->shouldReceive('ensureValidToken')
                ->once()
                ->andThrow(new StravaAuthException('Refresh token permanently invalid'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaTokenExpiredException) {
            // Expected — job re-throws
        }

        Mail::assertSent(StravaTokenExpiredNotification::class, function ($mail) {
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
                ->andThrow(new StravaTokenExpiredException('Access token expired'));
        });

        $this->mock(StravaTokenService::class, function ($mock) {
            $mock->shouldReceive('ensureValidToken')
                ->once()
                ->andThrow(new StravaAuthException('Refresh token permanently invalid'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaTokenExpiredException) {
            // Expected
        }

        Mail::assertNothingSent();
    }

    public function test_transient_refresh_failure_does_not_send_notification(): void
    {
        Mail::fake();

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaTokenExpiredException('Access token expired'));
        });

        $this->mock(StravaTokenService::class, function ($mock) {
            $mock->shouldReceive('ensureValidToken')
                ->once()
                ->andThrow(new StravaTokenRefreshException('Network error'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
        } catch (StravaTokenExpiredException) {
            // Expected
        }

        Mail::assertNothingSent();
        $this->account->refresh();
        $this->assertNull($this->account->notified_at);
    }

    public function test_incremental_sync_sends_notification_when_token_refresh_permanently_fails(): void
    {
        Mail::fake();

        $this->account->update(['last_sync_at' => now()->subDay()]);

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaTokenExpiredException('Access token expired'));
        });

        $this->mock(StravaTokenService::class, function ($mock) {
            $mock->shouldReceive('ensureValidToken')
                ->once()
                ->andThrow(new StravaAuthException('Refresh token permanently invalid'));
        });

        try {
            SyncStravaIncrementalActivities::dispatch($this->account);
        } catch (StravaTokenExpiredException) {
            // Expected
        }

        Mail::assertSent(StravaTokenExpiredNotification::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        $this->account->refresh();
        $this->assertNotNull($this->account->notified_at);
    }

    public function test_no_notification_when_fetch_succeeds_despite_token_issues(): void
    {
        Mail::fake();

        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andReturn(collect([]));
        });

        SyncStravaHistoricalActivities::dispatch($this->account);

        Mail::assertNothingSent();
        $this->account->refresh();
        $this->assertNull($this->account->notified_at);
    }
}
