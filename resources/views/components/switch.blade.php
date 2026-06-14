@props([
    'label'   => '',
    'name'    => '',
    'checked' => false,
])

<div x-data="{ on: @js((bool) $checked) }" class="pq-switch">
    <input type="hidden" name="{{ $name }}" :value="on ? '1' : '0'">
    <button type="button"
            role="switch"
            :aria-checked="on.toString()"
            @click="on = !on"
            class="pq-switch-track"
            :class="{ 'pq-switch-on': on }">
        <span class="pq-switch-thumb"></span>
    </button>
    @if($label)
        <span class="pq-switch-label">{{ $label }}</span>
    @endif
</div>
