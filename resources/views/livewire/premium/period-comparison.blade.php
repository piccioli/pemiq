<div class="space-y-6">
    {{-- Header with export placeholder --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">{{ __('messages.compare_title') }}</h2>
        <button
            disabled
            title="{{ __('messages.compare_export_tooltip') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            {{ __('messages.compare_export') }}
        </button>
    </div>

    {{-- Period selectors --}}
    <x-card padding="md">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Period A --}}
            <div>
                <label class="block text-xs font-medium text-blue-700 mb-1">{{ __('messages.compare_period_a') }}</label>
                <div class="flex gap-2">
                    <input
                        type="date"
                        wire:model="fromA"
                        class="flex-1 text-sm border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500"
                    >
                    <input
                        type="date"
                        wire:model="toA"
                        class="flex-1 text-sm border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
            </div>

            {{-- Period B --}}
            <div>
                <label class="block text-xs font-medium text-violet-700 mb-1">{{ __('messages.compare_period_b') }}</label>
                <div class="flex gap-2">
                    <input
                        type="date"
                        wire:model="fromB"
                        class="flex-1 text-sm border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:ring-violet-500 focus:border-violet-500"
                    >
                    <input
                        type="date"
                        wire:model="toB"
                        class="flex-1 text-sm border border-gray-300 rounded-md px-2 py-1.5 text-gray-700 focus:ring-violet-500 focus:border-violet-500"
                    >
                </div>
            </div>

            {{-- Sport filter --}}
            <div>
                <x-form.select :label="__('messages.col_sport')" wire:model="sportType">
                    <option value="">{{ __('messages.trend_all_sports') }}</option>
                    @foreach ($sportTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Update button --}}
            <div class="lg:col-span-2">
                <button
                    wire:click="compare"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="compare">{{ __('messages.compare_update') }}</span>
                    <span wire:loading wire:target="compare">{{ __('messages.compare_updating') }}</span>
                </button>
            </div>
        </div>

        {{-- Period labels --}}
        <div class="flex flex-wrap gap-4 mt-3 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                {{ __('messages.compare_period_a') }}: {{ $periodALabel }}
            </span>
            <span class="flex items-center gap-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                {{ __('messages.compare_period_b') }}: {{ $periodBLabel }}
            </span>
        </div>
    </x-card>

    {{-- 4 KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($kpis as $kpi)
            @php
                $valA = $kpi['valueA'];
                $valB = $kpi['valueB'];
                $deltaRaw = $valA != 0 ? (($valB - $valA) / $valA) * 100 : null;
                $deltaText = match(true) {
                    $deltaRaw === null => '—',
                    $deltaRaw > 0     => '↑ ' . number_format(abs($deltaRaw), 1) . '%',
                    $deltaRaw < 0     => '↓ ' . number_format(abs($deltaRaw), 1) . '%',
                    default           => '0%',
                };
                $deltaClass = match(true) {
                    $deltaRaw === null => 'text-gray-400',
                    $deltaRaw > 0     => 'text-green-600',
                    $deltaRaw < 0     => 'text-red-600',
                    default           => 'text-gray-400',
                };
            @endphp
            <x-card padding="md">
                <p class="text-sm text-gray-500 font-medium mb-2">{{ $kpi['label'] }}</p>

                {{-- Period B value (primary) --}}
                <p class="text-2xl font-bold text-gray-900">
                    {{ $kpi['decimals'] > 0 ? fmt_number($valB, $kpi['decimals']) : (int) $valB }}
                    @if($kpi['unit'])
                        <span class="text-sm font-normal text-gray-500">{{ $kpi['unit'] }}</span>
                    @endif
                </p>

                {{-- Delta --}}
                <p class="text-sm mt-1 font-medium {{ $deltaClass }}">{{ $deltaText }}</p>

                {{-- Period A reference --}}
                <p class="text-xs text-gray-400 mt-1">
                    {{ __('messages.compare_vs') }}
                    {{ $kpi['decimals'] > 0 ? fmt_number($valA, $kpi['decimals']) : (int) $valA }}{{ $kpi['unit'] ? ' ' . $kpi['unit'] : '' }}
                </p>
            </x-card>
        @endforeach
    </div>

    {{-- Dual-line comparison chart --}}
    <x-card padding="md">
        {{-- Chart header with toggles --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="text-sm font-semibold text-gray-700">{{ $chartInitData['title'] }}</h3>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Granularity toggle: Mese / Settimana --}}
                <div class="inline-flex rounded-md border border-gray-200 overflow-hidden text-xs">
                    <button
                        wire:click="setGranularity('month')"
                        class="px-3 py-1.5 font-medium transition-colors {{ $granularity === 'month' ? 'bg-violet-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                    >{{ __('messages.compare_granularity_month') }}</button>
                    <button
                        wire:click="setGranularity('week')"
                        class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ $granularity === 'week' ? 'bg-violet-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                    >{{ __('messages.compare_granularity_week') }}</button>
                </div>
                {{-- Chart type toggle: Linee / Barre --}}
                <div class="inline-flex rounded-md border border-gray-200 overflow-hidden text-xs">
                    <button
                        wire:click="setChartType('line')"
                        class="px-3 py-1.5 font-medium transition-colors {{ $chartType === 'line' ? 'bg-violet-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                    >{{ __('messages.compare_chart_type_line') }}</button>
                    <button
                        wire:click="setChartType('bar')"
                        class="px-3 py-1.5 font-medium transition-colors border-l border-gray-200 {{ $chartType === 'bar' ? 'bg-violet-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                    >{{ __('messages.compare_chart_type_bar') }}</button>
                </div>
            </div>
        </div>

        <script>window.__periodChartInitData = @json($chartInitData);</script>
        <div
            x-data="{
                chart: null,
                init() {
                    const d = window.__periodChartInitData;
                    this.chart = new window.ApexCharts(this.$refs.chartEl, this.buildOptions(d));
                    this.chart.render();
                },
                buildOptions(d) {
                    const isBar = d.chartType === 'bar';
                    return {
                        chart: { type: isBar ? 'bar' : 'line', height: 280, toolbar: { show: false }, fontFamily: 'inherit', animations: { enabled: true } },
                        series: [
                            { name: d.periodALabel, data: d.seriesA },
                            { name: d.periodBLabel, data: d.seriesB }
                        ],
                        colors: ['#3B82F6', '#7C3AED'],
                        xaxis: { categories: d.categories, labels: { rotate: -30, style: { fontSize: '11px' } } },
                        yaxis: { labels: { formatter: function(v) { return v != null ? v.toFixed(1) : ''; } } },
                        stroke: isBar ? { show: false } : { curve: 'smooth', width: 2 },
                        fill: isBar ? { opacity: 0.85 } : { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.05 } },
                        plotOptions: isBar ? { bar: { columnWidth: '60%', borderRadius: 3 } } : {},
                        dataLabels: { enabled: false },
                        tooltip: { shared: true, intersect: false, y: { formatter: function(v) { return v != null ? v.toFixed(1) + ' km' : '—'; } } },
                        legend: { show: true, position: 'top' },
                        noData: { text: d.noDataText },
                        grid: { borderColor: '#f1f5f9' }
                    };
                },
                updateChart(d) {
                    if (!this.chart) return;
                    const isBar = d.chartType === 'bar';
                    this.chart.updateOptions({
                        chart: { type: isBar ? 'bar' : 'line' },
                        series: [
                            { name: d.periodALabel, data: d.seriesA },
                            { name: d.periodBLabel, data: d.seriesB }
                        ],
                        xaxis: { categories: d.categories },
                        stroke: isBar ? { show: false } : { curve: 'smooth', width: 2 },
                        fill: isBar ? { opacity: 0.85 } : { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.05 } },
                        plotOptions: isBar ? { bar: { columnWidth: '60%', borderRadius: 3 } } : {}
                    }, true, false);
                }
            }"
            @period-chart-data.window="updateChart($event.detail.data)"
        >
            <div wire:ignore x-ref="chartEl" style="min-height: 280px;"></div>
        </div>
    </x-card>

    {{-- Detail comparison table --}}
    <x-card padding="none">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">{{ __('messages.compare_detail_title') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('messages.compare_col_metric') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-blue-600 uppercase tracking-wide">
                            {{ __('messages.compare_period_a') }}<br>
                            <span class="font-normal normal-case text-gray-400">{{ $periodALabel }}</span>
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-violet-600 uppercase tracking-wide">
                            {{ __('messages.compare_period_b') }}<br>
                            <span class="font-normal normal-case text-gray-400">{{ $periodBLabel }}</span>
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('messages.compare_col_delta_abs') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('messages.compare_col_delta_pct') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($kpis as $kpi)
                        @php
                            $valA     = $kpi['valueA'];
                            $valB     = $kpi['valueB'];
                            $unit     = $kpi['unit'];
                            $dec      = $kpi['decimals'];
                            $fmtA     = $dec > 0 ? fmt_number($valA, $dec) : (string)(int)$valA;
                            $fmtB     = $dec > 0 ? fmt_number($valB, $dec) : (string)(int)$valB;

                            $deltaAbs = $valB - $valA;
                            $fmtDeltaAbs = ($deltaAbs >= 0 ? '+' : '') . ($dec > 0 ? fmt_number($deltaAbs, $dec) : (string)(int)$deltaAbs);

                            $deltaRaw = $valA != 0 ? (($valB - $valA) / $valA) * 100 : null;
                            $fmtDeltaPct = match(true) {
                                $deltaRaw === null => '—',
                                $deltaRaw > 0      => '↑ ' . fmt_number(abs($deltaRaw), 1) . '%',
                                $deltaRaw < 0      => '↓ ' . fmt_number(abs($deltaRaw), 1) . '%',
                                default            => '0%',
                            };
                            $deltaClass = match(true) {
                                $deltaRaw === null => 'text-gray-400',
                                $deltaRaw > 0      => 'text-green-600',
                                $deltaRaw < 0      => 'text-red-600',
                                default            => 'text-gray-400',
                            };
                            $absClass = match(true) {
                                $deltaAbs > 0 => 'text-green-600',
                                $deltaAbs < 0 => 'text-red-600',
                                default       => 'text-gray-400',
                            };

                            $icons = [
                                __('messages.compare_kpi_activities') => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                __('messages.compare_kpi_distance')   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
                                __('messages.compare_kpi_elevation')  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l7 14 7-14"/>',
                                __('messages.compare_kpi_time')       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            ];
                            $iconPath = $icons[$kpi['label']] ?? '';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-700">
                                <span class="inline-flex items-center gap-2">
                                    @if ($iconPath)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            {!! $iconPath !!}
                                        </svg>
                                    @endif
                                    {{ $kpi['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700 tabular-nums">
                                {{ $fmtA }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900 tabular-nums">
                                {{ $fmtB }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium tabular-nums {{ $absClass }}">
                                {{ $fmtDeltaAbs }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium tabular-nums {{ $deltaClass }}">
                                {{ $fmtDeltaPct }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
