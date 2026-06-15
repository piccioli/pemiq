@props([
    'variant'   => 'primary',
    'size'      => 'md',
    'href'      => null,
    'disabled'  => false,
    'fullWidth' => false,
])

@php
    $classes = 'pq-btn pq-btn-' . $variant . ' pq-btn-' . $size . ($fullWidth ? ' pq-btn-full' : '');
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @isset($icon){{ $icon }}@endisset
        {{ $slot }}
        @isset($iconRight){{ $iconRight }}@endisset
    </a>
@elseif($href && $disabled)
    <span {{ $attributes->class($classes) }} aria-disabled="true">
        @isset($icon){{ $icon }}@endisset
        {{ $slot }}
        @isset($iconRight){{ $iconRight }}@endisset
    </span>
@else
    <button {{ $attributes->class($classes) }} @if($disabled) disabled @endif>
        @isset($icon){{ $icon }}@endisset
        {{ $slot }}
        @isset($iconRight){{ $iconRight }}@endisset
    </button>
@endif
