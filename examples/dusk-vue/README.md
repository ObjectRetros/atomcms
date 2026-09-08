# Dusk Vue

An independent Vue 3 frontend for Atom's `/api/v1` HTTP contract. It recreates Dusk's navy panels, purple navigation, pixel artwork, account card, news and community layouts. It does not import PHP, Blade, backend source files, or a backend asset manifest.

## Run locally

Use Node 22 or later. Start an installed Atom backend configured for the example's origin, then:

```sh
cp .env.example .env
npm ci
npm run dev
```

Open `http://127.0.0.1:5173`. The development server proxies API and authentication requests to `VITE_DEV_API_URL` (default `http://127.0.0.1:8000`). Include `127.0.0.1:5173` in the backend's Sanctum stateful domains.

For a separately hosted API, set `VITE_API_URL` before building. Frontend and API must share a top-level domain for Sanctum session cookies; configure the backend cookie domain, explicit credentialed CORS origin, and stateful domains accordingly. No API secret belongs in a `VITE_` environment variable.

## Deploy

```sh
npm ci
npm run build
```

Serve `dist/` with an SPA fallback to `index.html`. When `VITE_API_URL` is empty, proxy `/api/*`, `/sanctum/*`, and authentication POST/PUT/DELETE requests to Atom while serving page GET requests from the SPA. Serve the backend's uploaded `/storage/*` and configured media paths through the API origin. The frontend does not need a PHP runtime. Generated contract types are bundled locally in `src/api-schema.d.ts`; the repository contract generator keeps that snapshot in sync with OpenAPI.

## Flows

-   Registration, login, password reset, authenticator/recovery-code challenge, logout.
-   Profile, password, session history, two-factor enrollment and recovery codes.
-   News, comments and reactions; staff, teams, leaderboards, photos and applications.
-   Store categories, gifts, vouchers, purchase history and PayPal payment status.
-   Support tickets and replies, hotel rules, profile homes, widgets, inventory and layout editing.
-   Pixel badge drawing/import and GIF download/purchase; the existing seven pixel logo fonts; rare values and Nitro launch.

Availability and permissions come from Atom. Unsupported emulator features show the backend's error; the existing unimplemented groups home widget shows an unavailable state. Flash URLs can be launched by the API, but modern browsers do not include a Flash player. A live Nitro renderer and emulator must be configured to play the game.

The frontend never calculates purchase eligibility, grants inventory, modifies balances, or marks a payment successful. Commerce retries retain their idempotency key until a successful response. Other mutations are not retried automatically. Passwords, TOTP/recovery codes and game tickets are not persisted in browser storage.

## Checks

```sh
npm run typecheck
npm run build
```

Browser verification requires a real backend: register, log out, log in, update the motto, enroll an authenticator, log out and confirm that a challenge is required, then browse news, homes, support and the store. Confirm errors for incorrect credentials and expired CSRF sessions. A successful build alone does not prove these integrations.

Static artwork is copied from Atom's existing Dusk/public assets and retains the repository's attribution and licensing. `gifenc` encodes the same 40 × 40 GIF badge format accepted by Atom; DOMPurify sanitizes rich content before rendering.
