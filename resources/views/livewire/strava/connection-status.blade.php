<x-card padding="lg">
    @if ($stravaAccount && $syncStatus === 'running')
        <span wire:poll.5000ms hidden></span>
    @endif
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Connessione Strava</h2>

    @if ($syncStatus === 'running')
        <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-blue-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Sincronizzazione in corso ({{ $syncActivitiesImported }} attività importate)
        </div>
    @elseif ($syncStatus === 'completed')
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            Sincronizzazione completata
        </div>
    @elseif ($syncStatus === 'failed')
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            Sincronizzazione fallita — {{ \Illuminate\Support\Str::limit($syncErrorMessage, 100) }}
        </div>
    @elseif ($syncMessage)
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            {{ $syncMessage }}
        </div>
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
        <div class="flex items-center gap-4">
            @if ($connectionStatus === 'error')
                <div class="flex items-center gap-2 text-amber-700">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p>Il token Strava è scaduto — riconnetti il tuo account per riprendere la sincronizzazione.</p>
                </div>
            @else
                <p style="color: var(--text-muted); font-size: var(--fs-sm);">Collega il tuo account Strava per importare le attività.</p>
            @endif
            <x-button href="{{ route('strava.redirect') }}">
                {{ $connectionStatus === 'error' ? 'Riconnetti Strava' : 'Collega Strava' }}
            </x-button>
        </div>
    @endif
</x-card>
