@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Benvenuto, {{ auth()->user()->name }}!</p>
    </div>

    {{-- Connessione Strava --}}
    @livewire('strava.connection-status')

    {{-- Statistiche Generali --}}
    @livewire('dashboard.overview-stats')

    {{-- Analisi per Anno --}}
    @livewire('dashboard.annual-analysis')

    {{-- Analisi per Mese --}}
    @livewire('dashboard.monthly-analysis')

    {{-- Distribuzione per Sport --}}
    @livewire('dashboard.sport-distribution')
</div>
@endsection
