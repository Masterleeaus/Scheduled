# Pass 34 — Secure Fresh Installer Identity Bootstrap

## Implemented
- Database installer omits **seeded admins and users INSERT/REPLACE statements** while preserving their native table schema and other fixture data. The seed identities are untrusted and must never be usable on new installs.
- Mandatory first-administrator form after schema import: name, email, password >=14 chars, confirmation, server-side validation, password hashing.
- Creates one native `admins` row, using the pre-existing `role_id = NULL` superuser convention. Does not invent a second account table.
- Session-gated administrator setup, session regeneration and concurrency lock; refuses to overwrite existing accounts.
- `/install/final` cannot mark the site installed without successful administrator bootstrap and an active administrator record.
- Removed legacy raw `.env` editor and unguarded `/install/database` entrypoint from installer routing; disabled the legacy updater by default.
- Keeps the generated APP_KEY rather than rotating it at the end of installation.

## Safety limitations
- This is **not a successful clean install**; the current test environment has no pdo_mysql or running MariaDB.
- The vendor package contains other demo content and untrusted third-party code; review the DB dump and original source licensing before production deployment.
- Native Cleanto activation, migration ordering, provider integrations, email/Stripe and runtime browser tests remain open.
- Installer still relies on the existing legacy license verification and its third-party endpoint; not reworked in this pass.
- `GET_LOCK` expects MariaDB/MySQL and must be tested on staging.
- The original installer imports the SQL dump before writing `.env`; partial failures require a fresh empty database.

## Deployment guidance
Use **isolated staging only**. Before deployment, verify HTTPS, web-root and private folder denial, PHP extension availability, empty dedicated MariaDB database, vendor code provenance, and admin login after bootstrap.

## Scoreboard
Pass 34; 50 business verticals; 19 website and 10 landing templates; 0/29 full browser verified; live booking/payment/installation acceptance pending.
