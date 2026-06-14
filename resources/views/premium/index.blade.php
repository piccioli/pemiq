@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <x-page-header
        eyebrow="Premium"
        :title="__('messages.premium_title')"
        :subtitle="__('messages.premium_subtitle')"
    />

    {{-- Free vs Premium comparison --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        {{-- Free plan --}}
        <x-card padding="lg">
            <div class="flex items-center gap-2 mb-4">
                <span style="font-size: var(--fs-2xl); font-weight: 700; color: var(--text)">Free</span>
            </div>
            <ul class="space-y-3" style="font-size: var(--fs-sm); color: var(--text-muted)">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_sync') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_dashboard') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_activities') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_charts') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span style="color: var(--text-faint)">{{ __('messages.premium_feat_trends') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span style="color: var(--text-faint)">{{ __('messages.premium_feat_compare') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span style="color: var(--text-faint)">{{ __('messages.premium_feat_yoy') }}</span>
                </li>
            </ul>
        </x-card>

        {{-- Premium plan --}}
        <x-card padding="lg" :accent="true" style="position: relative">
            <div style="position: absolute; top: -12px; right: 24px;">
                <x-badge variant="zone-4">★ Premium</x-badge>
            </div>
            <div class="flex items-center gap-2 mb-4">
                <span style="font-size: var(--fs-2xl); font-weight: 700; color: var(--zone-4)">Premium</span>
            </div>
            <ul class="space-y-3" style="font-size: var(--fs-sm); color: var(--text)">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_sync') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_dashboard') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_activities') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--success)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('messages.premium_free_charts') }}
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--zone-4)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_trends') }}</strong>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--zone-4)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_compare') }}</strong>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--zone-4)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <strong>{{ __('messages.premium_feat_yoy') }}</strong>
                </li>
            </ul>
        </x-card>
    </div>

    {{-- CTA --}}
    <x-card padding="lg" class="text-center">
        <h2 style="font-size: var(--fs-xl); font-weight: 600; color: var(--text-strong); margin-bottom: 8px">{{ __('messages.premium_cta_title') }}</h2>
        <p style="color: var(--text-muted); margin-bottom: 16px">{{ __('messages.premium_cta_body') }}</p>
        <x-button
            href="mailto:{{ config('app.admin_email', 'admin@pemiq.com') }}?subject={{ urlencode(__('messages.premium_email_subject')) }}"
            variant="primary"
            size="lg"
        >
            {{ __('messages.premium_cta_button') }}
        </x-button>
        <p style="margin-top: 16px; font-size: var(--fs-xs); color: var(--text-faint)">{{ __('messages.premium_self_service_note') }}</p>
    </x-card>

</div>
@endsection
