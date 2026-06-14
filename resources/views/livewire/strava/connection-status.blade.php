<div class="bg-white rounded-lg shadow p-6"
     @if ($stravaAccount && $syncStatus === 'running') wire:poll.5000ms @endif>
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
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Connesso
                </span>
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
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-500 font-medium rounded-lg text-sm cursor-not-allowed">
                        <svg class="animate-spin h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Sincronizzazione in corso...
                    </span>
                @else
                    <button wire:click="startHistoricalSync"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Sincronizza attività storiche
                    </button>
                @endif

                <button @click="confirmDisconnect = true"
                        class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition text-sm">
                    Scollega Strava
                </button>

                {{-- Alpine.js confirmation dialog --}}
                <div x-show="confirmDisconnect"
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sei sicuro?</h3>
                        <p class="text-gray-600 mb-6">Le attività importate rimarranno.</p>
                        <div class="flex gap-3 justify-end">
                            <button @click="confirmDisconnect = false"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                                Annulla
                            </button>
                            <form action="{{ route('strava.disconnect') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                                    Scollega
                                </button>
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
                <p class="text-gray-600">Collega il tuo account Strava per importare le attività.</p>
            @endif
            <a href="{{ route('strava.redirect') }}"
               class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition whitespace-nowrap">
                {{ $connectionStatus === 'error' ? 'Riconnetti Strava' : 'Collega Strava' }}
            </a>
        </div>
    @endif
</div>
