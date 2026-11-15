# Pass 63 — MariaDB acceptance gate checkpoint (BLOCKED)

Baseline: Pass 52 full master overlaid with available Pass 53–57 and Pass 59–62 deltas. Pass 58 is not present and its unique edits have not been recovered. **Not a certified cumulative Pass 62 master.**

## Executed checks
- Complete extracted archive, real PHP integration regression scripts: **1197/1197 passed** (excluding bootstrap/support helpers).
- PHP syntax checks for booking sidecar, booking app and MySQL acceptance source: **545/545 passed**.
- MariaDB infrastructure acceptance: **SKIPPED (exit 77)** because `pdo_mysql` was unavailable.
- Actual committed MariaDB booking, readback, rollback, concurrency and checkout: **NOT TESTED**.
- Booking public-write release gate remains **CLOSED**.
- Original PlusAgency/Cleanto application source was not modified by this checkpoint.

## New execution utility
`integration/mysql/pass63_preflight.sh` is a fail-closed orchestration entrypoint. It rejects missing `pdo_mysql`, unsafe/absent dedicated disposable test settings, missing acknowledgement or fixture; runs the existing schema+rollback+advisory-lock probe and then the real booking lifecycle on valid disposable inputs. It does not modify the booking application, grant write permissions, or claim production verification.

## Remaining blockers
1. Locate Pass 58 delta or certified Pass 58+ master; reconcile its source changes with this archive.
2. Provide an isolated MariaDB host, `pdo_mysql`, actual Cleanto schema and valid disposable fixtures.
3. Execute transaction/readback, two-session concurrency and browser tests; capture logs and independent SQL proof before calling this milestone complete.
