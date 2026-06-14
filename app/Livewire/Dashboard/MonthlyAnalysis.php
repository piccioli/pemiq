<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class MonthlyAnalysis extends Component
{
    public int $year;

    public function mount(): void
    {
        $this->year = (int) date('Y');
    }

    public function render(DashboardStatsService $service): View
    {
        $user = auth()->user();
        $annualStats = $service->getAnnualStats($user);
        $availableYears = $annualStats->pluck('year')->map(fn ($y) => (int) $y)->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        if (! in_array($this->year, $availableYears)) {
            $this->year = $availableYears[0];
        }

        $monthlyStats = $service->getMonthlyStats($user, $this->year);

        return view('livewire.dashboard.monthly-analysis', [
            'monthlyStats'   => $monthlyStats,
            'availableYears' => $availableYears,
        ]);
    }
}
