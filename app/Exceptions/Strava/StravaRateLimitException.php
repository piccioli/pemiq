<?php

namespace App\Exceptions\Strava;

class StravaRateLimitException extends StravaApiException
{
    public function __construct(string $message = 'Strava rate limit exceeded')
    {
        parent::__construct($message);
    }
}
