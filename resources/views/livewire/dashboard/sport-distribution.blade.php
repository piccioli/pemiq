<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard_sport_dist_title') }}</h2>

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
                        @php
                            $badgeClass = $sportColors[$row->sport_type] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $row->sport_type }}
                                </span>
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
</div>
