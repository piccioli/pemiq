<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremium
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            ! $user ||
            ! $user->is_premium ||
            ($user->premium_expires_at !== null && $user->premium_expires_at->isPast())
        ) {
            return redirect()->route('dashboard')
                ->with('error', 'Funzionalità riservata agli utenti Premium.');
        }

        return $next($request);
    }
}
