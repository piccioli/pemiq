<x-card padding="lg">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard_sport_dist_title') }}</h2>

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

    @if ($sportStats->isEmpty())
        <p class="text-gray-500 text-sm">
            {{ __('messages.dashboard_no_activities') }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_sport') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_activities') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.stat_distance_km') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_hours') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($sportStats as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <x-badge :variant="$sportVariants[$row->sport_type] ?? 'outline'">{{ $row->sport_type }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->activities) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->distance_km, 1) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->hours, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-card>
