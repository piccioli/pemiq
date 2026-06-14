<?php

namespace Tests\Feature\Dashboard;

use App\Models\Activity;
use App\Models\User;
use App\Services\Dashboard\TrendAnalysisService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TrendAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrendAnalysisService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TrendAnalysisService::class);
        $this->user    = User::factory()->create();
    }

    public function test_monthly_volume_with_zero_activities_returns_empty_collection(): void
    {
        $result = $this->service->monthlyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_weekly_volume_with_zero_activities_returns_empty_collection(): void
    {
        $result = $this->service->weeklyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_monthly_volume_aggregates_by_month(): void
    {
        // 5 Run activities on Jan 15, 2024 — each 10 000 m, 3 600 s
        $this->createActivities(5, Carbon::create(2024, 1, 15), 10_000, 3_600, 'Run');
        // 3 Ride activities on Mar 20, 2024 — each 5 000 m, 1 800 s
        $this->createActivities(3, Carbon::create(2024, 3, 20), 5_000, 1_800, 'Ride');

        $result = $this->service->monthlyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertCount(2, $result);

        $jan = $result->firstWhere('period', '2024-01');
        $this->assertNotNull($jan);
        $this->assertSame(5, (int) $jan->activities);
        $this->assertSame(50.0, (float) $jan->distance_km); // 5 * 10 000 / 1 000
        $this->assertSame(5.0, (float) $jan->hours);         // 5 * 3 600 / 3 600

        $mar = $result->firstWhere('period', '2024-03');
        $this->assertNotNull($mar);
        $this->assertSame(3, (int) $mar->activities);
        $this->assertSame(15.0, (float) $mar->distance_km); // 3 * 5 000 / 1 000
        $this->assertSame(1.5, (float) $mar->hours);         // 3 * 1 800 / 3 600
    }

    public function test_monthly_volume_filters_by_sport_type(): void
    {
        $this->createActivities(5, Carbon::create(2024, 1, 15), 10_000, 3_600, 'Run');
        $this->createActivities(3, Carbon::create(2024, 1, 20), 5_000, 1_800, 'Ride');

        $runOnly = $this->service->monthlyVolume(
            $this->user,
            'Run',
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertCount(1, $runOnly);
        $this->assertSame(5, (int) $runOnly->first()->activities);
        $this->assertSame(50.0, (float) $runOnly->first()->distance_km);
    }

    public function test_monthly_volume_excludes_activities_outside_range(): void
    {
        // Before range
        $this->createActivities(2, Carbon::create(2023, 12, 31), 5_000, 1_800, 'Run');
        // Inside range
        $this->createActivities(3, Carbon::create(2024, 6, 15), 5_000, 1_800, 'Run');
        // After range
        $this->createActivities(2, Carbon::create(2025, 1, 1), 5_000, 1_800, 'Run');

        $result = $this->service->monthlyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31, 23, 59, 59)
        );

        $this->assertSame(3, (int) $result->sum(fn ($r) => (int) $r->activities));
    }

    public function test_weekly_volume_aggregates_by_week(): void
    {
        // Jan 15 and Feb 5 are 3 weeks apart — definitely different week buckets
        $this->createActivities(4, Carbon::create(2024, 1, 15), 10_000, 3_600, 'Run');
        $this->createActivities(2, Carbon::create(2024, 2, 5), 5_000, 1_800, 'Run');

        $result = $this->service->weeklyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertCount(2, $result);

        $totalActivities = (int) $result->sum(fn ($r) => (int) $r->activities);
        $this->assertSame(6, $totalActivities);

        // Results should be ordered chronologically (earlier week first)
        $periods = $result->pluck('period')->toArray();
        $sorted  = collect($periods)->sort()->values()->toArray();
        $this->assertSame($sorted, $periods);
    }

    public function test_weekly_volume_groups_same_week_activities_together(): void
    {
        // Jan 15 (Mon) and Jan 17 (Wed) are in the same week
        $this->createActivities(2, Carbon::create(2024, 1, 15), 10_000, 3_600, 'Run');
        $this->createActivities(3, Carbon::create(2024, 1, 17), 10_000, 3_600, 'Run');

        $result = $this->service->weeklyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertCount(1, $result);
        $this->assertSame(5, (int) $result->first()->activities);
    }

    public function test_weekly_volume_filters_by_sport_type(): void
    {
        $this->createActivities(3, Carbon::create(2024, 1, 15), 10_000, 3_600, 'Run');
        $this->createActivities(2, Carbon::create(2024, 1, 15), 5_000, 1_800, 'Ride');

        $result = $this->service->weeklyVolume(
            $this->user,
            'Run',
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertCount(1, $result);
        $this->assertSame(3, (int) $result->first()->activities);
    }

    public function test_weekly_volume_excludes_activities_outside_range(): void
    {
        // Before range
        $this->createActivities(2, Carbon::create(2023, 12, 31), 5_000, 1_800, 'Run');
        // Inside range
        $this->createActivities(4, Carbon::create(2024, 6, 15), 5_000, 1_800, 'Run');
        // After range
        $this->createActivities(2, Carbon::create(2025, 1, 1), 5_000, 1_800, 'Run');

        $result = $this->service->weeklyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31, 23, 59, 59)
        );

        $this->assertSame(4, (int) $result->sum(fn ($r) => (int) $r->activities));
    }

    public function test_monthly_volume_scoped_to_user(): void
    {
        $otherUser = User::factory()->create();

        // 5 activities for $this->user
        $this->createActivities(5, Carbon::create(2024, 3, 10), 10_000, 3_600, 'Run');

        // 3 activities for other user
        for ($i = 0; $i < 3; $i++) {
            Activity::factory()->create([
                'user_id'            => $otherUser->id,
                'strava_activity_id' => fake()->unique()->numberBetween(1_000_000, 99_999_999),
                'sport_type'         => 'Run',
                'started_at'         => Carbon::create(2024, 3, 10),
                'distance'           => 10_000,
                'elapsed_time'       => 3_600,
                'moving_time'        => 3_600,
                'elevation_gain'     => 100,
            ]);
        }

        $result = $this->service->monthlyVolume(
            $this->user,
            null,
            Carbon::create(2024, 1, 1),
            Carbon::create(2024, 12, 31)
        );

        $this->assertSame(5, (int) $result->sum(fn ($r) => (int) $r->activities));
    }

    private function createActivities(
        int $count,
        Carbon $date,
        int $distance,
        int $elapsed,
        string $sportType
    ): void {
        for ($i = 0; $i < $count; $i++) {
            Activity::factory()->create([
                'user_id'            => $this->user->id,
                'strava_activity_id' => fake()->unique()->numberBetween(1_000_000, 99_999_999),
                'sport_type'         => $sportType,
                'started_at'         => $date,
                'distance'           => $distance,
                'elapsed_time'       => $elapsed,
                'moving_time'        => $elapsed,
                'elevation_gain'     => 100,
            ]);
        }
    }
}
