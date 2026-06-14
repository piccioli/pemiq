<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class AnnualAnalysis extends Component
{
    public function render(DashboardStatsService $service): View
    {
        $annualStats = $service->getAnnualStats(auth()->user());

        return view('livewire.dashboard.annual-analysis', [
            'annualStats' => $annualStats,
        ]);
    }
}
