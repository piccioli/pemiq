<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard_annual_analysis_title') }}</h2>

    @if ($annualStats->isEmpty())
        <p class="text-gray-500 text-sm">
            {{ __('messages.dashboard_no_activities') }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_year') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_activities') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.stat_distance_km') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.stat_elevation_m') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_hours') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($annualStats as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $row->year }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->activities) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->distance_km, 1) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->elevation_m, 0) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ fmt_number($row->hours, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
