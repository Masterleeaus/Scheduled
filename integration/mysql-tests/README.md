# Pass 16 — Isolated MySQL runtime gate

Run only on a **disposable** test database. The suite does not import real customer data. It never flips booking feature flags.

Requirements: PHP 8.2+ with `pdo_mysql`, MariaDB/MySQL with permission to create a **dedicated test database**, existing Cleanto schema installed separately for the full booking audit.

Set `BOOKING_TEST_DSN` to a connection selecting a disposable database whose name starts with `titan_booking_test_`; set `BOOKING_TEST_USER` and `BOOKING_TEST_PASSWORD`. Invoke `php integration/mysql-tests/run.php`. The test runner refuses any other database name, and exits **77 = environment unavailable**, **1 = test failed**, **0 = passed**. It creates only two test-owned tables (prefix `titan_test16_`) and removes them afterward. It verifies PDO transactions, rollbacks, unique request claims, and connection-scoped named locks. More extensive Cleanto schema parity is reported separately by `schema_audit.php` (read-only). Never run on production.
