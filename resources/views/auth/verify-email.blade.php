@extends('layouts.auth')

@section('title', 'Verifica Email')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-4">Verifica il tuo indirizzo email</h1>

<p class="text-gray-600 mb-6 text-sm leading-relaxed">
    Prima di continuare, controlla la tua casella email e clicca sul link di verifica che ti abbiamo inviato.
    Se non hai ricevuto l'email, puoi richiederne un'altra.
</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit"
        class="w-full py-2 px-4 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
        Reinvia email di verifica
    </button>
</form>

<form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button type="submit"
        class="w-full py-2 px-4 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition text-sm">
        Esci dall'account
    </button>
</form>
@endsection
