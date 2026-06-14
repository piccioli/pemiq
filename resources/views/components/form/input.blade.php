@props([
    'label'    => null,
    'type'     => 'text',
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    $inputId = $attributes->get('id') ?? $attributes->get('name') ?? uniqid('input-');
    $hasError = !empty($error);
    $inputClasses = 'pq-input' . ($hasError ? ' pq-input-error' : '');
@endphp

<div class="pq-field">
    @if($label)
        <label for="{{ $inputId }}" class="pq-field-label">
            {{ $label }}
            @if($required)
                <span class="pq-field-required" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="pq-field-control">
        @isset($leadingIcon)
            <span class="pq-field-icon">{{ $leadingIcon }}</span>
        @endisset

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            {{ $attributes->except(['id'])->class([$inputClasses, 'pq-field-icon-pad' => isset($leadingIcon)]) }}
        >
    </div>

    @if($hint && !$hasError)
        <p class="pq-field-hint">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="pq-field-error">{{ $error }}</p>
    @endif
</div>
