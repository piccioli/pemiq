@extends('layouts.auth')

@section('title', 'Password dimenticata')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-2">Password dimenticata?</h1>
<p class="text-sm text-gray-600 mb-6">Inserisci la tua email e ti invieremo un link per reimpostare la password.</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            autofocus
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors"
    >
        Invia link di reset
    </button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="text-sm text-orange-600 hover:underline">Torna al login</a>
</div>
@endsection
