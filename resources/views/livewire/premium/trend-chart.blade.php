<x-card :eyebrow="__('messages.trend_chart_title')" padding="lg">
    <x-slot:action>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Range selector --}}
            <x-form.select size="sm" wire:model.live="range">
                <option value="3months">{{ __('messages.trend_range_3months') }}</option>
                <option value="6months">{{ __('messages.trend_range_6months') }}</option>
                <option value="1year">{{ __('messages.trend_range_1year') }}</option>
                <option value="custom">{{ __('messages.trend_range_custom') }}</option>
            </x-form.select>

            {{-- Sport filter --}}
            <x-form.select size="sm" wire:model.live="sportType">
                <option value="">{{ __('messages.trend_all_sports') }}</option>
                @foreach ($sportTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </x-form.select>

            {{-- Metric toggle --}}
            <x-segmented-control
                name="metric"
                :selected="$metric"
                size="sm"
                :options="[
                    ['value' => 'distance', 'label' => __('messages.metric_distance')],
                    ['value' => 'hours',    'label' => __('messages.col_hours')],
                ]"
            />
        </div>
    </x-slot:action>

    {{-- Custom date range inputs --}}
    @if ($range === 'custom')
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <label style="font-size: var(--fs-sm); color: var(--text-muted)">{{ __('messages.trend_from') }}</label>
                <input type="date" wire:model.live="customFrom" class="pq-input">
            </div>
            <div class="flex items-center gap-2">
                <label style="font-size: var(--fs-sm); color: var(--text-muted)">{{ __('messages.trend_to') }}</label>
                <input type="date" wire:model.live="customTo" class="pq-input">
            </div>
        </div>
    @endif

    @if ($hasData)
        <script>window.__trendChartInitData = @json($chartData);</script>
        <div
            x-data="{
                chart: null,
                init() {
                    const data = window.__trendChartInitData;
                    this.chart = new window.ApexCharts(this.$refs.chartEl, {
                        chart: {
                            type: 'area',
                            height: 320,
                            toolbar: { show: false },
                            animations: { enabled: true }
                        },
                        series: [{ name: data.seriesName, data: data.seriesData }],
                        xaxis: {
                            categories: data.categories,
                            labels: { rotate: -30, style: { fontSize: '11px' } }
                        },
                        yaxis: {
                            title: { text: data.yAxisTitle },
                            labels: { formatter: function(val) { return val.toFixed(1); } }
                        },
                        colors: ['#16D4B4'],
                        fill: {
                            type: 'gradient',
                            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
                        },
                        stroke: { curve: 'smooth', width: 2 },
                        dataLabels: { enabled: false },
                        tooltip: {
                            y: { formatter: function(val) { return val.toFixed(1); } }
                        },
                        grid: { borderColor: 'var(--border)' },
                        markers: { size: 4 }
                    });
                    this.chart.render();
                },
                updateChart(data) {
                    if (!this.chart) return;
                    this.chart.updateOptions({
                        series: [{ name: data.seriesName, data: data.seriesData }],
                        xaxis: { categories: data.categories },
                        yaxis: { title: { text: data.yAxisTitle }, labels: { formatter: function(val) { return val.toFixed(1); } } }
                    }, true, false);
                }
            }"
            @trend-chart-data.window="updateChart($event.detail.data)"
        >
            <div wire:ignore x-ref="chartEl" style="min-height: 320px;"></div>
        </div>
    @else
        <p style="color: var(--text-muted); font-size: var(--fs-sm)">{{ __('messages.trend_no_data') }}</p>
    @endif
</x-card>
