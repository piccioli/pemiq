@props([
    'variant' => 'outline',
    'size'    => 'sm',
    'dot'     => false,
])

@php
    $classes = 'pq-badge pq-badge-' . $variant . ' pq-badge-' . $size;
@endphp

<span {{ $attributes->class($classes) }}>
    @if($dot)
        <span class="pq-badge-dot-indicator"></span>
    @endif
    {{ $slot }}
</span>
