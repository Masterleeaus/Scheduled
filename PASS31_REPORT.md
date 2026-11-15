# PlusAgency + Cleanto Pass 31

Source-level public read-only catalogue through an exact verified host mapping. The new `booking_public_domains` table defaults to pending: a trusted operator must verify domain ownership before setting `status=active` and `verified_at`. No public mutation operations were added.

Changes: 42 substantive PHP/JS/Blade files; 41 new paths and 2 modified paths. PHP regression scripts: 544/544 passed. PHP syntax checked for changed PHP files and JS syntax passed. No MySQL booking transactions or live Laravel HTTP routes were tested because this environment has no MySQL PDO driver/server. Production ready: NO.

Deploy: migrate the new table, configure trusted hosts/proxies, verify ownership out of band before activating a domain, set up tenant instances, configure private signed sidecar, and test against real MySQL and web server. Public catalogue 404s for unknown/unverified hostnames. The widget remains preview only. Original Cleanto source unchanged.
