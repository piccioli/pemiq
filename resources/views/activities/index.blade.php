@extends('layouts.app')

@section('title', 'Attività')

@section('content')
<div class="space-y-6">

    <x-page-header title="Attività">
        <x-slot:actions>
            <span style="font-size: var(--fs-sm); color: var(--text-muted)">{{ $activities->total() }} attività trovate</span>
        </x-slot:actions>
    </x-page-header>

    {{-- Filtri --}}
    <x-card>
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
            <div class="flex flex-wrap items-center gap-2 mt-3 pt-3" style="border-top: 1px solid var(--border)">
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
    </x-card>

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

    <x-card padding="none" style="overflow: hidden;">
        @if ($activities->isEmpty())
            <div style="padding: 32px; text-align: center; font-size: var(--fs-sm); color: var(--text-muted)">
                @if ($sport || $year || $month)
                    Nessuna attività trovata.
                @else
                    Nessuna attività ancora. Collega Strava e avvia la sincronizzazione.
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="pq-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Sport</th>
                            <th>Titolo</th>
                            <th class="col-right">Distanza</th>
                            <th class="col-right">Durata</th>
                            <th class="col-right">Dislivello</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <tr>
                                <td class="col-muted">{{ fmt_date($activity->started_at, 'd M Y') }}</td>
                                <td>
                                    <x-badge :variant="$sportVariants[$activity->sport_type] ?? 'outline'">{{ $activity->sport_type }}</x-badge>
                                </td>
                                <td class="col-truncate">
                                    <a href="{{ route('activities.show', $activity) }}" style="color: var(--accent); text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                        {{ $activity->name }}
                                    </a>
                                </td>
                                <td class="col-right">{{ $distanceKm }}</td>
                                <td class="col-right">{{ $duration }}</td>
                                <td class="col-right">{{ $elevation }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div style="padding: 12px 16px; border-top: 1px solid var(--border)">
                    {{ $activities->links() }}
                </div>
            @endif
        @endif
    </x-card>

</div>
@endsection
