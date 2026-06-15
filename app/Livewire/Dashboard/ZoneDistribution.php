<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class ZoneDistribution extends Component
{
    public function render(): \Illuminate\View\View
    {
        // Placeholder mock data — no real zone data in DB yet
        $zoneData = [
            ['zone' => 1, 'pct' => 25.0, 'seconds' => 1800],
            ['zone' => 2, 'pct' => 38.0, 'seconds' => 2736],
            ['zone' => 3, 'pct' => 22.0, 'seconds' => 1584],
            ['zone' => 4, 'pct' => 10.0, 'seconds' => 720],
            ['zone' => 5, 'pct' => 5.0,  'seconds' => 360],
        ];

        return view('livewire.dashboard.zone-distribution', compact('zoneData'));
    }
}
