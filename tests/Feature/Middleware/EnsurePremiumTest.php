<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsurePremiumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['auth', 'premium'])->get('/test-premium-route', function () {
            return response('OK', 200);
        });
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/test-premium-route');

        $response->assertRedirect('/login');
    }

    public function test_non_premium_user_is_redirected_to_dashboard_with_flash(): void
    {
        $user = User::factory()->create(['is_premium' => false]);

        $response = $this->actingAs($user)->get('/test-premium-route');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Funzionalità riservata agli utenti Premium.');
    }

    public function test_premium_user_can_access_protected_route(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
            'premium_expires_at' => now()->addYear(),
        ]);

        $response = $this->actingAs($user)->get('/test-premium-route');

        $response->assertStatus(200);
        $response->assertSee('OK');
    }
}
