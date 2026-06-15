<x-card padding="lg">
    @if ($stravaAccount && $syncStatus === 'running')
        <span wire:poll.5000ms hidden></span>
    @endif
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Connessione Strava</h2>

    @if ($syncStatus === 'running')
        <x-alert variant="info" style="margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg class="animate-spin" style="width: 14px; height: 14px; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Sincronizzazione in corso ({{ $syncActivitiesImported }} attività importate)
            </div>
        </x-alert>
    @elseif ($syncStatus === 'completed')
        <x-alert variant="success" style="margin-bottom: 1rem;">Sincronizzazione completata</x-alert>
    @elseif ($syncStatus === 'failed')
        <x-alert variant="danger" style="margin-bottom: 1rem;">
            Sincronizzazione fallita — {{ \Illuminate\Support\Str::limit($syncErrorMessage, 100) }}
        </x-alert>
    @elseif ($syncMessage)
        <x-alert variant="success" style="margin-bottom: 1rem;">{{ $syncMessage }}</x-alert>
    @endif

    @if ($stravaAccount)
        <div class="flex items-center justify-between flex-wrap gap-3" x-data="{ confirmDisconnect: false }">
            <div class="flex items-center gap-3">
                <x-badge variant="success" size="md" :dot="true">Connesso</x-badge>
                @if ($stravaAccount->last_sync_at)
                    <span class="text-sm text-gray-500">
                        Ultima sync: {{ fmt_date($stravaAccount->last_sync_at, 'd M Y H:i') }}
                    </span>
                @else
                    <span class="text-sm text-gray-500">Nessuna sincronizzazione ancora</span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if ($syncStatus === 'running')
                    <x-badge variant="info" size="md">
                        <svg class="animate-spin h-3 w-3 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Sincronizzazione in corso...
                    </x-badge>
                @else
                    <x-button wire:click="startHistoricalSync" wire:loading.attr="disabled">
                        Sincronizza attività storiche
                    </x-button>
                @endif

                <x-button variant="danger" @click="confirmDisconnect = true">
                    Scollega Strava
                </x-button>

                {{-- Alpine.js confirmation dialog --}}
                <div x-show="confirmDisconnect"
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div style="background: var(--surface-2); border: 1px solid var(--border-strong); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 1.5rem; max-width: 24rem; width: 100%; margin: 0 1rem;">
                        <h3 style="font-size: var(--fs-lg); font-weight: 600; color: var(--text-strong); margin-bottom: 0.5rem;">Sei sicuro?</h3>
                        <p style="color: var(--text-muted); font-size: var(--fs-sm); margin-bottom: 1.5rem;">Le attività importate rimarranno.</p>
                        <div class="flex gap-3 justify-end">
                            <x-button variant="secondary" @click="confirmDisconnect = false">
                                Annulla
                            </x-button>
                            <form action="{{ route('strava.disconnect') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger">
                                    Scollega
                                </x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @if ($connectionStatus === 'error')
                <x-alert variant="warning">
                    Il token Strava è scaduto — riconnetti il tuo account per riprendere la sincronizzazione.
                </x-alert>
            @else
                <p style="color: var(--text-muted); font-size: var(--fs-sm);">Collega il tuo account Strava per importare le attività.</p>
            @endif
            <div>
                <x-button href="{{ route('strava.redirect') }}">
                    {{ $connectionStatus === 'error' ? 'Riconnetti Strava' : 'Collega Strava' }}
                </x-button>
            </div>
        </div>
    @endif
</x-card>
