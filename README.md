# PEMIQ

Piattaforma di analisi performance per atleti endurance: autenticazione, integrazione Strava, dashboard base, backoffice Filament.

**Stack**: Laravel 13 · PHP 8.4 · Filament v4 · Livewire 3 · MariaDB · Redis · Docker

---

## Sviluppo Locale

```bash
cp .env.example .env
# Configura le variabili nel .env (STRAVA_CLIENT_ID, STRAVA_CLIENT_SECRET, ecc.)

docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

L'app è disponibile su http://pemiq.local (aggiungi `127.0.0.1 pemiq.local` al tuo `/etc/hosts`).

Admin backoffice: http://pemiq.local/admin  
Email (Mailpit): http://localhost:8025

---

## Deploy UAT (https://uat.pemiq.com)

### Pre-requisiti
- Certificato SSL Let's Encrypt: `certbot certonly --webroot -d uat.pemiq.com`
- Certs in `/etc/letsencrypt/live/uat.pemiq.com/`

### Setup

```bash
# 1. Copia i file di configurazione
cp docker/uat/.env.uat.example docker/uat/.env.uat
# Compila tutte le variabili in docker/uat/.env.uat

# 2. Avvia i container
cd docker/uat
docker compose up -d

# 3. Prima installazione
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan filament:assets
```

### Aggiornamento

```bash
cd docker/uat
docker compose exec app php artisan down
git pull
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
docker compose exec app php artisan filament:assets
docker compose exec app php artisan up
```

---

## Deploy Production (https://pemiq.com)

### Pre-requisiti
- Certificato SSL Let's Encrypt: `certbot certonly --webroot -d pemiq.com -d www.pemiq.com`
- Certs in `/etc/letsencrypt/live/pemiq.com/`
- Account Postmark configurato per l'invio email

### Setup

```bash
# 1. Copia i file di configurazione
cp docker/production/.env.production.example docker/production/.env.production
# Compila tutte le variabili in docker/production/.env.production

# 2. Avvia i container
cd docker/production
docker compose up -d

# 3. Prima installazione
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan filament:assets
```

### Aggiornamento

```bash
cd docker/production
docker compose exec app php artisan down
git pull
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
docker compose exec app php artisan filament:assets
docker compose exec app php artisan up
```

---

## Differenze tra ambienti

| Feature           | Local      | UAT           | Production     |
|-------------------|------------|---------------|----------------|
| URL               | pemiq.local| uat.pemiq.com | pemiq.com      |
| SSL               | No         | Let's Encrypt | Let's Encrypt  |
| Email             | Mailpit    | Mailpit       | Postmark       |
| APP_ENV           | local      | staging       | production     |
| APP_DEBUG         | true       | false         | false          |
| Mailpit UI        | :8025      | :8025         | —              |
| Scheduler         | No         | Sì            | Sì             |

---

## Credenziali Admin di Default

- Email: `admin@pemiq.com`
- Password: `password`

**Cambia la password dopo il primo accesso in produzione.**
