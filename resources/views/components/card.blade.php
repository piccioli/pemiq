@props([
    'eyebrow'     => null,
    'title'       => null,
    'padding'     => 'md',
    'interactive' => false,
    'accent'      => false,
])

@php
    $padClass = match($padding) {
        'none' => '',
        'sm'   => 'pq-card-pad-sm',
        'lg'   => 'pq-card-pad-lg',
        default => 'pq-card-pad-md',
    };
    $classes = 'pq-card'
        . ($padClass ? ' ' . $padClass : '')
        . ($interactive ? ' pq-card-interactive' : '')
        . ($accent ? ' pq-card-accent' : '');
@endphp

<div {{ $attributes->class($classes) }}>
    @if ($eyebrow || $title || isset($action))
        <div class="pq-card-header">
            <div>
                @if ($eyebrow)
                    <p class="pq-eyebrow">{{ $eyebrow }}</p>
                @endif
                @if ($title)
                    <h2 class="pq-card-title">{{ $title }}</h2>
                @endif
            </div>
            @isset($action)
                <div class="pq-card-action">{{ $action }}</div>
            @endisset
        </div>
    @endif
    {{ $slot }}
</div>
