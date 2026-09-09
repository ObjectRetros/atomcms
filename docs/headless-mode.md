# Headless mode

Atom runs one application in either `full` (the default, with the Atom or Dusk PHP theme) or `headless` mode. `/api/v1` is available in both. Headless mode removes public HTML routes while retaining session authentication, housekeeping, Livewire, uploads, payment callbacks, queues, and scheduled work. The independent [Atom Vue frontend](https://github.com/DennisObject/atom-vue) uses HTTP and public assets; its source and build do not require PHP or database access.

The [OpenAPI contract](api/openapi.json) describes the implemented requests and responses. [Generated TypeScript definitions](api/schema.d.ts) are published by the backend. When updating the frontend to a new API version, copy these definitions into `atom-vue/src/api-schema.d.ts` and run its build. The [design specification](headless-mode-spec.md) retains the acceptance criteria; deployment readiness still requires checking your configured emulator, mail, game client, and payments.

## Prerequisites

Use this repository's PHP/Composer and Node lockfiles. Configure the database and selected `EMULATOR_DRIVER` as for a normal Atom install; never run installer import/fresh options against an existing hotel when you only intend to change presentation. Serve the backend's `public/` directory through your PHP web server, set `APP_URL` to its external origin, set `APP_DEBUG=false` in production, and provide a persistent `APP_KEY`. Keep `storage/` and `bootstrap/cache/` writable by the application.

Supported browser deployments use HTTPS on the same site: either sibling hosts with an explicit shared cookie domain, or one browser origin with a proxy. Unrelated frontend/backend domains need the same-origin proxy recipe below. `ATOM_FRONTEND_URL` is an origin (scheme, host, optional port), without a path or query. The mode command checks its format, scheme agreement, and cookie-domain relationship.

Install/build dependencies before activating the frontend:

```bash
composer install --no-interaction --prefer-dist
npm ci --ignore-scripts
npm run build:housekeeping
php artisan storage:link
```

Housekeeping reads `public/build-housekeeping/manifest.json`. Public PHP themes use `public/build/manifest.json`. Headless deployment needs the former, public media, and any separately hosted game assets; it does not need the latter.

## Fresh installation

Set `APP_URL` and database/environment settings first. For a fresh Arcturus installation:

```bash
php artisan atom:install --emulator=arcturus --headless \
  --frontend-url=https://hotel.example.com \
  --session-domain=example.com --admin=HotelOwner \
  --settings=/secure/atom-settings.json
```

This retains the installer's database/import safeguards, migrates/seeds CMS data, completes the shared setup, explicitly creates or promotes the named administrator, and builds housekeeping. Interactive setup asks for a new administrator's email and password, with protected password input. In noninteractive automation provide `ATOM_ADMIN_EMAIL` and `ATOM_ADMIN_PASSWORD` through your secret manager/process environment, not command arguments or a committed file. No default password or automatic administrator promotion of API registrants is used. Existing users are retained; `--admin` explicitly promotes that username.

Use the Ada installer with `--emulator=ada` against an Ada database prepared according to the existing emulator installation guidance. It does not import the Arcturus schema. Consult `php artisan atom:install --help` for the existing import options; `--fresh` destroys target tables and is not part of this headless recipe. `--headless` and `--theme` cannot be combined. `--skip-build` requires you to supply housekeeping assets separately.

A settings file contains only the shared setup allowlist. For example:

```json
{
  "hotel_name": "My Hotel",
  "cms_color_mode": "dark",
  "disable_registration": "0",
  "requires_beta_code": "0",
  "start_credits": 1000,
  "max_accounts_per_ip": 3
}
```

Other supported setup fields are `start_duckets`, `start_diamonds`, `start_points`, `max_comment_per_article`, `google_recaptcha_enabled`, `cloudflare_turnstile_enabled`, `website_wordfilter_enabled`, and `give_hc_on_register`. Boolean settings use `"0"`/`"1"`. Unknown keys are rejected. Configure CAPTCHA provider credentials, mail, payments, emulator connections, and other operator settings through their normal environment/housekeeping paths.

For a database already migrated and seeded but still waiting for the installation wizard, finish through the same setup operation:

```bash
php artisan atom:setup --complete --settings=/secure/atom-settings.json --admin=HotelOwner
```

## Convert and reverse an existing installation

Take your normal deployment backup, deploy the code/migrations, and build housekeeping. `atom:mode` changes configuration and relevant caches; it does not import, reseed, change emulator selection, or modify hotel accounts/balances/content.

```bash
php artisan migrate --force
php artisan atom:mode headless --frontend-url=https://hotel.example.com --session-domain=example.com
php artisan atom:mode --status
php artisan queue:restart
```

The command persists `ATOM_MODE`, `ATOM_FRONTEND_URL`, `ATOM_CORS_ORIGINS`, `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN`, and `SESSION_SECURE_COOKIE` atomically. It rebuilds config/routes, restores the previous environment file and clears affected caches if cache rebuilding fails, and reports missing assets. Restart persistent application servers and workers through your deployment process. `--status` reports effective configuration and asset readiness; it does not prove browser or integration readiness.

For a container or externally managed environment, print the required values and apply them in your deployment configuration:

```bash
php artisan atom:mode headless --frontend-url=https://hotel.example.com \
  --session-domain=example.com --environment-managed
```

Rebuild caches/restart after applying those values. Editing `.env` cannot override an injected process environment; the command detects conflicting externally managed values. Repeating the same mode configuration is harmless.

To return to the retained theme:

```bash
npm run build:dusk
# Use build:atom instead if Atom is the retained theme.
php artisan atom:mode full
php artisan queue:restart
```

The mode command retains the theme selection and hotel data. Deploy the build matching that selection, then reload persistent application servers. The API remains available in full mode. Retained frontend/CORS configuration still supports API clients.

## Sibling hosts

Example external origins: backend `https://api.example.com`, frontend `https://hotel.example.com`. Set `APP_URL=https://api.example.com` and run the headless mode command with `--session-domain=example.com`. The resulting browser settings are:

```dotenv
ATOM_MODE=headless
ATOM_FRONTEND_URL=https://hotel.example.com
ATOM_CORS_ORIGINS=https://hotel.example.com
SANCTUM_STATEFUL_DOMAINS=hotel.example.com,api.example.com
SESSION_DOMAIN=example.com
SESSION_SECURE_COOKIE=true
```

Use the explicit origin list, including any development ports. Browser requests use `credentials: 'include'`. The frontend must be able to read the shared-domain `XSRF-TOKEN` cookie; Laravel's session cookie stays HTTP-only. Production requires HTTPS. Keep session SameSite behavior consistent with same-site deployments; do not try to fix an unrelated-site architecture by turning off CSRF.

CORS allows credentials only on the v1/auth paths. The four legacy `/api` endpoints retain wildcard, noncredentialed behavior for existing third-party consumers. A disallowed origin cannot read credentialed responses; authorization and CSRF still protect unsafe operations.

## Same-origin proxy and local development

This is also the recommended recipe when the frontend's hosting domain differs from the backend's. Expose one browser origin, for example `https://hotel.example.com`, and proxy backend paths to a private PHP service. Set both `APP_URL` and `ATOM_FRONTEND_URL` to that external origin and leave `SESSION_DOMAIN` empty for host-only cookies.

The proxy must route these paths to Atom, preserve the external Host/scheme, and never cache authenticated responses:

- `/api/`, `/sanctum/`, `/housekeeping`, `/housekeeping/`, `/livewire/`, `/storage/`, `/build-housekeeping/`, and backend public assets such as `/assets/`.
- POST `/login`, `/register`, `/logout`, `/two-factor-challenge`, `/forgot-password`, and `/reset-password/{token}`.
- `/user/confirm-password`, `/user/confirmed-password-status`, `/user/two-factor-*`, `/user/confirmed-two-factor-authentication`, and `/user/settings/two-factor-authentication` (including its `/confirm` child).
- `/paypal/` callbacks and the configured PayPal webhook path from the backend route table.

Serve GET public page URLs through the Vue history fallback to `index.html`, including `/login`, `/register`, and `/reset-password/{token}`. This method distinction matters: forwarding every `/login` request to a headless backend would swallow the frontend page. Keep game clients/media on their configured public origins or proxy those separately. Configure trusted proxies using the project's existing deployment mechanism so generated URLs retain HTTPS.

For Nginx terminating TLS directly, the core routing can look like this inside the HTTPS server block (adapt paths and upstream to your deployment):

```nginx
root /srv/atom-vue/dist;
index index.html;
proxy_set_header Host $http_host;
proxy_set_header X-Forwarded-Host $http_host;
proxy_set_header X-Forwarded-Proto $scheme;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_cache off;

location /assets/ { try_files $uri @atom_assets; }
location @atom_assets { proxy_pass http://127.0.0.1:8000; }
location ~ ^/(api|sanctum|housekeeping|livewire(?:-[a-z0-9]+)?|storage|build-housekeeping|css|js|fonts|images|vendor|paypal)(/|$) {
    proxy_pass http://127.0.0.1:8000;
}
location ~ ^/user/(confirm-password|confirmed-password-status|two-factor-[^/]+|confirmed-two-factor-authentication|settings/two-factor-authentication)(/|$) {
    proxy_pass http://127.0.0.1:8000;
}
location ~ ^/(login|logout|register|two-factor-challenge|forgot-password|reset-password)(/|$) {
    if ($request_method = GET) { rewrite ^ /index.html last; }
    proxy_pass http://127.0.0.1:8000;
}
location / { try_files $uri $uri/ /index.html; }
```

Here `8000` represents an HTTP backend server, not a PHP-FPM socket. Configure TLS certificates and any separately served game-client paths outside this excerpt. If TLS terminates at a trusted outer proxy (as in the Averin tailnet demo), preserve its validated external HTTPS scheme rather than replacing it with the inner HTTP scheme; only trust forwarded headers from that proxy. Check that response redirects and absolute asset URLs keep the public origin and port.

The example Vite server already supplies a development proxy. Start Atom on its private/local port, then from the independent client directory:

```bash
npm ci --ignore-scripts
npm run dev
```

See the example's README for its exact backend target configuration. Use local HTTP only for development, use the browser-facing origin in `SANCTUM_STATEFUL_DOMAINS` including the Vite port, and clear/rebuild cached configuration after changing origins. The Averin verification deployment is `https://ovh-averin-remote-ssh.tail81b71b.ts.net:9443`, using this same-origin topology over tailnet HTTPS. It uses a separate Arcturus database and persistent backend/proxy services. Its private credentials are supplied separately, never committed here; no live PayPal credentials or isolated running game emulator are configured.

## Browser authentication and response handling

1. GET `/sanctum/csrf-cookie` with credentials.
2. On unsafe requests send the URL-decoded `XSRF-TOKEN` cookie as `X-XSRF-TOKEN`, plus `Accept: application/json`. Use `Content-Type: application/json` for JSON bodies; let the browser supply the multipart boundary for logo uploads.
3. POST `/login` with `username`, `password`, and optional `remember`. A `{ "two_factor": true }` response means login is pending. POST `/two-factor-challenge` with the code or recovery code using the same session. A successful challenge returns 204.
4. Read `/api/v1/me` for the authenticated account. Logout is POST `/logout` and returns 204. Registration returns 201 with JSON `""`; it accepts `mail`, not `email`, and requires accepted `terms`.

Atom's recommended enrollment flow is POST `/user/settings/two-factor-authentication` with `current_password`, render its `data.qr_code`, then POST its `/confirm` child with a six-digit `code`. DELETE the enrollment path with `current_password` to disable. GET `/api/v1/me/two-factor` returns enrollment/recovery data only after recent password confirmation; POST `/user/confirm-password` with `password` to refresh confirmation. Treat all QR/recovery data as private. The contract also records Fortify's existing management routes for compatibility; they use the same stored credentials and their native response shapes.

Password reset starts at POST `/forgot-password` with `mail`. Queue mail links to the frontend `/reset-password/{token}`; submit new `password` and `password_confirmation` to the backend path with the same token. The reset request response does not reveal whether the account exists. Required frontend paths are configurable through `config/atom.php`'s semantic destination map, including maintenance, banned, enrollment, and payment results.

Success responses normally use `{ "data": ... }`; paginated resources additionally use Laravel `links` and `meta`. Auth endpoints deliberately retain their native Fortify/Atom shape. Do not parse translated messages for application logic. V1 failures carry `code`, `message`, and optional field-keyed `errors`:

| Outcome | Client behavior |
| --- | --- |
| 401 `unauthenticated` | Clear private client state and return to login. |
| 419 `csrf_token_mismatch` | Refresh CSRF/session state and ask the user to retry; do not blindly replay purchases. |
| 422 `validation_failed` | Render each field's error array. Other purchase-related 422 codes describe rejected operations. |
| 423 `password_confirmation_required` | Prompt for password confirmation, then retry the protected read. |
| 429 `rate_limited` | Respect `Retry-After`; keep user input. |
| Restriction codes | Handle `installation_incomplete`, `maintenance`, `account_banned`, or `two_factor_required` staff enrollment from the server rather than guessing from HTTP status alone. |
| 503 integration failure | Report unavailability; retain the idempotency key for an authorized retry. |

Read `/api/v1/bootstrap` for safe hotel branding, locale choices, viewer flags, CAPTCHA site keys, advertised operations, and emulator capability flags. Ada supports rare values but does not advertise camera photos; unsupported camera requests are rejected. Always use the advertised capability flags for the configured driver. Home widgets return typed content; `my-groups` explicitly returns `supported: false` and null content because there is no existing group widget implementation.

Website money responses use integer minor units and an ISO currency code. POST PayPal order `amount` uses whole major units, 1–250. Home/badge costs use emulator currency units/codes. Package purchases and PayPal order creation require `Idempotency-Key` (1–100 letters, digits, `.`, `_`, `:`, or `-`). Reuse the same key and normalized payload after a network failure; different input returns 409. Successful results are retained for 30 days. Unresolved PayPal creation is retryable within the provider's six-hour idempotency window; older unresolved operations return 409 for reconciliation. Vouchers, referrals, and other writes keep their existing transactional protections.

Follow the returned PayPal `approval_url`; callbacks return to Atom for processing, then to the frontend. A frontend success query parameter never credits a balance. Poll authenticated order status to display completion. POST `/api/v1/client/launch` issues the configured client URL and SSO after access checks; never prefetch, cache, persist, or log that response.

## Build, workers, and verification

In the separate `atom-vue` checkout, build the client with its own `npm ci` and `npm run build`, then serve its `dist/` with history fallback. Preserve `storage/app/public` and the `public/storage` link across deployments. Media URLs and game-client configuration must be reachable by the browser. Keep queue workers and the existing scheduler running: queued password-reset mail and payment reconciliation do not depend on public theme rendering. Use the normal process supervisor for `php artisan queue:work`, schedule `php artisan schedule:run` every minute, and restart workers when configuration/code changes. Configure real mail/PayPal/RCON secrets through deployment secrets.

Maintainers update `docs/api/openapi.json` with behavior changes, then run:

```bash
npm run api:generate
npm run api:check
npm run api:test
```

The check compares `docs/api/schema.d.ts` with the generated definitions byte-for-byte, compiles schemas with AJV, and checks every registered v1 operation plus each documented auth operation against Laravel's route table. Pest contract checks feed actual application responses to that validator. The Vue client keeps its own snapshot of the generated definitions and builds in its own repository; backend tooling does not write to the frontend checkout. Vue, React, Angular, and Svelte consumers can all use the same generated `paths`/`components` types and credential/CSRF sequence.

Before serving users, verify a real browser journey: initialize CSRF, register/login, complete pending 2FA, change account data, browse articles, logout and see private state clear. Verify required staff enrollment and housekeeping login challenge, reset mail, bans/maintenance/support exceptions, uploads, payment return/reconciliation, and the configured game launch. Check conversion/reversal and retained theme rendering on disposable Arcturus/Ada installs. Builds, route coverage, schema tests, and a demo without real integration credentials do not establish live payment delivery or emulator game entry.
