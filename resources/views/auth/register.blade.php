@extends('layouts.auth')

@section('title', 'Registrazione')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Crea il tuo account</h1>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-4">
        <x-form.input
            label="Nome"
            type="text"
            name="name"
            id="name"
            :value="old('name')"
            required
            autofocus
            autocomplete="name"
            :error="$errors->first('name')"
        />
    </div>

    <div class="mb-4">
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
    </div>

    <div class="mb-4">
        <x-form.input
            label="Password"
            type="password"
            name="password"
            id="password"
            required
            autocomplete="new-password"
            :error="$errors->first('password')"
        />
    </div>

    <div class="mb-6">
        <x-form.input
            label="Conferma password"
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            required
            autocomplete="new-password"
        />
    </div>

    <x-button type="submit" fullWidth>Registrati</x-button>
</form>

<p class="mt-4 text-center text-sm text-gray-600">
    Hai già un account?
    <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Accedi</a>
</p>
@endsection
