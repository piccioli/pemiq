@extends('layouts.auth')

@section('title', 'Accedi')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Accedi al tuo account</h1>

<form method="POST" action="{{ route('login') }}" class="space-y-5">
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
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
        >
    </div>

    <div class="flex items-center">
        <input
            type="checkbox"
            id="remember"
            name="remember"
            class="h-4 w-4 text-orange-600 border-gray-300 rounded"
        >
        <label for="remember" class="ml-2 text-sm text-gray-600">Ricordami</label>
    </div>

    <button
        type="submit"
        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors"
    >
        Accedi
    </button>
</form>

<div class="mt-6 text-center space-y-2">
    <p class="text-sm text-gray-600">
        <a href="{{ route('password.request') }}" class="text-orange-600 hover:underline">Password dimenticata?</a>
    </p>
    <p class="text-sm text-gray-600">
        <a href="{{ route('register') }}" class="text-orange-600 hover:underline">Crea un account</a>
    </p>
</div>
@endsection
