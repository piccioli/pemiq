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
    <div class="bg-white rounded-lg shadow p-5">
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
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('messages.trend_all_sports') }}</label>
                <select
                    wire:model="sportType"
                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-1.5 text-gray-700 focus:ring-violet-500 focus:border-violet-500"
                >
                    <option value="">{{ __('messages.trend_all_sports') }}</option>
                    @foreach ($sportTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
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
    </div>

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
            <div class="bg-white rounded-lg shadow p-5">
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
            </div>
        @endforeach
    </div>
</div>
