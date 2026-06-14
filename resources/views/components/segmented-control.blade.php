@props([
    'options'  => [],
    'name'     => null,
    'size'     => 'md',
    'selected' => '',
])

@php
    $normalizedOptions = array_map(function ($opt) {
        if (is_array($opt)) {
            return ['value' => (string) ($opt['value'] ?? $opt), 'label' => $opt['label'] ?? $opt['value'] ?? $opt];
        }
        return ['value' => (string) $opt, 'label' => (string) $opt];
    }, $options);
@endphp

<div
    x-data="{ current: @js((string) $selected) }"
    {{ $attributes->class(['pq-segmented-control', 'pq-segmented-sm' => $size === 'sm']) }}
>
    @foreach ($normalizedOptions as $opt)
        <button
            type="button"
            class="pq-segmented-option"
            :class="{ 'pq-segmented-option-active': current === @js($opt['value']) }"
            @if ($name)
                @click="current = @js($opt['value']); $wire.set(@js($name), @js($opt['value']))"
            @else
                @click="current = @js($opt['value'])"
            @endif
        >
            {{ $opt['label'] }}
        </button>
    @endforeach
</div>
