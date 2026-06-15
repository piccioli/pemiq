@extends('layouts.auth')

@section('title', 'Password dimenticata')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-2">Password dimenticata?</h1>
<p class="text-sm text-gray-600 mb-6">Inserisci la tua email e ti invieremo un link per reimpostare la password.</p>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <x-form.input
        label="Email"
        type="email"
        name="email"
        id="email"
        :value="old('email')"
        required
        autocomplete="email"
        autofocus
        :error="$errors->first('email')"
    />

    <x-button type="submit" fullWidth>Invia link di reset</x-button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="text-sm text-orange-600 hover:underline">Torna al login</a>
</div>
@endsection
