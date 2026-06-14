<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);
        RateLimiter::clear('test@example.com|127.0.0.1');
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_valid_credentials_redirect_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email'             => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_return_error(): void
    {
        User::factory()->create([
            'email'             => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_error_message_is_generic(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $errors = session('errors');
        $allErrors = implode(' ', $errors->all());
        $this->assertStringNotContainsStringIgnoringCase('email', $allErrors);
    }

    public function test_throttling_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email'             => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $errors = session('errors');
        $this->assertTrue(str_contains(implode(' ', $errors->all()), 'secondi') || str_contains(implode(' ', $errors->all()), 'seconds'));
    }

    public function test_unverified_user_is_redirected_to_verification(): void
    {
        User::factory()->create([
            'email'             => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/email/verify');
        $this->assertGuest();
    }

    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }
}
