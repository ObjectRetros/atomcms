# Atom CMS

- Use the installed Laravel 13 documentation and Laravel Boost guidance. Keep the existing kernel and provider registration structure; it remains supported by Laravel.
- Preserve Arcturus and Ada support. Access emulator-owned data through `app/Emulator/Contracts` and the selected driver, and leave emulator-owned schemas intact.
- Keep CMS tables under the `website_` prefix. Ada and Arcturus tests use separate disposable databases because their schemas share table names.
- Preserve both Atom and Dusk themes and existing translations. Build each theme with `npm run build:atom` and `npm run build:dusk` when changing frontend assets.
- Follow `pint.json` and the existing naming conventions. Run focused Pest tests for changed behavior, `composer lint`, and `composer analyse`; use `composer test` for changes to shared behavior.
- Configure disposable test databases using `.env.testing.example` before running tests. Tests recreate these databases; never point them at a hotel database.
- Keep documentation changes alongside behavior changes. Regenerate Boost guidance with `composer boost:update`; change this file for project-specific rules instead of editing generated guidance.
