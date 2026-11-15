# Pass 18 — Database acceptance infrastructure

- Base: Pass 17 verified cumulative master.
- Added 12 source-level PHP components for test-only MySQL connection guards, verified schema inventory, InnoDB engine verification, advisory-lock checks, rollback probes, and concurrency-result evaluation.
- Added a CLI-only acceptance runner and isolated execution documentation.
- Added 16 executable PHP regression tests (all passed).
- Cumulative integration suite: **261/261 passed**, executing from the full master contents.
- New PHP lint: **30/30 passed**.
- Original Cleanto files modified: **0**.
- Real MySQL tests: **NOT RUN** (`pdo_mysql` not installed; no database server verified). Runner returns exit 77 when unavailable.
- Public booking writes remain gated; booking creation, cancellation and rescheduling have NOT been proven functional.
- Accepting a schema/lock/rollback probe does not constitute real booking-concurrency acceptance.

To complete the genuine lifecycle test, install `pdo_mysql` and provision a disposable database with the reviewed Cleanto SQL and tenant-safe migrations. Run `php integration/mysql/run_acceptance.php`. A separate two-process same-cleaner booking test and actual create/cancel/reschedule parity remain required.
