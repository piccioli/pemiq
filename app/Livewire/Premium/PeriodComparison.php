<?php

namespace App\Livewire\Premium;

use App\Models\Activity;
use App\Services\Dashboard\TrendAnalysisService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class PeriodComparison extends Component
{
    public string $fromA = '';
    public string $toA   = '';
    public string $fromB = '';
    public string $toB   = '';
    public string $sportType   = '';
    public string $granularity = 'month';
    public string $chartType   = 'line';

    public function mount(): void
    {
        $now = Carbon::now();
        $this->fromA = $now->copy()->subMonths(6)->format('Y-m-d');
        $this->toA   = $now->copy()->subMonths(3)->format('Y-m-d');
        $this->fromB = $now->copy()->subMonths(3)->format('Y-m-d');
        $this->toB   = $now->format('Y-m-d');
    }

    public function compare(): void
    {
        // Triggers re-render with current model values
    }

    public function setGranularity(string $granularity): void
    {
        $this->granularity = in_array($granularity, ['month', 'week']) ? $granularity : 'month';
    }

    public function setChartType(string $type): void
    {
        $this->chartType = in_array($type, ['line', 'bar']) ? $type : 'line';
    }

    public function render(TrendAnalysisService $service): View
    {
        $user        = auth()->user();
        $sportFilter = $this->sportType !== '' ? $this->sportType : null;

        [$fromA, $toA] = $this->parseDateRange($this->fromA, $this->toA, 6, 3);
        [$fromB, $toB] = $this->parseDateRange($this->fromB, $this->toB, 3, 0);

        $statsA = $service->periodStats($user, $sportFilter, $fromA, $toA);
        $statsB = $service->periodStats($user, $sportFilter, $fromB, $toB);

        $sportTypes = Activity::where('user_id', $user->id)
            ->distinct()
            ->orderBy('sport_type')
            ->pluck('sport_type')
            ->filter()
            ->values()
            ->toArray();

        if ($this->granularity === 'week') {
            $dataA = $service->weeklyVolume($user, $sportFilter, $fromA, $toA);
            $dataB = $service->weeklyVolume($user, $sportFilter, $fromB, $toB);
        } else {
            $dataA = $service->monthlyVolume($user, $sportFilter, $fromA, $toA);
            $dataB = $service->monthlyVolume($user, $sportFilter, $fromB, $toB);
        }

        $chartInitData = $this->buildChartData($dataA, $dataB);
        $this->dispatch('period-chart-data', data: $chartInitData);

        return view('livewire.premium.period-comparison', [
            'statsA'        => $statsA,
            'statsB'        => $statsB,
            'kpis'          => $this->buildKpis($statsA, $statsB),
            'sportTypes'    => $sportTypes,
            'periodALabel'  => $fromA->format('d/m/Y') . ' – ' . $toA->format('d/m/Y'),
            'periodBLabel'  => $fromB->format('d/m/Y') . ' – ' . $toB->format('d/m/Y'),
            'chartInitData' => $chartInitData,
        ]);
    }

    private function buildChartData(Collection $dataA, Collection $dataB): array
    {
        $dataA    = $dataA->values();
        $dataB    = $dataB->values();
        $countA   = $dataA->count();
        $countB   = $dataB->count();
        $maxCount = max($countA, $countB, 1);

        if ($this->granularity === 'week') {
            $weekShort  = __('messages.compare_week_short');
            $categories = array_map(
                fn ($i) => $weekShort . ' ' . ($i + 1),
                range(0, $maxCount - 1)
            );
        } else {
            /** @var array<int,string> $monthsShort */
            $monthsShort = trans('messages.months_short');
            $categories  = [];
            for ($i = 0; $i < $maxCount; $i++) {
                if ($i < $countB) {
                    $month        = (int) substr($dataB[$i]->period, 5, 2);
                    $categories[] = $monthsShort[$month - 1] ?? ('M' . $month);
                } elseif ($i < $countA) {
                    $month        = (int) substr($dataA[$i]->period, 5, 2);
                    $categories[] = $monthsShort[$month - 1] ?? ('M' . $month);
                } else {
                    $categories[] = 'M' . ($i + 1);
                }
            }
        }

        $seriesA = $dataA->pluck('distance_km')->toArray();
        $seriesB = $dataB->pluck('distance_km')->toArray();

        while (count($seriesA) < $maxCount) {
            $seriesA[] = null;
        }
        while (count($seriesB) < $maxCount) {
            $seriesB[] = null;
        }

        $titleKey = $this->granularity === 'week'
            ? 'messages.compare_chart_title_week'
            : 'messages.compare_chart_title_month';

        return [
            'categories'    => $categories,
            'seriesA'       => array_slice($seriesA, 0, $maxCount),
            'seriesB'       => array_slice($seriesB, 0, $maxCount),
            'periodALabel'  => __('messages.compare_period_a'),
            'periodBLabel'  => __('messages.compare_period_b'),
            'chartType'     => $this->chartType,
            'title'         => __($titleKey),
            'noDataText'    => __('messages.compare_no_chart_data'),
        ];
    }

    private function parseDateRange(string $from, string $to, int $defaultSubFrom, int $defaultSubTo): array
    {
        $now      = Carbon::now();
        $fromDate = $from !== ''
            ? Carbon::parse($from)->startOfDay()
            : $now->copy()->subMonths($defaultSubFrom)->startOfDay();
        $toDate   = $to !== ''
            ? Carbon::parse($to)->endOfDay()
            : ($defaultSubTo === 0
                ? $now->copy()->endOfDay()
                : $now->copy()->subMonths($defaultSubTo)->endOfDay());

        return [$fromDate, $toDate];
    }

    private function buildKpis(\stdClass $a, \stdClass $b): array
    {
        return [
            [
                'label'    => __('messages.compare_kpi_distance'),
                'valueA'   => $a->distance_km,
                'valueB'   => $b->distance_km,
                'unit'     => 'km',
                'decimals' => 1,
            ],
            [
                'label'    => __('messages.compare_kpi_elevation'),
                'valueA'   => $a->elevation_m,
                'valueB'   => $b->elevation_m,
                'unit'     => 'm',
                'decimals' => 0,
            ],
            [
                'label'    => __('messages.compare_kpi_time'),
                'valueA'   => $a->hours,
                'valueB'   => $b->hours,
                'unit'     => 'h',
                'decimals' => 1,
            ],
            [
                'label'    => __('messages.compare_kpi_activities'),
                'valueA'   => (float) $a->activities,
                'valueB'   => (float) $b->activities,
                'unit'     => '',
                'decimals' => 0,
            ],
        ];
    }
}
