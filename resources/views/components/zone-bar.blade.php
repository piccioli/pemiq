@props([
    'data'       => [],   // array of ['zone'=>1..5, 'pct'=>float 0-100, 'seconds'=>int]
    'showLegend' => true,
])

@php
    $formatTime = function(int $seconds): string {
        if ($seconds <= 0) return '0m';
        $hours = (int) floor($seconds / 3600);
        $mins  = (int) floor(($seconds % 3600) / 60);
        if ($hours === 0) return $mins . 'm';
        return $hours . 'h ' . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'm';
    };
@endphp

<div class="pq-zone-bar"
     x-data="{ animated: false }"
     x-init="$nextTick(() => animated = true)">

    {{-- Stacked bar --}}
    <div class="pq-zone-bar-track">
        @foreach ($data as $item)
            @php $pct = max(0, min(100, (float) ($item['pct'] ?? 0))); @endphp
            <div
                class="pq-zone-bar-segment"
                data-zone="{{ (int) ($item['zone'] ?? 1) }}"
                style="--pct: {{ number_format($pct, 2, '.', '') }}%"
                :style="{ width: animated ? 'var(--pct)' : '0%' }"
            ></div>
        @endforeach
    </div>

    @if ($showLegend && count($data))
        <div class="pq-zone-bar-legend">
            @foreach ($data as $item)
                @php $pct = max(0, min(100, (float) ($item['pct'] ?? 0))); @endphp
                <div class="pq-zone-bar-legend-item">
                    <span class="pq-zone-bar-dot" style="background: var(--zone-{{ (int) ($item['zone'] ?? 1) }})"></span>
                    <span class="pq-zone-bar-legend-code">Z{{ (int) ($item['zone'] ?? 1) }}</span>
                    <span class="pq-zone-bar-legend-pct">{{ fmt_number($pct, 0) }}%</span>
                    <span class="pq-zone-bar-legend-time">{{ $formatTime((int) ($item['seconds'] ?? 0)) }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
