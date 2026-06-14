<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class SportDistributionChart extends Component
{
    private array $sportColors = [
        'Run'              => '#F97316',
        'TrailRun'         => '#22C55E',
        'Ride'             => '#3B82F6',
        'GravelRide'       => '#14B8A6',
        'MountainBikeRide' => '#EAB308',
        'VirtualRide'      => '#0EA5E9',
        'VirtualRun'       => '#F59E0B',
        'Hike'             => '#84CC16',
        'Walk'             => '#A855F7',
        'Swim'             => '#06B6D4',
        'Workout'          => '#9CA3AF',
    ];

    public function render(DashboardStatsService $service): View
    {
        $sportStats = $service->getSportDistribution(auth()->user());

        $chartData = [
            'labels'    => $sportStats->pluck('sport_type')->toArray(),
            'series'    => $sportStats->pluck('activities')->map(fn ($v) => (int) $v)->toArray(),
            'distances' => $sportStats->pluck('distance_km')->map(fn ($v) => round((float) $v, 1))->toArray(),
            'colors'    => $sportStats->pluck('sport_type')->map(fn ($s) => $this->sportColors[$s] ?? '#9CA3AF')->toArray(),
        ];

        $this->dispatch('sport-distribution-chart-data', data: $chartData);

        return view('livewire.dashboard.sport-distribution-chart', [
            'chartData' => $chartData,
            'hasData'   => $sportStats->isNotEmpty(),
        ]);
    }
}
