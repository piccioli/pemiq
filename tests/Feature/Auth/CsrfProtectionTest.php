<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
    }

    protected function tearDown(): void
    {
        // Restore the testing environment after each test
        $this->app->instance('env', 'testing');
        parent::tearDown();
    }

    private function disableCsrfBypass(): void
    {
        // Override env so runningUnitTests() returns false,
        // forcing the CSRF middleware to actually verify the token.
        $this->app->instance('env', 'production');
    }

    public function test_post_register_without_csrf_token_returns_419(): void
    {
        $this->disableCsrfBypass();

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'csrf@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(419);
    }

    public function test_post_login_without_csrf_token_returns_419(): void
    {
        $this->disableCsrfBypass();

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(419);
    }

    public function test_all_post_forms_have_csrf_directive(): void
    {
        $viewsWithForms = [
            'auth/register.blade.php',
            'auth/login.blade.php',
            'auth/forgot-password.blade.php',
            'auth/reset-password.blade.php',
            'auth/verify-email.blade.php',
            'profile/show.blade.php',
            'profile/password.blade.php',
            'livewire/strava/connection-status.blade.php',
            'partials/impersonation-banner.blade.php',
        ];

        foreach ($viewsWithForms as $viewPath) {
            $fullPath = resource_path("views/{$viewPath}");
            $this->assertFileExists($fullPath, "View file not found: {$viewPath}");
            $content = file_get_contents($fullPath);
            $this->assertStringContainsString('@csrf', $content, "Missing @csrf in: {$viewPath}");
        }
    }
}
