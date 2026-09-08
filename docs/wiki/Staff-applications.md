# Staff and team applications

Authorized staff can create positions through **Housekeeping → Hotel → Open Positions**. Choose a rank or team, enter the position description, and optionally set start/end dates. Rank options come from the active emulator; team positions reference Atom's teams.

A missing start date allows applications immediately. A missing end date leaves the position open; a supplied end date closes it at that time. Dates use the application's timezone (`UTC` by default).

Rank applications are managed through the Staff Applications resource; team applications use the Team Applications resource. Access depends on the user's housekeeping permissions.

For integrations, positions are stored in `website_open_positions`: `position_kind` is `rank` or `team`, with the matching `permission_id` or `team_id`, plus `description`, `apply_from` and `apply_to`. Prefer Housekeeping to direct table edits so validation and cache invalidation run.
