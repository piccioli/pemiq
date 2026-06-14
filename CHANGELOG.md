# Changelog

## [0.0.1] - 2026-06-14

Prima release del progetto PEMIQ — MVP completo.

### Infrastruttura
- [US-001] Inizializzazione progetto Laravel e installazione pacchetti
- [US-002] Docker Compose ambiente local
- [US-033] Docker Compose ambienti UAT e Production

### Database & Modelli
- [US-003] Migration tabella users + User model
- [US-004] Migration tabella strava_accounts + StravaAccount model
- [US-005] Migration tabella activities + Activity model + indici compositi
- [US-006] Migration sync_logs + audit_logs + modelli

### Autenticazione & Utenti
- [US-007] Spatie Permission: configurazione ruoli e seeder
- [US-009] Registrazione utente + verifica email
- [US-010] Login, logout e throttling
- [US-011] Reset password e cambio password
- [US-012] Pagina profilo utente (modifica dati personali)
- [US-034] Internazionalizzazione IT/EN + selettore lingua nel profilo
- [US-035] EnsurePremium middleware + feature tests

### Integrazione Strava
- [US-014] Strava OAuth: flusso di collegamento account
- [US-015] Strava OAuth: scollegamento account
- [US-016] StravaApiService: fetch attività con paginazione e rate limit
- [US-017] StravaTokenService: refresh automatico token
- [US-018] ActivitySyncService: upsert attività e gestione SyncLog
- [US-019] Job SyncStravaHistoricalActivities
- [US-020] Job SyncStravaIncrementalActivities + Laravel Scheduler

### Dashboard
- [US-013] Layout principale app (Blade + Tailwind + Alpine.js)
- [US-021] Componente Livewire stato connessione Strava
- [US-022] DashboardStatsService: query di aggregazione ottimizzate
- [US-023] Componente Livewire stato connessione Strava e avvio sync
- [US-024] Componente Livewire AnnualAnalysis (analisi per anno)
- [US-025] Componente Livewire MonthlyAnalysis (analisi per mese)
- [US-026] Componente Livewire SportDistribution (distribuzione per sport)
- [US-027] Pagina dashboard: assemblaggio componenti e routing

### Pannello Admin (Filament)
- [US-008] Filament: configurazione panel provider e navigazione
- [US-028] Filament UserResource: gestione utenti e Premium
- [US-029] Filament StravaAccountResource e ActivityResource
- [US-030] Filament SyncLogResource e AuditLogResource
- [US-031] Impersonificazione utente con banner e audit log
- [US-032] Filament widget dashboard admin (StatsOverview)

### Fix
- Rimosso import duplicato Alpine.js CDN (conflitto con Livewire v3)
- Aggiornati namespace azioni Filament v4 (`Filament\Actions`)
- Aggiunta estensione PHP `intl` al Dockerfile
