<x-card padding="lg">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard_sport_dist_chart_title') }}</h2>

    @if ($hasData)
        <script>window.__sportDistChartInitData = @json($chartData);</script>
        <div
            x-data="{
                chart: null,
                _distances: [],
                init() {
                    const d = window.__sportDistChartInitData;
                    this._distances = d.distances;
                    const self = this;
                    this.chart = new window.ApexCharts(this.$refs.chartEl, {
                        chart: { type: 'donut', height: 340, toolbar: { show: false } },
                        series: d.series,
                        labels: d.labels,
                        colors: d.colors,
                        dataLabels: { enabled: true, formatter: function(val, opts) { return opts.w.config.labels[opts.seriesIndex]; } },
                        legend: { position: 'bottom' },
                        tooltip: {
                            custom: function({ series, seriesIndex, w }) {
                                const label = w.config.labels[seriesIndex];
                                const activities = series[seriesIndex];
                                const dist = self._distances[seriesIndex];
                                return '<div style=\'padding:8px;font-size:13px;\'><b>' + label + '</b><br/>' + activities + ' {{ __('messages.activities_label') }}<br/>' + dist + ' km</div>';
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: '{{ __('messages.chart_total_label') }}',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce(function(a, b) { return a + b; }, 0);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    });
                    this.chart.render();
                },
                updateChart(data) {
                    if (!this.chart) return;
                    this._distances = data.distances;
                    this.chart.updateOptions({ labels: data.labels, colors: data.colors }, false, false);
                    this.chart.updateSeries(data.series);
                }
            }"
            @sport-distribution-chart-data.window="updateChart($event.detail.data)"
        >
            <div wire:ignore x-ref="chartEl" style="min-height: 340px;"></div>
        </div>
    @else
        <p class="text-gray-500 text-sm">{{ __('messages.dashboard_no_activities') }}</p>
    @endif
</x-card>
