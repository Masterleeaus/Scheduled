# PlusAgency + Cleanto — Pass 111 factual evidence report

## Baseline
Pass 110 cumulative recovery archive. All original application paths are preserved; this pass adds a date-relative synthetic acceptance fixture generator, tests and documentation only.

## Code change
- Creates fresh lifecycle and competing-booking windows in Australia/Melbourne, on workdays, using the reviewed Pass 108 fixture identities.
- Preserves the exact Pass 108 native schema and synthetic seed bytes.
- Rejects tampering using the reviewed source SHA-256 manifest, writes a fresh review-only manifest and validates it via the existing Pass 110 input preflight.
- Refuses to overwrite a nonempty destination, and removes partially written files on validation failure.
- Includes examples spanning weekends, year rollover and both Melbourne DST transition periods.

## Executed checks
- Pass 111 fresh fixture assertions: 55/55 passed.
- Pass 110 existing input preflight scenarios: 11/11 passed.
- Python compile check: passed.
- Generated fixture bundle: 36 native tables, 2 booking-ledger CREATEs, 6 approved seed table inserts, four future appointment windows; INPUT_PREFLIGHT_ONLY.
- No live database test: PHP PDO exists but pdo_mysql, MariaDB and Docker are not available in this environment.
- Real booking commit, SQL readback, competing-customer acceptance, browser checkout and payments: NOT EXECUTED.
- Pass 58 cumulative provenance remains unresolved.

## Gate
This is a staging-fixture readiness improvement, NOT a production booking feature or release certification. No customer-facing booking writes were enabled.
