# Pass 19: database acceptance and two-session lock verification

> **Legacy MySQL verification only (architecture review: 2026-11-18).** This harness tests inherited source behaviour, not the approved final database design. Scheduled's [root README](../../README.md) and [Central ADR-009](https://github.com/Masterleeaus/Central/blob/main/TITAN_ARCHITECTURE_DECISIONS.md) target one integrated Laravel booking authority and one centrally governed PostgreSQL database. Do not interpret a passing MySQL advisory-lock test as PostgreSQL migration, booking concurrency, or production acceptance evidence.

Use a **disposable** MySQL/MariaDB database with a name beginning `titan_booking_test_` and an isolated account. Configure `BOOKING_TEST_HOST`, `BOOKING_TEST_DATABASE`, `BOOKING_TEST_USER`, and `BOOKING_TEST_PASSWORD`. Run `php integration/mysql/run_acceptance.php`.

The runner checks required schema, storage engine, rollback, session restrictions, and whether two distinct database connections contend for the same advisory lock and successfully release it. No booking inserts or external HTTP requests are performed.

Exit `0` = infrastructure tests passed ONLY; `1` = failed; `77` = skipped because pdo_mysql is unavailable. Even exit 0 **does not** verify creation, pricing parity, simultaneous real bookings, cancellation, or rescheduling; release gates must remain closed. The runner cannot establish that a server called `localhost` is safely isolated; use dedicated database credentials and infrastructure.
