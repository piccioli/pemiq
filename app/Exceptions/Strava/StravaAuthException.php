<?php

namespace App\Exceptions\Strava;

use Exception;

class StravaAuthException extends Exception
{
    public function __construct(string $message = 'Strava account authorization is invalid and must be re-connected')
    {
        parent::__construct($message);
    }
}
