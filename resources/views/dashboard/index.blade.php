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

    {{-- Grafico Analisi Annuale --}}
    @livewire('dashboard.annual-analysis-chart')

    {{-- Analisi per Mese --}}
    @livewire('dashboard.monthly-analysis')

    {{-- Grafico Analisi Mensile --}}
    @livewire('dashboard.monthly-analysis-chart')

    {{-- Distribuzione per Sport --}}
    @livewire('dashboard.sport-distribution')

    {{-- Grafico Distribuzione Sport --}}
    @livewire('dashboard.sport-distribution-chart')

    {{-- Tempo in Zona --}}
    @livewire('dashboard.zone-distribution')
</div>
@endsection
