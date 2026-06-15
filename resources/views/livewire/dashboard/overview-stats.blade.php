<div>
    @if ($stats['total_activities'] === 0)
        <x-card padding="md">
            <p style="color: var(--text-muted); font: var(--text-body)">
                {{ __('messages.dashboard_no_activities') }}
            </p>
        </x-card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

            <x-metric-tile
                label="{{ __('messages.stat_total_activities') }}"
                value="{{ fmt_number($stats['total_activities']) }}"
                accent="zone-5"
            >
                <x-slot:icon><i data-lucide="activity"></i></x-slot:icon>
            </x-metric-tile>

            <x-metric-tile
                label="{{ __('messages.stat_distance_km') }}"
                value="{{ fmt_number($stats['total_distance_km'], 1) }}"
                unit="km"
                accent="zone-2"
            >
                <x-slot:icon><i data-lucide="route"></i></x-slot:icon>
            </x-metric-tile>

            <x-metric-tile
                label="{{ __('messages.stat_elevation_m') }}"
                value="{{ fmt_number($stats['total_elevation_m'], 0) }}"
                unit="m"
                accent="zone-3"
            >
                <x-slot:icon><i data-lucide="mountain-snow"></i></x-slot:icon>
            </x-metric-tile>

            <x-metric-tile
                label="{{ __('messages.stat_total_time') }}"
                value="{{ $totalTime }}"
                accent="brand"
            >
                <x-slot:icon><i data-lucide="clock"></i></x-slot:icon>
            </x-metric-tile>

            <x-metric-tile
                label="{{ __('messages.stat_moving_time') }}"
                value="{{ $movingTime }}"
                accent="zone-4"
            >
                <x-slot:icon><i data-lucide="zap"></i></x-slot:icon>
            </x-metric-tile>

        </div>
    @endif
</div>
