# Username filtering

Set `website_wordfilter_enabled=1` through Housekeeping's CMS settings to filter new usernames. Add blocked terms to `website_wordfilter`; comparisons reject matches contained within a username as well as whole-name matches.

Check expected allowed and blocked names before enabling a broad term, since substring matching can reject innocent names too. Existing usernames are not renamed. This CMS registration filter is separate from the emulator's in-game word filter and remains available independently of that feature.
