@props([
    'label' => null,
    'size'  => 'md',
    'hint'  => null,
    'error' => null,
])

@php
    $selectId  = $attributes->get('id') ?? $attributes->get('name') ?? uniqid('select-');
    $hasError  = !empty($error);
    $selectClasses = 'pq-select'
        . ($size === 'sm' ? ' pq-select-sm' : '')
        . ($hasError ? ' pq-select-error' : '');
@endphp

<div class="pq-field">
    @if($label)
        <label for="{{ $selectId }}" class="pq-field-label">{{ $label }}</label>
    @endif

    <select
        id="{{ $selectId }}"
        {{ $attributes->except(['id'])->class([$selectClasses]) }}
    >
        {{ $slot }}
    </select>

    @if($hint && !$hasError)
        <p class="pq-field-hint">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="pq-field-error">{{ $error }}</p>
    @endif
</div>
