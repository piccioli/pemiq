<?php

namespace App\Exceptions\Strava;

use Exception;

class StravaTokenRefreshException extends Exception
{
    public function __construct(string $message = 'Failed to refresh Strava access token')
    {
        parent::__construct($message);
    }
}
