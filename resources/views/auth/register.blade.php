@extends('layouts.auth')

@section('title', 'Registrazione')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Crea il tuo account</h1>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}">
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Conferma password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>

    <button type="submit"
        class="w-full py-2 px-4 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
        Registrati
    </button>
</form>

<p class="mt-4 text-center text-sm text-gray-600">
    Hai già un account?
    <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Accedi</a>
</p>
@endsection
