@extends('layouts.auth')

@section('title', 'Accedi')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Accedi al tuo account</h1>

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <x-form.input
        label="Email"
        type="email"
        name="email"
        id="email"
        :value="old('email')"
        required
        autocomplete="email"
        :error="$errors->first('email')"
    />

    <x-form.input
        label="Password"
        type="password"
        name="password"
        id="password"
        required
        autocomplete="current-password"
        :error="$errors->first('password')"
    />

    <div class="flex items-center">
        <input
            type="checkbox"
            id="remember"
            name="remember"
            class="h-4 w-4 text-orange-600 border-gray-300 rounded"
        >
        <label for="remember" class="ml-2 text-sm text-gray-600">Ricordami</label>
    </div>

    <x-button type="submit" fullWidth>Accedi</x-button>
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
