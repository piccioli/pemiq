<?php

namespace Tests\Feature\Strava;

use App\Exceptions\Strava\StravaApiException;
use App\Jobs\SyncStravaIncrementalActivities;
use App\Models\StravaAccount;
use App\Models\User;
use App\Services\Strava\StravaApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncIncrementalActivitiesTest extends TestCase
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
            'last_sync_at'      => now()->subDay(),
            'connection_status' => 'connected',
        ]);
    }

    public function test_no_new_activities_creates_sync_log_with_zero_count(): void
    {
        Http::fake([
            'https://www.strava.com/api/v3/athlete/activities*' => Http::response([], 200),
        ]);

        SyncStravaIncrementalActivities::dispatch($this->account);

        $this->assertDatabaseCount('activities', 0);
        $this->assertDatabaseHas('sync_logs', [
            'user_id'            => $this->user->id,
            'sync_type'          => 'incremental',
            'status'             => 'completed',
            'activities_imported' => 0,
        ]);
    }

    public function test_new_activities_are_saved_correctly(): void
    {
        Http::fake([
            'https://www.strava.com/api/v3/athlete/activities*' => Http::response(
                [$this->makeActivity(9001), $this->makeActivity(9002)],
                200
            ),
        ]);

        SyncStravaIncrementalActivities::dispatch($this->account);

        $this->assertDatabaseCount('activities', 2);
        $this->assertDatabaseHas('activities', [
            'user_id'            => $this->user->id,
            'strava_activity_id' => 9001,
        ]);
        $this->assertDatabaseHas('activities', [
            'user_id'            => $this->user->id,
            'strava_activity_id' => 9002,
        ]);
        $this->assertDatabaseHas('sync_logs', [
            'user_id'            => $this->user->id,
            'sync_type'          => 'incremental',
            'status'             => 'completed',
            'activities_imported' => 2,
        ]);
    }

    public function test_api_failure_creates_failed_sync_log_with_error_message(): void
    {
        $this->mock(StravaApiService::class, function ($mock) {
            $mock->shouldReceive('fetchActivities')
                ->once()
                ->andThrow(new StravaApiException('Connection refused'));
        });

        try {
            SyncStravaIncrementalActivities::dispatch($this->account);
            $this->fail('Expected StravaApiException was not thrown');
        } catch (StravaApiException) {
            // Expected — job re-throws after saving log
        }

        $this->assertDatabaseCount('activities', 0);
        $this->assertDatabaseHas('sync_logs', [
            'user_id'       => $this->user->id,
            'sync_type'     => 'incremental',
            'status'        => 'failed',
            'error_message' => 'Connection refused',
        ]);
    }

    private function makeActivity(int $id): array
    {
        return [
            'id'                   => $id,
            'name'                 => 'Test Activity ' . $id,
            'sport_type'           => 'Run',
            'start_date'           => '2025-06-10T08:00:00Z',
            'distance'             => 8000.0,
            'elapsed_time'         => 2400,
            'moving_time'          => 2350,
            'total_elevation_gain' => 80.0,
            'average_speed'        => 3.33,
            'max_speed'            => 4.0,
            'average_heartrate'    => 155.0,
            'max_heartrate'        => 175.0,
            'average_watts'        => null,
            'kilojoules'           => null,
            'calories'             => null,
            'map'                  => ['summary_polyline' => null],
        ];
    }
}
