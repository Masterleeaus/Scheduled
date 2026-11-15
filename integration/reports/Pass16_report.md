# Pass 16: Database release barriers and executable MySQL checks

## Verified changes

- Enforced MySQL PDO and non-emulated statements at booking mutation boundaries.
- Bound booking writes to the authenticated tenant's selected database; no tenant value is accepted from customer payloads.
- Added read-only INFORMATION_SCHEMA audit of required Cleanto and integration columns, uniqueness constraints and InnoDB storage.
- Corrected booking replay to reject invalid/missing completed booking IDs.
- Added a separate release predicate that refuses unverified schema or booking capabilities; existing release gate preserved.
- Updated creation, cancellation, rescheduling and owner write dispatcher paths to use the database connection guard.
- Added explicitly disposable MySQL smoke suite for transaction commit/rollback, unique idempotency and advisory lock acquisition/release, plus a read-only schema audit runner.
- Added 20 executable regressions for tenant matching, schema conditions, indexes, release gates and replay.

## Measurement

- Added files before report: 33; modified files: 4; deleted: 0.
- Changed substantive PHP files: 36.
- All cumulative PHP test scripts: 220/220 passed.
- Changed PHP syntax: 36/36 passed.
- MySQL smoke: skipped (exit 77, pdo_mysql unavailable).
- Original Cleanto files altered: 0.

## Important limitations

No live MySQL booking create, cancellation, reschedule, pricing parity or concurrent double-booking test occurred in this environment. Booking mutations remain feature-gated and must not be enabled based solely on these results. Docker is not installed; real MySQL testing requires external runtime with pdo_mysql and a disposable database.

The test runner only checks selected transaction primitives, not the full Cleanto lifecycle. Its `schema_audit.php` can be executed against a disposable Cleanto test database to identify schema differences. Never run test DDL on production.
