@props([
    'label'     => null,
    'value'     => 0,        // 0-100
    'color'     => 'brand',  // brand|success|warning|danger|zone-1..5
    'height'    => 8,        // px
    'showValue' => false,
    'valueText' => null,
])

@php
    $fillColor = match($color) {
        'zone-1', 'zone1' => 'var(--zone-1)',
        'zone-2', 'zone2' => 'var(--zone-2)',
        'zone-3', 'zone3' => 'var(--zone-3)',
        'zone-4', 'zone4' => 'var(--zone-4)',
        'zone-5', 'zone5' => 'var(--zone-5)',
        'success' => 'var(--success)',
        'warning' => 'var(--warning)',
        'danger'  => 'var(--danger)',
        default   => 'var(--brand)',
    };
    $pct = max(0, min(100, (float) $value));
@endphp

<div {{ $attributes->class(['pq-progress-bar']) }}>

    @if ($label)
        <p class="pq-progress-bar-label">{{ $label }}</p>
    @endif

    <div class="pq-progress-bar-row">
        <div class="pq-progress-bar-track" style="height: {{ (int) $height }}px"
             x-data="{ animated: false }"
             x-init="$nextTick(() => animated = true)">
            <div class="pq-progress-bar-fill"
                 style="--pct: {{ number_format($pct, 2, '.', '') }}%; background: {{ $fillColor }}"
                 :style="{ width: animated ? 'var(--pct)' : '0%' }">
            </div>
        </div>

        @if ($showValue && $valueText)
            <span class="pq-progress-bar-value">{{ $valueText }}</span>
        @endif
    </div>

</div>
