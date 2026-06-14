@extends('layouts.app')

@section('title', $activity->name ?? 'Dettaglio attività')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    @php
        $sportVariants = [
            'Run'                => 'zone3',
            'TrailRun'           => 'zone3',
            'VirtualRun'         => 'zone3',
            'Hike'               => 'zone3',
            'Walk'               => 'zone3',
            'Ride'               => 'zone2',
            'GravelRide'         => 'zone2',
            'MountainBikeRide'   => 'zone2',
            'VirtualRide'        => 'zone2',
            'Swim'               => 'zone1',
            'Workout'            => 'outline',
        ];

        $distanceVal  = $activity->distance !== null ? fmt_number($activity->distance / 1000, 2) : '—';
        $distanceUnit = $activity->distance !== null ? 'km' : null;

        $elapsedSec   = $activity->elapsed_time;
        $durationVal  = $elapsedSec !== null
            ? floor($elapsedSec / 3600) . ':' . str_pad(floor(($elapsedSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
            : '—';

        $movingSec      = $activity->moving_time;
        $movingDuration = $movingSec !== null
            ? floor($movingSec / 3600) . ':' . str_pad(floor(($movingSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
            : '—';

        $elevationVal  = $activity->elevation_gain !== null ? fmt_number($activity->elevation_gain, 0) : '—';
        $elevationUnit = $activity->elevation_gain !== null ? 'm' : null;

        $avgSpeedVal  = $activity->average_speed !== null ? fmt_number($activity->average_speed * 3.6, 1) : '—';
        $avgSpeedUnit = $activity->average_speed !== null ? 'km/h' : null;

        $avgHrVal  = $activity->average_heartrate !== null ? fmt_number($activity->average_heartrate, 0) : null;
        $avgHrUnit = 'bpm';

        $maxHr    = $activity->max_heartrate !== null ? fmt_number($activity->max_heartrate, 0) . ' bpm' : null;
        $avgWatts = $activity->average_watts !== null ? fmt_number($activity->average_watts, 0) . ' W' : null;
        $calories = $activity->calories !== null ? fmt_number($activity->calories, 0) . ' kcal' : null;
    @endphp

    <a href="{{ route('activities.index') }}" style="font-size: var(--fs-sm); color: var(--accent); display: block; margin-bottom: 8px;">&larr; Indietro alla lista</a>
    <x-page-header
        :title="$activity->name ?? 'Attività senza titolo'"
        :subtitle="fmt_date($activity->started_at, 'd M Y H:i')"
    >
        <x-slot:actions>
            <x-badge :variant="$sportVariants[$activity->sport_type] ?? 'outline'">{{ $activity->sport_type }}</x-badge>
            @if ($activity->strava_activity_id)
                <x-button
                    href="https://www.strava.com/activities/{{ $activity->strava_activity_id }}"
                    variant="ghost"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Vedi su Strava
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Tab navigation --}}
    @php
        $tabItems = [
            ['id' => 'panoramica', 'label' => __('messages.tab_overview'), 'icon' => 'layout-dashboard'],
            ['id' => 'mappa',      'label' => __('messages.tab_map'),      'icon' => 'map'],
        ];
    @endphp

    <x-tabs :items="$tabItems" active="panoramica">

        {{-- Panoramica panel --}}
        <div x-show="activeTab === 'panoramica'" class="space-y-6">

            {{-- KPI metriche principali --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

                <x-metric-tile
                    :label="__('messages.metric_distance')"
                    :value="$distanceVal"
                    :unit="$distanceUnit"
                    accent="zone-2"
                >
                    <x-slot:icon>
                        <i data-lucide="route" style="width: 20px; height: 20px;"></i>
                    </x-slot:icon>
                </x-metric-tile>

                <x-metric-tile
                    :label="__('messages.col_duration')"
                    :value="$durationVal"
                    accent="brand"
                >
                    <x-slot:icon>
                        <i data-lucide="clock" style="width: 20px; height: 20px;"></i>
                    </x-slot:icon>
                </x-metric-tile>

                <x-metric-tile
                    :label="__('messages.compare_kpi_elevation')"
                    :value="$elevationVal"
                    :unit="$elevationUnit"
                    accent="zone-3"
                >
                    <x-slot:icon>
                        <i data-lucide="mountain-snow" style="width: 20px; height: 20px;"></i>
                    </x-slot:icon>
                </x-metric-tile>

                <x-metric-tile
                    :label="__('messages.col_avg_speed')"
                    :value="$avgSpeedVal"
                    :unit="$avgSpeedUnit"
                    accent="zone-2"
                >
                    <x-slot:icon>
                        <i data-lucide="gauge" style="width: 20px; height: 20px;"></i>
                    </x-slot:icon>
                </x-metric-tile>

                @if ($avgHrVal !== null)
                <x-metric-tile
                    :label="__('messages.col_avg_hr')"
                    :value="$avgHrVal"
                    :unit="$avgHrUnit"
                    accent="zone-5"
                >
                    <x-slot:icon>
                        <i data-lucide="heart-pulse" style="width: 20px; height: 20px;"></i>
                    </x-slot:icon>
                </x-metric-tile>
                @endif

            </div>

            {{-- Metriche secondarie --}}
            @if ($movingDuration !== '—' || $maxHr !== null || $avgWatts !== null || $calories !== null)
            <x-card padding="sm">
                <h2 style="font-size: var(--fs-sm); font-weight: 600; color: var(--text); margin-bottom: 12px;">{{ __('messages.activity_additional_details') }}</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @if ($movingDuration !== '—')
                    <div>
                        <dt style="font-size: var(--fs-xs); color: var(--text-muted)">{{ __('messages.col_moving_time') }}</dt>
                        <dd style="font-size: var(--fs-sm); font-weight: 500; color: var(--text-strong); margin-top: 2px">{{ $movingDuration }}</dd>
                    </div>
                    @endif
                    @if ($maxHr !== null)
                    <div>
                        <dt style="font-size: var(--fs-xs); color: var(--text-muted)">{{ __('messages.col_max_hr') }}</dt>
                        <dd style="font-size: var(--fs-sm); font-weight: 500; color: var(--text-strong); margin-top: 2px">{{ $maxHr }}</dd>
                    </div>
                    @endif
                    @if ($avgWatts !== null)
                    <div>
                        <dt style="font-size: var(--fs-xs); color: var(--text-muted)">{{ __('messages.col_avg_watts') }}</dt>
                        <dd style="font-size: var(--fs-sm); font-weight: 500; color: var(--text-strong); margin-top: 2px">{{ $avgWatts }}</dd>
                    </div>
                    @endif
                    @if ($calories !== null)
                    <div>
                        <dt style="font-size: var(--fs-xs); color: var(--text-muted)">{{ __('messages.col_calories') }}</dt>
                        <dd style="font-size: var(--fs-sm); font-weight: 500; color: var(--text-strong); margin-top: 2px">{{ $calories }}</dd>
                    </div>
                    @endif
                </dl>
            </x-card>
            @endif

        </div>

        {{-- Mappa panel --}}
        <div x-show="activeTab === 'mappa'">

            @if ($activity->polyline)
            <x-card padding="none">
                <div id="activity-map" style="height: 400px;"></div>
            </x-card>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (!window.L || !window.PolylineDecoder) return;
                    var encoded = @json($activity->polyline);
                    var latlngs = window.PolylineDecoder.decode(encoded);
                    var map = window.L.map('activity-map');
                    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map);
                    var track = window.L.polyline(latlngs, { color: '#16D4B4', weight: 3 }).addTo(map);
                    map.fitBounds(track.getBounds(), { padding: [20, 20] });
                });
                // Invalidate map size when tab becomes visible so Leaflet re-renders correctly
                document.addEventListener('tab-changed', function (e) {
                    if (e.detail.tab === 'mappa' && window.L) {
                        setTimeout(function () {
                            var mapEl = document.getElementById('activity-map');
                            if (mapEl && mapEl._leaflet_id) {
                                window.L.map('activity-map').invalidateSize();
                            }
                        }, 50);
                    }
                });
            </script>
            @else
            <x-card>
                <p style="color: var(--text-muted); font-size: var(--fs-sm); text-align: center; padding: 32px 0">{{ __('messages.map_not_available') }}</p>
            </x-card>
            @endif

        </div>

    </x-tabs>

</div>
@endsection
