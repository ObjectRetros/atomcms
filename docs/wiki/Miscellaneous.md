# Other hotel settings

Edit these values through Housekeeping's CMS settings. Themes and emulator capabilities determine which settings appear in the resulting experience.

| Setting | Purpose |
| --- | --- |
| `start_credits`, `start_duckets`, `start_diamonds` | Initial currency granted when a user registers. |
| `give_hc_on_register` | Set to `1` to grant Habbo Club on registration. |
| `hc_on_register_duration` | Duration in **seconds**, added to the registration time; `2592000` grants 30 days. This is not a Unix expiry timestamp. |
| `cms_color_mode` | Initial color mode, `light` or `dark`; a saved browser preference can take precedence. |
| `cms_logo` | URL/path of the hotel logo. |
| `cms_header` | Header artwork used by the Atom theme. |
| `cms_me_backdrop` | Backdrop used on the Atom theme's user page. |

Each `website_settings` row has a comment explaining its purpose. See [changing settings](README.md#changing-settings) when working directly with the database.
