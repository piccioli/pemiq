<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class SportDistribution extends Component
{
    public function render(DashboardStatsService $service): View
    {
        $sportStats = $service->getSportDistribution(auth()->user());

        return view('livewire.dashboard.sport-distribution', [
            'sportStats' => $sportStats,
        ]);
    }
}
