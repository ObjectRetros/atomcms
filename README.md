<div align="center">
<img src="https://i.imgur.com/9ePNdJ4.png" alt="Atom CMS"/>

A modern, community-driven Retro CMS built with Laravel 13.x

[![Discord](https://img.shields.io/badge/Discord-Join%20Server-5865F2?style=flat&logo=discord&logoColor=white)](https://discord.gg/pP6HyZedAj)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)

[Live Demos](#live-preview) • [Installation](#installation) • [Documentation](docs/wiki/README.md) • [Contributing](#contributing)

</div>

>[!NOTE]
>Disclaimer: Educational Use Only
> 
> Atom CMS is provided as an educational resource for learning purposes only. The creators and contributors are not responsible for any misuse or unintended consequences arising from its use. By using Atom CMS, you agree to take full responsibility for your actions and ensure compliance with all applicable laws and regulations in your jurisdiction.


## About

Atom CMS is a modern, community-driven CMS designed to provide a flexible and user-friendly platform for retro hotel management. Built on Laravel 13.x with a focus on extensibility and ease of use, Atom CMS features a built-in theme system that allows you to use any CSS framework or create fully customized vanilla designs.

### Built With

- **[Laravel 13.x](https://laravel.com/docs/13.x)** - Elegant PHP framework powering the backend
- **[Livewire 4](https://livewire.laravel.com/)** - Dynamic frontend components without leaving Blade
- **[Filament 5](https://filamentphp.com/)** - Powering the integrated housekeeping panel
- **[Vite](https://vitejs.dev/)** - Next-generation frontend tooling for blazing-fast builds
- **[TailwindCSS 4](https://tailwindcss.com/)** - Utility-first CSS framework for responsive design

---

## Features

- **Built-in Theme System** - Use any CSS framework or create custom themes
- **Secure Authentication** - Laravel-powered authentication and authorization
- **Multi-language Support** - Built-in localization for global audiences
- **Integrated Housekeeping** - Comprehensive Filament-powered admin panel
- **Rcon System** - Real-time server communication
- **Responsive Design** - Mobile-first approach with TailwindCSS
- **Modern Stack** - Latest PHP 8.5+ features, PHPStan level 8 static analysis

---

## Live Preview

Experience Atom CMS with our official themes:

- **Dusk Theme**: [https://dusk.atomcms.dev](https://dusk.atomcms.dev)
- **Atom Theme**: [https://atom.atomcms.dev](https://atom.atomcms.dev)

---

## Requirements

| Requirement | Version |
|------------|---------|
| PHP | 8.5 or higher |
| MySQL | 8.x or higher |
| MariaDB | 10.x or higher |
| Composer | v2 |
| Node.js | LTS |
| Emulator database | Arcturus Morningstar 3.5.5 (default) or Ada |

### Required PHP Extensions

Ensure the following extensions are enabled in your `php.ini`:

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=sockets
extension=intl
```

**Note:** Remove the semicolon (`;`) prefix if the extension is commented out.

---

## Installation

### Quick Setup (Recommended)

One command installs everything - dependencies, emulator database integration, app key, storage link, migrations, seeders and your theme's assets:

```bash
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms

composer setup
```

The installer asks which emulator you use. Arcturus imports the bundled base SQL automatically. For Ada, start the emulator once first so its EF migrations create the database, then run `php artisan atom:install --emulator=ada` against that same database. The installer then activates and builds your chosen theme. When it finishes, serve the site and visit `/installation` to configure your hotel.

The installer never writes over a database it did not build. If the target already holds an emulator's schema - Arcturus tables, Polaris' Flyway history, or Ada's Entity Framework history - the base import is refused rather than offered, `--fresh` included. Point Atom at that same database with `--skip-arcturus` (or the matching `--emulator`) and it adds only its own tables. Atom keeps its password reset tokens in `website_password_resets` for the same reason: `password_resets` belongs to the emulator, and an existing one is left untouched.

Docker Compose includes separate `mariadb` and `mariadb-ada` services with independent volumes, so testing Ada never requires clearing the Arcturus database. Use `mariadb-ada:3306` from another Compose service, or `127.0.0.1:3308` from the host. The default Ada database and credentials are `atomcms_ada`, `atomcms`, and `password`. Point Ada at it first so EF applies its migrations, then set Atom to the same database with `EMULATOR_DRIVER=ada`.

Useful variations:

```bash
php artisan atom:install                            # Re-run just the installer (no dependency install)
php artisan atom:install --sql=/path/to/db.sql      # Use your own Arcturus dump (.sql or .sql.gz)
php artisan atom:install --emulator=ada             # Install against an EF-migrated Ada database
php artisan atom:install --catalog-sql=/path/to.sql # Use your own catalog dump on top of the base
php artisan atom:install --fresh                    # Clear the target database first (destroys existing data, refused on a hotel)
php artisan atom:install --skip-catalog             # Keep the stock catalog from the base database
php artisan atom:install --skip-arcturus            # Skip the base database + catalog import entirely
php artisan atom:install --theme=dusk               # Pick the theme without being asked
php artisan atom:install --skip-build               # Skip building theme assets (npm run build:atom|dusk)
```

For step-by-step Linux and Windows installation, IIS/NGINX permissions, production deployment, updates and troubleshooting, see the [installation and deployment guide](docs/wiki/1.-Installing-Atom-CMS.md).

Keep the site restricted to the operator until `/installation` is complete. Serve only the `public/` directory, and give the web process write access to `storage/` and `bootstrap/cache/`; keep source code and `.env` owned by the deployment user.

---

## Configuration

### Production Environment

Update these variables in your `.env` file for production:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hotel.example
SESSION_SECURE_COOKIE=true
TRUSTED_PROXIES=
```

Use an empty `TRUSTED_PROXIES` for direct visitor traffic, or explicitly list your trusted proxy IPs/CIDRs. `FORCE_HTTPS=true` can force generated URLs to HTTPS; configure TLS on the web server or proxy as well. Rebuild cached configuration after changing `.env`. See [production configuration](docs/wiki/1.-Installing-Atom-CMS.md#production-configuration).

### Cloudflare Turnstile Captcha

Protect your site from bots:

1. Visit [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/)
2. Sign in and select your site
3. Copy the site and secret keys to `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` in your `.env` file
4. Enable `cloudflare_turnstile_enabled` in Housekeeping's CMS settings and disable `google_recaptcha_enabled`. See [bot protection](docs/wiki/4.-Bot-protection.md).

### Important: Disable Rocket Loader

Atom CMS uses JavaScript that conflicts with Cloudflare's Rocket Loader. To disable:

1. Go to your Cloudflare dashboard
2. Navigate to **Speed** → **Optimization**
3. Find **Rocket Loader™** and disable it

### Migrating from Another CMS

Back up the hotel database and review each schema collision before migrating. `RENAME_COLLIDING_TABLES=true` allows migrations to archive colliding tables/columns under `old_<timestamp>_<name>` names; it does not reconcile data for you. Leave it disabled unless the affected data is safe to archive. See [installation and updates](docs/wiki/1.-Installing-Atom-CMS.md).

---

## Testing

Atom CMS uses Pest with separate disposable MariaDB databases for Arcturus and Ada (`testing` and `testing_ada` by default). The suite drops and recreates tables with `RefreshDatabase`; configure database credentials restricted to those databases and clear cached configuration before running tests. See [contribution checks](docs/wiki/0.-Contribution-guidelines.md#checks).

### Run Tests

```bash
# Using Pest
vendor/bin/pest

# Using Artisan
php artisan test
```

---

## Documentation

The [versioned documentation](docs/wiki/README.md) covers installation, themes, translations, clients, payments and hotel settings. These pages are reviewed through pull requests and provide the source for updates to the [GitHub wiki](https://github.com/ObjectRetros/atomcms/wiki).

### Learning Laravel

New to Laravel? These free resources will help:
- [Official bootcamp & course](https://learn.laravel.com) - Official Laravel bootcamp & course
- [Laracasts](https://laracasts.com) - Video courses covering Laravel, Livewire, testing and more
---

## Contributing

We welcome contributions! To maintain code quality and streamline reviews, please read our [contribution guidelines](docs/wiki/0.-Contribution-guidelines.md) before submitting a pull request.

---

### Laravel Boost

For development, set `APP_ENV=local` in your local `.env` and run `php artisan boost:update` after installing dependencies or changing framework packages to refresh the repository's Laravel Boost guidance. Run `php artisan boost:install` to configure your preferred agent, MCP integration and skills locally. See [Laravel Boost documentation](https://laravel.com/docs/13.x/boost).

## Credits

Atom CMS is made possible by our amazing community:

### Core Contributors

- **Kasja** - Design direction, Dusk theme, ideas & graphics
- **INicollas** - Dark mode, Turbolinks, article reactions, user sessions, PT-BR translations, Orion Housekeeping
- **Kani** - Rcon system, FindRetros API, Atom CMS v2 creator/maintainer
- **DuckieTM** - Badge drawer, bugfixes, housekeeping features
- **EntenKoeniq** - Auto language registration, color scheme selection, various page fixes

### Contributors

- **Dominic** - Performance improvements, user sessions
- **Beny** - FindRetros API fixes, Cloudflare fixes
- **Live** - French translations, bugfixes
- **MisterDeen** - Custom Discord widget
- **DamienJolly**, **Danbo**, **Diddy/Josh** - Various bugfixes and improvements
- **Sonay** - Material theme inspiration
- **Raizer** - Circinus

### Translations

- **Oliver** - Finnish
- **Damue & EntenKoeniq** - German
- **Talion** - Turkish
- **CentralCee, Rille & Tuborgs** - Swedish
- **Yannick** - Dutch
- **Gedomi** - Spanish
- **Lorenzune** - Italian
- **Twana** - Norwegian
- **Plow** - French

---

<div align="center">

**[⬆ Back to Top](#readme)**

Made with ❤️ by the Atom CMS Community

</div>
