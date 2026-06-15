# PEMIQ Design System — Componenti Blade

> Progetto Claude Design: https://claude.ai/design/p/e74541dc-e4b8-4382-ad6f-27ecb5aa1be1?via=share

Tutti i componenti usano CSS custom properties (token semantici) e supportano sia dark che light mode senza classi hardcoded. Il tema è persistito in `localStorage` con chiave `pemiq-theme`.

---

## Componenti

### `x-button`

Pulsante o link stilato. Renderizza `<button>` se assente `href`, `<a>` se presente.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `variant` | `primary\|secondary\|ghost\|danger` | `primary` | |
| `size` | `sm\|md\|lg` | `md` | |
| `href` | `string` | — | Rende `<a>` invece di `<button>` |
| `disabled` | `bool` | `false` | |
| `fullWidth` | `bool` | `false` | `width: 100%` |
| `$icon` | slot | — | Icona a sinistra |
| `$iconRight` | slot | — | Icona a destra |

```blade
<x-button variant="primary" size="lg">Inizia</x-button>
<x-button variant="secondary" href="{{ route('dashboard') }}">Dashboard</x-button>
<x-button variant="danger" wire:click="delete">Elimina</x-button>
<x-button variant="ghost" size="sm">
    <x-slot:icon><i data-lucide="plus" style="width:14px;height:14px"></i></x-slot:icon>
    Aggiungi
</x-button>
```

---

### `x-card`

Contenitore surface con bordo, ombra e opzionale header con titolo/eyebrow.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `eyebrow` | `string` | — | Label mono uppercase sopra il titolo |
| `title` | `string` | — | Titolo card in font-display |
| `padding` | `none\|sm\|md\|lg` | `md` | `0\|12px\|16px\|24px` |
| `interactive` | `bool` | `false` | Hover translateY(-1px) + shadow-md |
| `accent` | `bool` | `false` | `border-top: 2px solid var(--brand)` |
| `$action` | slot | — | Contenuto a destra nell'header (pulsanti, toggle) |

```blade
<x-card eyebrow="Analisi" :title="$title" padding="lg">
    Contenuto
</x-card>

<x-card :eyebrow="__('messages.chart_title')" :accent="true">
    <x-slot:action>
        <x-segmented-control ... />
    </x-slot:action>
    <div id="chart"></div>
</x-card>
```

---

### `x-badge`

Etichetta inline per stato, sport type, zone, Premium.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `variant` | `success\|warning\|danger\|info\|brand\|outline\|zone1..zone5` | `outline` | |
| `size` | `sm\|md` | `sm` | |
| `dot` | `bool` | `false` | Cerchio 8px colorato a sinistra |

```blade
<x-badge variant="success" :dot="true">Connesso</x-badge>
<x-badge variant="zone3">Run</x-badge>
<x-badge variant="zone4" size="md">★ Premium</x-badge>
<x-badge variant="outline">FREE</x-badge>
```

---

### `x-tag`

Chip interattivo per filtri attivi con opzione di rimozione.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `selected` | `bool` | `false` | Sfondo accent-soft + bordo brand |
| `removable` | `bool` | `false` | Mostra pulsante × |
| `interactive` | `bool` | `true` | Hover states |
| `removeHref` | `string` | — | URL per il link × (altrimenti `<button>`) |
| `$icon` | slot | — | Icona a sinistra |

```blade
@if($sport)
<x-tag selected removable :remove-href="route('activities.index', array_filter(['year' => $year, 'month' => $month]))">
    {{ $sport }}
</x-tag>
@endif
```

---

### `x-form.input`

Campo input testuale con label, hint ed errore.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `label` | `string` | — | Label visuale sopra il campo |
| `type` | `string` | `text` | Tipo HTML input |
| `hint` | `string` | — | Testo help sotto il campo |
| `error` | `string` | — | Messaggio errore (rosso) |
| `required` | `bool` | `false` | Asterisco e `required` attribute |
| `$leadingIcon` | slot | — | Icona sovrapposta a sinistra |

```blade
<x-form.input
    label="Email"
    type="email"
    name="email"
    :value="old('email')"
    :error="$errors->first('email')"
    required
/>
```

