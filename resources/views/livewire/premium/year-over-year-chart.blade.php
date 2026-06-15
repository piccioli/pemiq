<x-card :eyebrow="__('messages.yoy_chart_title')" padding="lg">
    <x-slot:action>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Year A selector --}}
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background: var(--zone-2)"></span>
                <label style="font-size: var(--fs-sm); color: var(--text-muted)">{{ __('messages.yoy_year_a') }}</label>
                <x-form.select size="sm" wire:model.live="yearA">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Year B selector --}}
            <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background: var(--zone-4)"></span>
                <label style="font-size: var(--fs-sm); color: var(--text-muted)">{{ __('messages.yoy_year_b') }}</label>
                <x-form.select size="sm" wire:model.live="yearB">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Sport filter --}}
            <x-form.select size="sm" wire:model.live="sportType">
                <option value="">{{ __('messages.yoy_all_sports') }}</option>
                @foreach ($sportTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </x-form.select>
        </div>
    </x-slot:action>

    @if ($hasData)
        <script>window.__yoyChartInitData = @json($chartData);</script>
        <div
            x-data="{
                chart: null,
                init() {
                    const data = window.__yoyChartInitData;
                    this.chart = new window.ApexCharts(this.$refs.chartEl, this.buildOptions(data));
                    this.chart.render();
                },
                buildOptions(data) {
                    return {
                        chart: {
                            type: 'line',
                            height: 360,
                            toolbar: { show: false },
                            animations: { enabled: true }
                        },
                        series: [
                            { name: data.labelA, data: data.seriesA },
                            { name: data.labelB, data: data.seriesB }
                        ],
                        xaxis: {
                            categories: data.categories,
                            labels: { style: { fontSize: '12px' } }
                        },
                        yaxis: {
                            title: { text: 'km' },
                            labels: {
                                formatter: function(val) {
                                    return val !== null ? val.toFixed(1) : '';
                                }
                            }
                        },
                        colors: ['#4F8DF5', '#F97316'],
                        stroke: { curve: 'smooth', width: 2 },
                        dataLabels: { enabled: false },
                        markers: { size: 4 },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val !== null ? val.toFixed(1) + ' km' : '—';
                                }
                            }
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'right',
                            markers: { shape: 'circle' }
                        },
                        grid: { borderColor: 'var(--border)' },
                        noData: { text: data.noDataText }
                    };
                },
                updateChart(data) {
                    if (!this.chart) return;
                    this.chart.updateOptions({
                        series: [
                            { name: data.labelA, data: data.seriesA },
                            { name: data.labelB, data: data.seriesB }
                        ],
                        xaxis: { categories: data.categories }
                    }, true, false);
                }
            }"
            @yoy-chart-data.window="updateChart($event.detail.data)"
        >
            <div wire:ignore x-ref="chartEl" style="min-height: 360px;"></div>
        </div>
    @else
        <p style="color: var(--text-muted); font-size: var(--fs-sm)">{{ __('messages.yoy_no_data') }}</p>
    @endif
</x-card>
