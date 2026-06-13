<?php

namespace App\Services\Strava;

use App\Exceptions\Strava\StravaApiException;
use App\Exceptions\Strava\StravaTokenExpiredException;
use App\Models\StravaAccount;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class StravaApiService
{
    private const ACTIVITIES_URL = 'https://www.strava.com/api/v3/athlete/activities';
    private const PER_PAGE = 100;

    /**
     * Fetch all activities from Strava with pagination and rate-limit handling.
     *
     * @throws StravaTokenExpiredException on HTTP 401
     * @throws StravaApiException on HTTP 5xx or network errors
     */
    public function fetchActivities(StravaAccount $account, ?Carbon $after = null): Collection
    {
        $activities = collect();
        $page = 1;

        while (true) {
            $params = [
                'per_page' => self::PER_PAGE,
                'page' => $page,
            ];

            if ($after !== null) {
                $params['after'] = $after->timestamp;
            }

            $response = $this->requestWithRetry($account, $params);

            $data = $response->json();

            if (! is_array($data) || empty($data)) {
                break;
            }

            $activities = $activities->concat($data);

            if (count($data) < self::PER_PAGE) {
                break;
            }

            $page++;

            // Rate-limit delay between pages
            sleep(1);
        }

        return $activities;
    }

    /**
     * Perform a GET request with exponential backoff on HTTP 429.
     *
     * @throws StravaTokenExpiredException
     * @throws StravaApiException
     */
    private function requestWithRetry(StravaAccount $account, array $params): \Illuminate\Http\Client\Response
    {
        $delays = [1, 2, 4]; // seconds for retry attempts after 429
        $attempt = 0;

        while (true) {
            try {
                $response = Http::withToken($account->access_token)
                    ->get(self::ACTIVITIES_URL, $params);
            } catch (\Exception $e) {
                throw new StravaApiException('Network error: ' . $e->getMessage());
            }

            if ($response->status() === 401) {
                throw new StravaTokenExpiredException();
            }

            if ($response->status() === 429) {
                if ($attempt >= count($delays)) {
                    throw new StravaApiException('Strava rate limit exceeded after retries');
                }
                sleep($delays[$attempt]);
                $attempt++;
                continue;
            }

            if ($response->serverError()) {
                throw new StravaApiException('Strava server error: HTTP ' . $response->status());
            }

            if ($response->failed()) {
                throw new StravaApiException('Strava API error: HTTP ' . $response->status());
            }

            return $response;
        }
    }
}