---

### `x-form.select`

Select con label e freccia custom SVG.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `label` | `string` | — | Label visuale |
| `size` | `sm\|md` | `md` | `sm` per controlli inline nei card header |
| `hint` | `string` | — | |
| `error` | `string` | — | |

```blade
<x-form.select name="sport" label="{{ __('messages.col_sport') }}">
    <option value="">Tutti</option>
    @foreach($sportTypes as $s)
        <option value="{{ $s }}" @selected($sport === $s)>{{ $s }}</option>
    @endforeach
</x-form.select>

{{-- Inline, dentro card header --}}
<x-form.select name="year" size="sm" wire:model.live="year">
    @foreach($availableYears as $y)
        <option value="{{ $y }}">{{ $y }}</option>
    @endforeach
</x-form.select>
```

---

### `x-switch`

Toggle boolean per preferenze utente.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `label` | `string` | — | Label a destra del toggle |
| `name` | `string` | — | Nome del campo per form submission |
| `checked` | `bool` | `false` | Stato iniziale |

```blade
<x-switch
    name="email_notifications"
    :label="__('messages.email_notifications_label')"
    :checked="(bool) old('email_notifications', auth()->user()->email_notifications)"
/>
```

---

### `x-metric-tile`

Tile KPI con valore grande, unità, delta e icona colorata.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `label` | `string` | — | Label metrica |
| `value` | `string\|number` | — | Valore principale (es. `"6.43"`) |
| `unit` | `string` | — | Unità (es. `"km"`) |
| `delta` | `string` | — | Solo il numero+% (es. `"+12%"`) — il componente aggiunge ▲/▼/— |
| `deltaDir` | `up\|down\|flat` | `flat` | Direzione per colore e freccia |
| `deltaContext` | `string` | — | Contesto (es. `"vs sett."`) |
| `accent` | `zone-1..5\|brand\|info\|success\|warning\|danger` | `brand` | Colore icona |
| `$icon` | slot | — | Icona Lucide |

```blade
<x-metric-tile
    :label="__('messages.stat_distance_km')"
    :value="number_format($stats->distance_km, 1)"
    unit="km"
    delta="+8%"
    deltaDir="up"
    deltaContext="vs mese scorso"
    accent="zone-2"
>
    <x-slot:icon><i data-lucide="route" style="width:20px;height:20px"></i></x-slot:icon>
</x-metric-tile>
```

---

### `x-zone-bar`

Barra stacked delle zone cardiache con legenda.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `data` | `array` | — | Array di `{zone: 1-5, pct: float, seconds: int}` |
| `showLegend` | `bool` | `true` | Mostra legenda Z1-Z5 sotto la barra |

```blade
<x-zone-bar :data="$zoneData" />
<x-zone-bar :data="$zoneData" :show-legend="false" />
```

---

### `x-progress-bar`

Barra di progresso con animazione su mount.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `label` | `string` | — | Label sopra la barra |
| `value` | `int` (0-100) | `0` | Percentuale di riempimento |
| `color` | `brand\|success\|warning\|danger\|zone-1..5` | `brand` | |
| `height` | `int` (px) | `8` | Altezza track in pixel |
| `showValue` | `bool` | `false` | Mostra testo a destra |
| `valueText` | `string` | — | Testo custom a destra (es. `"6.4 km"`) |

```blade
<x-progress-bar
    :value="($row->distance_m / $maxDistance) * 100"
    color="brand"
    :show-value="true"
    :value-text="number_format($row->distance_m / 1000, 1) . ' km'"
/>
```

---

### `x-tabs`

Barra tab con underline style e pannelli Alpine.js o link di navigazione.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `items` | `array` | — | Array di `{id, label, icon?, badge?, href?}` |
| `active` | `string` | — | ID del tab attivo |
| `route` | `bool` | `false` | `true` → `<a href>` link, `false` → Alpine toggle |

```blade
<x-tabs :items="[
    ['id' => 'overview', 'label' => 'Panoramica', 'icon' => 'layout-dashboard'],
    ['id' => 'map',      'label' => 'Mappa',       'icon' => 'map'],
]" active="overview">
    <div x-show="activeTab === 'overview'">...</div>
    <div x-show="activeTab === 'map'" x-cloak>...</div>
</x-tabs>
```

