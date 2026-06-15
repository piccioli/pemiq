@extends('layouts.app')

@section('title', 'Profilo')

@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Profilo</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-form.input
                label="Nome"
                type="text"
                name="name"
                id="name"
                :value="old('name', auth()->user()->name)"
                required
                autocomplete="name"
                :error="$errors->first('name')"
            />

            <x-form.input
                label="Email"
                type="email"
                name="email"
                id="email"
                :value="old('email', auth()->user()->email)"
                required
                autocomplete="email"
                :error="$errors->first('email')"
            />

            <x-form.select :label="__('messages.language')" name="locale" id="locale">
                <option value="it" {{ old('locale', auth()->user()->locale) === 'it' ? 'selected' : '' }}>{{ __('messages.italian') }}</option>
                <option value="en" {{ old('locale', auth()->user()->locale) === 'en' ? 'selected' : '' }}>{{ __('messages.english') }}</option>
            </x-form.select>

            <x-switch
                name="email_notifications"
                :label="__('messages.email_notifications_label')"
                :checked="(bool) old('email_notifications', auth()->user()->email_notifications)"
            />

            <x-button type="submit" fullWidth>Salva modifiche</x-button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Password</h2>
            <x-button href="{{ route('profile.password') }}" variant="secondary" fullWidth>
                Cambia password
            </x-button>
        </div>

        @php
            $isPremiumProfile = auth()->user()->is_premium && (!auth()->user()->premium_expires_at || auth()->user()->premium_expires_at->isFuture());
        @endphp
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Account</h2>
            @if($isPremiumProfile)
                <div class="flex items-center space-x-2">
                    <x-badge variant="zone4" size="md">★ Premium</x-badge>
                    @if(auth()->user()->premium_expires_at)
                        <span class="text-sm text-gray-500">scade il {{ fmt_date(auth()->user()->premium_expires_at, 'd M Y') }}</span>
                    @endif
                </div>
            @else
                <div class="flex flex-col space-y-2">
                    <p class="text-sm text-gray-500">Stai usando il piano <strong>Free</strong>.</p>
                    <x-button href="/premium" fullWidth>Passa a Premium</x-button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
