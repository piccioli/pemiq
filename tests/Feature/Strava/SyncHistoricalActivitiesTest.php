<?php

namespace Tests\Feature\Strava;

use App\Exceptions\Strava\StravaRateLimitException;
use App\Jobs\SyncStravaHistoricalActivities;
use App\Models\StravaAccount;
use App\Models\User;
use App\Services\Strava\StravaApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncHistoricalActivitiesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StravaAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->account = StravaAccount::create([
            'user_id'           => $this->user->id,
            'strava_athlete_id' => 12345,
            'access_token'      => 'valid-access-token',
            'refresh_token'     => 'valid-refresh-token',
            'token_expires_at'  => now()->addHour(),
            'connection_status' => 'connected',
        ]);
    }

    public function test_first_sync_saves_all_activities_from_paginated_api(): void
    {
        Http::fake([
            'https://www.strava.com/api/v3/athlete/activities*' => Http::sequence()
                ->push($this->makeActivities(100), 200)
                ->push($this->makeActivities(2, 200), 200),
        ]);

        SyncStravaHistoricalActivities::dispatch($this->account);

        $this->assertDatabaseCount('activities', 102);
        $this->assertDatabaseHas('sync_logs', [
            'user_id'            => $this->user->id,
            'sync_type'          => 'historical',
            'status'             => 'completed',
            'activities_imported' => 102,
        ]);
    }

    public function test_existing_activity_is_not_duplicated(): void
    {
        Http::fake([
            'https://www.strava.com/api/v3/athlete/activities*' => Http::response(
                [$this->makeActivity(5000)],
                200
            ),
        ]);

        SyncStravaHistoricalActivities::dispatch($this->account);
        $this->assertDatabaseCount('activities', 1);

        SyncStravaHistoricalActivities::dispatch($this->account);
        $this->assertDatabaseCount('activities', 1);

        $this->assertDatabaseHas('activities', [
            'strava_activity_id' => 5000,
            'user_id'            => $this->user->id,
        ]);
        $this->assertDatabaseCount('sync_logs', 2);
    }

    public function test_rate_limited_response_saves_rate_limited_sync_log(): void
    {
        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaRateLimitException('Strava rate limit exceeded after retries'));
        });

        try {
            SyncStravaHistoricalActivities::dispatch($this->account);
            $this->fail('Expected StravaRateLimitException was not thrown');
        } catch (StravaRateLimitException) {
            // Expected — job re-throws after saving log
        }

        $this->assertDatabaseHas('sync_logs', [
            'user_id'        => $this->user->id,
            'status'         => 'rate_limited',
            'error_message'  => 'Strava rate limit exceeded after retries',
        ]);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_expired_token_triggers_refresh_and_sync_completes(): void
    {
        $this->account->update(['token_expires_at' => now()->subHour()]);

        Http::fake([
            'https://www.strava.com/oauth/token' => Http::response([
                'access_token'  => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_at'    => now()->addHour()->timestamp,
            ], 200),
            'https://www.strava.com/api/v3/athlete/activities*' => Http::response(
                [$this->makeActivity(7001)],
                200
            ),
        ]);

        SyncStravaHistoricalActivities::dispatch($this->account);

        $this->assertDatabaseHas('sync_logs', [
            'user_id'             => $this->user->id,
            'status'              => 'completed',
            'activities_imported' => 1,
        ]);

        $this->assertDatabaseHas('activities', [
            'user_id'            => $this->user->id,
            'strava_activity_id' => 7001,
        ]);

        // Verify the token was refreshed in the DB
        $this->account->refresh();
        $this->assertEquals('new-access-token', $this->account->access_token);
    }

    private function makeActivity(int $id): array
    {
        return [
            'id'                   => $id,
            'name'                 => 'Test Activity ' . $id,
            'sport_type'           => 'Run',
            'start_date'           => '2025-06-01T08:00:00Z',
            'distance'             => 5000.0,
            'elapsed_time'         => 1800,
            'moving_time'          => 1750,
            'total_elevation_gain' => 50.0,
            'average_speed'        => 2.78,
            'max_speed'            => 3.5,
            'average_heartrate'    => 150.0,
            'max_heartrate'        => 170.0,
            'average_watts'        => null,
            'kilojoules'           => null,
            'calories'             => null,
            'map'                  => ['summary_polyline' => null],
        ];
    }

    private function makeActivities(int $count, int $startId = 1000): array
    {
        return array_map(fn ($i) => $this->makeActivity($startId + $i), range(0, $count - 1));
    }
}
