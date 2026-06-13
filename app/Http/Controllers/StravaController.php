<?php

namespace App\Http\Controllers;

use App\Jobs\SyncStravaHistoricalActivities;
use App\Models\StravaAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
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

    public function syncHistorical(): RedirectResponse
    {
        $user = auth()->user();
        $stravaAccount = $user->stravaAccount;

        if (! $stravaAccount || $stravaAccount->connection_status !== 'connected') {
            return redirect()->route('dashboard')
                ->with('error', 'Nessun account Strava collegato.');
        }

        SyncStravaHistoricalActivities::dispatch($stravaAccount);

        return redirect()->route('dashboard')
            ->with('success', 'Sincronizzazione storica avviata. Le attività saranno importate a breve.');
    }

    public function disconnect(): RedirectResponse
    {
        $user = auth()->user();
        $stravaAccount = $user->stravaAccount;

        if (!$stravaAccount || $stravaAccount->connection_status !== 'connected') {
            return redirect()->route('dashboard')
                ->with('error', 'Nessun account Strava collegato.');
        }

        try {
            Http::post('https://www.strava.com/oauth/deauthorize', [
                'access_token' => $stravaAccount->access_token,
            ]);
        } catch (\Exception $e) {
            // Continue with local disconnect even if Strava API call fails
        }

        $stravaAccount->update([
            'connection_status' => 'disconnected',
            'access_token' => null,
            'refresh_token' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Account Strava scollegato.');
    }
}
