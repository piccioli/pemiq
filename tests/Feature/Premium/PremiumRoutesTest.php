<?php

namespace Tests\Feature\Premium;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/premium/trends');

        $response->assertRedirect('/login');
    }

    public function test_free_user_is_redirected_to_dashboard_with_error_flash(): void
    {
        $user = User::factory()->create(['is_premium' => false]);

        $response = $this->actingAs($user)->get('/premium/trends');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Funzionalità riservata agli utenti Premium.');
    }

    public function test_premium_user_can_access_trends(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($user)->get('/premium/trends');

        $response->assertStatus(200);
    }

    public function test_premium_user_can_access_compare(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($user)->get('/premium/compare');

        $response->assertStatus(200);
    }

    public function test_premium_user_can_access_year_over_year(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($user)->get('/premium/year-over-year');

        $response->assertStatus(200);
    }

    public function test_free_user_redirected_from_compare(): void
    {
        $user = User::factory()->create(['is_premium' => false]);

        $response = $this->actingAs($user)->get('/premium/compare');

        $response->assertRedirect('/dashboard');
    }

    public function test_free_user_redirected_from_year_over_year(): void
    {
        $user = User::factory()->create(['is_premium' => false]);

        $response = $this->actingAs($user)->get('/premium/year-over-year');

        $response->assertRedirect('/dashboard');
    }
}