---

### `x-segmented-control`

Toggle compatto per 2–4 opzioni mutuamente esclusive.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `options` | `array` | — | Array di `string` o `{value, label}` |
| `name` | `string` | — | Nome property Livewire per `$wire.set()` |
| `size` | `sm\|md` | `md` | |
| `selected` | `mixed` | — | Valore attivo iniziale |

```blade
<x-segmented-control
    name="metric"
    :selected="$metric"
    size="sm"
    :options="[
        ['value' => 'distance', 'label' => __('messages.stat_distance_km')],
        ['value' => 'hours',    'label' => __('messages.col_hours')],
    ]"
/>
```

---

### `x-alert`

Banner per flash message, stato sync e avvisi inline.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `variant` | `info\|success\|warning\|danger` | `info` | |
| `dismissible` | `bool` | `false` | Mostra pulsante × con Alpine dismiss |
| `icon` | `bool` | `true` | Icona SVG automatica per variant |

```blade
<x-alert variant="success" dismissible>
    {{ session('status') }}
</x-alert>

<x-alert variant="warning">
    Token Strava scaduto — <a href="{{ route('strava.connect') }}">ricollegati</a>.
</x-alert>
```

---

### `x-page-header`

Header di pagina con eyebrow, titolo, sottotitolo e slot azioni.

| Prop | Tipo | Default | Note |
|------|------|---------|------|
| `eyebrow` | `string` | — | Etichetta mono uppercase |
| `title` | `string` | `''` | Titolo principale |
| `subtitle` | `string` | — | Sottotitolo in testo muted |
| `$actions` | slot | — | Pulsanti o badge a destra |

```blade
<x-page-header
    eyebrow="Premium"
    :title="__('messages.premium_title')"
    :subtitle="__('messages.premium_subtitle')"
>
    <x-slot:actions>
        <x-badge variant="zone4">★ Premium</x-badge>
    </x-slot:actions>
</x-page-header>
```

---

## Token di riferimento

I token semantici adattano i valori al tema attivo (dark/light). Usare sempre i token semantici, mai i raw token.

| Token | Quando usarlo |
|-------|--------------|
| `--bg` | Sfondo della pagina e del body |
| `--surface` | Sfondo card e pannelli primari |
| `--surface-2` | Sfondo elementi secondari, input, dropdown |
| `--text` | Testo corpo principale |
| `--text-strong` | Titoli, valori numerici evidenziati |
| `--text-muted` | Label, placeholder, testo secondario |
| `--border` | Bordi sottili tra sezioni |
| `--accent` | Link, icone attive, stato hover |
| `--brand` | CTA primarie, logo, badge brand |
| `--danger` | Errori, azioni distruttive, stato failed |
| `--success` | Conferme, stati OK, delta positivi |
| `--zone-1..5` | Colori specifici per le zone cardiache |

---

## Anti-pattern

### 1. Colori Tailwind hardcoded invece di token semantici

```blade
{{-- NO --}}
<span class="bg-teal-500 text-white">...</span>
<div class="text-gray-400">...</div>

{{-- SI --}}
<span style="background: var(--brand); color: var(--text-on-brand);">...</span>
<div style="color: var(--text-muted);">...</div>
```

### 2. Raw token invece di alias semantici

```blade
{{-- NO: usa raw token, ignora il tema --}}
<div style="background: #0A0E16; color: #f1f5f9;">...</div>
<div style="background: var(--ink-900);">...</div>

{{-- SI: usa alias semantici che si adattano a dark/light --}}
<div style="background: var(--bg); color: var(--text);">...</div>
```

### 3. Emoji nel product UI

```blade
{{-- NO: le emoji non rispettano il DS e variano tra OS --}}
<span>★ Premium</span>
<button>🗑️ Elimina</button>

{{-- SI: usa Lucide icons o testo puro --}}
<x-badge variant="zone4">★ Premium</x-badge>
<x-button variant="danger">
    <x-slot:icon><i data-lucide="trash-2" style="width:14px;height:14px"></i></x-slot:icon>
    Elimina
</x-button>
```
