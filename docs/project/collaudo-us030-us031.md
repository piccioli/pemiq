# Documento di Collaudo — US-030 e US-031

**Data:** 2026-06-14  
**Branch:** `ralph/pemiq-fasi-0-1-2`  
**Ambiente:** locale — `http://pemiq.local`  
**Test automatici:** 80/80 pass al momento del rilascio

---

## Prerequisiti

### Account necessari


| Ruolo          | Email                             | Password   | Note                                             |
| -------------- | --------------------------------- | ---------- | ------------------------------------------------ |
| Admin          | `admin@pemiq.com`                 | `password` | Creato da `AdminUserSeeder`                      |
| Utente Free    | registra un nuovo account         | a scelta   | Email verificata manualmente dall'admin Filament |
| Utente Premium | promuovi l'utente Free dall'admin | —          | Vedi procedura sotto                             |


### Come promuovere un utente a Premium (Filament)

1. Accedi a `http://pemiq.local/admin` con le credenziali admin
2. Menu laterale → **Utenti**
3. Clicca sull'utente da promuovere → **Modifica**
4. Attiva il toggle **Is Premium** → **Salva**

### Stato iniziale richiesto

- Docker in esecuzione (`docker compose ps` → tutti i servizi `Up`)
- Database seedato (`docker compose exec app php artisan db:seed`)
- Asset compilati (`npm run build` se necessario)

---

## US-030 — Confronto Anno su Anno

**Route:** `http://pemiq.local/premium/year-over-year`  
**Componente Livewire:** `App\Livewire\Premium\YearOverYearChart`

---

### TC-030-01 — Gating: utente non autenticato

**Precondizione:** nessuna sessione attiva (apri finestra in incognito)

1. Naviga a `http://pemiq.local/premium/year-over-year`

**Risultato atteso:** redirect a `http://pemiq.local/login`  
**Pass / Fail:** Pass

---

### TC-030-02 — Gating: utente Free bloccato

**Precondizione:** loggato come utente Free (non premium)

1. Naviga a `http://pemiq.local/premium/year-over-year`

**Risultato atteso:**

- Redirect a `http://pemiq.local/dashboard`
- Messaggio flash visibile: *"Funzionalità disponibile per utenti Premium"* (o equivalente)

**Pass / Fail:** Pass

---

### TC-030-03 — Accesso pagina come utente Premium

**Precondizione:** loggato come utente Premium

1. Naviga a `http://pemiq.local/premium/year-over-year`

**Risultato atteso:**

- Pagina carica senza errori (HTTP 200)
- Titolo visibile: **"Anno su Anno"**
- Componente grafico presente nella pagina

**Pass / Fail:** Pass

---

### TC-030-04 — Selettori anno presenti e funzionanti

**Precondizione:** TC-030-03 passato

1. Verifica che siano presenti due select: **Anno A** (con pallino blu) e **Anno B** (con pallino arancione)
2. Verifica che i valori di default siano: Anno A = anno corrente (2026), Anno B = anno precedente (2025)
3. Cambia Anno A con un anno diverso

**Risultato atteso:**

- I due select sono visibili con i rispettivi indicatori colorati
- Default: Anno A = 2026, Anno B = 2025
- Dopo il cambio, il grafico si aggiorna senza ricaricare la pagina (Livewire reactive)

**Pass / Fail:** Pass

---

### TC-030-05 — Grafico con due serie sovrapposte

**Precondizione:** utente Premium con attività in almeno due anni distinti

1. Seleziona Anno A e Anno B che contengono attività
2. Osserva il grafico

**Risultato atteso:**

- Grafico a linee visibile (ApexCharts)
- **Due linee distinte:** Anno A in blu (`#3B82F6`), Anno B in arancione (`#F97316`)
- Asse X: 12 mesi (Gen, Feb, …, Dic)
- Asse Y: distanza in km
- Legenda in alto a destra con i due anni come etichette

**Pass / Fail:** Pass

---

### TC-030-06 — Tooltip al hover

**Precondizione:** TC-030-05 passato, grafico con dati

1. Porta il cursore sopra un punto del grafico

**Risultato atteso:**

- Tooltip mostra i valori di entrambe le serie per il mese selezionato
- Formato: `X.X km` (con un decimale); se nessuna attività nel mese: `—`

**Pass / Fail:** Pass

---

### TC-030-07 — Filtro sport

**Precondizione:** utente Premium con attività di sport diversi

1. Seleziona un singolo sport dal menu a tendina del filtro sport
2. Osserva il grafico

**Risultato atteso:**

- Il grafico si aggiorna mostrando solo i km del tipo di sport selezionato
- I valori sono diversi da "Tutti gli sport"

**Pass / Fail:** Pass

---

### TC-030-08 — Nessun dato disponibile

**Precondizione:** seleziona due anni per cui l'utente non ha attività

1. Imposta Anno A e Anno B su anni senza attività (es. 2010, 2011)

**Risultato atteso:**

- Al posto del grafico compare il messaggio testuale di assenza dati (es. *"Nessun dato disponibile per il periodo selezionato"*)
- Nessun errore JavaScript in console

**Pass / Fail:** Pass

---

### TC-030-09 — Responsività mobile

**Precondizione:** TC-030-03 passato

1. Apri DevTools → modalità mobile (es. iPhone 14, 390px)
2. Naviga a `http://pemiq.local/premium/year-over-year`

**Risultato atteso:**

- I selettori anno e sport si dispongono in colonna o wrappano correttamente
- Il grafico occupa tutta la larghezza disponibile senza overflow orizzontale
- Nessun elemento taglia fuori dallo schermo

