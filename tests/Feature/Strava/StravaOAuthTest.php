<?php

namespace Tests\Feature\Strava;

use App\Models\StravaAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StravaOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'user', 'guard_name' => 'web']);
        Role::create(['name' => 'administrator', 'guard_name' => 'web']);
    }

    public function test_redirect_to_strava_oauth(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('scopes')->andReturnSelf();
        $provider->shouldReceive('redirect')->andReturn(redirect('https://www.strava.com/oauth/authorize'));

        Socialite::shouldReceive('driver')->with('strava')->andReturn($provider);

        $response = $this->actingAs($user)->get('/strava/redirect');
        $response->assertRedirect();
    }

    public function test_callback_creates_strava_account(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $socialiteUser = Mockery::mock('Laravel\Socialite\Contracts\User');
        $socialiteUser->shouldReceive('getId')->andReturn('987654321');
        $socialiteUser->token = 'fake-access-token';
        $socialiteUser->refreshToken = 'fake-refresh-token';
        $socialiteUser->expiresIn = 21600;

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('strava')->andReturn($provider);

        $response = $this->actingAs($user)->get('/strava/callback');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('strava_accounts', [
            'user_id'             => $user->id,
            'strava_athlete_id'   => 987654321,
            'connection_status'   => 'connected',
        ]);
    }

    public function test_callback_with_denied_permissions_redirects_with_error(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/strava/callback?error=access_denied');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('strava_accounts', ['user_id' => $user->id]);
    }

    public function test_tokens_are_stored_encrypted(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $socialiteUser = Mockery::mock('Laravel\Socialite\Contracts\User');
        $socialiteUser->shouldReceive('getId')->andReturn('111222333');
        $socialiteUser->token = 'plain-access-token';
        $socialiteUser->refreshToken = 'plain-refresh-token';
        $socialiteUser->expiresIn = 21600;

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('strava')->andReturn($provider);

        $this->actingAs($user)->get('/strava/callback');

        $account = StravaAccount::where('user_id', $user->id)->first();
        $this->assertNotNull($account);

        $rawAccessToken = \Illuminate\Support\Facades\DB::table('strava_accounts')
            ->where('id', $account->id)
            ->value('access_token');

        $this->assertNotEquals('plain-access-token', $rawAccessToken);
        $this->assertEquals('plain-access-token', $account->access_token);
    }

    public function test_disconnect_removes_strava_connection(): void
    {
        Http::fake([
            'www.strava.com/oauth/deauthorize' => Http::response(['deauthorized' => true], 200),
        ]);

        $user = User::factory()->create(['email_verified_at' => now()]);

        StravaAccount::create([
            'user_id'           => $user->id,
            'strava_athlete_id' => 111222333,
            'access_token'      => 'token',
            'refresh_token'     => 'refresh',
            'token_expires_at'  => now()->addHour(),
            'connection_status' => 'connected',
        ]);

        $response = $this->actingAs($user)->delete('/strava/disconnect');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('strava_accounts', [
            'user_id'           => $user->id,
            'connection_status' => 'disconnected',
        ]);
    }
}
