# PRD: PEMIQ — Fasi 0, 1 e 2

**Versione:** 1.1  
**Data:** 2026-06-14  
**Stato:** Approvato  
**Riferimenti:** `docs/project/ideas.md`, `tasks/prd-pemiq.md`

---

## Indice

1. [Introduzione](#1-introduzione)
2. [Obiettivi](#2-obiettivi)
3. [Fase 0 — Consolidamento MVP, QA e beta chiusa](#fase-0--consolidamento-mvp-qa-e-beta-chiusa)
4. [Fase 1 — UX dashboard, onboarding e notifiche](#fase-1--ux-dashboard-onboarding-e-notifiche)
5. [Fase 2 — Premium: analisi trend e confronti](#fase-2--premium-analisi-trend-e-confronti)
6. [Requisiti funzionali](#6-requisiti-funzionali)
7. [Non-Goals (fuori scope)](#7-non-goals-fuori-scope)
8. [Considerazioni tecniche](#8-considerazioni-tecniche)
9. [Metriche di successo](#9-metriche-di-successo)
10. [Open Questions](#10-open-questions)

---

## 1. Introduzione

La release v0.0.1 di PEMIQ è feature-complete rispetto all'MVP definito nel PRD principale. Tuttavia mancano test automatici su aree critiche, la UX della dashboard è essenziale (solo tabelle HTML), e il layer Premium non offre ancora valore reale agli utenti promossi.

Questo documento definisce le tre fasi successive prioritarie:

- **Fase 0:** porta v0.0.1 da "feature-complete" a "production-ready" tramite QA, hardening e beta chiusa.
- **Fase 1:** colma i gap UX tra PRD e implementazione attuale (grafici, lista attività, i18n, landing page).
- **Fase 2:** attiva il primo blocco di valore Premium (trend analysis e confronto periodi) dietro middleware `EnsurePremium`.

Le tre fasi sono sequenziali: la Fase 1 dipende dalla Fase 0, la Fase 2 dipende dalla Fase 1.

---

## 2. Obiettivi

- Garantire stabilità e sicurezza prima del lancio pubblico su `pemiq.com`
- Offrire un'esperienza dashboard moderna e comprensibile per gli utenti Free
- Sbloccare il modello freemium con funzionalità Premium reali e gated
- Validare il prodotto con utenti beta reali prima di investire in monetizzazione (Fase 4)

---

## Fase 0 — Consolidamento MVP, QA e beta chiusa

**Obiettivo:** produzione-ready prima del lancio pubblico.  
**Stima:** 2–3 settimane.  
**Definition of Done:** deploy su `uat.pemiq.com` stabile, CI verde, zero bug critici aperti, onboarding completato da >60% dei beta tester.

---

### US-001: Test feature sincronizzazione Strava (storica)

**Description:** As a developer, I want full coverage on the historical Strava sync job so that regressions in the core data pipeline are caught by CI.

**Acceptance Criteria:**
- [ ] Test `SyncStravaHistoricalActivities` con mock HTTP completo (`Http::fake`) che simula paginazione Strava API
- [ ] Caso: prima sync — tutte le attività vengono salvate in `activities`
- [ ] Caso: attività già esistente — nessun duplicato (upsert corretto su `strava_activity_id`)
- [ ] Caso: Strava risponde 429 — job fa retry con backoff, salva `sync_logs` con status `rate_limited`
- [ ] Caso: token scaduto — `StravaTokenService::refresh()` viene chiamato, sync prosegue
- [ ] `SyncLog` viene creato con `status`, `activities_synced`, `error_message` corretti per ogni caso
- [ ] Typecheck e test suite passano (`php artisan test`)

---

### US-002: Test feature sincronizzazione Strava (incrementale)

**Description:** As a developer, I want coverage on the incremental sync job so that the scheduled hourly sync is reliable.

**Acceptance Criteria:**
- [ ] Test `SyncStravaIncrementalActivities` con mock HTTP
- [ ] Caso: nessuna attività nuova — `SyncLog` con `activities_synced = 0`, nessun write su `activities`
- [ ] Caso: nuove attività presenti — salvate correttamente
- [ ] Caso: API inaccessibile — eccezione catturata, `SyncLog` con status `failed`
- [ ] Typecheck e test suite passano

---

### US-003: Test feature impersonificazione e audit log

**Description:** As a developer, I want tests for admin impersonation so that we can verify it cannot be abused.

**Acceptance Criteria:**
- [ ] Test: admin può impersonare un utente non-admin → redirect corretto, banner visibile
- [ ] Test: un utente `user` non può usare la route di impersonificazione → 403
- [ ] Test: al termine dell'impersonificazione la sessione torna all'admin
- [ ] Test: ogni impersonificazione crea un record in `audit_logs` con `admin_id`, `target_user_id`, `action = 'impersonate'`
- [ ] Typecheck e test suite passano

---

### US-004: Test middleware EnsurePremium

**Description:** As a developer, I want tests for the EnsurePremium middleware so that gating logic is verifiable before it's applied to Premium routes.

**Acceptance Criteria:**
- [ ] Test: utente `is_premium = false` che accede a route protetta → redirect con messaggio flash
- [ ] Test: utente `is_premium = true` → accede normalmente
- [ ] Test: utente non autenticato → redirect a login (non errore 500)
- [ ] Il middleware è registrato in `bootstrap/app.php` con alias `premium` (già presente, verificare alias corretto)
- [ ] Typecheck e test suite passano

---

### US-005: Test DashboardStatsService con dataset edge case

**Description:** As a developer, I want tests for DashboardStatsService to prevent silent data errors for users with unusual activity histories.

**Acceptance Criteria:**
- [ ] Factory che genera utente con 0 attività → tutti i metodi restituiscono valori neutri (0, `null`, array vuoto), nessuna eccezione
- [ ] Factory che genera utente con attività aventi campi `distance = null`, `elapsed_time = null` → nessuna eccezione, aggregati calcolati solo sui valori non-null
- [ ] Test con dataset 50 attività distribuite su 3 anni → valori annuali e mensili coerenti con somme manuali
- [ ] Typecheck e test suite passano

---

### US-006: Revisione sicurezza — isolamento dati utente

**Description:** As a developer, I want to audit all queries that touch user data to ensure they are always scoped to the authenticated user.

**Acceptance Criteria:**
- [ ] Revisione di tutti i metodi in `DashboardStatsService`, `ActivitySyncService`, `StravaTokenService` — ogni query include `where('user_id', $user->id)` o equivalente Eloquent relationship
- [ ] Test: utente B non può vedere attività di utente A tramite nessun endpoint (route `/activities`, `/dashboard`)
- [ ] Nessuna query con `->all()` o senza scope utente su tabelle `activities`, `strava_accounts`, `sync_logs`
- [ ] Risultati documentati in un commento nel PR o issue dedicata

---

### US-007: Audit CSRF su form utente

**Description:** As a developer, I want to verify that all user-facing forms are protected against CSRF.

**Acceptance Criteria:**
- [ ] Revisione di tutte le form in `resources/views/` (registrazione, login, reset password, profilo, connessione Strava)
- [ ] Ogni form POST contiene `@csrf`
- [ ] Test: richiesta POST senza token CSRF → 419 (non 500)
- [ ] Risultati documentati

---

### US-008: Procedura backup e rotazione APP_KEY

**Description:** As a developer, I want a documented procedure for APP_KEY rotation so that the team can respond to the R-4 risk without data loss.

**Acceptance Criteria:**
- [ ] Documento `docs/ops/app-key-rotation.md` che descrive: quando ruotare, come re-cifrare i dati (`StravaAccount` usa encrypted cast), come fare rollback
- [ ] Verifica che i token Strava cifrati siano recuperabili dopo una rotazione simulata in ambiente locale
- [ ] Procedura testata in locale e documentata con i passi esatti

---

### US-009: Profiling dashboard con dataset di carico

**Description:** As a developer, I want to verify dashboard performance under load so that we can identify bottlenecks before the public launch.

**Acceptance Criteria:**
- [ ] Seeder che genera 10.000+ attività per un singolo utente test
- [ ] Profiling della route `/dashboard` con `DEBUGBAR_ENABLED=true` o Laravel Telescope
- [ ] Verifica che l'indice composito `(user_id, started_at, sport_type)` su `activities` sia presente e usato dalle query principali
- [ ] Tempo di risposta `/dashboard` con 10k attività < 2s in ambiente locale senza cache
- [ ] Se il tempo supera 2s: issue documentata con query da ottimizzare (non necessariamente risolta in Fase 0)

---

### US-010: Deploy beta su UAT e raccolta feedback

**Description:** As a product owner, I want to deploy to UAT and invite beta users so that we can validate the product before the public launch.

**Acceptance Criteria:**
- [ ] Deploy su `uat.pemiq.com` funzionante (app, queue worker, scheduler attivi)
- [ ] Almeno 5 utenti beta invitati e registrati
- [ ] Form o documento strutturato per raccolta feedback (onboarding → sync → dashboard)
- [ ] Monitoraggio `sync_logs` per errori e rate limit durante beta (almeno 48h di osservazione)
- [ ] Report bug critici aperti in issue tracker, triaged per priorità

---

## Fase 1 — UX dashboard, onboarding e notifiche

**Obiettivo:** migliorare l'esperienza utente Free colmando i gap tra PRD e implementazione attuale.  
**Stima:** 3–4 settimane.  
**Dipende da:** Fase 0 completata.  
**Definition of Done:** un utente Free vede grafici, naviga le proprie attività e capisce lo stato della sincronizzazione senza aprire il backoffice.

---

### US-011: Grafico analisi annuale

**Description:** As a Free user, I want to see a bar or line chart of my yearly distance and hours so that I can understand my training volume at a glance.

**Acceptance Criteria:**
- [ ] Componente Livewire `AnnualAnalysisChart` che sostituisce o affianca la tabella esistente
- [ ] Asse X: mesi (Jan–Dec), asse Y: distanza (km) o ore (selezionabile)
- [ ] Dati passati dal server via Livewire (no query lato JS)
- [ ] Selettore anno (default: anno corrente)
- [ ] Libreria chart integrata via npm (Chart.js o ApexCharts) — NON CDN
- [ ] Responsive su mobile (min-width 320px)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-012: Grafico distribuzione sport

**Description:** As a Free user, I want to see a donut or horizontal bar chart of my sport distribution so that I understand how I split my training.

**Acceptance Criteria:**
- [ ] Componente Livewire `SportDistributionChart` che sostituisce o affianca la tabella badge esistente
- [ ] Dati: sport type → numero attività + distanza totale
- [ ] Tooltip al hover con valori
- [ ] Colori badge sport riutilizzati dal design esistente
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-013: Grafico analisi mensile

**Description:** As a Free user, I want to see a bar chart with daily or weekly breakdown for a selected month so that I can review my training cadence.

**Acceptance Criteria:**
- [ ] Componente Livewire `MonthlyAnalysisChart` che sostituisce o affianca la tabella mensile esistente
- [ ] Selettore anno + mese
- [ ] Barre per distanza o durata per giorno/settimana del mese
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-014: Lista attività utente

**Description:** As a Free user, I want to browse a paginated list of my activities so that I can find and review individual workouts.

**Acceptance Criteria:**
- [ ] Route `/activities` accessibile a utenti autenticati
- [ ] Controller `ActivityController@index` o componente Livewire `ActivityList`
- [ ] Colonne: data, sport, titolo, distanza, durata, dislivello
- [ ] Filtri: sport type (select), anno (select), mese (select)
- [ ] Paginazione: 20 attività per pagina, query sempre scoped su `user_id`
- [ ] Nessuna N+1 query (eager loading dove necessario)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-015: Pagina dettaglio singola attività

**Description:** As a Free user, I want to see the full details of a single activity, including a map, so that I can review my workout data.

**Acceptance Criteria:**
- [ ] Route `/activities/{activity}` con policy: solo l'owner può vedere la propria attività
- [ ] Sezione metriche: distanza, durata, dislivello, velocità media, FC media (se disponibile)
- [ ] Mappa polyline usando il campo `map_polyline` (già salvato) via Leaflet.js o Mapbox GL JS
- [ ] Se `map_polyline` è null: messaggio "Mappa non disponibile" senza errore
- [ ] Link "Vedi su Strava" se `strava_activity_id` presente
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-016: Indicatore progresso sync storica

**Description:** As a user, I want to see real-time progress of the historical sync so that I don't think the app is broken while it's importing my data.

**Acceptance Criteria:**
- [ ] Componente Livewire `ConnectionStatus` aggiornato con polling su `SyncLog` più recente (ogni 5s mentre sync è in corso)
- [ ] Stati visibili: "Sincronizzazione in corso (N attività importate)", "Completata", "Fallita — motivo: X"
- [ ] Polling si ferma automaticamente quando lo status non è più `running`
- [ ] Nessuna richiesta polling se l'utente non ha un `StravaAccount` collegato
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-017: Notifica email sync fallita o token scaduto

**Description:** As a user, I want to receive an email when my Strava sync fails or my token expires so that I can reconnect without losing data.

**Acceptance Criteria:**
- [ ] Mailable `SyncFailedNotification` inviata alla **prima** failure critica (errore HTTP non-recuperabile o eccezione non gestita nel job — NON per timeout transitori o 503 temporanei)
- [ ] Mailable `StravaTokenExpiredNotification` inviata quando il refresh del token fallisce definitivamente (nessun ulteriore retry possibile)
- [ ] La distinzione failure critica / transitoria è codificata in `ActivitySyncService` tramite eccezioni tipizzate (es. `StravaAuthException` vs `StravaNetworkException`)
- [ ] Email contiene link diretto a `/dashboard` per ricollgare Strava
- [ ] Notifiche testate con Mailpit in locale
- [ ] Nessuna email duplicata: flag `notified_at` su `StravaAccount` resettato a null solo dopo una sync riuscita
- [ ] Typecheck e test suite passano

---

### US-018: Internazionalizzazione completa dashboard

**Description:** As a developer, I want all hardcoded dashboard strings moved to lang files so that the UI is consistent in Italian and English.

**Acceptance Criteria:**
- [ ] Audit di tutti i file in `resources/views/livewire/dashboard/` — ogni stringa visibile estratta con `__('messages.key')` o `trans()`
- [ ] Chiavi aggiunte in `lang/it/messages.php` e `lang/en/messages.php`
- [ ] Formattazione date con `\Carbon\Carbon::setLocale($user->locale)` o helper locale-aware
- [ ] Formattazione numeri (distanza, dislivello) con separatore decimale corretto per locale
- [ ] Nessuna stringa italiana hardcoded visibile nel sorgente view dopo il refactor
- [ ] Typecheck e test suite passano
- [ ] Verify in browser con locale=en e locale=it usando dev-browser skill

---

### US-019: Landing page pubblica

**Description:** As a visitor, I want to see a public landing page that explains PEMIQ before I decide to register.

**Acceptance Criteria:**
- [ ] Route `/` restituisce la landing page (non redirect a login/dashboard)
- [ ] Utenti già autenticati vengono redirectati a `/dashboard` dalla landing page
- [ ] Sezioni minime: headline, descrizione del prodotto, feature highlights (Free vs Premium), CTA "Registrati"
- [ ] Link a login e registrazione chiaramente visibili
- [ ] Responsive su mobile
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

## Fase 2 — Premium: analisi trend e confronti

**Obiettivo:** attivare il primo blocco di valore Premium reale dietro `EnsurePremium`.  
**Stima:** 3–4 settimane.  
**Dipende da:** Fase 1 completata.  
**Definition of Done:** admin promuove un utente a Premium → l'utente accede alle sezioni trend/confronto; un Free user vede preview o messaggio di blocco.

---

### US-020: Middleware EnsurePremium applicato alle route Premium

**Description:** As a developer, I want to apply the EnsurePremium middleware to all Premium routes so that non-premium users cannot access gated features.

**Acceptance Criteria:**
- [ ] Middleware `premium` applicato al gruppo route `/premium/*`
- [ ] Utente Free che accede a `/premium/trends` → redirect a `/dashboard` con messaggio flash "Funzionalità disponibile per utenti Premium"
- [ ] Utente Premium → accede normalmente
- [ ] Admin impersonando un Free user → vede il blocco (comportamento corretto del middleware)
- [ ] Typecheck e test suite passano

---

### US-021: Badge Premium in navbar e profilo

**Description:** As a Premium user, I want a visible badge in the navbar and profile page so that I know my account is active.

**Acceptance Criteria:**
- [ ] Badge "Premium" visibile nella navbar per utenti con `is_premium = true`
- [ ] Badge visibile anche nella pagina profilo
- [ ] Badge assente per utenti Free
- [ ] CTA "Passa a Premium" visibile per utenti Free nella navbar o profilo (testo + link a `/premium` o contatto)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-022: Servizio TrendAnalysisService

**Description:** As a developer, I want a dedicated service for trend queries so that Premium analytics are centralized and testable.

**Acceptance Criteria:**
- [ ] Classe `app/Services/Dashboard/TrendAnalysisService.php`
- [ ] Metodo `weeklyVolume(User $user, string $sportType = null, Carbon $from, Carbon $to): Collection` — attività per settimana con distanza e durata aggregate
- [ ] Metodo `monthlyVolume(User $user, string $sportType = null, Carbon $from, Carbon $to): Collection` — attività per mese
- [ ] Ogni query scoped su `user_id`
- [ ] Filtro `sport_type` opzionale (null = tutti gli sport)
- [ ] Test unitari con dataset factory per i due metodi, incluso edge case zero attività nel range
- [ ] Typecheck e test suite passano

---

### US-023: Grafico trend volume nel tempo

**Description:** As a Premium user, I want to see a line or area chart of my training volume over time so that I can spot trends in my training.

**Acceptance Criteria:**
- [ ] Route `/premium/trends` accessibile solo a Premium (middleware applicato in US-020)
- [ ] Componente Livewire `TrendChart` alimentato da `TrendAnalysisService`
- [ ] Asse X: settimane o mesi (toggle), asse Y: distanza (km) o ore (toggle)
- [ ] Selettore sport (tutti / singolo sport)
- [ ] Selettore range date: ultimi 3 mesi, 6 mesi, 1 anno, personalizzato
- [ ] Grafico a linea/area con punti interattivi (tooltip con valore)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-024: Confronto tra due periodi

**Description:** As a Premium user, I want to compare two date ranges side by side — with KPI cards, a dual-line chart, and a detail table — so that I can measure my progression at a glance.

**Acceptance Criteria:**
- [ ] Route dedicata `/premium/compare` accessibile solo a Premium (middleware `EnsurePremium`)
- [ ] **Selettori:** date picker range libero per Periodo A e Periodo B; pulsante "Aggiorna confronto" che ricarica i dati
- [ ] **4 KPI cards:** Distanza (km), Dislivello (m), Tempo (h), Attività (conteggio) — ciascuna mostra il valore del Periodo B e il delta percentuale rispetto a Periodo A colorato (verde se positivo, rosso se negativo, grigio se zero)
- [ ] **Grafico linee ApexCharts:** titolo "Distanza mensile (km)", due serie (Periodo A = blu, Periodo B = viola), asse X normalizzato per mese (Gen, Feb, …), tooltip con valori di entrambe le serie
- [ ] Toggle granularità Mese / Settimana sul grafico
- [ ] Toggle tipo chart Linee / Barre sul grafico (stesso dato, rappresentazione diversa)
- [ ] **Tabella "Dettaglio confronto":** colonne Metrica (con icona sport), Periodo A (data range + valore), Periodo B (data range + valore), Delta assoluto, Delta (%) — righe: Attività, Distanza, Dislivello, Tempo
- [ ] Delta assoluto e Delta (%) usano stesso schema colori delle KPI cards
- [ ] Gestione divisione per zero: se Periodo A = 0 per una metrica, Delta (%) mostra "—"
- [ ] Filtro sport opzionale (default: tutti gli sport)
- [ ] Pulsante "Esporta" visibile (placeholder — funzionalità in fase futura, disabilitato con tooltip "Disponibile a breve")
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-025: Confronto anno su anno

**Description:** As a Premium user, I want to overlay two years on a monthly chart so that I can compare my training year over year.

**Acceptance Criteria:**
- [ ] Grafico con due linee (Anno A vs Anno B) sovrapposte, asse X: mesi Jan–Dec
- [ ] Selettori anno A e anno B (default: anno corrente vs anno precedente)
- [ ] Filtro sport opzionale
- [ ] Legenda chiara (Anno A = linea blu, Anno B = linea arancione)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-026: CTA upgrade per utenti Free su pagine Premium

**Description:** As a Free user who navigates to a Premium page, I want to see a clear upgrade message so that I understand what I'm missing and how to get access.

**Acceptance Criteria:**
- [ ] Pagina di blocco mostrata quando middleware `EnsurePremium` fa redirect (o componente overlay sulla pagina)
- [ ] Testo: descrizione della funzionalità bloccata + benefici Premium
- [ ] CTA: link a `/premium` (pagina di upgrade/contatto) o mailto con soggetto precompilato
- [ ] Design coerente con il resto dell'app (nessun placeholder generico)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

## 6. Requisiti funzionali

### Fase 0
- FR-0.1: La CI deve eseguire tutti i test Feature e Unit ad ogni push su `main`
- FR-0.2: Ogni job di sync deve scrivere un record `SyncLog` con status e conteggio attività
- FR-0.3: Nessuna query su `activities`, `strava_accounts`, `sync_logs` senza scope `user_id`
- FR-0.4: Il deploy su `uat.pemiq.com` deve includere queue worker e scheduler attivi

### Fase 1
- FR-1.1: I grafici devono essere serviti con dati pre-aggregati dal server (no aggregazioni lato client)
- FR-1.2: La lista attività deve essere sempre paginata (max 20 per pagina) e non usare `->all()`
- FR-1.3: La mappa polyline non deve causare errori se `map_polyline` è null
- FR-1.4: Il polling sync deve fermarsi automaticamente al completamento
- FR-1.5: Tutte le stringhe visibili nelle view devono passare per il sistema di traduzione `__()`

### Fase 2
- FR-2.1: Il middleware `premium` deve essere applicato a tutte le route sotto `/premium/`
- FR-2.2: `TrendAnalysisService` deve essere l'unico punto di accesso alle query di trend
- FR-2.3: I grafici trend devono supportare filtro sport e range date personalizzato
- FR-2.4: Il confronto periodi deve calcolare il delta in percentuale (con gestione divisione per zero)

---

## 7. Non-Goals (fuori scope)

Le seguenti funzionalità sono escluse da queste tre fasi:

- **Monetizzazione Stripe** (Fase 4): il gating Premium è manuale (admin promuove utenti)
- **Personal records e training load** (Fase 3)
- **Multi-provider** (Fase 6): solo Strava
- **GDPR export/cancellazione** (Fase 5)
- **Dashboard personalizzabili** (Fase 7)
- **AI coaching** (Fase 8)
- **Horizon / Sentry / CDN** (Fase 9): solo se si completa prima del previsto Fase 0
- **API REST pubbliche**

---

## 8. Considerazioni tecniche

### Libreria grafica — ApexCharts ✅
Usare **ApexCharts** installato via npm (`apricot-charts` non è corretto — pacchetto: `apexcharts` + `vue3-apexcharts` se necessario, altrimenti inizializzazione JS vanilla in Alpine.js). NON usare CDN. La stessa istanza npm va usata per tutti i grafici del progetto (US-011, US-012, US-013, US-023, US-024, US-025).

### Mappa polyline — Leaflet.js ✅
Usare **Leaflet.js** installato via npm (`leaflet`) con il plugin `@mapbox/polyline` per decodificare il formato Google Encoded Polyline. Nessun token API richiesto. Tile layer: OpenStreetMap (gratuito, no limiti di scala significativi per questo use case).

### Polling Livewire — 5s accettabile ✅
Usare `wire:poll.5000ms` condizionale: attivo solo quando `$syncStatus === 'running'`. Evitare polling su utenti senza `StravaAccount`. Il broadcast (Pusher/Soketi) è una potenziale ottimizzazione futura, non richiesta ora.

### Notifiche sync — 1 sola failure critica ✅
Inviare la notifica alla **prima** failure critica (non dopo 3 consecutive). Una "failure critica" è: token refresh definitivamente fallito, o errore HTTP non-recuperabile da Strava. Gli errori transitori (timeout di rete, 503 temporaneo) non devono triggerare la notifica. La distinzione va codificata in `ActivitySyncService` o nel job stesso.

### Pagina /premium — Placeholder statico ✅
Nella Fase 2 la pagina `/premium` è una pagina statica con: descrizione dei benefici Premium, istruzioni per contattare l'admin per l'upgrade (email o form contatto), e nota "Pagamento self-service disponibile a breve". Nessuna integrazione Stripe in questa fase.

### Confronto periodi — Grafico + Tabella ✅
Il mockup approvato (`docs/project/ideas.md`, sezione Fase 2) definisce:
- **Header:** titolo "Confronto Periodi", pulsante "Esporta" (placeholder in Fase 2)
- **Selettori:** Periodo A e Periodo B (date picker con range libero), pulsante "Aggiorna confronto"
- **KPI cards (4):** Distanza, Dislivello, Tempo, Attività — ciascuna con valore Periodo B e delta % colorato (verde positivo, rosso negativo)
- **Grafico linee:** "Distanza mensile (km)", due linee (Periodo A = blu, Periodo B = viola), asse X normalizzato per mese (Gen–…), toggle granularità Mese/Settimana, toggle tipo chart Linee/Barre
- **Tabella "Dettaglio confronto":** colonne Metrica (con icona), Periodo A (date + valore), Periodo B (date + valore), Delta assoluto, Delta (%) — colori come KPI cards

### TrendAnalysisService (US-022)
Le query di aggregazione devono usare `DB::raw()` con `DATE_FORMAT` (MySQL/MariaDB) per raggruppare per settimana o mese. Verificare compatibilità con la versione di MariaDB in uso nei container Docker.

### Gating Premium (US-020)
Il middleware `EnsurePremium` è già registrato in `bootstrap/app.php`. Verificare che l'alias sia `premium` prima di applicarlo ai gruppi route.

### Dipendenze esistenti da rispettare
- Laravel 12, Livewire v3, Filament v4, Spatie Permission, Socialite
- MariaDB, Redis, Mailpit (dev), queue worker separato
- Cifratura token Strava via `encrypted` cast su `StravaAccount`

---

## 9. Metriche di successo

| Metrica | Target | Fase |
|---------|--------|------|
| Copertura test su sync job | >80% linee | 0 |
| Bug critici aperti post-beta | 0 | 0 |
| Onboarding beta completato | >60% beta tester | 0 |
| Tempo caricamento `/dashboard` con 10k attività | <2s p95 | 0 |
| Utenti Free che navigano lista attività | >50% sessioni | 1 |
| Notifiche sync fallita inviate correttamente | 100% trigger | 1 |
| Utenti Premium che accedono trend analysis | >80% utenti Premium | 2 |
| Tempo risposta `/premium/trends` | <3s p95 | 2 |

---

## 10. Decisioni prese

Tutte le open question sono state risolte il 2026-06-14:

| # | Domanda | Decisione |
|---|---------|-----------|
| 1 | Libreria grafici | **ApexCharts** via npm — usata per tutti i grafici del progetto |
| 2 | Mappa attività | **Leaflet.js** via npm + `@mapbox/polyline` per decodifica — nessun token API |
| 3 | Soglia notifiche sync | **1 sola failure critica** (non 3 consecutive); failure transitorie non triggerano notifica |
| 4 | Polling status sync | **5 secondi accettabile** — broadcast futuro non richiesto ora |
| 5 | Pagina `/premium` | **Placeholder statico** in Fase 2 — integrazione Stripe rimandata a Fase 4 |
| 6 | Format confronto periodi | **Grafico + Tabella** — mockup approvato: KPI cards + dual-line chart ApexCharts + tabella dettaglio (vedi US-024 e sezione 8) |
