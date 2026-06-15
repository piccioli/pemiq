<div class="space-y-6">
    {{-- Header with export placeholder --}}
    <div class="flex items-center justify-between">
        <h2 style="font-family: var(--font-display); font-weight: 700; font-size: var(--fs-lg); color: var(--text-strong)">{{ __('messages.compare_title') }}</h2>
        <x-button variant="secondary" :disabled="true" title="{{ __('messages.compare_export_tooltip') }}">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </x-slot:icon>
            {{ __('messages.compare_export') }}
        </x-button>
    </div>

    {{-- Period selectors --}}
    <x-card padding="md">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Period A --}}
            <div>
                <label class="block mb-1" style="font-size: var(--fs-xs); font-weight: 500; color: var(--zone-2)">{{ __('messages.compare_period_a') }}</label>
                <div class="flex gap-2">
                    <input type="date" wire:model="fromA" class="flex-1 pq-input">
                    <input type="date" wire:model="toA" class="flex-1 pq-input">
                </div>
            </div>

            {{-- Period B --}}
            <div>
                <label class="block mb-1" style="font-size: var(--fs-xs); font-weight: 500; color: var(--accent)">{{ __('messages.compare_period_b') }}</label>
                <div class="flex gap-2">
                    <input type="date" wire:model="fromB" class="flex-1 pq-input">
                    <input type="date" wire:model="toB" class="flex-1 pq-input">
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
                <x-button
                    wire:click="compare"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto"
                >
                    <span wire:loading.remove wire:target="compare">{{ __('messages.compare_update') }}</span>
                    <span wire:loading wire:target="compare">{{ __('messages.compare_updating') }}</span>
                </x-button>
            </div>
        </div>

        {{-- Period labels --}}
        <div class="flex flex-wrap gap-4 mt-3" style="font-size: var(--fs-xs); color: var(--text-muted)">
            <span class="flex items-center gap-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: var(--zone-2)"></span>
                {{ __('messages.compare_period_a') }}: {{ $periodALabel }}
            </span>
            <span class="flex items-center gap-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: var(--accent)"></span>
                {{ __('messages.compare_period_b') }}: {{ $periodBLabel }}
            </span>
        </div>
    </x-card>

    {{-- 4 KPI tiles --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $kpiAccents = ['zone-2', 'zone-3', 'brand', 'zone-5'];
            $kpiIcons   = ['route', 'mountain-snow', 'clock', 'activity'];
        @endphp
        @foreach ($kpis as $kpi)
            @php
                $valA = $kpi['valueA'];
                $valB = $kpi['valueB'];
                $dec  = $kpi['decimals'];
                $fmtValue = $dec > 0 ? fmt_number($valB, $dec) : (string)(int)$valB;
                $deltaRaw = $valA != 0 ? (($valB - $valA) / $valA) * 100 : null;
                $deltaTxt = match(true) {
                    $deltaRaw === null => null,
                    $deltaRaw > 0     => fmt_number(abs($deltaRaw), 1) . '%',
                    $deltaRaw < 0     => fmt_number(abs($deltaRaw), 1) . '%',
                    default           => '0%',
                };
                $deltaDir = match(true) {
                    $deltaRaw === null => 'flat',
                    $deltaRaw > 0     => 'up',
                    $deltaRaw < 0     => 'down',
                    default           => 'flat',
                };
                $fmtValA  = $dec > 0 ? fmt_number($valA, $dec) : (string)(int)$valA;
                $deltaCtx = __('messages.compare_vs') . ' ' . $fmtValA . ($kpi['unit'] ? ' ' . $kpi['unit'] : '');
                $accent   = $kpiAccents[$loop->index] ?? 'brand';
                $iconName = $kpiIcons[$loop->index] ?? 'activity';
            @endphp
            <x-metric-tile
                :label="$kpi['label']"
                :value="$fmtValue"
                :unit="$kpi['unit']"
                :delta="$deltaTxt"
                :deltaDir="$deltaDir"
                :deltaContext="$deltaCtx"
                :accent="$accent"
            >
                <x-slot:icon><i data-lucide="{{ $iconName }}"></i></x-slot:icon>
            </x-metric-tile>
        @endforeach
    </div>

    {{-- Dual-line comparison chart --}}
    <x-card :eyebrow="$chartInitData['title']" padding="md">
        <x-slot:action>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Granularity toggle: Mese / Settimana --}}
                <x-segmented-control
                    name="granularity"
                    :selected="$granularity"
                    size="sm"
                    :options="[
                        ['value' => 'month', 'label' => __('messages.compare_granularity_month')],
                        ['value' => 'week',  'label' => __('messages.compare_granularity_week')],
                    ]"
                />
                {{-- Chart type toggle: Linee / Barre --}}
                <x-segmented-control
                    name="chartType"
                    :selected="$chartType"
                    size="sm"
                    :options="[
                        ['value' => 'line', 'label' => __('messages.compare_chart_type_line')],
                        ['value' => 'bar',  'label' => __('messages.compare_chart_type_bar')],
                    ]"
                />
            </div>
        </x-slot:action>

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
                        colors: ['#4F8DF5', '#16D4B4'],
                        xaxis: { categories: d.categories, labels: { rotate: -30, style: { fontSize: '11px' } } },
                        yaxis: { labels: { formatter: function(v) { return v != null ? v.toFixed(1) : ''; } } },
                        stroke: isBar ? { show: false } : { curve: 'smooth', width: 2 },
                        fill: isBar ? { opacity: 0.85 } : { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.05 } },
                        plotOptions: isBar ? { bar: { columnWidth: '60%', borderRadius: 3 } } : {},
                        dataLabels: { enabled: false },
                        tooltip: { shared: true, intersect: false, y: { formatter: function(v) { return v != null ? v.toFixed(1) + ' km' : '—'; } } },
                        legend: { show: true, position: 'top' },
                        noData: { text: d.noDataText },
                        grid: { borderColor: 'var(--border)' }
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
    <x-card padding="none" :eyebrow="__('messages.compare_detail_title')">
        <div class="overflow-x-auto">
            <table class="pq-table w-full">
                <thead>
                    <tr>
                        <th style="text-align: left">{{ __('messages.compare_col_metric') }}</th>
                        <th style="text-align: right; color: var(--zone-2)">
                            {{ __('messages.compare_period_a') }}<br>
                            <span style="font-weight: 400; text-transform: none; letter-spacing: 0; color: var(--text-faint)">{{ $periodALabel }}</span>
                        </th>
                        <th style="text-align: right; color: var(--accent)">
                            {{ __('messages.compare_period_b') }}<br>
                            <span style="font-weight: 400; text-transform: none; letter-spacing: 0; color: var(--text-faint)">{{ $periodBLabel }}</span>
                        </th>
                        <th style="text-align: right">{{ __('messages.compare_col_delta_abs') }}</th>
                        <th style="text-align: right">{{ __('messages.compare_col_delta_pct') }}</th>
                    </tr>
                </thead>
                <tbody>
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
                            $deltaStyle = match(true) {
                                $deltaRaw === null => 'color: var(--text-faint)',
                                $deltaRaw > 0      => 'color: var(--success)',
                                $deltaRaw < 0      => 'color: var(--danger)',
                                default            => 'color: var(--text-faint)',
                            };
                            $absStyle = match(true) {
                                $deltaAbs > 0 => 'color: var(--success)',
                                $deltaAbs < 0 => 'color: var(--danger)',
                                default       => 'color: var(--text-faint)',
                            };

                            $icons = [
                                __('messages.compare_kpi_activities') => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                __('messages.compare_kpi_distance')   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
                                __('messages.compare_kpi_elevation')  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l7 14 7-14"/>',
                                __('messages.compare_kpi_time')       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                            ];
                            $iconPath = $icons[$kpi['label']] ?? '';
                        @endphp
                        <tr>
                            <td style="color: var(--text)">
                                <span class="inline-flex items-center gap-2">
                                    @if ($iconPath)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" style="color: var(--text-muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            {!! $iconPath !!}
                                        </svg>
                                    @endif
                                    {{ $kpi['label'] }}
                                </span>
                            </td>
                            <td class="col-right" style="color: var(--text-muted)">
                                {{ $fmtA }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="col-right" style="color: var(--text-strong); font-weight: 600">
                                {{ $fmtB }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="col-right" style="font-weight: 500; {{ $absStyle }}">
                                {{ $fmtDeltaAbs }}{{ $unit ? ' ' . $unit : '' }}
                            </td>
                            <td class="col-right" style="font-weight: 500; {{ $deltaStyle }}">
                                {{ $fmtDeltaPct }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
