@extends('layouts.app')

@section('title', 'Profilo')

@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Profilo</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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

            <div>
                <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.language') }}</label>
                <select
                    id="locale"
                    name="locale"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
                    <option value="it" {{ old('locale', auth()->user()->locale) === 'it' ? 'selected' : '' }}>{{ __('messages.italian') }}</option>
                    <option value="en" {{ old('locale', auth()->user()->locale) === 'en' ? 'selected' : '' }}>{{ __('messages.english') }}</option>
                </select>
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

        @php
            $isPremiumProfile = auth()->user()->is_premium && (!auth()->user()->premium_expires_at || auth()->user()->premium_expires_at->isFuture());
        @endphp
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Account</h2>
            @if($isPremiumProfile)
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                        ★ Premium
                    </span>
                    @if(auth()->user()->premium_expires_at)
                        <span class="text-sm text-gray-500">scade il {{ fmt_date(auth()->user()->premium_expires_at, 'd M Y') }}</span>
                    @endif
                </div>
            @else
                <div class="flex flex-col space-y-2">
                    <p class="text-sm text-gray-500">Stai usando il piano <strong>Free</strong>.</p>
                    <a href="/premium"
                       class="inline-block w-full text-center bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors">
                        Passa a Premium
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
