# WinBoard ⚡

Tableau de bord de gamification en temps réel pour équipe commerciale VOIP.
Chaque RDV décroché déclenche une notification instantanée sur le grand écran : popup de célébration, confettis, fanfare sonore et mise à jour du classement.

---

## Fonctionnalités

- **Classement en direct** — leaderboard avec podium (top 3) et barres de progression
- **Déclaration de RDV** — formulaire avec niveau bronze / silver / gold (10 / 20 / 30 pts)
- **Notifications temps réel** — popup + confettis + son via WebSockets (Laravel Reverb)
- **Système de badges** — attribution automatique selon les performances
- **Vue grand écran** — page publique `/display` pour TV de bureau sans login
- **Google Calendar** — intégration iframe de l'agenda partagé sur la vue grand écran
- **Thème sombre** — interface gaming (fond #0b0f19, accent indigo #6366f1)

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | PHP 8.2 · Laravel 12 |
| Auth | Laravel Breeze (Blade) |
| BDD | MySQL |
| WebSockets | Laravel Reverb + Laravel Echo + pusher-js |
| Frontend | Vanilla JS · SCSS/Sass (BEM, 14 partiels) |
| Bundler | Vite + laravel-vite-plugin |

---

## Architecture

```
app/
├── Events/AppointmentDeclared.php   ← broadcast WebSocket (ShouldBroadcastNow)
├── Http/Controllers/
│   ├── AppointmentController.php    ← déclaration RDV + broadcast
│   ├── DashboardController.php      ← tableau de bord commercial
│   └── DisplayController.php        ← vue grand écran + API JSON
├── Models/
│   ├── User.php                     ← role, points, badges
│   ├── Appointment.php              ← pointsForLevel() static
│   └── Badge.php
└── Services/BadgeService.php        ← attribution automatique de badges

resources/
├── js/
│   ├── app.js                       ← JS interface commerciaux
│   └── display.js                   ← JS grand écran (Echo, popup, son, confettis)
└── scss/
    ├── app.scss                     ← point d'entrée (@use + @import)
    ├── _variables.scss              ← couleurs, espacements
    ├── _display.scss                ← vue TV, popup, confettis
    └── ...                          ← 11 autres partiels (layout, navbar, forms…)
```

---

## Déploiement local (WampServer / Laragon)

### Prérequis

- PHP 8.2+
- MySQL
- Node.js 18+
- Composer

### Installation

```bash
# 1. Cloner et installer les dépendances
git clone <repo> winboard
cd winboard
composer install
npm install

# 2. Configuration
cp .env.example .env
php artisan key:generate
```

Éditer `.env` :

```env
DB_DATABASE=winboard
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=winboard
REVERB_APP_KEY=winboard-key-local
REVERB_APP_SECRET=winboard-secret-local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Optionnel — URL src de l'iframe Google Calendar
GOOGLE_CALENDAR_EMBED_URL=
```

```bash
# 3. Base de données
php artisan migrate
php artisan db:seed   # crée 1 admin + 4 commerciaux de démo + 5 badges

# 4. Assets
npm run build
# ou en développement :
npm run dev

# 5. Lancer les serveurs (3 terminaux séparés)
php artisan serve          # Laravel (port 8000)
php artisan reverb:start   # WebSocket (port 8080)
php artisan queue:listen   # Files d'attente (optionnel en local)
```

### Comptes de démo

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@winboard.fr | password |
| Commercial | alice@winboard.fr | password |
| Commercial | bob@winboard.fr | password |

### URLs locales

| Page | URL |
|------|-----|
| Connexion | http://localhost:8000/login |
| Tableau de bord | http://localhost:8000/dashboard |
| Déclarer un RDV | http://localhost:8000/appointments/create |
| Grand écran (TV) | http://localhost:8000/display |

---

## Déploiement en production (VPS Linux + Nginx)

### 1. Variables d'environnement

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tondomaine.com

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=winboard
REVERB_APP_KEY=<clé-aléatoire-robuste>
REVERB_APP_SECRET=<secret-aléatoire-robuste>
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=tondomaine.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

### 2. Installation

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Supervisor — maintenir Reverb en vie

Créer `/etc/supervisor/conf.d/reverb.conf` :

```ini
[program:reverb]
command=php /var/www/winboard/artisan reverb:start --host=0.0.0.0 --port=8080
directory=/var/www/winboard
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/reverb.log
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start reverb
```

### 4. Nginx — reverse proxy WebSocket

```nginx
server {
    listen 443 ssl;
    server_name tondomaine.com;

    root /var/www/winboard/public;
    index index.php;

    # SSL (Let's Encrypt / Certbot)
    ssl_certificate     /etc/letsencrypt/live/tondomaine.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/tondomaine.com/privkey.pem;

    # WebSocket → Reverb
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 60;
    }

    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Architecture des ports en production

```
Client (navigateur)
    │
    ├── HTTPS :443  ──────────→ Nginx ──────────→ PHP-FPM (Laravel)
    │
    └── WSS :443 /app/...  ──→ Nginx ──────────→ Reverb :8080 (interne)
```

Reverb n'est jamais exposé directement — tout passe par Nginx.

---

## Google Calendar

1. Ouvrir Google Calendar → ⚙️ Paramètres → sélectionner le calendrier partagé
2. Section **Intégrer l'agenda** → copier le lien `src` de l'iframe
3. Ajouter dans `.env` :

```env
GOOGLE_CALENDAR_EMBED_URL=https://calendar.google.com/calendar/embed?src=...
```

---


## Demarrage
```bash
npm run dev
php artisan serve
php artisan reverb:start
```

## Licence

Usage interne — projet privé.
