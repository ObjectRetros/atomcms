# Error logging

Keep `APP_DEBUG=false` on a public hotel. Read errors through server access or your configured log service; do not enable debug output for visitors. See [Laravel's debug-mode guidance](https://laravel.com/docs/13.x/deployment#debug-mode).

By default, `LOG_CHANNEL=stack` writes to `storage/logs/laravel.log`. `LOG_CHANNEL=daily` rotates files in that directory with the configured retention. Container deployments can use `LOG_CHANNEL=stderr`. Rebuild configuration after changing log settings.

There is no bundled `/log-viewer` route in this version. Housekeeping activity logs record administrative activity and are separate from application exception logs. If Laravel cannot write its log, inspect the PHP/web server logs and the write permissions on `storage/` and `bootstrap/cache/`.

Reproduce failures in a private development or staging environment when detailed debug output is needed. Remove credentials, tokens, personal data and request payloads before sharing logs in an issue or chat. See [Laravel logging](https://laravel.com/docs/13.x/logging) for channel configuration.
