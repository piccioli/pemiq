<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardStatsService;
use Illuminate\View\View;
use Livewire\Component;

class OverviewStats extends Component
{
    public function render(DashboardStatsService $service): View
    {
        $stats = $service->getOverviewStats(auth()->user());

        return view('livewire.dashboard.overview-stats', [
            'stats' => $stats,
            'totalTime' => $this->formatSeconds($stats['total_elapsed_seconds']),
            'movingTime' => $this->formatSeconds($stats['total_moving_seconds']),
        ]);
    }

    private function formatSeconds(int $seconds): string
    {
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        return $hours . ':' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    }
}
