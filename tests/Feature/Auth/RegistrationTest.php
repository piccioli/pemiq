<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\Dashboard\DashboardStatsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);
    }

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_registration_creates_user_and_fires_event(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        Event::assertDispatched(Registered::class);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->post('/register', [
            'name'                  => '',
            'email'                 => 'not-an-email',
            'password'              => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_dashboard_is_blocked_before_email_verification(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->mock(DashboardStatsService::class, function ($mock) {
            $mock->shouldReceive('getOverviewStats')->andReturn([
                'total_activities' => 0, 'total_distance_km' => 0,
                'total_elevation_m' => 0, 'total_elapsed_seconds' => 0,
                'total_moving_seconds' => 0,
            ]);
            $mock->shouldReceive('getAnnualStats')->andReturn(collect());
            $mock->shouldReceive('getMonthlyStats')->andReturn(collect());
            $mock->shouldReceive('getSportDistribution')->andReturn(collect());
        });

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'New User',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
