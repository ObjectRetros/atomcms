# Registration controls

## Disable registration

Set `disable_registration=1` through Housekeeping's CMS settings to reject new registrations. Set it back to `0` to reopen registration.

## Beta codes

Leave registration enabled and set `requires_beta_code=1` to require invitations. Create a row in **`website_beta_codes`**, putting a unique, hard-to-guess invitation in **`code`** and leaving **`user_id` null**. The code can be redeemed once; successful registration records the new user's ID in `user_id`.

Give each invitee a separate code. An already assigned `user_id` marks the code as used. See [account limits](Account-limitation.md) for the separate per-IP restriction.
