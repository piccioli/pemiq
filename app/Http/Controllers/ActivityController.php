<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $sport = $request->query('sport');
        $year  = $request->query('year');
        $month = $request->query('month');

        $yearExpr  = $this->yearExpr('started_at');
        $monthExpr = $this->monthExpr('started_at');

        $query = $user->activities()->orderBy('started_at', 'desc');

        if ($sport) {
            $query->where('sport_type', $sport);
        }

        if ($year) {
            $query->whereRaw("{$yearExpr} = ?", [(int) $year]);
        }

        if ($month) {
            $query->whereRaw("{$monthExpr} = ?", [(int) $month]);
        }

        $activities = $query->paginate(20)->withQueryString();

        $sportTypes = Activity::where('user_id', $user->id)
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type')
            ->filter()
            ->values();

        $availableYears = Activity::where('user_id', $user->id)
            ->selectRaw("{$yearExpr} as year")
            ->distinct()
            ->orderByRaw("{$yearExpr} DESC")
            ->pluck('year')
            ->filter()
            ->values();

        return view('activities.index', compact(
            'activities',
            'sportTypes',
            'availableYears',
            'sport',
            'year',
            'month'
        ));
    }

    private function yearExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%Y', {$column}) AS INTEGER)";
        }

        return "YEAR({$column})";
    }

    private function monthExpr(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%m', {$column}) AS INTEGER)";
        }

        return "MONTH({$column})";
    }
}
