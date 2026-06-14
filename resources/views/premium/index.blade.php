@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="text-center mb-10">
        <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide mb-3">Premium</span>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.premium_title') }}</h1>
        <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">{{ __('messages.premium_subtitle') }}</p>
    </div>

    {{-- Free vs Premium comparison --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        {{-- Free plan --}}
        <div class="border border-gray-200 rounded-xl p-6 bg-white shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl font-bold text-gray-700">Free</span>
            </div>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_sync') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_dashboard') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_activities') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_charts') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="text-gray-400">{{ __('messages.premium_feat_trends') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="text-gray-400">{{ __('messages.premium_feat_compare') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="text-gray-400">{{ __('messages.premium_feat_yoy') }}</span>
                </li>
            </ul>
        </div>

        {{-- Premium plan --}}
        <div class="border-2 border-amber-400 rounded-xl p-6 bg-amber-50 shadow-sm relative">
            <div class="absolute -top-3 right-6">
                <span class="bg-amber-400 text-white text-xs font-bold px-3 py-1 rounded-full">★ Premium</span>
            </div>
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl font-bold text-amber-700">Premium</span>
            </div>
            <ul class="space-y-3 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_sync') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_dashboard') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_activities') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_charts') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_trends') }}</strong>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_compare') }}</strong>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_yoy') }}</strong>
                </li>
            </ul>
        </div>
    </div>

    {{-- CTA --}}
    <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.premium_cta_title') }}</h2>
        <p class="text-gray-600 mb-4">{{ __('messages.premium_cta_body') }}</p>
        <x-button
            href="mailto:{{ config('app.admin_email', 'admin@pemiq.com') }}?subject={{ urlencode(__('messages.premium_email_subject')) }}"
            size="lg"
        >
            {{ __('messages.premium_cta_button') }}
        </x-button>
        <p class="mt-4 text-xs text-gray-400">{{ __('messages.premium_self_service_note') }}</p>
    </div>

</div>
@endsection