**Pass / Fail:** Pass

---

## US-031 — Pagina /premium Placeholder

**Route:** `http://pemiq.local/premium`  
**Controller:** `App\Http\Controllers\PremiumController@index`

---

### TC-031-01 — Utente non autenticato rediretto a login

**Precondizione:** nessuna sessione attiva (finestra incognito)

1. Naviga a `http://pemiq.local/premium`

**Risultato atteso:** redirect a `http://pemiq.local/login`  
**Pass / Fail:** Pass

---

### TC-031-02 — Utente Free accede alla pagina

**Precondizione:** loggato come utente Free

1. Naviga a `http://pemiq.local/premium`

**Risultato atteso:**

- Pagina carica correttamente (HTTP 200, **nessun redirect**)
- Badge **"Premium"** in alto visibile
- Titolo principale presente
- Due colonne visibili: **Free** (bordo grigio) e **Premium** (bordo ambra con badge "★ Premium")

**Pass / Fail:** Pass

---

### TC-031-03 — Confronto funzionalità Free vs Premium

**Precondizione:** TC-031-02 passato

1. Osserva la colonna **Free**
2. Osserva la colonna **Premium**

**Risultato atteso:**

Colonna Free — voci con spunta verde:

- Sincronizzazione Strava
- Dashboard analisi base
- Lista attività
- Grafici annuale/mensile/sport

Colonna Free — voci con X grigia (funzioni bloccate):

- Trend analysis
- Confronto periodi
- Anno su anno

Colonna Premium — tutte le voci con spunta (comprese quelle Premium in ambra)

**Pass / Fail:** Pass

---

### TC-031-04 — CTA "Contatta l'amministratore"

**Precondizione:** TC-031-02 passato

1. Individua il pulsante arancione **"Contatta l'amministratore"**
2. Copia il link dell'href (senza cliccare, o ispeziona il DOM)

**Risultato atteso:**

- Il link è un `mailto:` con indirizzo admin e soggetto precompilato: `Richiesta accesso PEMIQ Premium`
- Nota in piccolo sotto il pulsante: *"Pagamento self-service disponibile a breve."*

**Pass / Fail:** Pass

---

### TC-031-05 — Utente Premium rediretto alla dashboard

**Precondizione:** loggato come utente Premium

1. Naviga a `http://pemiq.local/premium`

**Risultato atteso:**

- Redirect automatico a `http://pemiq.local/dashboard`
- Nessuna pagina premium mostrata

**Pass / Fail:** Pass

---

### TC-031-06 — CTA "Passa a Premium" per utenti Free

**Precondizione:** loggato come utente Free

1. Controlla la navbar e/o la pagina profilo

**Risultato atteso:**

- Nella navbar è visibile il link o badge **"Passa a Premium"** (con link a `/premium`)
- Gli utenti Premium vedono invece il badge **"Premium"**

**Pass / Fail:** Pass

---

### TC-031-07 — Responsività mobile

**Precondizione:** TC-031-02 passato

1. Apri DevTools → modalità mobile (390px)
2. Naviga a `http://pemiq.local/premium`

**Risultato atteso:**

- Le due colonne Free/Premium si impilano verticalmente (grid a 1 colonna)
- Il testo non fuoriesce dai contenitori
- Il pulsante CTA è cliccabile e centrato

**Pass / Fail:** Pass

---

## Matrice riepilogativa


| Test      | Titolo                                     | Pass | Fail | Note |
| --------- | ------------------------------------------ | ---- | ---- | ---- |
| TC-030-01 | Gating: non autenticato → login            | OK   |      |      |
| TC-030-02 | Gating: Free → redirect dashboard          | OK   |      |      |
| TC-030-03 | Accesso Premium: pagina carica             | OK   |      |      |
| TC-030-04 | Selettori anno presenti e default corretti | OK   |      |      |
| TC-030-05 | Grafico due serie sovrapposte              | OK   |      |      |
| TC-030-06 | Tooltip al hover                           | OK   |      |      |
| TC-030-07 | Filtro sport                               | OK   |      |      |
| TC-030-08 | Nessun dato: messaggio testuale            | OK   |      |      |
| TC-030-09 | Responsività mobile                        | OK   |      |      |
| TC-031-01 | Gating: non autenticato → login            | OK   |      |      |
| TC-031-02 | Free accede alla pagina                    | OK   |      |      |
| TC-031-03 | Confronto funzionalità Free vs Premium     | OK   |      |      |
| TC-031-04 | CTA mailto con soggetto precompilato       | OK   |      |      |
| TC-031-05 | Premium rediretto a dashboard              | OK   |      |      |
| TC-031-06 | Badge "Passa a Premium" in navbar          | OK   |      |      |
| TC-031-07 | Responsività mobile                        | OK   |      |      |


---

## In caso di anomalia

- **Grafico non renderizza:** apri la console del browser (F12) e verifica errori JavaScript. Controlla che ApexCharts sia disponibile in `window.ApexCharts`.
- **Livewire non reagisce ai selettori:** verifica che il WebSocket/Livewire polling sia attivo nella tab Network. Prova a ricaricare la pagina.
- **Redirect inatteso su `/premium`:** verifica il valore di `is_premium` e `premium_expires_at` dell'utente via Filament admin o tinker: `docker compose exec app php artisan tinker --execute="dump(App\Models\User::find(ID)->only('is_premium','premium_expires_at'))"`
- **Flash message non compare:** verifica che la view del layout includa la sezione per i messaggi flash (cerca `session('error')` o `session('success')` nel layout).

