<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => "Troppi tentativi di accesso. Riprova tra {$seconds} secondi."]);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], (bool) $request->remember)) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Credenziali non valide.']);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'Devi verificare la tua email prima di accedere.']);
        }

        return redirect()->intended(route('dashboard'));
    }
}
