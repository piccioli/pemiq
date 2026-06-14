<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">{{ __('messages.dashboard_monthly_analysis_title') }}</h2>

        <div>
            <label for="year-select" class="sr-only">{{ __('messages.col_year') }}</label>
            <select
                id="year-select"
                wire:model.live="year"
                class="border border-gray-300 rounded-md px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
            >
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @php
        $monthNames = trans('messages.months');
        $hasData = $monthlyStats->sum('activities') > 0;
    @endphp

    @if (! $hasData)
        <p class="text-gray-500 text-sm">
            {{ __('messages.dashboard_no_activities_year', ['year' => $year]) }}
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_month') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_activities') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.stat_distance_km') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.stat_elevation_m') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.col_hours') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($monthlyStats as $row)
                        <tr class="{{ $row->activities > 0 ? 'hover:bg-gray-50' : 'text-gray-400' }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $monthNames[$row->month - 1] }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? fmt_number($row->activities) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? fmt_number($row->distance_km, 1) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? fmt_number($row->elevation_m, 0) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? fmt_number($row->hours, 1) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
