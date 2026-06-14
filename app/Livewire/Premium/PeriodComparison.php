<?php

namespace App\Livewire\Premium;

use App\Models\Activity;
use App\Services\Dashboard\TrendAnalysisService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class PeriodComparison extends Component
{
    public string $fromA = '';
    public string $toA   = '';
    public string $fromB = '';
    public string $toB   = '';
    public string $sportType = '';

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

        return view('livewire.premium.period-comparison', [
            'statsA'       => $statsA,
            'statsB'       => $statsB,
            'kpis'         => $this->buildKpis($statsA, $statsB),
            'sportTypes'   => $sportTypes,
            'periodALabel' => $fromA->format('d/m/Y') . ' – ' . $toA->format('d/m/Y'),
            'periodBLabel' => $fromB->format('d/m/Y') . ' – ' . $toB->format('d/m/Y'),
        ]);
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
