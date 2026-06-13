@extends('layouts.auth')

@section('title', 'Profilo')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Profilo</h1>

<form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', auth()->user()->name) }}"
            required
            autocomplete="name"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', auth()->user()->email) }}"
            required
            autocomplete="email"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors"
    >
        Salva modifiche
    </button>
</form>

<div class="mt-8 pt-6 border-t border-gray-200">
    <h2 class="text-base font-semibold text-gray-700 mb-3">Password</h2>
    <a
        href="{{ route('profile.password') }}"
        class="inline-block w-full text-center border border-gray-300 hover:border-gray-400 text-gray-700 font-medium py-2 px-4 rounded-lg text-sm transition-colors"
    >
        Cambia password
    </a>
</div>

<div class="mt-6 text-center">
    <a href="{{ route('dashboard') }}" class="text-sm text-orange-600 hover:underline">Torna alla dashboard</a>
</div>
@endsection
