<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class AnnualAnalysisChart extends Component
{
    public int $year;
    public string $metric = 'distance';

    public function mount(): void
    {
        $this->year = (int) date('Y');
    }

    public function render(DashboardStatsService $service): View
    {
        $user = auth()->user();
        $annualStats = $service->getAnnualStats($user);
        $availableYears = $annualStats->pluck('year')->sort()->values()->toArray();

        if (!empty($availableYears) && !in_array($this->year, $availableYears)) {
            $this->year = (int) max($availableYears);
        }

        $monthlyStats = $service->getMonthlyStats($user, $this->year);
        $months = trans('messages.months_short');

        $seriesData = $monthlyStats->map(fn ($row) =>
            $this->metric === 'distance' ? (float) $row->distance_km : (float) $row->hours
        )->values()->toArray();

        $chartData = [
            'categories' => $months,
            'seriesName' => $this->metric === 'distance' ? trans('messages.stat_distance_km') : trans('messages.col_hours'),
            'seriesData' => $seriesData,
            'yAxisTitle' => $this->metric === 'distance' ? 'km' : 'h',
        ];

        $this->dispatch('annual-chart-data', data: $chartData);

        return view('livewire.dashboard.annual-analysis-chart', [
            'availableYears' => $availableYears,
            'chartData' => $chartData,
            'hasData' => !empty($availableYears),
        ]);
    }
}
