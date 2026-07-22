<div align="center">
<img src="https://i.imgur.com/9ePNdJ4.png" alt="Atom CMS"/>

A modern, community-driven Retro CMS built with Laravel 13.x

[![Discord](https://img.shields.io/badge/Discord-Join%20Server-5865F2?style=flat&logo=discord&logoColor=white)](https://discord.gg/pP6HyZedAj)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)

[Live Demos](#live-preview) • [Installation](#installation) • [Documentation](https://github.com/ObjectRetros/atomcms/wiki) • [Contributing](#contributing)

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
| Database | Arcturus Morningstar 3.5.5 (bundled, imported by the installer) |

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

One command installs everything - dependencies, the bundled Arcturus Morningstar 3.5.5 base database with an up-to-date catalog, app key, storage link, migrations, seeders and your theme's assets:

```bash
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms

composer setup
```

The installer prompts for your database credentials, offers to create the database if it doesn't exist, and imports the Arcturus base SQL automatically (skipped if the tables already exist). On Windows it connects the questions directly to the console even though Composer cannot allocate a child-process TTY. It then asks which theme you want (atom or dusk), activates it and builds its assets. When it finishes, serve the site and visit `/installation` to configure your hotel.

Useful variations:

```bash
php artisan atom:install                            # Re-run just the installer (no dependency install)
php artisan atom:install --sql=/path/to/db.sql      # Use your own Arcturus dump (.sql or .sql.gz)
php artisan atom:install --catalog-sql=/path/to.sql # Use your own catalog dump on top of the base
php artisan atom:install --skip-catalog             # Keep the stock catalog from the base database
php artisan atom:install --skip-arcturus            # Skip the base database + catalog import entirely
php artisan atom:install --theme=dusk               # Pick the theme without being asked
php artisan atom:install --skip-build               # Skip building theme assets (npm run build:atom|dusk)
```

Prefer to do it step by step? Follow the manual guides below.

### Windows Setup

```bash
# Clone the repository
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms

# Configure environment
copy .env.example .env
# Edit .env and update your database credentials

# Install dependencies
composer install
npm install

# Generate key and set up database
php artisan key:generate
php artisan migrate --seed

# Build assets
npm run build:atom
# For development: npm run dev:atom
```

#### IIS Configuration

Point your IIS site to the `public` folder inside the `atomcms` directory.

#### Required Permissions

Grant "Full control" to both `IUSR` and `IIS_IUSRS` for the atomcms folder.

**Visual guide:** [Permission setup tutorial](https://gyazo.com/7d5f38525a762c1b26bbd7552ca93478)

#### Troubleshooting cURL SSL Errors

If you encounter cURL 60 errors:

1. Download the latest [cacert.pem](https://curl.se/docs/caextract.html)
2. Place it in `C:/`
3. Edit `php.ini` and update:
   ```ini
   curl.cainfo = "C:/cacert-2025-09-09.pem"
   ```
4. Restart your web server

#### Complete Windows Tutorial

New to retro hotel setup? Follow our comprehensive three-part series:

- [Part 1: Basic Setup](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-1.92532/)
- [Part 2: Configuration](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-2.92533/)
- [Part 3: Finalization](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-3.92543/)

---

### Linux Setup

```bash
# Clone the repository
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms

# Configure environment
cp .env.example .env
# Edit .env and update your database credentials

# Install dependencies
composer install
npm install

# Generate key and set up database
php artisan key:generate
php artisan migrate --seed

# Build assets
npm run build:atom
# For development: npm run dev:atom
```

#### Set Permissions

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### NGINX Configuration

For NGINX setup, refer to Laravel's [deployment documentation](https://laravel.com/docs/13.x/deployment#nginx).

#### Complete Linux Tutorial

Need help setting up your retro hotel on Linux? Follow our comprehensive Ubuntu tutorial:

- [Complete Ubuntu Setup Guide](https://git.krews.org/duckietm/ubuntu-tutorial) - Step-by-step instructions for Ubuntu

---

## Configuration

### Production Environment

Update these variables in your `.env` file for production:

```dotenv
APP_ENV=production
APP_DEBUG=false
FORCE_HTTPS=true  # If using Cloudflare's "Always use HTTPS"
```

### Cloudflare Turnstile Captcha

Protect your site from bots:

1. Visit [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/)
2. Sign in and select your site
3. Copy the site and secret keys to `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` in your `.env` file

### Important: Disable Rocket Loader

Atom CMS uses JavaScript that conflicts with Cloudflare's Rocket Loader. To disable:

1. Go to your Cloudflare dashboard
2. Navigate to **Speed** → **Optimization**
3. Find **Rocket Loader™** and disable it

### Migrating from Another CMS

If migrating from Cosmic CMS or similar platforms:

Set `RENAME_COLLIDING_TABLES=true` in your `.env` file. Atom CMS will automatically handle table conflicts.

**Note:** We recommend proper manual cleanup, but this feature helps avoid common migration issues.

---

## Testing

Atom CMS includes a growing test suite using Pest.

### Run Tests

```bash
# Using Pest
vendor/bin/pest

# Using Artisan
php artisan test
```

---

## Documentation

For detailed documentation, addons, tips, and tricks, visit our [official wiki](https://github.com/ObjectRetros/atomcms/wiki).

### Learning Laravel

New to Laravel? These free resources will help:
- [Official bootcamp & course](https://learn.laravel.com) - Official Laravel bootcamp & course
- [Laracasts](https://laracasts.com) - Video courses covering Laravel, Livewire, testing and more
---

## Contributing

We welcome contributions! To maintain code quality and streamline reviews, please read our [contribution guidelines](https://github.com/ObjectRetros/atomcms/wiki/0.-Contribution-guidelines) before submitting a pull request.

---

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
