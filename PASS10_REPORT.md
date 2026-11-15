# PASS 10 — MariaDB tenant diagnostic and booking write safety

**Status:** source implemented; live MariaDB acceptance BLOCKED.

## Changes
Added a read-only Artisan command `booking:verify-tenants` to examine actual trusted Cleanto tenant database connections and validate required tables/columns without touching bookings. Added strict native prepared-statement and tenant DB identity checks, machine-readable JSON evidence, regression tests and a real-DB acceptance runbook. **No public booking commit flag was enabled. No booking writes or Cleanto application source were changed.**

## Verification
- New PHP syntax checks: 5 passed.
- New tests: 2 passed.
- Existing booking standalone tests: 7/10 passed.
- Existing failures: pass89_checkout_commit_compatibility_test.php, pass92_checkout_native_handoff_test.php, pass100_response_contract_test.php.
- pdo_mysql: not installed. MariaDB runtime: unavailable. Live booking, concurrency, rollback and settlement tests: **not run**.

## Roadmap coverage
- Pass 10 attempted; **8 planned passes remain**.
- Website templates registered: **15/15**, partial editing in source **15/15**, fully browser verified **0/15**.
- Landing templates registered: **10/10**, partial editing in source **10/10**, fully browser verified **0/10**.
- Templates awaiting registration: **0**. Templates awaiting complete universal editor + browser acceptance: **25/25**.

## Acceptance gate
**NOT PASSED:** a read-only checker is not proof of a working booking transaction. Must run the operational checklist against real MariaDB and record independent persisted readback, concurrent booking, tenant isolation and rollback evidence.
