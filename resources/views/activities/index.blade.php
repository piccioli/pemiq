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
            <x-form.select :label="__('messages.col_sport')" name="sport">
                <option value="">Tutti gli sport</option>
                @foreach ($sportTypes as $type)
                    <option value="{{ $type }}" @selected($sport === $type)>{{ $type }}</option>
                @endforeach
            </x-form.select>

            <x-form.select :label="__('messages.col_year')" name="year">
                <option value="">Tutti gli anni</option>
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                @endforeach
            </x-form.select>

            @php
                $monthNames = trans('messages.months');
            @endphp
            <x-form.select :label="__('messages.col_month')" name="month">
                <option value="">Tutti i mesi</option>
                @foreach ($monthNames as $idx => $name)
                    <option value="{{ $idx + 1 }}" @selected((string) $month === (string) ($idx + 1))>{{ $name }}</option>
                @endforeach
            </x-form.select>

            <x-button type="submit">Filtra</x-button>
        </form>

        @if ($sport || $year || $month)
            <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t" style="border-color: var(--border)">
                <span style="font-size: var(--fs-xs); color: var(--text-faint)">{{ __('messages.active_filters') }}</span>
                @if ($sport)
                    <x-tag selected removable
                           :remove-href="route('activities.index', array_filter(['year' => $year, 'month' => $month]))">
                        {{ $sport }}
                    </x-tag>
                @endif
                @if ($year)
                    <x-tag selected removable
                           :remove-href="route('activities.index', array_filter(['sport' => $sport, 'month' => $month]))">
                        {{ $year }}
                    </x-tag>
                @endif
                @if ($month)
                    <x-tag selected removable
                           :remove-href="route('activities.index', array_filter(['sport' => $sport, 'year' => $year]))">
                        {{ $monthNames[$month - 1] ?? '' }}
                    </x-tag>
                @endif
            </div>
        @endif
    </div>

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
                                    <x-badge :variant="$sportVariants[$activity->sport_type] ?? 'outline'">{{ $activity->sport_type }}</x-badge>
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
