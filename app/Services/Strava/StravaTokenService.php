<?php

namespace App\Services\Strava;

use App\Exceptions\Strava\StravaTokenRefreshException;
use App\Models\StravaAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class StravaTokenService
{
    private const TOKEN_URL = 'https://www.strava.com/oauth/token';
    private const EXPIRY_BUFFER_MINUTES = 5;

    /**
     * Ensure the StravaAccount has a valid (non-expired) token, refreshing if necessary.
     *
     * @throws StravaTokenRefreshException
     */
    public function ensureValidToken(StravaAccount $account): StravaAccount
    {
        if (! $this->needsRefresh($account)) {
            return $account;
        }

        return $this->refreshToken($account);
    }

    private function needsRefresh(StravaAccount $account): bool
    {
        if ($account->token_expires_at === null) {
            return true;
        }

        return $account->token_expires_at->lte(Carbon::now()->addMinutes(self::EXPIRY_BUFFER_MINUTES));
    }

    /**
     * @throws StravaTokenRefreshException
     */
    private function refreshToken(StravaAccount $account): StravaAccount
    {
        try {
            $response = Http::post(self::TOKEN_URL, [
                'client_id' => config('services.strava.client_id'),
                'client_secret' => config('services.strava.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $account->refresh_token,
            ]);
        } catch (\Exception $e) {
            $account->update(['connection_status' => 'error']);
            throw new StravaTokenRefreshException('Network error during token refresh: ' . $e->getMessage());
        }

        if ($response->failed()) {
            $account->update(['connection_status' => 'error']);
            throw new StravaTokenRefreshException('Strava token refresh failed: HTTP ' . $response->status());
        }

        $data = $response->json();

        $account->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_expires_at' => Carbon::createFromTimestamp($data['expires_at']),
        ]);

        $account->refresh();

        return $account;
    }
}
