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
                    $sportColors = [
                        'Run'                => 'bg-orange-100 text-orange-800',
                        'TrailRun'           => 'bg-green-100 text-green-800',
                        'Ride'               => 'bg-blue-100 text-blue-800',
                        'GravelRide'         => 'bg-teal-100 text-teal-800',
                        'MountainBikeRide'   => 'bg-yellow-100 text-yellow-800',
                        'VirtualRide'        => 'bg-sky-100 text-sky-800',
                        'VirtualRun'         => 'bg-amber-100 text-amber-800',
                        'Hike'               => 'bg-lime-100 text-lime-800',
                        'Walk'               => 'bg-purple-100 text-purple-800',
                        'Swim'               => 'bg-cyan-100 text-cyan-800',
                        'Workout'            => 'bg-gray-100 text-gray-800',
                    ];
                    $badgeClass = $sportColors[$activity->sport_type] ?? 'bg-gray-100 text-gray-700';
                @endphp
                &nbsp;
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                    {{ $activity->sport_type }}
                </span>
            </p>
        </div>

        @if ($activity->strava_activity_id)
            <a href="https://www.strava.com/activities/{{ $activity->strava_activity_id }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-md hover:bg-orange-600 transition">
                Vedi su Strava
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
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
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Distanza</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $distanceKm }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Durata</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $duration }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dislivello</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $elevation }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Velocità media</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgSpeedKmh }}</p>
        </div>

        @if ($avgHr !== null)
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">FC media</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgHr }}</p>
        </div>
        @endif
    </div>

    {{-- Metriche secondarie --}}
    @if ($movingDuration !== '—' || $maxHr !== null || $avgWatts !== null || $calories !== null)
    <div class="bg-white rounded-lg shadow p-4">
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
    </div>
    @endif

    {{-- Mappa percorso --}}
    @if ($activity->polyline)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <h2 class="text-sm font-semibold text-gray-700 px-4 pt-4 pb-2">Percorso</h2>
        <div id="activity-map" style="height: 400px;"></div>
    </div>
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
    <div class="bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center" style="height: 400px;">
        <p class="text-gray-500 text-sm">Mappa non disponibile</p>
    </div>
    @endif

</div>
@endsection
