# Two-factor authentication

Open **User settings → Two factor**, confirm your current password, and follow the setup flow with a compatible authenticator app. Enter a valid code to confirm enrollment. Save recovery codes privately so you can recover access if the authenticator is unavailable.

To require two-factor authentication for staff, set `force_staff_2fa=1` through Housekeeping's CMS settings. Users whose rank is at least `min_staff_rank` must complete enrollment. The requirement also applies when accessing Housekeeping.

Keep the application key stable across deployments: Laravel encrypts the stored two-factor secrets and recovery codes with it. See [deployment](1.-Installing-Atom-CMS.md#production-configuration) and [Laravel Fortify](https://laravel.com/docs/13.x/fortify#two-factor-authentication).
