@props([
    'selected'    => false,
    'removable'   => false,
    'interactive' => true,
    'removeHref'  => null,
])

@php
    $classes = 'pq-tag';
    if ($selected)     $classes .= ' pq-tag-selected';
    if (!$interactive) $classes .= ' pq-tag-static';
@endphp

<span {{ $attributes->class($classes) }}>
    @isset($icon)
        {{ $icon }}
    @endisset
    <span class="pq-tag-label">{{ $slot }}</span>
    @if ($removable)
        @if ($removeHref)
            <a href="{{ $removeHref }}" class="pq-tag-remove" aria-label="{{ __('messages.remove_filter') }}">×</a>
        @else
            <button type="button" class="pq-tag-remove" aria-label="{{ __('messages.remove_filter') }}">×</button>
        @endif
    @endif
</span>
