@props([
    'eyebrow'  => null,
    'title'    => '',
    'subtitle' => null,
])

<div {{ $attributes->class('pq-page-header') }}>
    <div class="pq-page-header-left">
        @if ($eyebrow)
            <p class="pq-eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="pq-page-header-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="pq-page-header-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="pq-page-header-actions">{{ $actions }}</div>
    @endisset
</div>
