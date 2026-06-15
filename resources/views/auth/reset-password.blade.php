@extends('layouts.auth')

@section('title', 'Reimposta password')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Reimposta la password</h1>

<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <x-form.input
        label="Email"
        type="email"
        name="email"
        id="email"
        :value="old('email', $email)"
        required
        autocomplete="email"
        :error="$errors->first('email')"
    />

    <x-form.input
        label="Nuova password"
        type="password"
        name="password"
        id="password"
        required
        autocomplete="new-password"
        :error="$errors->first('password')"
    />

    <x-form.input
        label="Conferma nuova password"
        type="password"
        name="password_confirmation"
        id="password_confirmation"
        required
        autocomplete="new-password"
    />

    <x-button type="submit" fullWidth>Reimposta password</x-button>
</form>
@endsection
