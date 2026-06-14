<?php

namespace Tests\Feature\Dashboard;

use App\Models\Activity;
use App\Models\User;
use App\Services\Dashboard\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardStatsService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardStatsService::class);
        $this->user    = User::factory()->create();
    }

    public function test_user_with_no_activities_returns_neutral_values(): void
    {
        $overview = $this->service->getOverviewStats($this->user);
        $annual   = $this->service->getAnnualStats($this->user);
        $monthly  = $this->service->getMonthlyStats($this->user, 2024);
        $sport    = $this->service->getSportDistribution($this->user);

        $this->assertSame(0, $overview['total_activities']);
        $this->assertSame(0.0, $overview['total_distance_km']);
        $this->assertSame(0.0, $overview['total_elevation_m']);
        $this->assertSame(0, $overview['total_elapsed_seconds']);
        $this->assertSame(0, $overview['total_moving_seconds']);

        $this->assertTrue($annual->isEmpty());
        $this->assertTrue($sport->isEmpty());

        // getMonthlyStats always returns all 12 months with zeros
        $this->assertCount(12, $monthly);
        foreach ($monthly as $row) {
            $this->assertSame(0, $row->activities);
            $this->assertSame(0.0, $row->distance_km);
            $this->assertSame(0.0, $row->hours);
        }
    }

    public function test_activities_with_null_distance_and_elapsed_time_do_not_throw(): void
    {
        Activity::factory()->create([
            'user_id'           => $this->user->id,
            'strava_activity_id' => 111111,
            'sport_type'        => 'Run',
            'started_at'        => Carbon::create(2024, 5, 1),
            'distance'          => null,
            'elapsed_time'      => null,
            'moving_time'       => null,
            'elevation_gain'    => null,
        ]);

        $overview = $this->service->getOverviewStats($this->user);

        $this->assertSame(1, $overview['total_activities']);
        $this->assertSame(0.0, $overview['total_distance_km']);
        $this->assertSame(0.0, $overview['total_elevation_m']);
        $this->assertSame(0, $overview['total_elapsed_seconds']);
        $this->assertSame(0, $overview['total_moving_seconds']);

        $sport = $this->service->getSportDistribution($this->user);
        $this->assertCount(1, $sport);
        $this->assertSame('Run', $sport->first()->sport_type);
        $this->assertSame(0.0, (float) $sport->first()->distance_km);

        $annual = $this->service->getAnnualStats($this->user);
        $this->assertCount(1, $annual);
        $this->assertSame(1, (int) $annual->first()->activities);

        $monthly = $this->service->getMonthlyStats($this->user, 2024);
        $mayRow  = $monthly->firstWhere('month', 5);
        $this->assertSame(1, $mayRow->activities);
        $this->assertSame(0.0, $mayRow->distance_km);
    }

    public function test_50_activities_across_3_years_have_consistent_aggregates(): void
    {
        // 20 activities in January 2023 — each: distance=1000m, elapsed=3600s, elevation=100m
        $this->createActivities(20, Carbon::create(2023, 1, 15), 1_000, 3_600, 100, 'Run');
        // 20 activities in June 2024 — each: distance=2000m, elapsed=7200s, elevation=200m
        $this->createActivities(20, Carbon::create(2024, 6, 15), 2_000, 7_200, 200, 'Ride');
        // 10 activities in November 2025 — each: distance=3000m, elapsed=10800s, elevation=300m
        $this->createActivities(10, Carbon::create(2025, 11, 15), 3_000, 10_800, 300, 'Hike');

        // --- Overview ---
        $overview = $this->service->getOverviewStats($this->user);

        $this->assertSame(50, $overview['total_activities']);
        // Total distance: 20*1000 + 20*2000 + 10*3000 = 20000+40000+30000 = 90000m = 90.0km
        $this->assertSame(90.0, $overview['total_distance_km']);
        // Total elevation: 20*100 + 20*200 + 10*300 = 2000+4000+3000 = 9000m
        $this->assertSame(9_000.0, $overview['total_elevation_m']);
        // Total elapsed: 20*3600 + 20*7200 + 10*10800 = 72000+144000+108000 = 324000s
        $this->assertSame(324_000, $overview['total_elapsed_seconds']);

        // --- Annual stats (ordered DESC) ---
        $annual = $this->service->getAnnualStats($this->user);

        $this->assertCount(3, $annual);
        [$y2025, $y2024, $y2023] = $annual->values()->all();

        $this->assertSame(2025, (int) $y2025->year);
        $this->assertSame(10, (int) $y2025->activities);
        $this->assertSame(30.0, (float) $y2025->distance_km);    // 10*3000/1000
        $this->assertSame(3_000.0, (float) $y2025->elevation_m); // 10*300
        $this->assertSame(30.0, (float) $y2025->hours);          // 10*10800/3600

        $this->assertSame(2024, (int) $y2024->year);
        $this->assertSame(20, (int) $y2024->activities);
        $this->assertSame(40.0, (float) $y2024->distance_km);    // 20*2000/1000
        $this->assertSame(4_000.0, (float) $y2024->elevation_m); // 20*200
        $this->assertSame(40.0, (float) $y2024->hours);          // 20*7200/3600

        $this->assertSame(2023, (int) $y2023->year);
        $this->assertSame(20, (int) $y2023->activities);
        $this->assertSame(20.0, (float) $y2023->distance_km);    // 20*1000/1000
        $this->assertSame(2_000.0, (float) $y2023->elevation_m); // 20*100
        $this->assertSame(20.0, (float) $y2023->hours);          // 20*3600/3600

        // --- Monthly stats for 2023 (all 20 activities in January) ---
        $monthly = $this->service->getMonthlyStats($this->user, 2023);

        $this->assertCount(12, $monthly);

        $jan = $monthly->firstWhere('month', 1);
        $this->assertSame(20, $jan->activities);
        $this->assertSame(20.0, $jan->distance_km);
        $this->assertSame(20.0, $jan->hours);

        // All other months should be empty
        foreach ($monthly as $row) {
            if ($row->month !== 1) {
                $this->assertSame(0, $row->activities, "Month {$row->month} should have 0 activities");
            }
        }

        // --- Monthly stats for 2024 (all 20 activities in June) ---
        $monthly2024 = $this->service->getMonthlyStats($this->user, 2024);
        $jun = $monthly2024->firstWhere('month', 6);
        $this->assertSame(20, $jun->activities);
        $this->assertSame(40.0, $jun->distance_km);
    }

    public function test_user_b_cannot_see_user_a_activities(): void
    {
        $userA = $this->user;
        $userB = User::factory()->create();

        // Create 5 activities for user A
        $this->createActivities(5, Carbon::create(2024, 3, 10), 5_000, 1_800, 50, 'Run');

        // User B has no activities
        $overviewB = $this->service->getOverviewStats($userB);
        $annualB   = $this->service->getAnnualStats($userB);
        $monthlyB  = $this->service->getMonthlyStats($userB, 2024);
        $sportB    = $this->service->getSportDistribution($userB);

        $this->assertSame(0, $overviewB['total_activities']);
        $this->assertSame(0.0, $overviewB['total_distance_km']);
        $this->assertTrue($annualB->isEmpty());
        $this->assertTrue($sportB->isEmpty());
        $this->assertSame(0, $monthlyB->firstWhere('month', 3)->activities);

        // User A should still see their own 5 activities
        $overviewA = $this->service->getOverviewStats($userA);
        $this->assertSame(5, $overviewA['total_activities']);
    }

    private function createActivities(
        int $count,
        Carbon $date,
        int $distance,
        int $elapsed,
        int $elevation,
        string $sportType
    ): void {
        for ($i = 0; $i < $count; $i++) {
            Activity::factory()->create([
                'user_id'           => $this->user->id,
                'strava_activity_id' => fake()->unique()->numberBetween(1_000_000, 99_999_999),
                'sport_type'        => $sportType,
                'started_at'        => $date,
                'distance'          => $distance,
                'elapsed_time'      => $elapsed,
                'moving_time'       => $elapsed,
                'elevation_gain'    => $elevation,
            ]);
        }
    }
}
