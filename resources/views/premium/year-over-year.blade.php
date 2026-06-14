@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('messages.yoy_page_title') }}</h1>
    @livewire('premium.year-over-year-chart')
</div>
@endsection
