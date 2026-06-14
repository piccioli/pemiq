@extends('layouts.app')

@section('title', 'Attività')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Attività</h1>
        <span class="text-sm text-gray-500">{{ $activities->total() }} attività totali</span>
    </div>

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
    @endphp

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($activities->isEmpty())
            <div class="p-8 text-center text-gray-500 text-sm">
                Nessuna attività ancora. Collega Strava e avvia la sincronizzazione.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titolo</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Distanza</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Durata</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dislivello</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($activities as $activity)
                            @php
                                $badgeClass = $sportColors[$activity->sport_type] ?? 'bg-gray-100 text-gray-700';
                                $distanceKm = $activity->distance !== null
                                    ? number_format($activity->distance / 1000, 1) . ' km'
                                    : '—';
                                $elapsedSec = $activity->elapsed_time;
                                $duration = $elapsedSec !== null
                                    ? floor($elapsedSec / 3600) . ':' . str_pad(floor(($elapsedSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
                                    : '—';
                                $elevation = $activity->elevation_gain !== null
                                    ? number_format($activity->elevation_gain, 0) . ' m'
                                    : '—';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                    {{ $activity->started_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $activity->sport_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $activity->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">{{ $distanceKm }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">{{ $duration }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right whitespace-nowrap">{{ $elevation }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $activities->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
