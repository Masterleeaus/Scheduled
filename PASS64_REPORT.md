# Pass 64 — isolated booking acceptance orchestration

**Baseline:** Pass 63 recovery candidate (Pass 52 full archive with known Pass 53–57 and 59–62 overlays). Pass 58 remains absent and this remains a recovery candidate, not a certified master.

## Implemented
- Added `integration/mysql/pass64/preflight.php`, running both full Cleanto booking fixture validators, disposable MariaDB host/database policy, explicit test-write consent and test-only override **before any SQL probe**.
- Added independent lifecycle and concurrency fixture file requirements and fail-closed guard against using the same file for both tests.
- Added strict timezone-aware window separation to avoid the original acceptance procedure contaminating the concurrency experiment with an earlier lifecycle booking.
- Added `integration/mysql/pass64/run_acceptance.sh` executing preflight, driver check, infrastructure probe, competing-customer concurrency evidence, then lifecycle evidence. Concurrency command receives its own fixture while the lifecycle command receives the original.
- Added reusable PHP validation classes and 14-case CLI regression test.

## Tests executed
- `php integration/tests/pass64/check_gates.php`: **14/14 passed**.
- Full command with two fixture JSONs and disposable settings: preflight **passed**; PHP MySQL driver check correctly **SKIPPED (exit 77)**. No database connection or booking was attempted.
- PHP syntax verification of all added PHP source: **passed**.
- Cumulative ZIP CRC: **passed**.

## Remaining release blockers
1. Recover original Pass 58 unique changes or otherwise establish equivalent source reconciliation.
2. Provide a disposable MariaDB with pdo_mysql, schema and populated fixture; run real independently read-back committed booking, rollback and concurrency.
3. Browser-based customer booking and checkout evidence unavailable.
4. No production public booking writes enabled by this pass.

## Scope
Security/acceptance repair with six substantive added source/test files and accompanying report/manifest. No pretense of 20+ edits; unrelated application sources preserved byte-for-byte.
