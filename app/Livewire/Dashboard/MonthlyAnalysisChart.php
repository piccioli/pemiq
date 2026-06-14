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

        $monthNames = collect(trans('messages.months'))->mapWithKeys(fn ($name, $i) => [$i + 1 => $name])->toArray();

        $chartData = [
            'categories' => $categories,
            'seriesName' => $this->metric === 'distance' ? trans('messages.stat_distance_km') : trans('messages.col_hours'),
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
