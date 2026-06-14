<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LargeDatasetSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'testdata@pemiq.com'],
            [
                'name' => 'Test Dataset User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Clear existing activities to avoid unique constraint conflicts on re-seed
        Activity::where('user_id', $user->id)->delete();

        $this->command->info('Generating 10,000 activities for testdata@pemiq.com...');

        // Create in batches of 500 using sequential strava_activity_id to guarantee uniqueness
        $batchSize = 500;
        $total = 10_000;
        $batches = intdiv($total, $batchSize);
        $baseId = 500_000_000;

        for ($batch = 0; $batch < $batches; $batch++) {
            $offset = $batch * $batchSize;

            Activity::factory()
                ->count($batchSize)
                ->sequence(fn ($seq) => [
                    'strava_activity_id' => $baseId + $offset + $seq->index,
                ])
                ->create(['user_id' => $user->id]);

            $created = $offset + $batchSize;
            $this->command->info("  {$created}/{$total} activities created...");
        }

        $this->command->info("Done! Created {$total} activities for testdata@pemiq.com (id: {$user->id})");
    }
}
