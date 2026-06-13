@extends('layouts.auth')

@section('title', 'Accedi')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Accedi al tuo account</h1>

<p class="text-sm text-gray-500 mb-4 bg-yellow-50 border border-yellow-200 rounded p-3">
    Il login sarà disponibile a breve.
</p>

<p class="mt-4 text-center text-sm text-gray-600">
    Non hai un account?
    <a href="{{ route('register') }}" class="text-orange-600 hover:underline">Registrati</a>
</p>
@endsection
