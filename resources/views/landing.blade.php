<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEMIQ — Il tuo compagno di allenamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white min-h-screen flex flex-col">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-gray-100" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <span class="text-2xl font-bold text-orange-600 tracking-tight">PEMIQ</span>
                </div>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center space-x-3">
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 rounded-lg transition-colors">
                        Accedi
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-medium bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg transition-colors shadow-sm">
                        Registrati gratis
                    </a>
                </div>

                {{-- Mobile hamburger --}}
                <div class="md:hidden">
                    <button @click="open = !open"
                            class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" class="md:hidden border-t border-gray-100 bg-white" style="display:none;">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('login') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Accedi
                </a>
                <a href="{{ route('register') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium bg-orange-600 text-white text-center rounded-lg">
                    Registrati gratis
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="bg-gradient-to-b from-orange-50 to-white py-16 sm:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center space-x-2 bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full mb-6">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Connesso a Strava</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                Analizza i tuoi<br>
                <span class="text-orange-600">allenamenti</span> in modo<br>
                più intelligente
            </h1>

            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                PEMIQ si sincronizza con Strava e trasforma i tuoi dati di allenamento in grafici e statistiche
                che ti aiutano a capire i tuoi progressi a colpo d'occhio.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-md transition-colors">
                    Inizia gratis
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-gray-700 hover:text-gray-900 bg-white border border-gray-200 hover:border-gray-300 rounded-xl shadow-sm transition-colors">
                    Ho già un account
                </a>
            </div>
        </div>
    </section>

    {{-- Stats bar --}}
    <section class="bg-white border-y border-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-3xl font-bold text-orange-600">∞</div>
                    <div class="text-sm text-gray-500 mt-1">Attività sincronizzate</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600">6+</div>
                    <div class="text-sm text-gray-500 mt-1">Sport supportati</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600">3</div>
                    <div class="text-sm text-gray-500 mt-1">Grafici interattivi</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-orange-600">IT/EN</div>
                    <div class="text-sm text-gray-500 mt-1">Lingue supportate</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Tutto quello che ti serve per analizzare i tuoi dati</h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">Collega Strava una volta, e PEMIQ penserà a tutto il resto.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Dashboard con grafici</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Visualizza distanza e ore per mese con grafici a barre interattivi. Seleziona l'anno e la metrica in un click.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mappa del percorso</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Ogni attività ha la sua mappa interattiva con il tracciato esatto del tuo allenamento su OpenStreetMap.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 4-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Storico completo</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sfoglia e filtra tutte le tue attività per sport, anno e mese. Trova ogni allenamento in pochi secondi.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Distribuzione sport</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Grafico donut che mostra come distribuisci i tuoi allenamenti tra corsa, ciclismo, nuoto e altro.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sync automatica</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sincronizzazione oraria automatica con Strava. I tuoi dati sono sempre aggiornati senza alcuna azione manuale.</p>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Italiano e Inglese</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Interfaccia completamente localizzata in italiano e inglese, con date e numeri nel formato corretto per ogni lingua.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Pricing / Free vs Premium --}}
    <section class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Free vs Premium</h2>
                <p class="text-lg text-gray-500">Inizia gratuitamente. Sblocca funzionalità avanzate con Premium.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 max-w-3xl mx-auto">

                {{-- Free Plan --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
                    <div class="mb-6">
                        <span class="inline-block bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full mb-3">FREE</span>
                        <div class="text-4xl font-extrabold text-gray-900 mb-1">€0<span class="text-lg font-normal text-gray-500">/mese</span></div>
                        <p class="text-sm text-gray-500">Per sempre gratuito</p>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Collegamento Strava</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Sync storica completa</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Dashboard con 3 grafici</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Lista e dettaglio attività</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Mappa percorso</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-sm text-gray-700">Filtri per sport, anno, mese</span>
                        </li>
                    </ul>

                    <a href="{{ route('register') }}"
                       class="block w-full text-center py-3 px-6 rounded-xl border-2 border-orange-600 text-orange-600 font-semibold text-sm hover:bg-orange-50 transition-colors">
                        Inizia gratis
                    </a>
                </div>

                {{-- Premium Plan --}}
                <div class="bg-orange-600 rounded-2xl p-8 shadow-lg relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
                    <div class="relative">
                        <div class="mb-6">
                            <span class="inline-block bg-white bg-opacity-20 text-white text-xs font-bold px-3 py-1 rounded-full mb-3">PREMIUM</span>
                            <div class="text-4xl font-extrabold text-white mb-1">Su richiesta</div>
                            <p class="text-sm text-orange-100">Contatta l'admin per l'upgrade</p>
                        </div>

                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">Tutto del piano Free</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">Analisi trend nel tempo</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">Confronto tra due periodi</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">Confronto anno su anno</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">KPI card con delta %</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-orange-200 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm text-white">Grafici linea/area avanzati</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}"
                           class="block w-full text-center py-3 px-6 rounded-xl bg-white text-orange-600 font-semibold text-sm hover:bg-orange-50 transition-colors shadow-sm">
                            Registrati e richiedi l'upgrade
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA Banner --}}
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Pronto ad analizzare i tuoi dati?</h2>
            <p class="text-lg text-gray-500 mb-8">Crea il tuo account in meno di un minuto. Nessuna carta di credito richiesta.</p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center px-10 py-4 text-base font-semibold bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-md transition-colors">
                Registrati gratis
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <p class="mt-4 text-sm text-gray-400">
                Hai già un account?
                <a href="{{ route('login') }}" class="text-orange-600 hover:underline font-medium">Accedi</a>
            </p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-50 border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xl font-bold text-orange-600">PEMIQ</span>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="hover:text-gray-700 transition-colors">Accedi</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-700 transition-colors">Registrati</a>
                </div>
                <p class="text-sm text-gray-400">PEMIQ &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </footer>

</body>
</html>
