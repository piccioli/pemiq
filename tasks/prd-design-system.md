# PRD: Fase 2.5 — Design System

**Versione:** 1.0  
**Data:** 2026-06-14  
**Branch:** da creare — `ralph/pemiq-fase-2-5-design-system`  
**Design System:** [Claude Design — PEMIQ](https://claude.ai/design/p/e74541dc-e4b8-4382-ad6f-27ecb5aa1be1?via=share)  
**Riferimento locale:** `/tmp/pemiq-design-system/` (decompresso dal bundle Claude Design)

---

## 1. Introduzione

L'app PEMIQ usa attualmente Tailwind CSS con classi hardcoded (bg-white, text-gray-900, rounded-lg, ecc.) e nessun linguaggio visivo coerente. Questa fase introduce il **PEMIQ Design System** — un sistema di token CSS, componenti Blade riutilizzabili e un'estetica "dark performance lab" — e applica immediatamente ogni componente alle view esistenti che lo usano.

**Aesthetic principale:** dark cockpit con superfici blu-inchiostro profondo, accento teal elettrico (`#16D4B4`), e palette 5 zone allenamento come firma visiva. Tema light disponibile tramite `data-theme="light"`.

---

## 2. Obiettivi

- Importare i token CSS del design system e configurarli in Tailwind
- Caricare i font brand (Space Grotesk, Hanken Grotesk, JetBrains Mono) e Lucide icons
- Creare 16 componenti Blade riutilizzabili mappati 1:1 ai componenti del design system
- Ogni componente viene creato e immediatamente refactato nelle view che lo usano
- Passare il tema dell'app da light (default attuale) a dark (default design system), con supporto light via `data-theme`
- Dark mode: classi `dark:` Tailwind su tutti i componenti

---

## 3. Fondamenti del Design System

### Token di riferimento (da usare nei componenti Blade via CSS variables)

**Font:**
```
Space Grotesk  → display, titoli, numeri metrici  (--font-display)
Hanken Grotesk → UI, body copy                    (--font-sans)
JetBrains Mono → dati, timestamp, etichette       (--font-mono)
```
Google Fonts CDN (self-hosting in produzione):
```
https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Hanken+Grotesk:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap
```

**Alias semantici (usare SEMPRE questi, mai i raw):**
```css
--bg, --bg-elevated, --surface, --surface-2, --surface-3, --surface-hover
--text, --text-strong, --text-muted, --text-faint, --text-disabled, --text-on-brand
--border, --border-strong, --border-focus
--accent, --accent-hover, --accent-soft
```

**Brand:**
```css
--brand: #16D4B4  (teal-500)
--brand-strong: #34E6C9
--brand-soft: rgba(22,212,180,0.14)
--ember-500: #FF6B4A  (accento secondario "effort")
```

**Zone allenamento (firma PEMIQ):**
```css
--zone-1: #8C97A8  /* Recovery  — grigio */
--zone-2: #4F8DF5  /* Endurance — blu   */
--zone-3: #1FB573  /* Tempo     — verde */
--zone-4: #F5A623  /* Threshold — ambra */
--zone-5: #F0454B  /* Anaerobic — rosso */
```

**Semantic status:**
```css
--success: #1FB573  --warning: #F5A623  --danger: #F0454B  --info: #4F8DF5
```

**Radii:** `--radius-xs:4px` `--radius-sm:6px` `--radius-md:10px` `--radius-lg:14px` `--radius-xl:20px` `--radius-pill:999px`

**Spacing (grid 4px):** `--space-1:4px` `--space-2:8px` `--space-3:12px` `--space-4:16px` `--space-6:24px` `--space-8:32px`

**Classi utility (definire in CSS globale):**
```css
.pq-eyebrow { font: 600 11px/1.4 var(--font-mono); letter-spacing:0.12em; text-transform:uppercase; color:var(--text-faint); }
.pq-metric  { font-family:var(--font-display); font-weight:700; letter-spacing:-0.015em; font-variant-numeric:tabular-nums; }
```

**Icone:** Lucide (`lucide@latest` via CDN — `<i data-lucide="nome"></i>` + `lucide.createIcons()`). Stroke 2px, 16px in UI densa, 18–20px in nav. **Niente emoji nel product UI.**

---

## 4. User Stories

---

### US-DS-001: Token CSS, font e icone

**Description:** As a developer, I want to import all design system CSS tokens and brand fonts so that every component can use the design language consistently.

**Acceptance Criteria:**
- [ ] File `public/css/pemiq-ds.css` creato con `@import` di tutti i token dal design system (colors, typography, spacing, effects, base, fonts)
- [ ] Font Google Fonts caricati via `<link>` in `resources/views/layouts/app.blade.php`
- [ ] Lucide Icons caricato via CDN in `layouts/app.blade.php` con `lucide.createIcons()` all'init
- [ ] `resources/css/app.css` importa `pemiq-ds.css` oppure i token sono inline nel CSS
- [ ] `tailwind.config.js` esteso con i colori semantici del DS (brand, zone-1..5, success, warning, danger, info, surface, border) mappati ai CSS custom properties
- [ ] `<html>` in `layouts/app.blade.php` ha attributo `data-theme` impostato a `"dark"` di default
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-002: Layout shell — dark topbar e sidebar

**Description:** As a user, I want the app shell (topbar and sidebar) to reflect the dark cockpit aesthetic of the design system so that PEMIQ looks and feels like a performance platform.

**Acceptance Criteria:**
- [ ] `resources/views/layouts/app.blade.php` aggiornato: `background: var(--bg)` (`--ink-900` = `#0A0E16`)
- [ ] Topbar (h=60px) con `background: color-mix(in srgb, var(--bg) 80%, transparent)` + `backdrop-filter: blur(16px)` + `border-bottom: 1px solid var(--border)`
- [ ] Logo PEMIQ in topbar usa asset SVG dal design system (`logo-lockup.svg` copiato in `public/assets/`) oppure wordmark in Space Grotesk 700 con colore `var(--brand)`
- [ ] Sidebar (256px) con `background: var(--bg-elevated)` e nav link che usano `var(--text-muted)` / `var(--text-strong)` con stato active `var(--accent)`
- [ ] Badge "Premium" in navbar usa `--zone-4` (amber) per utenti premium
- [ ] Badge "Passa a Premium" usa `var(--accent)` per utenti Free
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-003: Componente `x-button`

**Description:** As a developer, I want a reusable Button Blade component so that all actions in the app use a consistent, branded control.

**Acceptance Criteria:**
- [ ] File `resources/views/components/button.blade.php` creato
- [ ] Props: `variant` (primary|secondary|ghost|danger, default: primary), `size` (sm|md|lg, default: md), `href` (se passato usa `<a>`, altrimenti `<button>`), `disabled`, `fullWidth`, `icon` (slot opzionale sinistro), `iconRight` (slot opzionale destro)
- [ ] `primary`: `background: var(--brand)` + `color: var(--brand-contrast)` + hover glow `var(--glow-brand)` + `translateY(0)` → press `translateY(1px)`
- [ ] `secondary`: `background: var(--surface-2)` + `border: 1px solid var(--border-strong)` + hover `var(--surface-3)`
- [ ] `ghost`: nessun background + hover `var(--accent-soft)`
- [ ] `danger`: `background: var(--danger-soft)` + `color: var(--danger)` → hover background `var(--danger)` + `color: white`
- [ ] Size `sm`: padding `6px 12px`, font 13px. `md`: `10px 18px`, 15px. `lg`: `13px 24px`, 17px
- [ ] Radii: `var(--radius-md)` (10px)
- [ ] Transizione: `var(--dur-fast)` (120ms) `var(--ease-out)`
- [ ] View refactorate: tutti i pulsanti in `layouts/app.blade.php`, `strava/connection-status.blade.php`, `activities/index.blade.php`, `activities/show.blade.php`, `premium/index.blade.php` aggiornati a `<x-button>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-004: Componente `x-card`

**Description:** As a developer, I want a reusable Card component so that all panels and tiles use consistent surface styling.

**Acceptance Criteria:**
- [ ] File `resources/views/components/card.blade.php` creato
- [ ] Props: `eyebrow` (string opzionale), `title` (string opzionale), `padding` (none|sm|md|lg, default: md), `interactive` (bool, default: false), `accent` (bool, default: false)
- [ ] Base: `background: var(--surface)` + `border: 1px solid var(--border)` + `border-radius: var(--radius-lg)` + `box-shadow: var(--shadow-sm), var(--edge-top)`
- [ ] `accent=true`: `border-top: 2px solid var(--brand)` invece del border standard
- [ ] `interactive=true`: hover `background: var(--surface-hover)` + `translateY(-1px)` + `shadow-md`, cursor pointer
- [ ] `padding` none=0, sm=12px, md=16px, lg=24px
- [ ] Slot `action` per icone/pulsanti nell'header della card
- [ ] `eyebrow` renderizzato con classe `.pq-eyebrow`
- [ ] View refactorate: tutti i pannelli in `livewire/dashboard/`, `livewire/strava/connection-status.blade.php`, `livewire/premium/`, `activities/show.blade.php` aggiornati a `<x-card>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-005: Componente `x-badge`

**Description:** As a developer, I want a reusable Badge component for status, sport type, zones, and Premium labels.

**Acceptance Criteria:**
- [ ] File `resources/views/components/badge.blade.php` creato
- [ ] Props: `variant` (success|warning|danger|info|brand|outline|zone1|zone2|zone3|zone4|zone5, default: outline), `size` (sm|md, default: sm), `dot` (bool)
- [ ] Zone variants: `zone1` usa `--zone-1-soft` bg + `--zone-1` text; idem per zone2..5
- [ ] Status variants: `success` usa `--success-soft` bg + `--success` text; idem per warning/danger/info
- [ ] `brand`: `--brand-soft` bg + `--brand` text
- [ ] `dot=true`: piccolo cerchio colorato `8px` a sinistra del testo
- [ ] Border-radius: `var(--radius-pill)`
- [ ] View refactorate: badge sport type nelle activity views, badge "Premium"/"Connesso"/"Sincronizzazione" in navbar e connection-status aggiornati a `<x-badge>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-006: Componente `x-tag`

**Description:** As a developer, I want a reusable Tag component for interactive filter chips in the activities list.

**Acceptance Criteria:**
- [ ] File `resources/views/components/tag.blade.php` creato
- [ ] Props: `selected` (bool), `removable` (bool — aggiunge ×), `interactive` (bool, default: true), `icon` (slot opzionale)
- [ ] Default: `background: var(--surface-2)` + `border: 1px solid var(--border)` + `color: var(--text-muted)`
- [ ] `selected=true`: `background: var(--accent-soft)` + `border-color: var(--brand)` + `color: var(--accent)`
- [ ] `removable=true`: × button con hover `color: var(--danger)`
- [ ] Radii: `var(--radius-pill)`
- [ ] View refactorate: filtri sport/anno/mese in `activities/index.blade.php` aggiornati a `<x-tag>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-007: Componente `x-form-input`

**Description:** As a developer, I want a reusable Input component so that all text fields use the design system styling consistently.

**Acceptance Criteria:**
- [ ] File `resources/views/components/form/input.blade.php` creato
- [ ] Props: `label` (string), `type` (default: text), `hint` (string opzionale), `error` (string opzionale), `leadingIcon` (slot opzionale), `required` (bool)
- [ ] Input: `background: var(--surface-2)` + `border: 1px solid var(--border-strong)` + `color: var(--text-strong)` + `border-radius: var(--radius-md)` + padding `10px 14px`
- [ ] Focus: `border-color: var(--border-focus)` + `box-shadow: var(--ring-focus)`
- [ ] Error state: `border-color: var(--danger)` + messaggio error in `var(--danger)` sotto il campo
- [ ] Label: `font: var(--text-label)` + `color: var(--text-muted)`
- [ ] Hint: `font: var(--text-caption)` + `color: var(--text-faint)`
- [ ] Placeholder: `color: var(--text-faint)` (via `::placeholder`)
- [ ] View refactorate: tutti i campi input in `auth/` e `profile/show.blade.php` aggiornati a `<x-form.input>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-008: Componente `x-form-select`

**Description:** As a developer, I want a reusable Select component so that all dropdowns use design system styling.

**Acceptance Criteria:**
- [ ] File `resources/views/components/form/select.blade.php` creato
- [ ] Props: `label` (string opzionale), `size` (sm|md, default: md), `hint` (string opzionale), `error` (string opzionale)
- [ ] Stesso stile base di `x-form.input` (background, border, border-radius)
- [ ] Freccia custom via `background-image` SVG inline + `appearance: none`
- [ ] View refactorate: tutti i `<select>` nei filtri attività, nei selettori Livewire (anno, mese, sport) aggiornati a `<x-form.select>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-009: Componente `x-switch`

**Description:** As a developer, I want a reusable Switch toggle for boolean settings.

**Acceptance Criteria:**
- [ ] File `resources/views/components/switch.blade.php` creato
- [ ] Props: `label` (string), `name`, `checked` (bool)
- [ ] Track 40×22px: `background: var(--surface-3)` → checked `var(--brand)` con transizione `var(--dur-base)`
- [ ] Thumb 18px cerchio bianco: `translateX` 0 → 18px con `var(--ease-spring)`
- [ ] Label a destra: `var(--text-label)`
- [ ] View refactorate: toggle preferenze in `profile/show.blade.php` (es. locale, notifiche) aggiornati a `<x-switch>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-010: Componente `x-metric-tile`

**Description:** As a developer, I want a KPI MetricTile component so that dashboard stats use the signature data-display style of the design system.

**Acceptance Criteria:**
- [ ] File `resources/views/components/metric-tile.blade.php` creato
- [ ] Props: `label` (string), `value` (string|number), `unit` (string opzionale), `delta` (string opzionale, es. "12%"), `deltaDir` (up|down|flat), `deltaContext` (string opzionale, es. "vs sett."), `accent` (zone-1..5|brand|info|success|warning|danger — tinta l'icona)
- [ ] Valore con classe `.pq-metric` + font-size 32px+
- [ ] `deltaDir=up`: `color: var(--success)` + triangolo ▲. `down`: `var(--danger)` ▼. `flat`: `var(--text-faint)` —
- [ ] Slot `icon` per l'icona Lucide (tintata con `accent`)
- [ ] Struttura card: usa `<x-card>` internamente con `padding="md"`
- [ ] View refactorate: le 4 KPI cards in `livewire/dashboard/overview-stats.blade.php` aggiornate a `<x-metric-tile>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-011: Componente `x-zone-bar`

**Description:** As a developer, I want a ZoneBar component to display time-in-zone distribution — the signature PEMIQ visual.

**Acceptance Criteria:**
- [ ] File `resources/views/components/zone-bar.blade.php` creato
- [ ] Props: `data` (array di `{zone: 1..5, value: float (%), time: int (secondi)}`)
- [ ] Barra stacked: 5 segmenti proporzionali alle percentuali, colori `--zone-1..5`
- [ ] Altezza 12px, border-radius `var(--radius-pill)`, nessun gap tra segmenti
- [ ] Legenda sotto: 5 voci con pallino zona, codice zona (Z1..Z5), percentuale, tempo in formato "1h 12m"
- [ ] `showLegend=false` (default: true) per mini-bar inline senza legenda
- [ ] Transizione crescita segmenti: `width` 0 → valore con `var(--dur-slow)` (360ms) + `var(--ease-out)` su mount
- [ ] View refactorate: sezione "Tempo in zona" in `livewire/dashboard/` aggiornata a `<x-zone-bar>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-012: Componente `x-progress-bar`

**Description:** As a developer, I want a ProgressBar component for goal meters and weekly volume indicators.

**Acceptance Criteria:**
- [ ] File `resources/views/components/progress-bar.blade.php` creato
- [ ] Props: `label` (string opzionale), `value` (0-100), `color` (brand|success|warning|danger|zone-1..5, default: brand), `height` (px, default: 8), `showValue` (bool), `valueText` (string opzionale, es. "320 / 500 TSS")
- [ ] Track: `background: var(--surface-3)` + `border-radius: var(--radius-pill)`
- [ ] Fill: colore da prop + transizione width su mount
- [ ] `showValue=true` + `valueText`: mostra il testo a destra della barra
- [ ] View refactorate: indicatori progresso in `livewire/dashboard/` aggiornati a `<x-progress-bar>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-013: Componente `x-tabs`

**Description:** As a developer, I want a Tabs component for switching between views within a page.

**Acceptance Criteria:**
- [ ] File `resources/views/components/tabs.blade.php` creato (o Livewire component se reactive)
- [ ] Props: `items` (array di `{id, label, icon?, badge?}`), `active` (id attivo), `route` (bool — se true i tab sono link, altrimenti dispatch evento Livewire)
- [ ] Underline tab style: `border-bottom: 2px solid var(--accent)` sul tab attivo
- [ ] Tab inattivo: `color: var(--text-muted)` → hover `var(--text-strong)`
- [ ] Badge numerico sul tab: piccolo `<x-badge>` variant info
- [ ] View refactorate: se presenti tab nelle pagine attività o premium, usare `<x-tabs>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-014: Componente `x-segmented-control`

**Description:** As a developer, I want a SegmentedControl component for 2–4 mutually exclusive toggle choices (granularity, unit, range).

**Acceptance Criteria:**
- [ ] File `resources/views/components/segmented-control.blade.php` creato
- [ ] Props: `options` (array di string o `{value, label}`), `wire:model` per stato Livewire, `size` (sm|md, default: md)
- [ ] Container: `background: var(--surface-2)` + `border-radius: var(--radius-md)` + padding 3px
- [ ] Opzione attiva: `background: var(--surface-3)` + `color: var(--text-strong)` con transizione slide
- [ ] Opzione inattiva: `color: var(--text-muted)` + hover `var(--text)`
- [ ] View refactorate: toggle Mese/Settimana e Linee/Barre in `livewire/premium/period-comparison.blade.php`, toggle distanza/ore in chart Livewire aggiornati a `<x-segmented-control>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-015: Componente `x-alert`

**Description:** As a developer, I want an Alert component for flash messages, sync status notifications, and inline warnings.

**Acceptance Criteria:**
- [ ] File `resources/views/components/alert.blade.php` creato
- [ ] Props: `variant` (info|success|warning|danger, default: info), `dismissible` (bool), `icon` (bool, default: true)
- [ ] Background: `--{variant}-soft` + border `1px solid var(--{variant})` con alpha 30% + icona Lucide coerente (info-circle, check-circle, alert-triangle, alert-circle)
- [ ] `dismissible=true`: × button che rimuove il componente via Alpine.js `x-data / x-show`
- [ ] View refactorate: tutti i flash message in `layouts/app.blade.php` e i banner di stato sync in `connection-status.blade.php` aggiornati a `<x-alert>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-016: Componente `x-page-header`

**Description:** As a developer, I want a PageHeader component for consistent page titles and action areas across all views.

**Acceptance Criteria:**
- [ ] File `resources/views/components/page-header.blade.php` creato
- [ ] Props: `eyebrow` (string opzionale), `title` (string), `subtitle` (string opzionale)
- [ ] Slot `actions` per i pulsanti header (es. "Esporta", "Confronta")
- [ ] `title` in `--font-display` bold, `color: var(--text-strong)`
- [ ] `eyebrow` con classe `.pq-eyebrow`
- [ ] `subtitle`: `color: var(--text-muted)` + `var(--text-body)`
- [ ] View refactorate: header di `activities/index.blade.php`, `activities/show.blade.php`, pagine premium aggiornate a `<x-page-header>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-017: Refactor Dashboard con Design System

**Description:** As a user, I want the dashboard to use the new design system components so that the analytics view feels like a professional performance platform.

**Acceptance Criteria:**
- [ ] `livewire/dashboard/overview-stats.blade.php`: 4 KPI cards → `<x-metric-tile>` con `delta` e `deltaDir` calcolati rispetto al periodo precedente
- [ ] `livewire/dashboard/annual-analysis-chart.blade.php`: container → `<x-card eyebrow="Analisi annuale">`, selettore anno → `<x-form.select size="sm">`, toggle km/ore → `<x-segmented-control>`
- [ ] `livewire/dashboard/monthly-analysis-chart.blade.php`: stesso pattern di annual
- [ ] `livewire/dashboard/sport-distribution-chart.blade.php`: container → `<x-card>`
- [ ] `livewire/strava/connection-status.blade.php`: stato "Connesso" → `<x-badge variant="success" dot>`, bottoni → `<x-button>`, alert sync → `<x-alert>`, banner token scaduto → `<x-alert variant="warning">`
- [ ] Colori ApexCharts aggiornati per usare i valori del DS: `--brand` per serie primaria, `--zone-2` per serie secondaria
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-018: Refactor pagine Attività con Design System

**Description:** As a user, I want the activities list and detail pages to use design system components for a cohesive experience.

**Acceptance Criteria:**
- [ ] `activities/index.blade.php`: header → `<x-page-header>`, ogni riga attività → `<x-card interactive>` oppure `<tr>` con hover `var(--surface-hover)`, badge sport → `<x-badge variant="zone{N}">`, filtri → `<x-form.select>` e `<x-tag>`
- [ ] `activities/show.blade.php`: header → `<x-page-header>`, KPI metriche → `<x-metric-tile>`, container mappa → `<x-card>`, link Strava → `<x-button variant="ghost" as="a">`
- [ ] Badge sport type usa la zona colore coerente: Run→zone-3 (verde), Ride→zone-2 (blu), Swim→zone-1 (grigio), altri→outline
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-019: Refactor pagine Premium con Design System

**Description:** As a premium user, I want all premium analytics pages to use the design system for a consistent high-end experience.

**Acceptance Criteria:**
- [ ] `livewire/premium/period-comparison.blade.php`: 4 KPI cards → `<x-metric-tile>`, container grafico → `<x-card eyebrow="Distanza mensile (km)">`, toggle Mese/Settimana e Linee/Barre → `<x-segmented-control>`, tabella dettaglio in `<x-card>`, pulsante Esporta → `<x-button variant="secondary" disabled>`
- [ ] `livewire/premium/trend-chart.blade.php`: container → `<x-card>`, selettori → `<x-form.select>`, toggle distanza/ore → `<x-segmented-control>`
- [ ] `livewire/premium/year-over-year-chart.blade.php`: container → `<x-card>`, selettori anno → `<x-form.select size="sm">`, filtro sport → `<x-form.select size="sm">`
- [ ] `premium/index.blade.php`: header → `<x-page-header eyebrow="Premium">`, card Free → `<x-card>`, card Premium → `<x-card accent>`, pulsante CTA → `<x-button variant="primary" size="lg">`
- [ ] `premium/trends.blade.php`, `compare.blade.php`, `year-over-year.blade.php`: header → `<x-page-header>`
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-020: Refactor Landing page con Design System

**Description:** As a visitor, I want the public landing page to use the dark performance-lab aesthetic to immediately communicate PEMIQ's brand.

**Acceptance Criteria:**
- [ ] `landing.blade.php`: body con `data-theme` non overridato (eredita dark dal layout pubblico)
- [ ] Hero section: gradient radiale `var(--brand-glow)` come sfondo + titolo in `var(--font-display)` bold
- [ ] Feature highlight cards → `<x-card>` con `eyebrow` per ogni sezione
- [ ] CTA principale → `<x-button variant="primary" size="lg">`
- [ ] CTA secondaria (login) → `<x-button variant="ghost">`
- [ ] Badge "Free" e "Premium" nelle feature list → `<x-badge>`
- [ ] Layout pubblico (`layouts/public.blade.php` o override di `app.blade.php`) con topbar trasparente + blur su dark bg
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-021: Dark mode toggle e persistenza tema

**Description:** As a user, I want to switch between dark and light mode so that I can use PEMIQ in different lighting conditions.

**Acceptance Criteria:**
- [ ] Toggle tema in navbar (IconButton con icona `sun`/`moon`) che commuta `data-theme="dark"/"light"` su `<html>`
- [ ] Preferenza salvata in `localStorage` chiave `pemiq-theme`
- [ ] All'init pagina: leggere `localStorage` e impostare `data-theme` prima del render (nessun flash di tema sbagliato)
- [ ] Light theme: variabili semantiche sostituite come da `tokens/colors.css` (`--bg: var(--paper-50)`, ecc.)
- [ ] Tutti i componenti Blade creati nelle US precedenti supportano entrambi i temi via le variabili semantiche (nessuna classe hardcoded light/dark)
- [ ] Typecheck e test suite passano
- [ ] Verify in browser usando dev-browser skill

---

### US-DS-022: Documentazione componenti

**Description:** As a developer, I want a components reference document so that future contributors know how to use the design system correctly.

**Acceptance Criteria:**
- [ ] File `docs/design/components.md` creato
- [ ] Per ogni componente Blade: nome, props con tipo e default, esempio d'uso Blade, varianti disponibili
- [ ] Sezione "Token di riferimento" con i 10 alias semantici più usati e quando usarli
- [ ] Sezione "Non fare" con anti-pattern (classi Tailwind hardcoded per colori brand, uso dei raw token invece degli alias semantici, emoji nel product UI)
- [ ] Link al progetto Claude Design per il riferimento visivo

---

## 5. Requisiti Funzionali

- FR-DS-1: L'app deve avere `data-theme="dark"` come default su `<html>` in `layouts/app.blade.php`
- FR-DS-2: Tutti i componenti Blade devono usare esclusivamente i CSS custom properties semantici del DS (`--bg`, `--surface`, `--text`, `--border`, `--accent`, ecc.) — nessuna classe Tailwind hardcoded per colori brand
- FR-DS-3: Tailwind può continuare a essere usato per layout, spaziatura, e responsività (flex, grid, gap, padding, breakpoints)
- FR-DS-4: I font Space Grotesk, Hanken Grotesk e JetBrains Mono devono caricarsi in ogni pagina app
- FR-DS-5: Le icone Lucide devono essere usate per tutte le nuove icone — `data-lucide="nome"` + `lucide.createIcons()` — nessuna emoji nel product UI
- FR-DS-6: I colori ApexCharts nei grafici esistenti devono usare i valori hex dei token DS (non le classi Tailwind)
- FR-DS-7: Il componente `<x-card>` deve essere il wrapper di ogni pannello/sezione nelle view Blade
- FR-DS-8: La classe `.pq-metric` deve essere usata per tutti i numeri metrici grandi (distanza, tempo, TSS, ecc.)
- FR-DS-9: La classe `.pq-eyebrow` deve essere usata per tutte le etichette di sezione uppercase mono

---

## 6. Non-Goals (fuori scope)

- **Nessun cambio ai componenti Livewire PHP** — solo le view Blade vengono aggiornate
- **Nessuna modifica alla logica di business** — solo layer presentazionale
- **Nessuna integrazione Stripe o funzionalità nuove** — solo restyling delle view esistenti
- **Nessuna animazione complessa** — solo transizioni CSS standard (`var(--dur-fast/base)`)
- **Nessun icon font custom** — solo Lucide CDN (self-hosting font in produzione fuori scope)
- **Nessuna slide template** — non richiesta in questa fase

---

## 7. Considerazioni Tecniche

- **Stack:** Laravel 13, Livewire 3, Alpine.js, Tailwind CSS v3
- **Approccio token:** CSS custom properties su `:root` in un file CSS globale importato da `app.css`; Tailwind esteso con `var(--...)` per i colori DS
- **Compatibilità:** nessuna modifica ai test PHP — la suite è backend e non verifica classi CSS
- **Font loading:** Google Fonts `<link rel="preconnect">` + `<link rel="stylesheet">` in `<head>` prima degli stylesheet locali
- **Lucide:** `<script src="https://unpkg.com/lucide@latest"></script>` + `document.addEventListener('DOMContentLoaded', () => lucide.createIcons())` nel layout; nei componenti Livewire che re-renderizzano, richiamare `lucide.createIcons()` nell'evento `livewire:navigated` o `wire:init`
- **Dark mode Livewire:** i componenti Livewire che renderizzano `<select>` o altri input devono avere `wire:ignore` sui wrapper che Alpine.js già gestisce

---

## 8. Metriche di Successo

- Nessuna classe Tailwind hardcoded per colori brand (`bg-orange-500`, `text-gray-900`, ecc.) rimasta nelle view dopo il refactor
- Tutti i 16 componenti Blade esistono e sono usati nelle view corrispondenti
- Dark e light mode funzionanti senza flash visivo
- Suite test 80/80 pass (nessuna regressione backend)
- Verifica browser su tutte le 8 US di refactor (017–021)

---

## 9. Open Questions

- **Tema di default per la landing pubblica:** dark (come da DS) o light per maggiore accessibilità ai nuovi utenti? Il DS usa dark come primario, ma la landing potrebbe optare per light per contrasto col brand teal.
- **Tailwind purge:** i token CSS custom properties non vengono "purgati" da Tailwind in build — verificare che il CSS finale includa tutte le variabili `:root`
- **Lucide in Livewire:** se un componente Livewire fa re-render, `lucide.createIcons()` deve essere richiamato di nuovo — valutare se wrappare in un Alpine.js component `x-data` con un `$nextTick`
