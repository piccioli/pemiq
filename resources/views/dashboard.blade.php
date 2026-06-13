@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600">Benvenuto, {{ auth()->user()->name }}!</p>

    {{-- Strava Connection Status --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Connessione Strava</h2>

        @if ($stravaAccount && $stravaAccount->connection_status === 'connected')
            <div class="flex items-center justify-between flex-wrap gap-3" x-data="{ confirmDisconnect: false }">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Connesso
                    </span>
                    @if ($stravaAccount->last_sync_at)
                        <span class="text-sm text-gray-500">
                            Ultima sync: {{ $stravaAccount->last_sync_at->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <span class="text-sm text-gray-500">Nessuna sincronizzazione ancora</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('strava.sync-historical') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition text-sm">
                            Sincronizza attività storiche
                        </button>
                    </form>
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
                <p class="text-gray-600">Collega il tuo account Strava per importare le attività.</p>
                <a href="{{ route('strava.redirect') }}"
                   class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                    Collega Strava
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
