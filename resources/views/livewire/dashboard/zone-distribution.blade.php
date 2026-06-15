<x-card padding="md">
    <div class="flex items-center justify-between mb-3">
        <h2 style="font: var(--text-label); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; font-size: 11px;">
            {{ __('messages.zone_distribution_title') }}
        </h2>
        <x-badge variant="outline">{{ __('messages.zone_distribution_mock') }}</x-badge>
    </div>

    <x-zone-bar :data="$zoneData" />
</x-card>
