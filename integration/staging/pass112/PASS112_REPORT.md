# PlusAgency + Cleanto — Pass 112 — Static transaction schema contract

**Baseline:** Pass 111 recovery candidate (Pass 58 still missing).  
**Status:** Static schema contract validated; **NOT** a certified MariaDB installation, booking transaction or release.

## Verified change

- Integrates `pass112/validate_transaction_schema.py` into the existing Pass 110 staging-input preflight before Docker starts.
- Verifies the actual `CREATE TABLE` definitions for `ct_bookings`, `ct_order_client_info`, `titan_booking_orders`, and `titan_booking_requests` have InnoDB storage engines and required columns/types.
- Checks standalone primary keys (not a composite or unrelated key) for the booking identity and idempotency ledger.
- Parses balanced SQL declarations rather than searching across table boundaries or equating index members with unique keys.
- Rejects duplicate table/column definitions and malformed or missing storage-engine declarations.
- Bundles the authentic Pass 108 schema *review candidate* as a regression fixture. This SQL is NOT an installation-ready or upgraded schema.

## Executed checks

- Pass 112 schema tests: **18 / 18 passed** (including adversarial cases).
- Pass 110 staging preflight regression tests: **11 / 11 passed**.
- Python compilation: **passed** for the changed preflight, checker and test.
- Standalone schema output explicitly sets `mysql_import_executed: false`, `booking_transaction_verified: false`, `release_authorized: false`.
- Pass 112 delta and cumulative archives: CRC check performed during packaging.

## Unresolved gates

- MariaDB server, Docker and `pdo_mysql` remain unavailable in this runtime; no live SQL import, booking persistence, concurrency or browser checkout was executed.
- Pass 58 remains missing from the cumulative ancestry.
- Native Cleanto historical migrations and review-only fixtures require reconciliation with the actual installed database.
- This is a readiness safeguard, not the customer-facing booking completion milestone.

## Next operational action

Run the staged import and acceptance suite on a disposable MariaDB-capable host using reviewed authentic migrations/fixtures; independently inspect booking and order records and prove concurrent-booking isolation. Do not enable production booking or payment writes until those checks pass.
