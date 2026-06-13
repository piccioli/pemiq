<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stravaAccount = auth()->user()->stravaAccount;

        return view('dashboard', compact('stravaAccount'));
    }
}
