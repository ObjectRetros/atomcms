# Documentation index

These pages are the versioned source for Atom CMS documentation. They cover all 18 pages from the GitHub wiki at revision `4fd17818bb65da60ed5584d91813b52c92335dac`, updated against the code in this repository.

## Guides

- [Home](Home.md)
- [1. Installing Atom CMS](1.-Installing-Atom-CMS.md)
- [2. Language & translations](2.-Language-%26-translations.md)
- [3. Theme system](3.-Theme-system.md)
- [4. Bot protection](4.-Bot-protection.md)
- [5. VPN Prevention](5.-VPN-Prevention.md)
- [6. Maintenance mode](6.-Maintenance-mode.md)
- [7. Findretros setup](7.-Findretros-setup.md)
- [8. Nitro setup](8.-Nitro-setup.md)
- [9. Shop setup](9.-Shop-setup.md)
- [Account limitation](Account-limitation.md)
- [Error logging](Error-logging.md)
- [Miscellaneous](Miscellaneous.md)
- [Registration limiting](Registration-limiting.md)
- [Staff applications](Staff-applications.md)
- [Two‐factor authentication](Two%E2%80%90factor-authentication.md)
- [Username filtering](Username-filtering.md)
- [0. Contribution guidelines](0.-Contribution-guidelines.md)

## Changing settings

Use the CMS Settings and Permissions resources under `/housekeeping` whenever available. Saving through those resources refreshes the application caches. Direct SQL changes skip model events: refresh the relevant caches before expecting the site to use new values. For `website_settings`, use `php artisan tinker` and run `app(\App\Services\SettingsService::class)->refresh();`. For website permissions, call `app(\App\Services\PermissionsService::class)->refresh();`; housekeeping permissions have their own `HousekeepingPermissionsService`. Changes to cached community data may also need `\App\Support\CommunityCache::forgetAll();`.

`.env` changes are separate: use `php artisan config:cache` in production or `php artisan config:clear` during development. Reload long-running application processes after deployment or configuration changes.

## Keeping the GitHub wiki current

Submit edits here through a pull request against `dev`. The files retain the wiki's page names so a maintainer can publish the reviewed pages to the separate [GitHub wiki](https://github.com/DennisObject/atomcms/wiki) after merge, adapting repository-relative links where necessary. A pull request in this repository does not update the public wiki automatically. Use these versioned pages when the public wiki differs.
