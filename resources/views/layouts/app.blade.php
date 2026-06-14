<!DOCTYPE html>
<html lang="it" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEMIQ — @yield('title', 'App')</title>

    {{-- Google Fonts: Space Grotesk (display), Inter (body), DM Mono (mono) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

</head>
<body class="min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- ─── Topbar ───────────────────────────────────────────────── --}}
    <header
        x-data="{ userOpen: false }"
        style="
            position: sticky;
            top: 0;
            z-index: 50;
            height: 60px;
            background: color-mix(in srgb, var(--bg) 80%, transparent);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        "
    >
        <div style="max-width: 1280px; margin: 0 auto; height: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; gap: 1rem;">

            {{-- Left: hamburger (mobile) + logo --}}
            <div style="display: flex; align-items: center; gap: 0.75rem;">

                {{-- Mobile sidebar toggle --}}
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden"
                    style="color: var(--text-muted); padding: 6px; border-radius: var(--radius-md); background: none; border: none; cursor: pointer;"
                    aria-label="Menu"
                >
                    <svg x-show="!sidebarOpen" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="sidebarOpen" x-cloak style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Logo: Space Grotesk 700, brand color --}}
                <a
                    href="{{ route('dashboard') }}"
                    style="
                        font-family: var(--font-display);
                        font-weight: 700;
                        font-size: 1.25rem;
                        color: var(--brand);
                        text-decoration: none;
                        letter-spacing: -0.02em;
                    "
                >PEMIQ</a>
            </div>

            {{-- Right: premium indicator + user menu --}}
            <div style="display: flex; align-items: center; gap: 0.75rem;">

                @php
                    $isPremiumActive = auth()->user()->is_premium && (!auth()->user()->premium_expires_at || auth()->user()->premium_expires_at->isFuture());
                @endphp

                {{-- Premium badge (zone-4 amber) or upgrade link (accent teal) --}}
                @if($isPremiumActive)
                    <span style="
                        display: inline-flex;
                        align-items: center;
                        gap: 4px;
                        padding: 3px 10px;
                        border-radius: var(--radius-pill);
                        background: color-mix(in srgb, var(--zone-4) 15%, transparent);
                        color: var(--zone-4);
                        font-size: var(--fs-xs);
                        font-weight: 600;
                        line-height: 1.4;
                    ">★ Premium</span>
                @else
                    <a
                        href="/premium"
                        style="
                            font-size: var(--fs-sm);
                            font-weight: 500;
                            color: var(--accent);
                            text-decoration: none;
                            transition: opacity var(--dur-fast) var(--ease-out);
                        "
                        onmouseover="this.style.opacity='0.8'"
                        onmouseout="this.style.opacity='1'"
                    >Passa a Premium</a>
                @endif

                {{-- User dropdown --}}
                <div style="position: relative;">
                    <button
                        @click="userOpen = !userOpen"
                        @click.outside="userOpen = false"
                        style="
                            display: flex;
                            align-items: center;
                            gap: 6px;
                            color: var(--text-muted);
                            font-size: var(--fs-sm);
                            font-weight: 500;
                            background: none;
                            border: none;
                            cursor: pointer;
                            padding: 6px 10px;
                            border-radius: var(--radius-md);
                            transition: color var(--dur-fast) var(--ease-out), background var(--dur-fast) var(--ease-out);
                        "
                        :style="userOpen ? 'color: var(--text-strong); background: var(--surface-2);' : ''"
                    >
                        <span style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</span>
                        <svg style="width: 14px; height: 14px; transition: transform var(--dur-fast) var(--ease-out);" :style="userOpen ? 'transform: rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div
                        x-show="userOpen"
                        x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="
                            position: absolute;
                            right: 0;
                            top: calc(100% + 6px);
                            min-width: 180px;
                            background: var(--surface-2);
                            border: 1px solid var(--border-strong);
                            border-radius: var(--radius-lg);
                            box-shadow: var(--shadow-md);
                            z-index: 60;
                            padding: 4px;
                        "
                    >
                        <a href="{{ route('profile.show') }}" class="pq-dropdown-item">Profilo</a>
                        <div style="height: 1px; background: var(--border); margin: 4px 0;"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="pq-dropdown-item">Esci</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- ─── App shell: sidebar + content ────────────────────────── --}}
    <div style="display: flex; min-height: calc(100vh - 60px);">

        {{-- Desktop sidebar (hidden on mobile) --}}
        <aside
            class="hidden md:block"
            style="
                width: 256px;
                flex-shrink: 0;
                background: var(--bg-elevated);
                border-right: 1px solid var(--border);
                position: sticky;
                top: 60px;
                height: calc(100vh - 60px);
                overflow-y: auto;
            "
        >
            @include('partials.sidebar-nav')
        </aside>

        {{-- Mobile sidebar overlay --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40;"
            class="md:hidden"
        ></div>

        {{-- Mobile sidebar drawer --}}
        <aside
            x-show="sidebarOpen"
            x-cloak
            style="
                position: fixed;
                top: 60px;
                left: 0;
                width: 256px;
                height: calc(100vh - 60px);
                background: var(--bg-elevated);
                border-right: 1px solid var(--border);
                overflow-y: auto;
                z-index: 45;
            "
            class="md:hidden"
        >
            @include('partials.sidebar-nav')
        </aside>

        {{-- Main content area --}}
        <div style="flex: 1; min-width: 0; display: flex; flex-direction: column;">

            {{-- Impersonation banner --}}
            @include('partials.impersonation-banner')

            {{-- Flash messages --}}
            @if(session('status') || session('success') || session('error') || $errors->any())
            <div style="padding: 1rem 1.5rem 0; display: flex; flex-direction: column; gap: 0.5rem; max-width: 1280px; width: 100%; margin: 0 auto; box-sizing: border-box;">

                @if(session('status') || session('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 1rem;
                        padding: 12px 16px;
                        background: var(--success-soft);
                        border: 1px solid color-mix(in srgb, var(--success) 30%, transparent);
                        border-radius: var(--radius-md);
                        color: var(--success);
                        font-size: var(--fs-sm);
                    "
                >
                    <span>{{ session('status') ?? session('success') }}</span>
                    <button @click="show = false" style="color: inherit; background: none; border: none; cursor: pointer; flex-shrink: 0; padding: 0;" aria-label="Chiudi">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 1rem;
                        padding: 12px 16px;
                        background: var(--danger-soft);
                        border: 1px solid color-mix(in srgb, var(--danger) 30%, transparent);
                        border-radius: var(--radius-md);
                        color: var(--danger);
                        font-size: var(--fs-sm);
                    "
                >
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" style="color: inherit; background: none; border: none; cursor: pointer; flex-shrink: 0; padding: 0;" aria-label="Chiudi">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="
                        display: flex;
                        align-items: flex-start;
                        justify-content: space-between;
                        gap: 1rem;
                        padding: 12px 16px;
                        background: var(--danger-soft);
                        border: 1px solid color-mix(in srgb, var(--danger) 30%, transparent);
                        border-radius: var(--radius-md);
                        color: var(--danger);
                        font-size: var(--fs-sm);
                    "
                >
                    <ul style="list-style: disc; list-style-position: inside; display: flex; flex-direction: column; gap: 2px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button @click="show = false" style="color: inherit; background: none; border: none; cursor: pointer; flex-shrink: 0; padding: 0;" aria-label="Chiudi">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @endif

            </div>
            @endif

            {{-- Main content --}}
            <main style="flex: 1; padding: 2rem 1.5rem; max-width: 1280px; width: 100%; margin: 0 auto; box-sizing: border-box;">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer style="border-top: 1px solid var(--border); padding: 1rem 1.5rem; margin-top: auto;">
                <p style="text-align: center; font-size: var(--fs-xs); color: var(--text-faint);">PEMIQ &copy; {{ date('Y') }}</p>
            </footer>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () { lucide.createIcons(); });
        document.addEventListener('livewire:navigated', function () { lucide.createIcons(); });
    </script>
</body>
</html>
