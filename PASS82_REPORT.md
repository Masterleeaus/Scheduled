# PlusAgency + Cleanto Pass 82 — concurrency barrier liveness repair

Baseline: Pass 81 cumulative recovery candidate; missing Pass 58 still unresolved.

## Source changes
- `ConcurrencySupervisor.php`: validate distinct, usable process/pipes; check both live workers *before* accepting ready markers and again immediately before release.
- `ConcurrencyAcceptanceRunner.php`: pass the genuine launched worker handles into the release gate, instead of releasing on marker evidence alone.
- `pass82_barrier_runtime.php`: five subprocess scenarios (dead workers with stale markers, live release and duplicate release rejection, duplicate handle rejection, corrupt marker timeout, and dead-on-release).

## Evidence
- New Pass 82 tests: five real PHP subprocess scenarios PASSED.
- Existing Pass 81 subprocess tests: five scenarios PASSED.
- Seven existing barrier-release tests: PASSED.
- PHP syntax for the three changed/added source/test files: PASSED.
- No MariaDB server or `pdo_mysql` present: real booking commit, order readback, rollback, same-slot race, payment and browser acceptance NOT executed.
- Pass 58 historical delta still missing, so this is a recovery candidate, not a certified release.

## Release gates
- Public booking and payment confirmation must remain disabled until disposable MariaDB acceptance, tenant isolation and browser checkout verification pass.
- On a MariaDB-capable host, execute the existing `integration/mysql/pass64/run_acceptance.sh` with approved disposable test fixtures and consent flags.
