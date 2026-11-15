# Pass 52: Fresh-install administrator atomicity

## Verified defect
The first-admin controller previously obtained a MySQL `GET_LOCK` inside `DB::transaction`, then released that named lock in a `finally` block *before the transaction committed*. A concurrent install request could acquire the named lock before the original insert was committed. This is an installer first-admin race.

## Implementation
The controller now acquires the lock before beginning the database transaction, uses the same database connection for the transaction, and only releases the lock in an outer `finally` after the transaction commits or rolls back. Existing account checks and first-admin input validation remain intact; installer session flags are set only after commit.

## Reproducible verification
- `php tests/pass52-first-admin-lock.php`: **12/12 assertions pass** (success, commit-before-unlock ordering, duplicate admin, lock contention, insert error and rollback-after-release ordering).
- `php -l core/vendor/rachidlaasri/laravel-installer/src/Controllers/AdministratorController.php`: pass.
- `php artisan --version`: Laravel Framework 9.52.15 starts without DB.
- `php artisan route:list --json`: 810 routes successfully parsed without DB.
- ZIP CRC integrity checks and preserved-file comparisons: pass.

## Limitations
This environment has neither `pdo_mysql` / `mysqli` nor a MariaDB service. Consequently, real MySQL named-lock scheduling and first-install HTTP form submission remain **unverified**. The in-memory test validates *source control flow*, not actual database concurrency isolation. This archive is **not production-certified**.

## Project metrics
Pass: 52. Registered business verticals: 50. Cleaning website starters: 15. Additional website starters: 4. Landing templates: 10. Templates with partial editing: 29. Fully browser-certified: 0/29. Verified clean MariaDB install: no. Verified booking and Stripe: no.
