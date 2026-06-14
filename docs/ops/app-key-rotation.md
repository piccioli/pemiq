# Procedura di rotazione APP_KEY

## Quando ruotare APP_KEY

Ruotare `APP_KEY` nei seguenti scenari:

- **Compromissione sospetta** della chiave (es. accidentalmente committata in git, esposta in log, violazione del server)
- **Cambio di personale** con accesso alle variabili d'ambiente di produzione
- **Politica di rotazione periodica** (es. ogni 12 mesi)
- **Migrazione di ambiente** (es. da un provider hosting a un altro)

> ⚠️ **NON** ruotare APP_KEY durante una finestra di traffico elevato — la procedura richiede un breve periodo di manutenzione.

---

## Rischio di perdita dati se si ruota senza re-cifrare prima

`APP_KEY` è la chiave master usata da Laravel per due operazioni critiche:

1. **Cifratura Eloquent** (`encrypted` cast) — usata da `StravaAccount` per `access_token` e `refresh_token`
2. **Firma delle sessioni e dei cookie**

Se si sostituisce `APP_KEY` senza re-cifrare i dati prima:

- `access_token` e `refresh_token` di tutti i record `StravaAccount` diventano **illeggibili** (decrypt fallisce con `DecryptException`)
- Tutti gli utenti vengono disconnessi (le sessioni e i cookie firmati con la vecchia chiave non sono più validi)
- La sincronizzazione Strava smette di funzionare per tutti gli account collegati — gli utenti devono ricollgare Strava manualmente

---

## Procedura sicura di rotazione

### Passo 1 — Backup

```bash
# Backup del database
docker compose exec mariadb mysqldump -u pemiq -ppemiq pemiq > backup_pre_key_rotation_$(date +%Y%m%d_%H%M%S).sql

# Annotare la APP_KEY corrente (per rollback)
grep APP_KEY .env
```

### Passo 2 — Generare la nuova chiave

```bash
# Genera la nuova chiave senza applicarla ancora
php artisan key:generate --show
# Output esempio: base64:aBcDeFgHiJkLmNoPqRsTuVwXyZ...=
```

Copiare il valore generato — servirà nei passi successivi.

### Passo 3 — Configurare la chiave precedente come fallback

In `.env`, aggiungere `APP_PREVIOUS_KEYS` con la chiave **corrente** (prima di cambiarla):

```dotenv
APP_KEY=base64:CHIAVE_CORRENTE...=
APP_PREVIOUS_KEYS=base64:CHIAVE_CORRENTE...=
```

Aggiungere `APP_PREVIOUS_KEYS` nel container:

```bash
docker compose exec app php artisan config:clear
```

### Passo 4 — Sostituire APP_KEY con la nuova chiave

```dotenv
APP_KEY=base64:NUOVA_CHIAVE...=
APP_PREVIOUS_KEYS=base64:CHIAVE_CORRENTE...=
```

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

A questo punto Laravel può **leggere** i dati cifrati con la vecchia chiave (via `APP_PREVIOUS_KEYS`) ma li **riscrive** con la nuova.

### Passo 5 — Re-cifrare i token Strava

Eseguire questo script via `php artisan tinker` nel container:

```bash
docker compose exec app php artisan tinker
```

Nell'REPL Tinker, incollare:

```php
use App\Models\StravaAccount;

$count = 0;
StravaAccount::chunk(100, function ($accounts) use (&$count) {
    foreach ($accounts as $account) {
        // Leggere i valori (usa APP_PREVIOUS_KEYS per decrypt)
        $accessToken  = $account->access_token;
        $refreshToken = $account->refresh_token;

        if ($accessToken === null && $refreshToken === null) {
            continue; // account disconnesso, skip
        }

        // Scrivere (re-cifra con la nuova APP_KEY)
        $account->update([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
        $count++;
    }
});

echo "Re-cifrati $count record StravaAccount.\n";
```

Verificare che il conteggio corrisponda al numero atteso di account con status `connected`.

### Passo 6 — Verificare il funzionamento

```bash
# Verifica che la sync parta senza DecryptException
docker compose exec app php artisan tinker --execute="App\Models\StravaAccount::where('connection_status','connected')->first()?->access_token;"
# Deve restituire una stringa non vuota (non un'eccezione)
```

### Passo 7 — Rimuovere la vecchia chiave dal fallback

Una volta confermato il corretto funzionamento, rimuovere `APP_PREVIOUS_KEYS` dal `.env`:

```dotenv
APP_KEY=base64:NUOVA_CHIAVE...=
# APP_PREVIOUS_KEYS rimosso
```

```bash
docker compose exec app php artisan config:clear
```

---

## Rollback

Se qualcosa va storto **prima** di aver rimosso la vecchia chiave da `APP_PREVIOUS_KEYS`:

```bash
# Ripristinare la APP_KEY originale nel .env
APP_KEY=base64:CHIAVE_ORIGINALE...=
# Rimuovere APP_PREVIOUS_KEYS

docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

Se la rotazione è già stata completata e i dati re-cifrati con la nuova chiave, ripristinare il backup del database:

```bash
docker compose exec -T mariadb mysql -u pemiq -ppemiq pemiq < backup_pre_key_rotation_YYYYMMDD_HHMMSS.sql
```

Poi ripristinare la `APP_KEY` originale nel `.env` e fare `config:clear`.

---

## Checklist rapida

- [ ] Backup database eseguito
- [ ] Vecchia APP_KEY annotata
- [ ] Nuova chiave generata con `key:generate --show`
- [ ] APP_PREVIOUS_KEYS configurato con la vecchia chiave
- [ ] APP_KEY aggiornata con la nuova chiave
- [ ] `config:clear` e `cache:clear` eseguiti
- [ ] Script re-cifratura Tinker eseguito e count verificato
- [ ] Funzionamento verificato (nessun DecryptException)
- [ ] APP_PREVIOUS_KEYS rimosso dal .env
- [ ] `config:clear` finale eseguito

---

## Note tecniche

- `StravaAccount` usa `encrypted` cast per `access_token` e `refresh_token` — sono gli **unici** campi cifrati nel progetto al momento
- `APP_PREVIOUS_KEYS` supporta multiple chiavi separate da virgola: `base64:KEY1...=,base64:KEY2...=`
- Le sessioni Redis non contengono dati cifrati con APP_KEY — vengono invaldate automaticamente al cambio di APP_KEY (tutti gli utenti vengono disconnessi), che è il comportamento desiderato
- `php artisan session:flush` può essere usato per invalidare esplicitamente tutte le sessioni attive prima della rotazione se si preferisce un cutover netto
