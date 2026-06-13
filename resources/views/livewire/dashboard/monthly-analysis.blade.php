<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Analisi per Mese</h2>

        <div>
            <label for="year-select" class="sr-only">Anno</label>
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
        $monthNames = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
        $hasData = $monthlyStats->sum('activities') > 0;
    @endphp

    @if (! $hasData)
        <p class="text-gray-500 text-sm">
            Nessuna attività per il {{ $year }}.
        </p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mese</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Attività</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Distanza (km)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dislivello (m)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ore</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($monthlyStats as $row)
                        <tr class="{{ $row->activities > 0 ? 'hover:bg-gray-50' : 'text-gray-400' }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $monthNames[$row->month - 1] }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? number_format($row->activities) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? number_format($row->distance_km, 1) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? number_format($row->elevation_m, 0) : '—' }}</td>
                            <td class="px-4 py-3 text-sm text-right">{{ $row->activities > 0 ? number_format($row->hours, 1) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
