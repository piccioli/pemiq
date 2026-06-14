<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = auth()->user()
            ->activities()
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        return view('activities.index', compact('activities'));
    }
}
