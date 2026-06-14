<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEMIQ — @yield('title', 'App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-orange-600 tracking-tight">PEMIQ</a>
                </div>

                {{-- Desktop center nav --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}"
                       class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-orange-600' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('activities.index') }}"
                       class="text-sm font-medium {{ request()->routeIs('activities.*') ? 'text-orange-600' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
                        Attività
                    </a>
                </div>

                {{-- Desktop user menu --}}
                <div class="hidden md:flex items-center" x-data="{ userOpen: false }">
                    <div class="relative">
                        <button
                            @click="userOpen = !userOpen"
                            @click.outside="userOpen = false"
                            class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none"
                        >
                            <span class="max-w-[160px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="userOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50 py-1"
                            style="display: none;"
                        >
                            <a href="{{ route('profile.show') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                Profilo
                            </a>
                            <div class="my-1 border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Esci
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Mobile hamburger --}}
                <div class="md:hidden">
                    <button
                        @click="open = !open"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none"
                        aria-label="Menu"
                    >
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" class="md:hidden border-t border-gray-200 bg-white" style="display: none;">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('activities.index') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('activities.*') ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:bg-gray-50' }}">
                    Attività
                </a>
                <a href="{{ route('profile.show') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Profilo
                </a>
                <div class="pt-2 border-t border-gray-100">
                    <p class="px-3 py-1 text-xs text-gray-500 truncate">{{ auth()->user()->name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Esci
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Impersonation banner --}}
    @include('partials.impersonation-banner')

    {{-- Flash messages --}}
    @if(session('status') || session('success') || session('error') || $errors->any())
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4 space-y-2">

        @if(session('status') || session('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-start justify-between p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm"
        >
            <span>{{ session('status') ?? session('success') }}</span>
            <button @click="show = false" class="ml-4 text-green-600 hover:text-green-800 flex-shrink-0" aria-label="Chiudi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="flex items-start justify-between p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm"
        >
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="ml-4 text-red-600 hover:text-red-800 flex-shrink-0" aria-label="Chiudi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="flex items-start justify-between p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm"
        >
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button @click="show = false" class="ml-4 text-red-600 hover:text-red-800 flex-shrink-0" aria-label="Chiudi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

    </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-400">PEMIQ &copy; {{ date('Y') }}</p>
        </div>
    </footer>

</body>
</html>
