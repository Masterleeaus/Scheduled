# Pass 46 — Exact Feature Migration Set & Schema Acceptance

## Verified source problem
Pass 45 `InitialFeatureMigrations::files()` accepted any set of at least 18 `2026_*.php` files. If one essential migration were absent and an unexpected one added, the count check could still pass, and an unexpected migration would be executed. Its post-migrate proof inspected only seven tables, although the 18 migrations create 19 tables and alter existing feature tables.

## Implemented
- Pin the full set of 18 known feature migration filenames in an explicit release allowlist. Reject missing or unexpected files rather than accepting a count alone.
- Preserve chronological order and Laravel migration invocation.
- Verify all 19 tables created by these migrations exist.
- Verify critical additional fields created by later alter migrations: booking request fields, UTM attribution, landing design and published snapshots.
- Extend the preceding Pass 45 test fixture for column existence and add executable tests for missing migrations, unexpected migrations, missing secondary tables, and absent columns.

## Verification
- `php tests/pass45-feature-migrations.php`: passed.
- `php tests/pass45-feature-migration-orchestration.php`: passed.
- `php tests/pass46-feature-schema-lock.php`: passed.
- PHP lint on changed helper and test scripts: passed.
- ZIP integrity tests: passed.
- No previous archive paths removed; all unmodified original archive entries preserved.

## Limits
These are executable source-level tests with mocked database facades. No live MariaDB or PDO MySQL driver is available. The importer and Laravel migration DDL have **not** been tested against a real MySQL/MariaDB server; a clean installation and confirmed bookings remain unverified.

## Project status
- 50 business verticals defined
- 19 website starter templates, including 15 cleaning starters
- 10 landing page templates
- 29 templates partially editable; 0 fully browser-certified
- Production-ready/simple installation: **not yet verified**
