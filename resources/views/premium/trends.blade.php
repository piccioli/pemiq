@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header :title="__('messages.trend_page_title')" />

    @livewire('premium.trend-chart')
</div>
@endsection
