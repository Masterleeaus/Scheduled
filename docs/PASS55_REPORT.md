# Pass 55 — MariaDB connection-port correctness

## Source
Pass 54 cumulative master, no deletions. MariaDB port was fixed to 3306 in `EnvironmentManager`, while the PDO validation, `mysqli` legacy importer, and first-time installation wizard offered no alternative.

## Implemented
- Introduced `DatabaseEndpoint::port()` to reject invalid ports consistently (range 1–65535, canonical decimal integer).
- Added an explicit database port field (default 3306) and server-side validation.
- Applied the same port to PDO preflight connection, native mysqli legacy SQL import, and persisted `DB_PORT` environment setting.
- Added an executable regression test for accepted/rejected values and all wiring points.

## Limits
- No live MariaDB service or php MySQL extensions in this environment; no end-to-end installation was performed.
- An installation using a Unix socket or provider-specific TLS settings still needs a dedicated configuration workflow.
- Existing licence verification, feature migrations, administrator creation, and booking write protections are unchanged.

## Source of truth
The cumulative archive is Pass 55; Pass 54 is retained unchanged as the rollback baseline.
