# Account limits

Set `max_accounts_per_ip` through Housekeeping's CMS settings to the maximum number of accounts that can register from one IP address. Shared households and networks count toward the same limit.

Configure [trusted proxies](1.-Installing-Atom-CMS.md#production-configuration) before relying on this control. Test both an allowed registration and one at the limit using a disposable test database. See [registration controls](Registration-limiting.md) for disabling registration or requiring beta codes.
