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
            <div class="flex items-center justify-between">
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
