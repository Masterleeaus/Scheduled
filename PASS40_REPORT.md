# PlusAgency + Cleanto — Pass 40

Pass 40 creates a reproducible **disposable MariaDB/PHP testing environment**, a schema and two-connection advisory-lock preflight, and a guarded CLI for the existing booking lifecycle/concurrency harness. Original Cleanto files remain unchanged.

- Baseline: Pass 39 cumulative master
- New substantive source/test/configuration files: 33
- Modified existing files: 0
- Deleted files: 0
- Previous entries preserved byte-identically: 21510
- Regression scripts executed: 693/693 passed (includes 20 new offline Pass 40 tests)
- New PHP lint: 27/27 passed
- Live MySQL transactions: **not executed** (no pdo_mysql or Docker)
- Production ready: **no**; public booking writes remain disabled

## Running the database harness

Read `integration/mysql/pass40/RUNBOOK.md` before executing on a trusted machine. The database starts empty. Import a reviewed Cleanto test schema and synthetic fixtures into `titan_booking_test`. Set disposable credentials in a local uncommitted environment, then invoke the acceptance commands. Passing source tests is not a substitute for committed-booking and actual contention evidence.
