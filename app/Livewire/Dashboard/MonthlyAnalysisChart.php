<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class MonthlyAnalysisChart extends Component
{
    public int $year;
    public int $month;
    public string $metric = 'distance';

    public function mount(): void
    {
        $this->year  = (int) date('Y');
        $this->month = (int) date('n');
    }

    public function render(DashboardStatsService $service): View
    {
        $user        = auth()->user();
        $annualStats = $service->getAnnualStats($user);
        $availableYears = $annualStats->pluck('year')->sort()->values()->toArray();

        if (!empty($availableYears) && !in_array($this->year, $availableYears)) {
            $this->year = (int) max($availableYears);
        }

        $dailyStats = $service->getDailyStats($user, $this->year, $this->month);

        $categories = $dailyStats->pluck('day')->map(fn ($d) => (string) $d)->values()->toArray();
        $seriesData = $dailyStats->map(fn ($row) =>
            $this->metric === 'distance' ? (float) $row->distance_km : (float) $row->hours
        )->values()->toArray();

        $monthNames = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre',
        ];

        $chartData = [
            'categories' => $categories,
            'seriesName' => $this->metric === 'distance' ? 'Distanza (km)' : 'Ore',
            'seriesData' => $seriesData,
            'yAxisTitle' => $this->metric === 'distance' ? 'km' : 'h',
        ];

        $this->dispatch('monthly-chart-data', data: $chartData);

        $hasData = !empty($availableYears);

        return view('livewire.dashboard.monthly-analysis-chart', [
            'availableYears' => $availableYears,
            'monthNames'     => $monthNames,
            'chartData'      => $chartData,
            'hasData'        => $hasData,
        ]);
    }
}
