<x-card padding="lg">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">{{ __('messages.dashboard_monthly_chart_title') }}</h2>

        @if ($hasData)
        <div class="flex items-center gap-3 flex-wrap">
            <x-form.select size="sm" wire:model.live="year">
                @foreach ($availableYears as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </x-form.select>

            <x-form.select size="sm" wire:model.live="month">
                @foreach ($monthNames as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </x-form.select>

            <div class="flex rounded-md border border-gray-300 overflow-hidden text-sm">
                <button
                    wire:click="$set('metric', 'distance')"
                    class="px-3 py-1.5 transition-colors {{ $metric === 'distance' ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                >
                    {{ __('messages.metric_distance') }}
                </button>
                <button
                    wire:click="$set('metric', 'hours')"
                    class="px-3 py-1.5 transition-colors border-l border-gray-300 {{ $metric === 'hours' ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                >
                    {{ __('messages.col_hours') }}
                </button>
            </div>
        </div>
        @endif
    </div>

    @if ($hasData)
        <script>window.__monthlyChartInitData = @json($chartData);</script>
        <div
            x-data="{
                chart: null,
                init() {
                    const data = window.__monthlyChartInitData;
                    this.chart = new window.ApexCharts(this.$refs.chartEl, {
                        chart: { type: 'bar', height: 300, toolbar: { show: false }, animations: { enabled: true } },
                        series: [{ name: data.seriesName, data: data.seriesData }],
                        xaxis: { categories: data.categories, labels: { rotate: -45, style: { fontSize: '11px' } } },
                        yaxis: { title: { text: data.yAxisTitle }, labels: { formatter: function(val) { return val.toFixed(1); } } },
                        colors: ['#3B82F6'],
                        plotOptions: { bar: { borderRadius: 3, columnWidth: '70%' } },
                        dataLabels: { enabled: false },
                        grid: { borderColor: '#f1f5f9' }
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
            @monthly-chart-data.window="updateChart($event.detail.data)"
        >
            <div wire:ignore x-ref="chartEl" style="min-height: 300px;"></div>
        </div>
    @else
        <p class="text-gray-500 text-sm">{{ __('messages.dashboard_no_activities') }}</p>
    @endif
</x-card>
