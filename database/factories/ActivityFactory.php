<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'strava_activity_id' => fake()->unique()->numberBetween(1_000_000, 99_999_999),
            'name'              => fake()->sentence(3),
            'sport_type'        => fake()->randomElement(['Run', 'Ride', 'Hike', 'Walk', 'TrailRun']),
            'started_at'        => fake()->dateTimeBetween('-3 years', 'now'),
            'distance'          => fake()->numberBetween(1_000, 50_000),
            'elapsed_time'      => fake()->numberBetween(600, 14_400),
            'moving_time'       => fake()->numberBetween(600, 14_400),
            'elevation_gain'    => fake()->numberBetween(0, 2_000),
            'average_speed'     => fake()->randomFloat(2, 1.0, 15.0),
            'max_speed'         => fake()->randomFloat(2, 2.0, 20.0),
            'average_heartrate' => null,
            'max_heartrate'     => null,
            'average_watts'     => null,
            'calories'          => fake()->numberBetween(100, 2_000),
            'polyline'          => null,
            'raw_data'          => [],
        ];
    }
}
