@php
    $isPremiumUser = isset($isPremiumActive)
        ? $isPremiumActive
        : (auth()->user()->is_premium && (!auth()->user()->premium_expires_at || auth()->user()->premium_expires_at->isFuture()));
@endphp

<nav style="padding: 1rem 0.75rem; display: flex; flex-direction: column; gap: 2px;">

    {{-- Core navigation --}}
    <a
        href="{{ route('dashboard') }}"
        class="pq-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
    >
        <i data-lucide="layout-dashboard" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
        Dashboard
    </a>

    <a
        href="{{ route('activities.index') }}"
        class="pq-nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}"
    >
        <i data-lucide="activity" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
        Attività
    </a>

    @if($isPremiumUser)
        {{-- Premium section header --}}
        <div style="margin: 16px 0 4px; padding: 0 12px;">
            <span class="pq-eyebrow">Premium</span>
        </div>

        <a
            href="{{ route('premium.trends') }}"
            class="pq-nav-link {{ request()->routeIs('premium.trends') ? 'active' : '' }}"
        >
            <i data-lucide="trending-up" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
            Trend
        </a>

        <a
            href="{{ route('premium.compare') }}"
            class="pq-nav-link {{ request()->routeIs('premium.compare') ? 'active' : '' }}"
        >
            <i data-lucide="git-compare-arrows" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
            Confronta
        </a>

        <a
            href="{{ route('premium.year-over-year') }}"
            class="pq-nav-link {{ request()->routeIs('premium.year-over-year') ? 'active' : '' }}"
        >
            <i data-lucide="calendar-range" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
            Anno su Anno
        </a>
    @endif

</nav>
