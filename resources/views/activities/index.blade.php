@extends('layouts.app')

@section('title', 'Attività')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Attività</h1>
        <span class="text-sm text-gray-500">{{ $activities->total() }} attività trovate</span>
    </div>

    {{-- Filtri --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('activities.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sport</label>
                <select name="sport" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tutti gli sport</option>
                    @foreach ($sportTypes as $type)
                        <option value="{{ $type }}" @selected($sport === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Anno</label>
                <select name="year" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tutti gli anni</option>
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                @php
                    $monthNames = trans('messages.months');
                @endphp
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mese</label>
                <select name="month" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tutti i mesi</option>
                    @foreach ($monthNames as $idx => $name)
                        <option value="{{ $idx + 1 }}" @selected((string) $month === (string) ($idx + 1))>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <x-button type="submit">Filtra</x-button>

            @if ($sport || $year || $month)
                <x-button href="{{ route('activities.index') }}" variant="secondary">Reset</x-button>
            @endif
        </form>
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
                @if ($sport || $year || $month)
                    Nessuna attività trovata.
                @else
                    Nessuna attività ancora. Collega Strava e avvia la sincronizzazione.
                @endif
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
                                    ? fmt_number($activity->distance / 1000, 1) . ' km'
                                    : '—';
                                $elapsedSec = $activity->elapsed_time;
                                $duration = $elapsedSec !== null
                                    ? floor($elapsedSec / 3600) . ':' . str_pad(floor(($elapsedSec % 3600) / 60), 2, '0', STR_PAD_LEFT)
                                    : '—';
                                $elevation = $activity->elevation_gain !== null
                                    ? fmt_number($activity->elevation_gain, 0) . ' m'
                                    : '—';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                    {{ fmt_date($activity->started_at, 'd M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $activity->sport_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">
                                    <a href="{{ route('activities.show', $activity) }}" class="hover:text-blue-600 hover:underline">
                                        {{ $activity->name }}
                                    </a>
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
