@props([
    'label'        => '',
    'value'        => '',
    'unit'         => null,
    'delta'        => null,
    'deltaDir'     => 'flat',
    'deltaContext' => null,
    'accent'       => 'brand',
])

@php
    $accentColor = match($accent) {
        'zone-1', 'zone1' => 'var(--zone-1)',
        'zone-2', 'zone2' => 'var(--zone-2)',
        'zone-3', 'zone3' => 'var(--zone-3)',
        'zone-4', 'zone4' => 'var(--zone-4)',
        'zone-5', 'zone5' => 'var(--zone-5)',
        'success' => 'var(--success)',
        'warning' => 'var(--warning)',
        'danger'  => 'var(--danger)',
        'info'    => 'var(--info)',
        default   => 'var(--brand)',
    };
    $accentSoft = match($accent) {
        'zone-1', 'zone1' => 'var(--zone-1-soft)',
        'zone-2', 'zone2' => 'var(--zone-2-soft)',
        'zone-3', 'zone3' => 'var(--zone-3-soft)',
        'zone-4', 'zone4' => 'var(--zone-4-soft)',
        'zone-5', 'zone5' => 'var(--zone-5-soft)',
        'success' => 'var(--success-soft)',
        'warning' => 'var(--warning-soft)',
        'danger'  => 'var(--danger-soft)',
        'info'    => 'var(--info-soft)',
        default   => 'var(--brand-soft)',
    };
    [$deltaIcon, $deltaColor] = match($deltaDir) {
        'up'   => ['▲', 'var(--success)'],
        'down' => ['▼', 'var(--danger)'],
        default => ['—', 'var(--text-faint)'],
    };
@endphp

<x-card padding="md">
    <div class="pq-metric-tile">

        @isset($icon)
            <div class="pq-metric-tile-icon" style="color: {{ $accentColor }}; background: {{ $accentSoft }}">
                {{ $icon }}
            </div>
        @endisset

        <p class="pq-metric-tile-label">{{ $label }}</p>

        <p class="pq-metric">
            {{ $value }}@if ($unit)<span class="pq-metric-tile-unit"> {{ $unit }}</span>@endif
        </p>

        @if ($delta)
            <p class="pq-metric-tile-delta" style="color: {{ $deltaColor }}">
                {{ $deltaIcon }}
                <span>{{ $delta }}</span>
                @if ($deltaContext)
                    <span class="pq-metric-tile-delta-ctx">{{ $deltaContext }}</span>
                @endif
            </p>
        @endif

    </div>
</x-card>
