<?php

namespace App\Exceptions\Strava;

use Exception;

class StravaTokenExpiredException extends Exception
{
    public function __construct(string $message = 'Strava access token has expired')
    {
        parent::__construct($message);
    }
}
