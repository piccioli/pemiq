<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.dashboard_overview_title') }}</h2>

    @if ($stats['total_activities'] === 0)
        <p class="text-gray-500 text-sm">
            {{ __('messages.dashboard_no_activities') }}
        </p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

            {{-- Attività Totali --}}
            <div class="flex flex-col items-center text-center p-4 bg-orange-50 rounded-lg">
                <svg class="w-8 h-8 text-orange-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_activities']) }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ __('messages.stat_total_activities') }}</span>
            </div>

            {{-- Distanza Totale --}}
            <div class="flex flex-col items-center text-center p-4 bg-blue-50 rounded-lg">
                <svg class="w-8 h-8 text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_distance_km'], 1) }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ __('messages.stat_distance_km') }}</span>
            </div>

            {{-- Dislivello Totale --}}
            <div class="flex flex-col items-center text-center p-4 bg-green-50 rounded-lg">
                <svg class="w-8 h-8 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 3l14 9-14 9V3z" />
                </svg>
                <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_elevation_m'], 0) }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ __('messages.stat_elevation_m') }}</span>
            </div>

            {{-- Tempo Totale --}}
            <div class="flex flex-col items-center text-center p-4 bg-purple-50 rounded-lg">
                <svg class="w-8 h-8 text-purple-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-3xl font-bold text-gray-900">{{ $totalTime }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ __('messages.stat_total_time') }}</span>
            </div>

            {{-- Tempo in Movimento --}}
            <div class="flex flex-col items-center text-center p-4 bg-yellow-50 rounded-lg">
                <svg class="w-8 h-8 text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-3xl font-bold text-gray-900">{{ $movingTime }}</span>
                <span class="text-sm text-gray-500 mt-1">{{ __('messages.stat_moving_time') }}</span>
            </div>

        </div>
    @endif
</div>
