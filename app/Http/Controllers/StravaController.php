<?php

namespace App\Http\Controllers;

use App\Models\StravaAccount;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class StravaController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('strava')
            ->scopes(['activity:read_all'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (request()->has('error')) {
            return redirect()->route('dashboard')
                ->with('error', 'Connessione a Strava annullata. Permessi non concessi.');
        }

        $stravaUser = Socialite::driver('strava')->user();

        $user = auth()->user();

        StravaAccount::updateOrCreate(
            ['user_id' => $user->id],
            [
                'strava_athlete_id' => $stravaUser->getId(),
                'access_token' => $stravaUser->token,
                'refresh_token' => $stravaUser->refreshToken,
                'token_expires_at' => now()->addSeconds($stravaUser->expiresIn),
                'connection_status' => 'connected',
            ]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Account Strava collegato con successo!');
    }
}
