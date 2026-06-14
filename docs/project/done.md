# PEMIQ — Fasi completate

Fasi di sviluppo rilasciate in produzione. Documento di riferimento storico.

---

## v0.2.0 — Fasi 0, 1 e 2 (2026-06-14)

**Branch:** `ralph/pemiq-fasi-0-1-2`  
**Tag:** `v0.2.0`  
**Test:** 80 test, 301 assertions — tutti verdi

---

### Stato del codice prima di queste fasi (v0.0.1)

| Area | Stato | Note |
|------|-------|------|
| Docker local / UAT / Production | ✅ | |
| Registrazione, login, reset password | ✅ | |
| Profilo utente + locale IT/EN | ✅ | |
| Ruoli Spatie + Premium (campi DB) | ✅ | |
| Middleware `EnsurePremium` | ⚠️ | Registrato ma non applicato a nessuna route |
| OAuth Strava + token refresh | ✅ | |
| Sync storica / incrementale | ✅ | |
| Dashboard con tabelle (no grafici) | ✅ | |
| Dettaglio attività | ❌ | Solo in backoffice Filament |
| Grafici interattivi | ❌ | |
| Test sync job / dashboard | ❌ | |
| Test `EnsurePremium` | ❌ | |

---

### Fase 0 — Consolidamento MVP, QA e beta chiusa

**Obiettivo:** portare v0.0.1 da "feature-complete" a "production-ready".

#### Deliverable completati

- [x] **Test automatici**
  - Feature test sync Strava storica con mock HTTP e paginazione (`SyncHistoricalActivitiesTest`)
  - Feature test sync incrementale con mock HTTP (`SyncIncrementalActivitiesTest`)
  - Feature test impersonificazione admin e audit log (`ImpersonationTest`)
  - Feature test middleware `EnsurePremium` (`EnsurePremiumTest`)
  - Test `DashboardStatsService` con dataset edge case (null, zero attività, 50 su 3 anni)
- [x] **Revisione sicurezza**
  - Fix scope `user_id` in `DashboardStatsService` + test cross-user isolation
  - Audit CSRF su tutte le form utente + test risposta 419
  - Documento procedura rotazione `APP_KEY` (`docs/ops/app-key-rotation.md`)
- [x] **Performance**
  - `LargeDatasetSeeder`: 10.000+ attività per utente test
- [x] **Eccezioni tipizzate Strava**
  - `StravaAuthException`, `StravaRateLimitException`

---

### Fase 1 — UX dashboard, onboarding e notifiche

**Obiettivo:** colmare i gap tra PRD e implementazione; esperienza Free tier completa.

#### Deliverable completati

- [x] **Grafici ApexCharts nella dashboard**
  - `AnnualAnalysisChart`: barre mensili con selettore anno e toggle distanza/ore
  - `MonthlyAnalysisChart`: barre giornaliere con selettore anno+mese
  - `SportDistributionChart`: donut con tooltip sport
- [x] **Lista attività**
  - Route `/activities` paginata (20 per pagina), scoped su `user_id`
  - Filtri sport, anno, mese via URL query params
  - Nessuna N+1 query
- [x] **Dettaglio attività**
  - Route `/activities/{activity}` con policy owner-only
  - Metriche principali, mappa Leaflet.js + `@mapbox/polyline`
  - Link "Vedi su Strava", gestione `map_polyline` null
- [x] **Stato sync Livewire**
  - `ConnectionStatus` con polling condizionale 5s (solo se `running`)
  - Messaggi per stato running / completed / failed
  - Fix UX token scaduto: distingue `error` da "mai collegato"
- [x] **Notifiche email**
  - `SyncFailedNotification`: prima failure critica, no duplicati, reset dopo sync ok
  - `StravaTokenExpiredNotification`: token refresh definitivamente fallito
- [x] **i18n completa**
  - Stringhe dashboard estratte in `lang/it/messages.php` e `lang/en/messages.php`
  - Formattazione date e numeri locale-aware (Carbon + helper)
- [x] **Landing page pubblica**
  - Route `/` con sezioni prodotto, Free vs Premium, CTA registrazione
  - Utenti autenticati redirectati a `/dashboard`

---

### Fase 2 — Premium: analisi trend e confronti

**Obiettivo:** primo blocco di valore Premium reale dietro `EnsurePremium`.

#### Deliverable completati

- [x] **Gating Premium**
  - Middleware `premium` applicato al route group `/premium/*`
  - Test gating: Free → redirect dashboard, Premium → 200, anonimo → login
- [x] **Badge Premium + CTA upgrade**
  - Badge "Premium" in navbar e pagina profilo
  - CTA "Passa a Premium" con link a `/premium` per utenti Free
- [x] **TrendAnalysisService**
  - `weeklyVolume()` e `monthlyVolume()` con `DB::raw` + `DATE_FORMAT`
  - Filtro sport opzionale, scope `user_id`, test completi incluso edge case zero attività
- [x] **Grafico trend** (`/premium/trends`)
  - `TrendChart` Livewire: linea/area, toggle distanza/ore, selettore sport e range date
- [x] **Confronto periodi** (`/premium/compare`)
  - `PeriodComparison` Livewire: date picker range libero per Periodo A e B
  - 4 KPI cards (Distanza, Dislivello, Tempo, Attività) con delta % colorato
  - Grafico dual-line ApexCharts con toggle Mese/Settimana e Linee/Barre
  - Tabella "Dettaglio confronto" con Delta assoluto e Delta %
  - Gestione divisione per zero, pulsante Esporta placeholder
- [x] **Confronto anno su anno** (`/premium/year-over-year`)
  - `YearOverYearChart` Livewire: due linee sovrapposte (blu/arancione)
  - Selettori anno A / B (range 2010–corrente), filtro sport opzionale
- [x] **Pagina /premium**
  - Placeholder statico: confronto Free vs Premium, CTA mailto admin
  - Nota "Pagamento self-service disponibile a breve"
  - Utente Premium rediretto a `/dashboard`

---

## v0.0.1 — MVP (data precedente)

Rilascio iniziale: autenticazione, integrazione Strava, sync storica/incrementale, dashboard base con tabelle, backoffice Filament, impersonificazione admin.

Archivio Ralph: `scripts/archive/prd-v0.0.1.json`, `scripts/archive/progress-v0.0.1.txt`
