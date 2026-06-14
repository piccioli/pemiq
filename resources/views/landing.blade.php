<!DOCTYPE html>
<html lang="it" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEMIQ — Il tuo compagno di allenamento</title>

    {{-- DS fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=DM+Mono:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">

    {{-- DS styles --}}
    @vite(['resources/css/app.css'])

    {{-- Lucide icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Alpine.js (no Livewire on public pages) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none; }

        .landing-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            height: 60px;
            display: flex;
            align-items: center;
            background: color-mix(in srgb, var(--bg) 75%, transparent);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .landing-hero {
            background:
                radial-gradient(ellipse 90% 60% at 50% 0%, rgba(22, 212, 180, 0.14) 0%, transparent 70%),
                var(--bg);
        }

        .landing-stat-value {
            font-family: var(--font-display);
            font-size: var(--fs-4xl);
            font-weight: 700;
            color: var(--brand);
            line-height: 1;
        }

        .landing-stat-label {
            font: var(--text-caption);
            color: var(--text-muted);
            margin-top: var(--space-1);
        }

        .landing-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: var(--accent-soft);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-3);
        }

        .landing-feature-icon i {
            width: 20px;
            height: 20px;
        }

        .landing-feature-desc {
            font: var(--text-body);
            color: var(--text-muted);
        }

        .landing-feature-title {
            font: var(--text-heading);
            color: var(--text-strong);
            margin-bottom: var(--space-2);
        }

        .landing-plan-feature {
            display: flex;
            align-items: flex-start;
            gap: var(--space-3);
            padding: var(--space-2) 0;
        }

        .landing-plan-feature-icon {
            flex-shrink: 0;
            margin-top: 2px;
            color: var(--success);
        }

        .landing-plan-feature-icon i {
            width: 16px;
            height: 16px;
        }

        .landing-plan-price {
            font-family: var(--font-display);
            font-size: var(--fs-4xl);
            font-weight: 700;
            color: var(--text-strong);
            line-height: 1;
        }

        .landing-plan-price-note {
            font: var(--text-body);
            color: var(--text-muted);
        }

        .landing-footer {
            background: var(--bg-elevated);
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body style="background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; font-family: var(--font-body);">

    {{-- Navigation --}}
    <nav class="landing-nav" x-data="{ open: false }">
        <div style="max-width: 80rem; width: 100%; margin: 0 auto; padding: 0 var(--space-6); display: flex; align-items: center; justify-content: space-between;">

            {{-- Logo --}}
            <a href="{{ route('home') }}" style="font-family: var(--font-display); font-size: var(--fs-2xl); font-weight: 700; color: var(--brand); text-decoration: none; letter-spacing: -0.02em;">
                PEMIQ
            </a>

            {{-- Desktop nav --}}
            <div style="display: flex; align-items: center; gap: var(--space-3);">
                <x-button variant="ghost" :href="route('login')">Accedi</x-button>
                <x-button variant="primary" :href="route('register')">Registrati gratis</x-button>
            </div>

        </div>
    </nav>

    {{-- Hero --}}
    <section class="landing-hero" style="padding: var(--space-20) 0 var(--space-16);">
        <div style="max-width: 56rem; margin: 0 auto; padding: 0 var(--space-6); text-align: center;">

            <div style="margin-bottom: var(--space-6);">
                <x-badge variant="brand" size="md">
                    <i data-lucide="zap" style="width: 12px; height: 12px; display: inline; vertical-align: middle; margin-right: 4px;"></i>
                    Connesso a Strava
                </x-badge>
            </div>

            <h1 style="font-family: var(--font-display); font-size: clamp(2.5rem, 6vw, 3.5rem); font-weight: 700; color: var(--text-strong); line-height: 1.1; margin-bottom: var(--space-6); letter-spacing: -0.02em;">
                Analizza i tuoi<br>
                <span style="color: var(--brand);">allenamenti</span> in modo<br>
                più intelligente
            </h1>

            <p style="font: var(--text-body); font-size: var(--fs-lg); color: var(--text-muted); max-width: 40rem; margin: 0 auto var(--space-10); line-height: 1.7;">
                PEMIQ si sincronizza con Strava e trasforma i tuoi dati di allenamento in grafici e statistiche
                che ti aiutano a capire i tuoi progressi a colpo d'occhio.
            </p>

            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: var(--space-4);">
                <x-button variant="primary" size="lg" :href="route('register')">
                    Inizia gratis
                    <x-slot:iconRight>
                        <i data-lucide="arrow-right" style="width: 18px; height: 18px; margin-left: 6px;"></i>
                    </x-slot:iconRight>
                </x-button>
                <x-button variant="ghost" :href="route('login')">Ho già un account</x-button>
            </div>

        </div>
    </section>

    {{-- Stats bar --}}
    <section style="background: var(--surface); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: var(--space-8) 0;">
        <div style="max-width: 56rem; margin: 0 auto; padding: 0 var(--space-6);">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-6); text-align: center;">
                <div>
                    <div class="landing-stat-value">∞</div>
                    <div class="landing-stat-label">Attività sincronizzate</div>
                </div>
                <div>
                    <div class="landing-stat-value">6+</div>
                    <div class="landing-stat-label">Sport supportati</div>
                </div>
                <div>
                    <div class="landing-stat-value">3</div>
                    <div class="landing-stat-label">Grafici interattivi</div>
                </div>
                <div>
                    <div class="landing-stat-value">IT/EN</div>
                    <div class="landing-stat-label">Lingue supportate</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section style="padding: var(--space-20) 0; background: var(--bg);">
        <div style="max-width: 80rem; margin: 0 auto; padding: 0 var(--space-6);">
            <div style="text-align: center; margin-bottom: var(--space-12);">
                <p class="pq-eyebrow" style="margin-bottom: var(--space-3);">Funzionalità</p>
                <h2 style="font-family: var(--font-display); font-size: var(--fs-3xl); font-weight: 700; color: var(--text-strong); margin-bottom: var(--space-4);">Tutto quello che ti serve per analizzare i tuoi dati</h2>
                <p style="font: var(--text-body); color: var(--text-muted); max-width: 36rem; margin: 0 auto;">Collega Strava una volta, e PEMIQ penserà a tutto il resto.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-6);">

                <x-card eyebrow="Dashboard">
                    <div class="landing-feature-icon"><i data-lucide="bar-chart-2"></i></div>
                    <h3 class="landing-feature-title">Grafici interattivi</h3>
                    <p class="landing-feature-desc">Visualizza distanza e ore per mese con grafici a barre interattivi. Seleziona l'anno e la metrica in un click.</p>
                </x-card>

                <x-card eyebrow="Mappa">
                    <div class="landing-feature-icon"><i data-lucide="map-pin"></i></div>
                    <h3 class="landing-feature-title">Percorso su mappa</h3>
                    <p class="landing-feature-desc">Ogni attività ha la sua mappa interattiva con il tracciato esatto del tuo allenamento su OpenStreetMap.</p>
                </x-card>

                <x-card eyebrow="Storico">
                    <div class="landing-feature-icon"><i data-lucide="database"></i></div>
                    <h3 class="landing-feature-title">Archivio completo</h3>
                    <p class="landing-feature-desc">Sfoglia e filtra tutte le tue attività per sport, anno e mese. Trova ogni allenamento in pochi secondi.</p>
                </x-card>

                <x-card eyebrow="Sport">
                    <div class="landing-feature-icon"><i data-lucide="pie-chart"></i></div>
                    <h3 class="landing-feature-title">Distribuzione sport</h3>
                    <p class="landing-feature-desc">Grafico donut che mostra come distribuisci i tuoi allenamenti tra corsa, ciclismo, nuoto e altro.</p>
                </x-card>

                <x-card eyebrow="Sync">
                    <div class="landing-feature-icon"><i data-lucide="refresh-cw"></i></div>
                    <h3 class="landing-feature-title">Sync automatica</h3>
                    <p class="landing-feature-desc">Sincronizzazione oraria automatica con Strava. I tuoi dati sono sempre aggiornati senza alcuna azione manuale.</p>
                </x-card>

                <x-card eyebrow="Lingua">
                    <div class="landing-feature-icon"><i data-lucide="languages"></i></div>
                    <h3 class="landing-feature-title">Italiano e Inglese</h3>
                    <p class="landing-feature-desc">Interfaccia completamente localizzata in italiano e inglese, con date e numeri nel formato corretto per ogni lingua.</p>
                </x-card>

            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section style="padding: var(--space-20) 0; background: var(--bg-elevated);">
        <div style="max-width: 56rem; margin: 0 auto; padding: 0 var(--space-6);">
            <div style="text-align: center; margin-bottom: var(--space-12);">
                <p class="pq-eyebrow" style="margin-bottom: var(--space-3);">Piani</p>
                <h2 style="font-family: var(--font-display); font-size: var(--fs-3xl); font-weight: 700; color: var(--text-strong); margin-bottom: var(--space-4);">Free vs Premium</h2>
                <p style="font: var(--text-body); color: var(--text-muted);">Inizia gratuitamente. Sblocca funzionalità avanzate con Premium.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-6); max-width: 44rem; margin: 0 auto;">

                {{-- Free Plan --}}
                <x-card padding="lg">
                    <div style="margin-bottom: var(--space-6);">
                        <div style="margin-bottom: var(--space-3);">
                            <x-badge variant="outline">FREE</x-badge>
                        </div>
                        <div class="landing-plan-price">€0<span class="landing-plan-price-note" style="font-size: var(--fs-lg); font-weight: 400;">/mese</span></div>
                        <p style="font: var(--text-caption); color: var(--text-faint); margin-top: var(--space-1);">Per sempre gratuito</p>
                    </div>

                    <div style="margin-bottom: var(--space-8);">
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Collegamento Strava</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Sync storica completa</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Dashboard con 3 grafici</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Lista e dettaglio attività</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Mappa percorso</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Filtri per sport, anno, mese</span>
                        </div>
                    </div>

                    <x-button variant="secondary" :href="route('register')" fullWidth>
                        Inizia gratis
                    </x-button>
                </x-card>

                {{-- Premium Plan --}}
                <x-card padding="lg" :accent="true">
                    <div style="margin-bottom: var(--space-6);">
                        <div style="margin-bottom: var(--space-3);">
                            <x-badge variant="zone4">PREMIUM</x-badge>
                        </div>
                        <div class="landing-plan-price">Su richiesta</div>
                        <p style="font: var(--text-caption); color: var(--text-faint); margin-top: var(--space-1);">Contatta l'admin per l'upgrade</p>
                    </div>

                    <div style="margin-bottom: var(--space-8);">
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Tutto del piano Free</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Analisi trend nel tempo</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Confronto tra due periodi</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Confronto anno su anno</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">KPI card con delta %</span>
                        </div>
                        <div class="landing-plan-feature">
                            <span class="landing-plan-feature-icon"><i data-lucide="check"></i></span>
                            <span style="font: var(--text-body); color: var(--text);">Grafici linea/area avanzati</span>
                        </div>
                    </div>

                    <x-button variant="primary" :href="route('register')" fullWidth>
                        Registrati e richiedi l'upgrade
                    </x-button>
                </x-card>

            </div>
        </div>
    </section>

    {{-- CTA Banner --}}
    <section style="padding: var(--space-20) 0; background: var(--bg);">
        <div style="max-width: 44rem; margin: 0 auto; padding: 0 var(--space-6); text-align: center;">
            <h2 style="font-family: var(--font-display); font-size: var(--fs-3xl); font-weight: 700; color: var(--text-strong); margin-bottom: var(--space-4);">Pronto ad analizzare i tuoi dati?</h2>
            <p style="font: var(--text-body); color: var(--text-muted); margin-bottom: var(--space-8);">Crea il tuo account in meno di un minuto. Nessuna carta di credito richiesta.</p>
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: var(--space-4);">
                <x-button variant="primary" size="lg" :href="route('register')">
                    Registrati gratis
                    <x-slot:iconRight>
                        <i data-lucide="arrow-right" style="width: 18px; height: 18px; margin-left: 6px;"></i>
                    </x-slot:iconRight>
                </x-button>
                <x-button variant="ghost" :href="route('login')">Ho già un account</x-button>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="landing-footer" style="margin-top: auto;">
        <div style="max-width: 80rem; margin: 0 auto; padding: var(--space-6) var(--space-6);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: var(--space-4);">
                <span style="font-family: var(--font-display); font-size: var(--fs-xl); font-weight: 700; color: var(--brand);">PEMIQ</span>
                <div style="display: flex; align-items: center; gap: var(--space-6);">
                    <a href="{{ route('login') }}" style="font: var(--text-label); color: var(--text-muted); text-decoration: none;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">Accedi</a>
                    <a href="{{ route('register') }}" style="font: var(--text-label); color: var(--text-muted); text-decoration: none;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text-muted)'">Registrati</a>
                </div>
                <p style="font: var(--text-caption); color: var(--text-faint);">PEMIQ &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) lucide.createIcons();
        });
    </script>

</body>
</html>
