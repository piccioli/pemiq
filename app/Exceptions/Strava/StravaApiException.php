<?php

namespace App\Exceptions\Strava;

use Exception;

class StravaApiException extends Exception
{
    public function __construct(string $message = 'Strava API error')
    {
        parent::__construct($message);
    }
}
