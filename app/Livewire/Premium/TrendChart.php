<?php

namespace App\Livewire\Premium;

use App\Models\Activity;
use App\Services\Dashboard\TrendAnalysisService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class TrendChart extends Component
{
    public string $metric = 'distance';
    public string $range = '6months';
    public string $sportType = '';
    public string $customFrom = '';
    public string $customTo = '';

    public function mount(): void
    {
        $this->customFrom = Carbon::now()->subMonths(6)->format('Y-m-d');
        $this->customTo   = Carbon::now()->format('Y-m-d');
    }

    public function render(TrendAnalysisService $service): View
    {
        $user = auth()->user();

        $sportTypes = Activity::where('user_id', $user->id)
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type')
            ->filter()
            ->values()
            ->toArray();

        [$from, $to] = $this->getDateRange();

        $data = $service->monthlyVolume(
            $user,
            $this->sportType !== '' ? $this->sportType : null,
            $from,
            $to
        );

        $categories = $data->pluck('period')->toArray();
        $seriesData = $data->map(fn ($row) =>
            $this->metric === 'distance' ? (float) $row->distance_km : (float) $row->hours
        )->values()->toArray();

        $chartData = [
            'categories' => $categories,
            'seriesName' => $this->metric === 'distance' ? trans('messages.stat_distance_km') : trans('messages.col_hours'),
            'seriesData' => $seriesData,
            'yAxisTitle' => $this->metric === 'distance' ? 'km' : 'h',
        ];

        $this->dispatch('trend-chart-data', data: $chartData);

        return view('livewire.premium.trend-chart', [
            'sportTypes' => $sportTypes,
            'chartData'  => $chartData,
            'hasData'    => !empty($categories),
        ]);
    }

    private function getDateRange(): array
    {
        $to = Carbon::now()->endOfDay();

        return match ($this->range) {
            '3months' => [Carbon::now()->subMonths(3)->startOfDay(), $to],
            '1year'   => [Carbon::now()->subYear()->startOfDay(), $to],
            'custom'  => [
                Carbon::parse($this->customFrom ?: Carbon::now()->subMonths(6)->format('Y-m-d'))->startOfDay(),
                Carbon::parse($this->customTo   ?: Carbon::now()->format('Y-m-d'))->endOfDay(),
            ],
            default   => [Carbon::now()->subMonths(6)->startOfDay(), $to],
        };
    }
}
