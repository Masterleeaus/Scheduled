# Pass 21 — real two-process concurrency test

Run ONLY on a separately provisioned, seeded, disposable MySQL database with the original Cleanto schema and integration ledger migrations. This intentionally commits test data. Do not use a production database or credentials.

Required configuration: same `MysqlTestConfig` env keys and `BOOKING_LIFECYCLE_FIXTURE` used by `run_lifecycle.php`, plus `BOOKING_LIFECYCLE_ACK=I_UNDERSTAND_THIS_WRITES_TEST_DATA` and `BOOKING_ACCEPT_TEST_ONLY=YES_DISPOSABLE_ONLY`.

Run `php integration/mysql/lifecycle/run_concurrency.php`. The script will SKIP (exit code 77) when `pdo_mysql` is absent. It launches two separate PHP processes and therefore separate DB sessions, releases them through a barrier, and calls the real `BookingWriteCoordinator` for both. Exactly one must commit, and the other must fail for an actual scheduling conflict. Infrastructure/credential errors are failures, never valid conflicts.

This is test-only admission, not proof of pricing parity or authorization for live customer writes. It requires disposable configuration, and reports `production_booking_writes_authorized=false`. Clean the disposable DB after use. No production feature gate is altered.
