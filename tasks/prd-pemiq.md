# PRD: PEMIQ — Performance Metrics Intelligence

**Versione:** 1.0  
**Data:** 2026-06-13  
**Stato:** Draft  

---

## Indice

1. [Product Vision](#1-product-vision)
2. [User Personas](#2-user-personas)
3. [MVP Scope](#3-mvp-scope)
4. [Premium Scope](#4-premium-scope)
5. [User Stories](#5-user-stories)
6. [Functional Requirements](#6-functional-requirements)
7. [Non-Functional Requirements](#7-non-functional-requirements)
8. [Data Model](#8-data-model)
9. [System Architecture](#9-system-architecture)
10. [Laravel Architecture](#10-laravel-architecture)
11. [Filament Architecture](#11-filament-architecture)
12. [Docker Architecture](#12-docker-architecture)
13. [Acceptance Criteria](#13-acceptance-criteria)
14. [Development Backlog](#14-development-backlog)
15. [Milestone di Sviluppo](#15-milestone-di-sviluppo)
16. [Rischi Tecnici](#16-rischi-tecnici)
17. [Strategia di Rilascio MVP](#17-strategia-di-rilascio-mvp)

---

## 1. Product Vision

### Descrizione

PEMIQ (Performance Metrics Intelligence) è una piattaforma web per atleti di endurance che consente di importare, analizzare e comprendere in modo avanzato i dati delle proprie attività sportive.

La prima versione si integra con **Strava** e permette agli utenti di visualizzare dashboard, statistiche e metriche evolute. L'obiettivo è trasformare le attività registrate in informazioni utili per comprendere l'allenamento, l'evoluzione nel tempo e il proprio livello di performance.

### Obiettivi di Business

- Offrire una piattaforma di analisi delle performance superiore a quella nativa di Strava
- Acquisire utenti tramite il tier gratuito e convertirli in Premium
- Costruire un'architettura estensibile verso Garmin, Polar, Suunto, Komoot e import GPX
- Preparare il terreno per funzionalità AI di coaching automatico

### Posizionamento

| Aspetto | PEMIQ |
|---|---|
| Target | Atleti endurance (running, cycling, MTB, trail, hiking) |
| Differenziatore | Analisi avanzate + AI, multi-provider futuro |
| Modello | Freemium (Free / Premium) |
| MVP | Integrazione Strava + dashboard base |

---

## 2. User Personas

### Persona 1 — Marco, Runner Amatoriale (Free User)
- **Età:** 34 anni
- **Sport:** Running, occasionalmente ciclismo
- **Utilizzo Strava:** Registra ogni uscita da 3 anni
- **Frustrazione:** Strava mostra poco, vuole capire la propria progressione
- **Obiettivo su PEMIQ:** Visualizzare trend e statistiche storiche aggregate
- **Competenza tech:** Media — usa app su smartphone, non è sviluppatore

### Persona 2 — Silvia, Trail Runner Agonista (Premium User)
- **Età:** 42 anni
- **Sport:** Trail running, hiking
- **Utilizzo Strava:** Carica ogni sessione, monitora HR e potenza
- **Frustrazione:** Vuole analisi avanzate del carico allenante e confronto periodi
- **Obiettivo su PEMIQ:** Personal records, training load, analisi evoluta delle metriche
- **Competenza tech:** Alta — abituata a tool professionali di analisi

### Persona 3 — Admin PEMIQ
- **Ruolo:** Gestione operativa della piattaforma
- **Compiti:** Gestione utenti, promozione Premium, monitoring sincronizzazioni, troubleshooting
- **Strumento:** Backoffice Filament
- **Competenza tech:** Alta

---

## 3. MVP Scope

L'MVP include esclusivamente le funzionalità necessarie per validare il prodotto con utenti reali.

### Incluso nell'MVP

- [ ] Registrazione e autenticazione (email/password + verifica email)
- [ ] Reset password, cambio password, gestione profilo
- [ ] Integrazione OAuth Strava (collegamento, scollegamento, sincronizzazione)
- [ ] Import attività storiche e nuove da Strava tramite Queue
- [ ] Dashboard base (overview, analisi annuale, mensile, distribuzione sport)
- [ ] Backoffice Filament (gestione utenti, account Strava, attività, log sincronizzazioni)
- [ ] Impersonificazione utente con audit log
- [ ] Gestione ruoli: Administrator, Free User, Premium User
- [ ] Promozione Premium manuale da parte dell'admin
- [ ] Infrastruttura Docker (local, UAT, production)
- [ ] Supporto multilingua: Italiano e Inglese
- [ ] Responsive Design, Mobile First

### Escluso dall'MVP (Future Release)

- Pagamento self-service per Premium (Stripe)
- Funzionalità Premium (trend analysis, personal records, training load, performance metrics, dashboard personalizzabili)
- Funzionalità AI
- API pubbliche REST
- Integrazioni con Garmin, Polar, Suunto, Komoot
- Import GPX manuale
- App mobile

---

## 4. Premium Scope

Le seguenti funzionalità sono pianificate per la versione Premium (post-MVP):

### Trend Analysis
- Andamento delle attività nel tempo (grafici linea/area)
- Confronto tra periodi personalizzati
- Confronto anno su anno per ogni metrica

### Personal Records
- Rilevamento automatico dei record personali per distanza, velocità, dislivello, HR
- Storico dei record con data e attività di riferimento

### Training Load
- Calcolo del carico allenante settimanale/mensile
- Visualizzazione dell'andamento dello sforzo nel tempo
- Alert su sovraccarico o sotto-allenamento

### Performance Metrics
- Metriche aggregate avanzate (VO2max stimato, TRIMP, TSS)
- Analisi della progressione per sport

### Dashboard Personalizzabili
- Widget configurabili per ogni utente
- Viste salvate e condivisibili

### Modello Premium (MVP → Futuro)
- **MVP:** Promozione Premium manuale da parte dell'admin (campo `is_premium` + `premium_expires_at`)
- **Futuro:** Integrazione Stripe con subscription mensile/annuale e gestione automatica dello stato

---

## 5. User Stories

### Autenticazione e Profilo

---

#### US-001: Registrazione utente
**Descrizione:** Come visitatore, voglio registrarmi con email e password per accedere alla piattaforma.

**Acceptance Criteria:**
- [ ] Form con campi: nome, email, password, conferma password
- [ ] Validazione lato client (Alpine.js) e lato server
- [ ] Email univoca — errore esplicito se già registrata
- [ ] Password minimo 8 caratteri
- [ ] Dopo la registrazione viene inviata email di verifica
- [ ] Redirect a pagina "Controlla la tua email"
- [ ] Typecheck/lint passa

---

#### US-002: Verifica email
**Descrizione:** Come utente appena registrato, voglio verificare il mio indirizzo email per attivare l'account.

**Acceptance Criteria:**
- [ ] Email di verifica contiene link con token firmato (Laravel `MustVerifyEmail`)
- [ ] Click sul link verifica l'account e fa il redirect al login
- [ ] Link scade dopo 60 minuti
- [ ] Possibilità di richiedere un nuovo link di verifica
- [ ] Accesso negato alle aree protette senza verifica completata
- [ ] In UAT le email sono intercettate da Mailpit

---

#### US-003: Login
**Descrizione:** Come utente registrato e verificato, voglio effettuare il login per accedere alla dashboard.

**Acceptance Criteria:**
- [ ] Form con email e password
- [ ] Errore generico su credenziali errate (no distinzione email/password per sicurezza)
- [ ] Protezione brute force (throttling dopo 5 tentativi)
- [ ] Redirect alla dashboard dopo login
- [ ] Redirect al login con messaggio se email non verificata

---

#### US-004: Logout
**Descrizione:** Come utente autenticato, voglio effettuare il logout per proteggere il mio account.

**Acceptance Criteria:**
- [ ] Pulsante logout sempre visibile nel menu di navigazione
- [ ] Sessione invalidata al logout
- [ ] Redirect alla home/login dopo logout

---

#### US-005: Reset password
**Descrizione:** Come utente che ha dimenticato la password, voglio ricevere un'email per reimpostarla.

**Acceptance Criteria:**
- [ ] Form "Password dimenticata" con campo email
- [ ] Email inviata con link di reset (scadenza 60 minuti)
- [ ] Form di impostazione nuova password con conferma
- [ ] Messaggio di successo dopo reset
- [ ] In UAT email intercettata da Mailpit

---

#### US-006: Cambio password
**Descrizione:** Come utente autenticato, voglio cambiare la mia password dalla pagina profilo.

**Acceptance Criteria:**
- [ ] Form con: password attuale, nuova password, conferma nuova password
- [ ] Validazione che la password attuale sia corretta
- [ ] Nuova password minimo 8 caratteri
- [ ] Messaggio di successo dopo aggiornamento

---

#### US-007: Gestione profilo
**Descrizione:** Come utente autenticato, voglio aggiornare i miei dati personali.

**Acceptance Criteria:**
- [ ] Modifica nome e email (con re-verifica se email cambia)
- [ ] Salvataggio con feedback visivo (messaggio successo/errore)
- [ ] Typecheck/lint passa

---

### Integrazione Strava

---

#### US-008: Collegamento account Strava
**Descrizione:** Come utente Free, voglio collegare il mio account Strava per importare le attività.

**Acceptance Criteria:**
- [ ] Pulsante "Collega Strava" nella dashboard o nella pagina profilo
- [ ] Redirect al flusso OAuth Strava con scope `activity:read_all`
- [ ] Dopo autorizzazione, salvataggio di: `strava_athlete_id`, `access_token`, `refresh_token`, `token_expires_at`
- [ ] `connection_status` impostato a `connected`
- [ ] Redirect alla dashboard con messaggio di successo
- [ ] Gestione errore se utente nega i permessi su Strava

---

#### US-009: Scollegamento account Strava
**Descrizione:** Come utente, voglio scollegare il mio account Strava.

**Acceptance Criteria:**
- [ ] Pulsante "Scollega Strava" visibile se account collegato
- [ ] Conferma prima di scollegare
- [ ] Token revocato su Strava via API
- [ ] Record `StravaAccount` aggiornato: `connection_status = disconnected`, token nullificati
- [ ] Le attività importate in precedenza rimangono nel database

---

#### US-010: Sincronizzazione attività storiche
**Descrizione:** Come utente con Strava collegato, voglio importare tutte le mie attività storiche.

**Acceptance Criteria:**
- [ ] Pulsante "Sincronizza attività storiche" nella dashboard
- [ ] Job accodato in Queue Worker
- [ ] Visualizzazione dello stato della sincronizzazione (in corso / completata / errore)
- [ ] Import paginato (gestione rate limit Strava: max 100 attività per richiesta, 200 richieste ogni 15 min)
- [ ] Evitare duplicati tramite `strava_activity_id` (upsert)
- [ ] Log della sincronizzazione in `SyncLogs`
- [ ] Notifica utente al termine (email o notifica in-app)

---

#### US-011: Sincronizzazione nuove attività
**Descrizione:** Come utente, voglio che le nuove attività su Strava vengano sincronizzate automaticamente.

**Acceptance Criteria:**
- [ ] Job schedulato (Laravel Scheduler) che controlla nuove attività ogni ora
- [ ] Sincronizzazione incrementale: solo attività dopo `last_sync_at`
- [ ] `last_sync_at` aggiornato dopo ogni sync riuscita
- [ ] Gestione token scaduti: refresh automatico tramite `refresh_token`
- [ ] Errori loggati in `SyncLogs`

---

#### US-012: Gestione errori sincronizzazione
**Descrizione:** Come sistema, devo gestire gli errori dell'API Strava in modo robusto.

**Acceptance Criteria:**
- [ ] Rate limit (HTTP 429): retry con backoff esponenziale
- [ ] Token scaduto (HTTP 401): refresh automatico, poi retry
- [ ] Errore generico API: log dell'errore, job marcato come `failed`
- [ ] Job falliti visibili nel backoffice Filament
- [ ] Nessun dato corrotto in caso di sincronizzazione parziale

---

### Dashboard Base

---

#### US-013: Overview statistiche generali
**Descrizione:** Come utente, voglio vedere un riepilogo delle mie statistiche totali nella dashboard.

**Acceptance Criteria:**
- [ ] Card con: numero totale attività, distanza totale, dislivello totale, tempo totale, tempo in movimento
- [ ] Valori calcolati su tutte le attività importate dell'utente
- [ ] Unità di misura: km per distanza, metri per dislivello, formato h:mm per il tempo
- [ ] Aggiornamento real-time dopo nuova sincronizzazione
- [ ] Responsive: visualizzazione ottimale su mobile
- [ ] Verify in browser

---

#### US-014: Analisi annuale
**Descrizione:** Come utente, voglio vedere le statistiche aggregate per anno.

**Acceptance Criteria:**
- [ ] Grafico/tabella con: attività per anno, distanza per anno, dislivello per anno, ore per anno
- [ ] Copertura degli ultimi N anni disponibili nei dati
- [ ] Responsive: visualizzazione ottimale su mobile
- [ ] Verify in browser

---

#### US-015: Analisi mensile
**Descrizione:** Come utente, voglio vedere le statistiche aggregate per mese.

**Acceptance Criteria:**
- [ ] Grafico/tabella con: attività per mese, distanza per mese, dislivello per mese, ore per mese
- [ ] Selettore anno per filtrare i mesi
- [ ] Responsive: visualizzazione ottimale su mobile
- [ ] Verify in browser

---

#### US-016: Distribuzione per sport
**Descrizione:** Come utente, voglio vedere come si distribuiscono le mie attività tra i vari sport.

**Acceptance Criteria:**
- [ ] Grafico (donut/pie o bar) con: numero attività per sport, distanza per sport, tempo per sport
- [ ] Legenda con colori distinti per sport
- [ ] Responsive: visualizzazione ottimale su mobile
- [ ] Verify in browser

---

### Backoffice Filament

---

#### US-017: Gestione utenti (admin)
**Descrizione:** Come admin, voglio gestire gli utenti dalla piattaforma backoffice.

**Acceptance Criteria:**
- [ ] Lista utenti con filtri per: nome, email, ruolo, stato Premium, data registrazione
- [ ] Dettaglio utente con tutti i campi
- [ ] Modifica dati utente
- [ ] Promozione/revoca Premium (con impostazione `premium_expires_at`)
- [ ] Azioni di massa: promozione, revoca

---

#### US-018: Visualizzazione account Strava (admin)
**Descrizione:** Come admin, voglio vedere lo stato degli account Strava collegati.

**Acceptance Criteria:**
- [ ] Lista account Strava con: utente, stato connessione, ultima sincronizzazione, `strava_athlete_id`
- [ ] Filtro per stato connessione
- [ ] Azione "Forza sincronizzazione" per account specifico

---

#### US-019: Visualizzazione attività (admin)
**Descrizione:** Come admin, voglio visualizzare e filtrare le attività importate.

**Acceptance Criteria:**
- [ ] Lista attività con filtri per: utente, sport, data, nome
- [ ] Dettaglio attività con tutti i campi
- [ ] Export CSV (opzionale MVP)

---

#### US-020: Log sincronizzazioni (admin)
**Descrizione:** Come admin, voglio monitorare le sincronizzazioni e individuare errori.

**Acceptance Criteria:**
- [ ] Lista log con: utente, tipo sync, stato (success/failed), data, attività importate, messaggio errore
- [ ] Filtro per stato e utente
- [ ] Dettaglio log con `error_message` completo
- [ ] Job falliti visibili con possibilità di retry

---

#### US-021: Impersonificazione utente (admin)
**Descrizione:** Come admin, voglio assumere temporaneamente l'identità di un utente per supporto e debug.

**Acceptance Criteria:**
- [ ] Pulsante "Impersona" nella pagina dettaglio utente nel backoffice
- [ ] Dopo impersonificazione: banner persistente visibile "Stai impersonando [nome utente]"
- [ ] Tutte le azioni eseguite durante l'impersonificazione vengono loggate in `AuditLogs`
- [ ] Pulsante "Esci dall'impersonificazione" sempre visibile nel banner
- [ ] Al termine, redirect al backoffice con sessione admin ripristinata
- [ ] Impersonificazione di un altro admin non consentita

---

## 6. Functional Requirements

### Autenticazione e Sicurezza

- **FR-1:** Il sistema deve richiedere la verifica dell'indirizzo email prima di consentire l'accesso alle funzionalità protette.
- **FR-2:** Il sistema deve implementare throttling sul login (max 5 tentativi per 1 minuto per IP).
- **FR-3:** Il sistema deve invalidare tutti i token di reset password dopo l'utilizzo.
- **FR-4:** I token Strava (access e refresh) devono essere crittografati a riposo nel database.
- **FR-5:** Il sistema deve supportare i ruoli: `administrator`, `user` (gestiti con Spatie Laravel Permission).
- **FR-6:** Gli utenti Premium sono identificati dal campo `is_premium = true` e `premium_expires_at`.

### Integrazione Strava

- **FR-7:** Il flusso OAuth Strava deve richiedere lo scope `activity:read_all`.
- **FR-8:** Il sistema deve gestire automaticamente il refresh del token Strava prima della scadenza.
- **FR-9:** La sincronizzazione storica deve essere eseguita come job asincrono in Queue.
- **FR-10:** Il sistema deve rispettare i rate limit di Strava: 100 richieste/15 min, 1000 richieste/giorno.
- **FR-11:** Ogni attività importata deve essere identificata univocamente da `strava_activity_id` per evitare duplicati.
- **FR-12:** Il sistema deve salvare il campo `raw_data` (JSON) con la risposta completa dell'API Strava per ogni attività.

### Attività

- **FR-13:** Il sistema deve importare le seguenti tipologie di sport: Running, Trail Running, Cycling, Mountain Bike, Hiking, Walking.
- **FR-14:** Per ogni attività importata, il sistema deve popolare tutti i campi definiti nel data model (valori null se non presenti nell'API Strava).
- **FR-15:** La polyline della mappa deve essere salvata nel formato Google Encoded Polyline.

### Dashboard

- **FR-16:** Tutte le statistiche della dashboard devono essere calcolate esclusivamente sulle attività dell'utente autenticato.
- **FR-17:** Le query di aggregazione per le dashboard devono essere ottimizzate con indici appropriati.
- **FR-18:** Le unità di misura devono essere: km per distanza, m per dislivello, formato h:mm:ss per il tempo.

### Backoffice

- **FR-19:** L'accesso al backoffice Filament (`/admin`) deve essere riservato esclusivamente agli utenti con ruolo `administrator`.
- **FR-20:** Ogni azione di impersonificazione deve generare un record in `AuditLogs` con admin, utente target, azione e timestamp.
- **FR-21:** L'admin non può impersonare un altro admin.

### Ambienti

- **FR-22:** In ambiente local e UAT, tutte le email devono essere intercettate da Mailpit e non recapitate a destinatari reali.
- **FR-23:** In produzione, il sistema deve usare un provider SMTP esterno (Postmark, Mailgun o Amazon SES).
- **FR-24:** Mailpit non deve essere installato o accessibile in produzione.

### Internazionalizzazione

- **FR-25:** L'interfaccia utente deve supportare italiano e inglese tramite i file di localizzazione Laravel.
- **FR-26:** La lingua predefinita è italiano; l'utente può cambiarla dalle impostazioni del profilo.

---

## 7. Non-Functional Requirements

### Performance

- **NFR-1:** Le pagine della dashboard devono caricarsi in meno di 2 secondi (p95) con 1000 attività per utente.
- **NFR-2:** Le query di aggregazione devono utilizzare indici compositi su `(user_id, started_at, sport_type)`.
- **NFR-3:** Le sincronizzazioni Strava devono essere completamente asincrone e non bloccare il thread HTTP.

### Sicurezza

- **NFR-4:** I token OAuth Strava devono essere cifrati con `app.key` prima del salvataggio (Laravel Encryption).
- **NFR-5:** Tutte le comunicazioni devono avvenire su HTTPS (SSL obbligatorio in produzione).
- **NFR-6:** Il sistema deve essere conforme al GDPR: possibilità di esportare e cancellare i propri dati.
- **NFR-7:** Le password devono essere hashate con bcrypt (default Laravel).
- **NFR-8:** Il backoffice deve avere protezione CSRF su tutti i form.

### Scalabilità

- **NFR-9:** L'architettura deve supportare l'aggiunta di nuovi provider di integrazione senza modifiche al core (pattern Strategy/Adapter).
- **NFR-10:** Le Queue devono essere gestite con Redis per supportare carichi elevati.

### Affidabilità

- **NFR-11:** I job falliti devono essere visibili nel backoffice e rilanciabili manualmente.
- **NFR-12:** Il sistema deve implementare retry automatico per i job di sincronizzazione (max 3 tentativi con backoff esponenziale).
- **NFR-13:** In produzione devono essere attivi backup automatici giornalieri del database.

### Monitoraggio

- **NFR-14:** In produzione deve essere attivo il monitoraggio applicativo (errori, performance).
- **NFR-15:** In produzione deve essere monitorato lo stato delle Queue e dello Scheduler.

### UI/UX

- **NFR-16:** L'interfaccia deve essere Mobile First e Responsive.
- **NFR-17:** Il design system deve essere coerente e basato su Tailwind CSS.
- **NFR-18:** Livewire deve essere utilizzato per componenti interattivi senza ricaricare la pagina.

---

## 8. Data Model

### Tabella: `users`

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `name` | varchar(255) | |
| `email` | varchar(255) UNIQUE | |
| `password` | varchar(255) | Bcrypt hash |
| `email_verified_at` | timestamp nullable | |
| `is_premium` | boolean | Default: false |
| `premium_started_at` | timestamp nullable | |
| `premium_expires_at` | timestamp nullable | Null = no scadenza |
| `remember_token` | varchar(100) nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

> Nota: `role` è gestito tramite Spatie Permission (tabella `roles` e pivot `model_has_roles`).

---

### Tabella: `strava_accounts`

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | bigint FK → users | |
| `strava_athlete_id` | bigint UNIQUE | |
| `access_token` | text | Cifrato |
| `refresh_token` | text | Cifrato |
| `token_expires_at` | timestamp | |
| `last_sync_at` | timestamp nullable | |
| `connection_status` | enum('connected','disconnected','error') | Default: connected |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### Tabella: `activities`

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | bigint FK → users | |
| `strava_activity_id` | bigint UNIQUE | |
| `name` | varchar(255) | |
| `sport_type` | varchar(100) | Es: 'Run', 'Ride', 'TrailRun' |
| `started_at` | timestamp | Data/ora UTC dell'attività |
| `distance` | float nullable | Metri |
| `elapsed_time` | integer nullable | Secondi |
| `moving_time` | integer nullable | Secondi |
| `elevation_gain` | float nullable | Metri |
| `average_speed` | float nullable | m/s |
| `max_speed` | float nullable | m/s |
| `average_heartrate` | float nullable | bpm |
| `max_heartrate` | float nullable | bpm |
| `average_watts` | float nullable | Watt |
| `calories` | integer nullable | kcal |
| `polyline` | text nullable | Google Encoded Polyline |
| `raw_data` | json | Risposta completa API Strava |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indici:**
- `(user_id, started_at)` — per query temporali
- `(user_id, sport_type)` — per filtri per sport
- `(user_id, started_at, sport_type)` — indice composito per aggregazioni dashboard

---

### Tabella: `sync_logs`

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `user_id` | bigint FK → users | |
| `strava_account_id` | bigint FK → strava_accounts | |
| `sync_type` | enum('historical','incremental') | |
| `status` | enum('pending','running','completed','failed') | |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |
| `activities_imported` | integer | Default: 0 |
| `error_message` | text nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### Tabella: `audit_logs`

| Campo | Tipo | Note |
|---|---|---|
| `id` | bigint PK | |
| `admin_user_id` | bigint FK → users | Admin che ha agito |
| `target_user_id` | bigint FK → users | Utente target |
| `action` | varchar(255) | Es: 'impersonate_start', 'impersonate_stop' |
| `metadata` | json nullable | Dati aggiuntivi |
| `created_at` | timestamp | |

---

### Tabella Spatie (generate automaticamente)

- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

---

## 9. System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     CLIENT (Browser)                     │
│              Blade + Livewire + Alpine.js                │
└─────────────────────┬───────────────────────────────────┘
                      │ HTTPS
┌─────────────────────▼───────────────────────────────────┐
│                   NGINX (reverse proxy)                  │
└─────────────────────┬───────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────┐
│              LARAVEL APPLICATION (PHP-FPM)               │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │   Web Routes │  │  Filament    │  │  Queue Jobs   │  │
│  │  (Frontend)  │  │  Backoffice  │  │  (Sync Strava)│  │
│  └──────────────┘  └──────────────┘  └───────────────┘  │
│  ┌──────────────────────────────────────────────────┐   │
│  │               Service Layer                       │   │
│  │  StravaService │ SyncService │ DashboardService  │   │
│  └──────────────────────────────────────────────────┘   │
└──────┬───────────────────┬─────────────────┬────────────┘
       │                   │                 │
┌──────▼──────┐   ┌────────▼──────┐  ┌──────▼──────┐
│   MariaDB   │   │     Redis     │  │  Strava API │
│  (primary   │   │ (cache/queue) │  │  (OAuth +   │
│   storage)  │   │               │  │   REST)     │
└─────────────┘   └───────────────┘  └─────────────┘
```

### Flusso Sincronizzazione Strava

```
User → Click "Sincronizza" → Controller → DispatchJob (Redis Queue)
  → QueueWorker → StravaService::fetchActivities()
    → Strava API (paginato, con rate limit handling)
    → ActivityRepository::upsert()
    → SyncLog::update(status: completed)
  → Notification → User
```

---

## 10. Laravel Architecture

### Struttura Directory

```
app/
├── Console/
│   └── Kernel.php                     # Scheduler configuration
├── Filament/
│   └── Resources/
│       ├── UserResource.php
│       ├── StravaAccountResource.php
│       ├── ActivityResource.php
│       └── SyncLogResource.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   └── StravaController.php
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   │   ├── OverviewStats.php
│   │   │   ├── AnnualAnalysis.php
│   │   │   ├── MonthlyAnalysis.php
│   │   │   └── SportDistribution.php
│   │   └── Strava/
│   │       └── ConnectionStatus.php
│   └── Middleware/
│       └── EnsurePremium.php
├── Jobs/
│   ├── SyncStravaHistoricalActivities.php
│   └── SyncStravaIncrementalActivities.php
├── Models/
│   ├── User.php
│   ├── StravaAccount.php
│   ├── Activity.php
│   ├── SyncLog.php
│   └── AuditLog.php
├── Services/
│   ├── Strava/
│   │   ├── StravaOAuthService.php     # Gestione OAuth
│   │   ├── StravaApiService.php       # Chiamate API
│   │   └── StravaTokenService.php     # Refresh token
│   ├── Sync/
│   │   └── ActivitySyncService.php    # Logica di sync
│   └── Dashboard/
│       └── DashboardStatsService.php  # Calcolo statistiche
├── Providers/
│   ├── AppServiceProvider.php
│   └── AuthServiceProvider.php
└── Policies/
    └── ActivityPolicy.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   ├── dashboard/
│   │   └── index.blade.php
│   └── profile/
└── lang/
    ├── it/
    └── en/
```

### Pattern Architetturali

- **Service Layer:** La logica di business è nei Service, i Controller sono thin (solo HTTP handling).
- **Repository Pattern (opzionale MVP):** Per query complesse, usare metodi dedicati nel Model o classi Query.
- **Jobs e Queue:** Ogni sincronizzazione è un Job separato con `ShouldQueue`.
- **Events/Listeners:** Al termine di una sync, emettere un evento `StravaSyncCompleted` per notifiche.
- **Strategy Pattern (futuro):** `IntegrationProviderInterface` per aggiungere Garmin/Polar senza modificare il core.

---

## 11. Filament Architecture

### Configurazione

```php
// Panel provider: /admin
// Guard: web (con middleware role:administrator)
// Colori brand: personalizzati PEMIQ
// Navigation groups: Utenti, Strava, Contenuto, Sistema
```

### Resources

| Resource | Model | Funzionalità |
|---|---|---|
| `UserResource` | User | CRUD, filtri, gestione Premium, impersonificazione |
| `StravaAccountResource` | StravaAccount | Lista read-only, stato, forza sync |
| `ActivityResource` | Activity | Lista read-only, filtri sport/utente/data |
| `SyncLogResource` | SyncLog | Lista read-only, filtri, retry job falliti |
| `AuditLogResource` | AuditLog | Lista read-only, filtri admin/target |

### Widget Dashboard Admin

- Totale utenti registrati
- Utenti Premium attivi
- Account Strava collegati
- Sincronizzazioni ultime 24h (completate / fallite)
- Attività totali nel sistema

### Impersonificazione

Utilizzare il package `filament-impersonate` o implementazione custom che:
1. Salva l'ID admin in sessione (`impersonating_admin_id`)
2. Autentica come utente target
3. Mostra banner persistente
4. Logga in `AuditLogs`

---

## 12. Docker Architecture

### Servizi per Ambiente

| Servizio | Local | UAT | Production |
|---|---|---|---|
| `app` (Laravel PHP-FPM) | ✅ | ✅ | ✅ |
| `nginx` | ✅ | ✅ | ✅ |
| `mariadb` | ✅ | ✅ | ✅ |
| `redis` | ✅ | ✅ | ✅ |
| `queue-worker` | ✅ | ✅ | ✅ |
| `scheduler` | ✅ | ✅ | ✅ |
| `mailpit` | ✅ | ✅ | ❌ |

### Struttura Docker Compose

```
docker/
├── local/
│   ├── docker-compose.yml
│   ├── nginx/
│   │   └── default.conf              # pemiq.local
│   ├── php/
│   │   └── Dockerfile
│   └── .env.example
├── uat/
│   ├── docker-compose.yml
│   └── nginx/
│       └── default.conf              # uat.pemiq.com + SSL
└── production/
    ├── docker-compose.yml
    └── nginx/
        └── default.conf              # pemiq.com + SSL
```

### Variabili d'Ambiente per Ambiente

```env
# Local
APP_ENV=local
APP_URL=http://pemiq.local
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# UAT
APP_ENV=staging
APP_URL=https://uat.pemiq.com
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Production
APP_ENV=production
APP_URL=https://pemiq.com
MAIL_MAILER=postmark  # o mailgun / ses
```

### Volumi e Persistenza

- `mariadb_data` — dati database (persistente)
- `redis_data` — dati Redis (persistente in produzione)
- `app_storage` — storage Laravel (logs, uploads)

---

## 13. Acceptance Criteria

### AC-1: Setup Iniziale Progetto
- [ ] Laravel installato e configurato (PHP 8.4+)
- [ ] Docker Compose funzionante per ambiente local
- [ ] URL `http://pemiq.local` raggiungibile
- [ ] MariaDB, Redis, Mailpit connessi e funzionanti
- [ ] Spatie Laravel Permission installato e configurato
- [ ] Filament installato e accessibile a `/admin`
- [ ] Ruoli creati: `administrator`, `user`

### AC-2: Autenticazione Completa
- [ ] Registrazione con invio email verifica (intercettata da Mailpit in local)
- [ ] Login funzionante solo per email verificate
- [ ] Reset password funzionante end-to-end
- [ ] Throttling login attivo

### AC-3: Integrazione Strava
- [ ] Flusso OAuth completato con redirect e salvataggio token
- [ ] Token cifrati nel database
- [ ] Sincronizzazione storica accodata e completata senza errori
- [ ] Rate limit gestito correttamente (nessun errore 429 non gestito)
- [ ] Duplicati non creati su sync multiple

### AC-4: Dashboard Base
- [ ] Overview mostra statistiche corrette per l'utente
- [ ] Analisi annuale mostra dati raggruppati per anno
- [ ] Analisi mensile mostra dati raggruppati per mese con selettore anno
- [ ] Distribuzione sport mostra grafico corretto
- [ ] Dashboard Mobile First: visualizzazione corretta su viewport 375px

### AC-5: Backoffice Filament
- [ ] Solo utenti `administrator` possono accedere a `/admin`
- [ ] Lista utenti con filtri funzionanti
- [ ] Promozione/revoca Premium funzionante
- [ ] Log sincronizzazioni visibili con stato corretto
- [ ] Impersonificazione funzionante con banner e audit log

### AC-6: Qualità e Sicurezza
- [ ] Token Strava cifrati (verifica che il valore in DB non sia in chiaro)
- [ ] Nessun dato di un utente visibile a un altro utente (test isolamento)
- [ ] Tutti i form con protezione CSRF
- [ ] Typecheck e lint senza errori
- [ ] Test automatici (Feature test per auth + sync) passano

---

## 14. Development Backlog

Ordinato per priorità (P1 = bloccante, P2 = alta, P3 = normale).

### P1 — Fondamenta

| # | Task | Stima |
|---|---|---|
| 1 | Setup progetto Laravel + Docker Compose local | 4h |
| 2 | Configurazione MariaDB, Redis, Mailpit, Nginx | 2h |
| 3 | Installazione Spatie Permission + seed ruoli | 1h |
| 4 | Installazione Filament + configurazione panel | 2h |
| 5 | Migrations: users, strava_accounts, activities, sync_logs, audit_logs | 3h |
| 6 | Autenticazione: registrazione + verifica email | 4h |
| 7 | Autenticazione: login, logout, throttling | 2h |
| 8 | Autenticazione: reset password, cambio password | 2h |
| 9 | Pagina profilo utente (modifica nome/email) | 2h |

### P1 — Integrazione Strava

| # | Task | Stima |
|---|---|---|
| 10 | Configurazione Laravel Socialite + Strava provider | 3h |
| 11 | Flusso OAuth: collegamento account Strava | 4h |
| 12 | Flusso OAuth: scollegamento account Strava | 2h |
| 13 | StravaApiService: fetch attività con paginazione | 4h |
| 14 | StravaTokenService: refresh automatico token | 2h |
| 15 | Job SyncStravaHistoricalActivities + rate limit handling | 6h |
| 16 | Job SyncStravaIncrementalActivities | 3h |
| 17 | Scheduler: esecuzione sync incrementale ogni ora | 1h |
| 18 | SyncLog: creazione e aggiornamento log | 2h |

### P1 — Dashboard Base

| # | Task | Stima |
|---|---|---|
| 19 | Layout principale (Blade + Alpine.js + Livewire) | 4h |
| 20 | Componente Livewire: OverviewStats | 3h |
| 21 | Componente Livewire: AnnualAnalysis (grafico) | 4h |
| 22 | Componente Livewire: MonthlyAnalysis (grafico) | 3h |
| 23 | Componente Livewire: SportDistribution (grafico) | 3h |
| 24 | Ottimizzazione query con indici compositi | 2h |

### P2 — Backoffice Filament

| # | Task | Stima |
|---|---|---|
| 25 | UserResource: lista, filtri, modifica, gestione Premium | 4h |
| 26 | StravaAccountResource: lista, stato | 2h |
| 27 | ActivityResource: lista, filtri | 2h |
| 28 | SyncLogResource: lista, filtri, dettaglio errori | 2h |
| 29 | AuditLogResource: lista, filtri | 1h |
| 30 | Impersonificazione utente + banner + audit log | 4h |
| 31 | Widget dashboard admin | 2h |

### P2 — Infrastruttura Completamento

| # | Task | Stima |
|---|---|---|
| 32 | Docker Compose per ambiente UAT | 3h |
| 33 | Docker Compose per ambiente Production | 3h |
| 34 | Configurazione SSL (produzione) | 2h |
| 35 | Configurazione SMTP provider produzione | 1h |
| 36 | Internazionalizzazione IT/EN | 4h |
| 37 | Middleware EnsurePremium per funzionalità future | 1h |

### P3 — Qualità e Test

| # | Task | Stima |
|---|---|---|
| 38 | Feature test: flusso autenticazione | 4h |
| 39 | Feature test: flusso integrazione Strava (mock API) | 4h |
| 40 | Feature test: accesso backoffice + impersonificazione | 2h |
| 41 | Revisione sicurezza: cifratura token, CSRF, isolamento dati | 2h |
| 42 | Performance: profiling query dashboard con dataset ampio | 2h |

---

## 15. Milestone di Sviluppo

### Milestone 1 — Fondamenta e Auth (Settimana 1-2)
**Goal:** Progetto configurato, autenticazione funzionante, ambienti Docker operativi.

**Deliverable:**
- Docker Compose local funzionante (`http://pemiq.local`)
- Registrazione, verifica email, login, logout, reset password
- Backoffice Filament accessibile a `/admin`
- Ruoli e permessi configurati

**Definition of Done:** Un admin può accedere al backoffice. Un utente può registrarsi, verificare l'email e fare login.

---

### Milestone 2 — Integrazione Strava (Settimana 3-4)
**Goal:** Utenti possono collegare Strava e importare le attività.

**Deliverable:**
- Flusso OAuth Strava completo (collega/scollega)
- Sincronizzazione storica funzionante via Queue
- Sincronizzazione incrementale schedulata
- Token cifrati, rate limit gestito, log sincronizzazioni attivi

**Definition of Done:** Un utente può collegare Strava, avviare la sync storica e vedere le attività nel backoffice.

---

### Milestone 3 — Dashboard Base (Settimana 5-6)
**Goal:** Dashboard pubblica con le 4 sezioni statistiche operative.

**Deliverable:**
- Overview statistiche totali
- Analisi annuale con grafico
- Analisi mensile con grafico e selettore anno
- Distribuzione per sport con grafico
- Design responsive Mobile First

**Definition of Done:** Un utente con attività importate vede dati corretti nella dashboard su desktop e mobile.

---

### Milestone 4 — Backoffice Completo e Infrastruttura (Settimana 7-8)
**Goal:** Backoffice Filament completo, impersonificazione, ambienti UAT e Production.

**Deliverable:**
- Tutti i Resource Filament operativi
- Impersonificazione con audit log e banner
- Docker Compose UAT e Production
- SSL produzione
- Internazionalizzazione IT/EN

**Definition of Done:** Piattaforma deployata su UAT, test end-to-end completati, pronta per rilascio MVP.

---

### Milestone 5 — QA e Release MVP (Settimana 9)
**Goal:** Test approfonditi, bug fixing, deploy produzione.

**Deliverable:**
- Feature test automatici completati
- Revisione sicurezza completata
- Performance ottimizzata
- Deploy su `https://pemiq.com`

---

## 16. Rischi Tecnici

### R-1: Rate Limit API Strava — Alta Probabilità / Alto Impatto
**Descrizione:** Strava impone 100 richieste/15 min e 1000 richieste/giorno. Un utente con molte attività storiche può saturare il limite.

**Mitigazione:**
- Implementare backoff esponenziale su HTTP 429
- Distributore con delay tra le richieste
- Sincronizzazione storica distribuita su più job/cicli
- Monitorare l'utilizzo del rate limit in `SyncLogs`

---

### R-2: Token Strava Scaduti — Media Probabilità / Medio Impatto
**Descrizione:** I token Strava scadono ogni 6 ore. Se il refresh fallisce, la sync si interrompe.

**Mitigazione:**
- Verificare la scadenza prima di ogni chiamata API
- Implementare retry automatico dopo refresh
- Loggare errori di refresh in `SyncLogs` con stato `error`
- Notificare l'utente se la riconnessione è necessaria

---

### R-3: Quantità Dati Utente — Bassa Probabilità / Alto Impatto
**Descrizione:** Atleti con anni di attività possono avere migliaia di record. Le query di aggregazione per la dashboard potrebbero diventare lente.

**Mitigazione:**
- Indici compositi su `(user_id, started_at, sport_type)` fin dall'inizio
- Valutare materialized/cached aggregates per dashboard (Redis)
- Profiling con dataset di test ampio (10k+ attività) prima del rilascio

---

### R-4: Cifratura Token nel Database — Media Probabilità / Alto Impatto
**Descrizione:** Se `APP_KEY` viene persa o ruotata, i token cifrati diventano inutilizzabili.

**Mitigazione:**
- Backup sicuro di `APP_KEY` separato dal backup del database
- Procedura documentata per rotazione chiave con re-cifratura dei token
- Non includere `APP_KEY` in ambienti non sicuri

---

### R-5: Compatibilità Futura Multi-Provider — Bassa Probabilità / Medio Impatto
**Descrizione:** Aggiungere Garmin o Polar in futuro potrebbe richiedere refactoring significativo se l'architettura è troppo Strava-specific.

**Mitigazione:**
- Usare interfaccia `IntegrationProviderInterface` fin dall'inizio
- Separare `StravaApiService` dalla logica di sync generica in `ActivitySyncService`
- Il data model `activities` non deve avere dipendenze Strava-specific (solo `strava_activity_id`)

---

### R-6: Qualità Dati Strava — Bassa Probabilità / Basso Impatto
**Descrizione:** Non tutte le attività Strava hanno tutti i campi (HR, watt, ecc.). Campi null potrebbero causare errori nei calcoli.

**Mitigazione:**
- Tutti i campi di `activities` nullable (già nel data model)
- Gestione null-safe nei calcoli di aggregazione (`COALESCE`, `SUM(NULLIF(...))`)
- Testare con dataset reali con campi mancanti

---

## 17. Strategia di Rilascio MVP

### Fase 1: Beta Chiusa (Milestone 4 completata)
- Deploy su `https://uat.pemiq.com`
- Invito manuale di 5-10 utenti beta (atleti selezionati)
- Raccolta feedback strutturato su: onboarding, sync Strava, dashboard
- Monitoraggio errori e sincronizzazioni
- Durata: 1-2 settimane

### Fase 2: Fix e Stabilizzazione
- Priorità ai bug critici rilevati in beta
- Ottimizzazioni performance se necessario
- Completamento internazionalizzazione sulla base del feedback

### Fase 3: Release MVP Pubblica
- Deploy su `https://pemiq.com`
- Apertura registrazioni pubblica
- Comunicazione via canali social/comunità atletica
- Monitoraggio attivo prime 48h post-release

### KPI di Successo MVP
- 50+ utenti registrati entro 2 settimane dal lancio
- 30+ account Strava collegati
- 0 bug critici non risolti (perdita dati, accesso non autorizzato)
- Dashboard carica in < 2s per p95
- Tasso di completamento onboarding (registrazione → sync Strava) > 60%

### Strategia Upgrade Premium (MVP → Futuro)
- **MVP:** L'admin promuove manualmente gli utenti via Filament. Nessun pagamento.
- **v1.1:** Integrazione Stripe con subscription mensile (es. €9.99/mese) e annuale (es. €79/anno). Il campo `is_premium` e `premium_expires_at` vengono gestiti da un webhook Stripe.
- Struttura database già predisposta per questa transizione.

---

*Fine del documento PRD — PEMIQ v1.0*
