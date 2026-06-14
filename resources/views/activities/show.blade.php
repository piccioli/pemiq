@extends('layouts.app')

@section('title', $activity->name ?? 'Dettaglio attività')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('activities.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">&larr; Indietro alla lista</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $activity->name ?? 'Attività senza titolo' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ fmt_date($activity->started_at, 'd M Y H:i') }}
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
                @endphp
                &nbsp;<x-badge :variant="$sportVariants[$activity->sport_type] ?? 'outline'">{{ $activity->sport_type }}</x-badge>
            </p>
        </div>

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
    </div>

    {{-- Metriche principali --}}
    @php
        $distanceKm = $activity->distance !== null
            ? fmt_number($activity->distance / 1000, 2) . ' km'
            : '—';

        $elapsedSec = $activity->elapsed_time;
        $duration = $elapsedSec !== null
            ? floor($elapsedSec / 3600) . ':' . str_pad(floor(($elapsedSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
            : '—';

        $movingSec = $activity->moving_time;
        $movingDuration = $movingSec !== null
            ? floor($movingSec / 3600) . ':' . str_pad(floor(($movingSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
            : '—';

        $elevation = $activity->elevation_gain !== null
            ? fmt_number($activity->elevation_gain, 0) . ' m'
            : '—';

        $avgSpeedKmh = $activity->average_speed !== null
            ? fmt_number($activity->average_speed * 3.6, 1) . ' km/h'
            : '—';

        $avgHr = $activity->average_heartrate !== null
            ? fmt_number($activity->average_heartrate, 0) . ' bpm'
            : null;

        $maxHr = $activity->max_heartrate !== null
            ? fmt_number($activity->max_heartrate, 0) . ' bpm'
            : null;

        $avgWatts = $activity->average_watts !== null
            ? fmt_number($activity->average_watts, 0) . ' W'
            : null;

        $calories = $activity->calories !== null
            ? fmt_number($activity->calories, 0) . ' kcal'
            : null;
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <x-card padding="sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Distanza</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $distanceKm }}</p>
        </x-card>

        <x-card padding="sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Durata</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $duration }}</p>
        </x-card>

        <x-card padding="sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dislivello</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $elevation }}</p>
        </x-card>

        <x-card padding="sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Velocità media</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgSpeedKmh }}</p>
        </x-card>

        @if ($avgHr !== null)
        <x-card padding="sm">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">FC media</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgHr }}</p>
        </x-card>
        @endif
    </div>

    {{-- Metriche secondarie --}}
    @if ($movingDuration !== '—' || $maxHr !== null || $avgWatts !== null || $calories !== null)
    <x-card padding="sm">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Dettagli aggiuntivi</h2>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @if ($movingDuration !== '—')
            <div>
                <dt class="text-xs text-gray-500">Tempo in movimento</dt>
                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $movingDuration }}</dd>
            </div>
            @endif
            @if ($maxHr !== null)
            <div>
                <dt class="text-xs text-gray-500">FC massima</dt>
                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $maxHr }}</dd>
            </div>
            @endif
            @if ($avgWatts !== null)
            <div>
                <dt class="text-xs text-gray-500">Potenza media</dt>
                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $avgWatts }}</dd>
            </div>
            @endif
            @if ($calories !== null)
            <div>
                <dt class="text-xs text-gray-500">Calorie</dt>
                <dd class="text-sm font-medium text-gray-900 mt-0.5">{{ $calories }}</dd>
            </div>
            @endif
        </dl>
    </x-card>
    @endif

    {{-- Mappa percorso --}}
    @if ($activity->polyline)
    <x-card padding="none">
        <h2 class="text-sm font-semibold text-gray-700 px-4 pt-4 pb-2">Percorso</h2>
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
            var track = window.L.polyline(latlngs, { color: '#E85D04', weight: 3 }).addTo(map);
            map.fitBounds(track.getBounds(), { padding: [20, 20] });
        });
    </script>
    @else
    <x-card padding="none" class="flex items-center justify-center" style="height: 400px;">
        <p class="text-gray-500 text-sm">Mappa non disponibile</p>
    </x-card>
    @endif

</div>
@endsection
