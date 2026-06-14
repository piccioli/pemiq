<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PremiumController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        $isPremium = $user->is_premium
            && ($user->premium_expires_at === null || $user->premium_expires_at->isFuture());

        if ($isPremium) {
            return redirect()->route('dashboard')
                ->with('success', __('messages.premium_already_premium'));
        }

        return view('premium.index');
    }
}
