# Pass 20 — disposable MySQL booking lifecycle acceptance

This harness is **not** a production booking endpoint. It exercises the existing Cleanto-backed PlusAgency coordinators on a seeded **disposable** database. It commits test booking records. Never point it at business data.

Requirements:

1. PHP CLI with `pdo_mysql`, a dedicated disposable MySQL/MariaDB instance and the Cleanto schema populated with known test services, staff schedules and prices.
2. Set `BOOKING_TEST_HOST`, `BOOKING_TEST_DATABASE` (must pass existing disposable-name guard), `BOOKING_TEST_USER`, `BOOKING_TEST_PASSWORD`.
3. Prepare `BOOKING_LIFECYCLE_FIXTURE` as an absolute path to a JSON file (sample structure in fixture.example.json); configure a valid future start, service, staff IDs, verified pricing flags, verified local schedules, and a different available reschedule start.
4. Set `BOOKING_LIFECYCLE_ACK=I_UNDERSTAND_THIS_WRITES_TEST_DATA` explicitly, and run `php integration/mysql/lifecycle/run_lifecycle.php`.
5. Inspect the resulting committed rows; dispose of the entire test database after testing.

The acceptance checks creation, idempotent replay, **sequential** overlapping booking rejection, rescheduling and cancellation. It does **not** prove simultaneous concurrency, tenant isolation, pricing parity, payment processing or production readiness. The resulting evidence report cannot enable production booking writes.

Exit 77 means `pdo_mysql` is absent and **nothing ran**. All errors exit nonzero, without dumping credentials.
