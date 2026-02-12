# Atom CMS Repository Guidelines

## Project Overview

Atom CMS is a Laravel 12 application with Filament v4, Livewire v3, Fortify authentication, and multi-theme Vite builds. PHP 8.4+, Pest v3, Tailwind CSS v4.

## Build Commands

```bash
composer install && npm install      # Install dependencies
php artisan serve                    # Laravel dev server
./vendor/bin/sail up                 # Or use Sail
npm run dev:atom                     # Hot reload atom theme
npm run dev:dusk                     # Hot reload dusk theme
npm run build:atom                   # Production build atom
npm run build:dusk                   # Production build dusk
php artisan migrate --seed           # Database setup
```

## Lint & Format Commands

```bash
./vendor/bin/pint                    # Format all PHP files
./vendor/bin/pint --dirty            # Format changed files only
npm run format                       # Prettier (JS/TS/Vue/Blade)
```

## Test Commands

```bash
./vendor/bin/pest                    # Run all tests
php artisan test --compact           # Alternative: all tests

# Run single test (IMPORTANT)
./vendor/bin/pest --filter=test_name
php artisan test --compact --filter=test_name

# Run specific file
./vendor/bin/pest tests/Feature/AuthenticationTest.php

# Run with coverage
./vendor/bin/pest --coverage
```

## Code Style Guidelines

### PHP Formatting (Pint/Laravel Preset)
- Control structure braces: same line (`if () {`)
- Function/class braces: next line
- Concatenation: single space (`'a' . 'b'`)
- Imports: alphabetically ordered, no unused imports
- Trailing commas in multiline arrays, arguments, parameters
- Visibility required on all properties, methods, constants
- Return type declarations required on all methods

### Type Declarations
```php
protected function isAccessible(User $user, ?string $path = null): bool
public function __construct(public GitHub $github) {}
```

### Models
- Use `casts()` method instead of `$casts` property
- Guarded mode (`$guarded`) preferred over `$fillable`
- Relationship methods with return type hints: `public function posts(): HasMany`
- Avoid `DB::` facade - use `Model::query()` instead

### Controllers & Validation
- Create Form Request classes for validation (never inline in controllers)
- Use array-based validation rules:
```php
public function rules(): array
{
    return [
        'username' => ['required', 'string', 'max:25'],
    ];
}
```

### Naming Conventions
- Controllers/Jobs: `HotelRoomController`, `SyncUserRanksJob` (PascalCase)
- Config keys: `snake_case`
- Asset filenames: `kebab-case`
- Vue components: `PascalCase`
- Enum keys: `TitleCase` (e.g., `FavoritePerson`)

### Comments & PHPDoc
- Prefer PHPDoc blocks over inline comments
- Only comment exceptionally complex logic

## Laravel Boost Tools

Laravel Boost is an MCP server with specialized tools. Use them when available.

- `search-docs` - Search version-specific Laravel docs **before** making changes. Use broad queries: `['rate limiting', 'routing']`
- `tinker` - Execute PHP for debugging Eloquent models
- `database-query` - Read-only SQL queries
- `browser-logs` - Read browser console errors (recent only)
- `list-artisan-commands` - Check available command parameters
- `get-absolute-url` - Generate correct project URLs

### Documentation Search Syntax
- Simple: `authentication` (auto-stemming)
- AND logic: `rate limit` (both terms)
- Exact phrase: `"infinite scroll"` (adjacent words)
- Multiple: `["authentication", "middleware"]` (any term)

## Architecture & Patterns

### Directory Structure (Laravel 10 style - do not migrate)
- `app/Http/Middleware/` - Middleware
- `app/Providers/` - Service providers
- `app/Http/Kernel.php` - Middleware registration
- `app/Console/Kernel.php` - Console commands & schedule
- `app/Exceptions/Handler.php` - Exception handling
- No `bootstrap/app.php` configuration file

### Creating Files
- Always use `php artisan make:` commands
- Pass `--no-interaction` to all Artisan commands
- Create factories and seeders alongside new models

### Database
- Migrations modifying columns must include all previously defined attributes
- Eager load relationships to prevent N+1 queries
- Laravel 12 supports `$query->latest()->limit(10)` on eager loads

### Configuration
- Use `config('app.name')` - never `env()` outside config files

### Frontend
- If UI changes don't appear, run `npm run build` or `npm run dev`
- Vite manifest error = needs build

## Testing Guidelines

### Test Creation
```bash
php artisan make:test --pest FeatureName    # Feature test (default)
php artisan make:test --pest --unit Name    # Unit test
```

### Conventions
- Feature tests in `tests/Feature/`, unit tests in `tests/Unit/`
- Use `*Test.php` suffix for discovery
- Use factories for model creation in tests
- Faker: use `$this->faker->word()` or `fake()->randomDigit()`
- Cover happy path and dominant failure modes
- Keep tests idempotent - prefer factories over manual SQL

### Pest Example
```php
test('users can authenticate', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    expect($response->status())->toBe(302)
        ->and(auth()->check())->toBeTrue();
});
```

## Skills Activation

When available, activate domain-specific skills:
- `pest-testing` - When writing/running tests or debugging test failures
- `tailwindcss-development` - When styling components or UI changes

## Commit Guidelines

- Imperative, lowercase subjects with optional scope
- Examples: `refactor(profile): improve dashboard grid`, `style: run laravel pint`
- Group related changes, avoid WIP commits
- Run `./vendor/bin/pest` and `./vendor/bin/pint` before committing
