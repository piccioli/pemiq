<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);

        $this->admin = User::factory()->create(['email_verified_at' => now()]);
        $this->admin->assignRole('administrator');

        $this->regularUser = User::factory()->create(['email_verified_at' => now()]);
        $this->regularUser->assignRole('user');
    }

    public function test_admin_can_impersonate_non_admin_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/impersonate/start/{$this->regularUser->id}");

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->regularUser);
        $response->assertSessionHas('impersonating_admin_id', $this->admin->id);
    }

    public function test_impersonation_banner_is_visible_after_impersonation(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->withSession(['impersonating_admin_id' => $this->admin->id])
            ->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Stai impersonando');
    }

    public function test_user_with_role_user_cannot_impersonate(): void
    {
        $anotherUser = User::factory()->create(['email_verified_at' => now()]);
        $anotherUser->assignRole('user');

        $response = $this->actingAs($this->regularUser)
            ->post("/impersonate/start/{$anotherUser->id}");

        $response->assertStatus(403);
    }

    public function test_stop_impersonation_restores_admin_session(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->withSession(['impersonating_admin_id' => $this->admin->id])
            ->post('/impersonate/stop');

        $response->assertRedirect('/admin/users');
        $this->assertAuthenticatedAs($this->admin);
        $response->assertSessionMissing('impersonating_admin_id');
    }

    public function test_impersonation_start_creates_audit_log(): void
    {
        $this->actingAs($this->admin)
            ->post("/impersonate/start/{$this->regularUser->id}");

        $this->assertDatabaseHas('audit_logs', [
            'admin_user_id'  => $this->admin->id,
            'target_user_id' => $this->regularUser->id,
            'action'         => 'impersonate_start',
        ]);
    }

    public function test_stop_impersonation_creates_audit_log(): void
    {
        $this->actingAs($this->regularUser)
            ->withSession(['impersonating_admin_id' => $this->admin->id])
            ->post('/impersonate/stop');

        $this->assertDatabaseHas('audit_logs', [
            'admin_user_id'  => $this->admin->id,
            'target_user_id' => $this->regularUser->id,
            'action'         => 'impersonate_stop',
        ]);
    }
}
