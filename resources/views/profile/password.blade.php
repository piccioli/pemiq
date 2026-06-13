@extends('layouts.auth')

@section('title', 'Cambia password')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Cambia password</h1>

<form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password attuale</label>
        <input
            type="password"
            id="current_password"
            name="current_password"
            required
            autocomplete="current-password"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nuova password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="new-password"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Conferma nuova password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            required
            autocomplete="new-password"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors"
    >
        Aggiorna password
    </button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('dashboard') }}" class="text-sm text-orange-600 hover:underline">Torna alla dashboard</a>
</div>
@endsection
