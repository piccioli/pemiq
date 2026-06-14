<?php

namespace App\Livewire\Premium;

use App\Models\Activity;
use App\Services\Dashboard\TrendAnalysisService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class YearOverYearChart extends Component
{
    public int    $yearA     = 0;
    public int    $yearB     = 0;
    public string $sportType = '';

    public function mount(): void
    {
        $this->yearA = (int) Carbon::now()->format('Y');
        $this->yearB = $this->yearA - 1;
    }

    public function render(TrendAnalysisService $service): View
    {
        $user        = auth()->user();
        $sportFilter = $this->sportType !== '' ? $this->sportType : null;

        $sportTypes = Activity::where('user_id', $user->id)
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type')
            ->filter()
            ->values()
            ->toArray();

        $currentYear = (int) Carbon::now()->format('Y');
        $availableYears = [];
        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
            $availableYears[] = $y;
        }

        $fromA = Carbon::create($this->yearA, 1, 1)->startOfDay();
        $toA   = Carbon::create($this->yearA, 12, 31)->endOfDay();
        $fromB = Carbon::create($this->yearB, 1, 1)->startOfDay();
        $toB   = Carbon::create($this->yearB, 12, 31)->endOfDay();

        $dataA = $service->monthlyVolume($user, $sportFilter, $fromA, $toA);
        $dataB = $service->monthlyVolume($user, $sportFilter, $fromB, $toB);

        $seriesA = array_fill(0, 12, null);
        foreach ($dataA as $row) {
            $month = (int) substr($row->period, 5, 2);
            $seriesA[$month - 1] = (float) $row->distance_km;
        }

        $seriesB = array_fill(0, 12, null);
        foreach ($dataB as $row) {
            $month = (int) substr($row->period, 5, 2);
            $seriesB[$month - 1] = (float) $row->distance_km;
        }

        /** @var array<int,string> $monthsShort */
        $monthsShort = trans('messages.months_short');
        $categories  = array_values($monthsShort);

        $chartData = [
            'categories' => $categories,
            'seriesA'    => $seriesA,
            'seriesB'    => $seriesB,
            'labelA'     => (string) $this->yearA,
            'labelB'     => (string) $this->yearB,
            'title'      => __('messages.yoy_chart_title'),
            'noDataText' => __('messages.yoy_no_data'),
        ];

        $this->dispatch('yoy-chart-data', data: $chartData);

        $hasData = !empty(array_filter($seriesA, fn ($v) => $v !== null))
            || !empty(array_filter($seriesB, fn ($v) => $v !== null));

        return view('livewire.premium.year-over-year-chart', [
            'sportTypes'     => $sportTypes,
            'availableYears' => $availableYears,
            'chartData'      => $chartData,
            'hasData'        => $hasData,
        ]);
    }
}
