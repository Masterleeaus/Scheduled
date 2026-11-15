# Pass 81 — Real subprocess concurrency evidence and duplicate-handle guard

Source baseline: Pass 80 cumulative recovery candidate.

## Changes
- Prevent concurrent process grouping from accepting the same PHP process resource twice; this previously risked double cleanup of the same process and pipes.
- Execute real independent PHP child processes for five scenarios: two successes, worker error, shared deadline, oversized stderr and duplicate process resource.

## Evidence
- pass81_worker_runtime.php: 5/5 scenarios PASS with real OS subprocesses, no database or mocked process statuses.
- Existing process guard, output bound and supervisor release regression tests: PASS.
- PHP lint for two changed PHP files: PASS.
- No MariaDB or pdo_mysql available in the execution environment. No appointment/order write, rollback, webhook, payment or web browser test occurred.
- Pass 58 remains missing. This is a recovery candidate, not a certified production master.

## Next blocking step
Run disposable MariaDB transaction and two-client concurrent booking with independently collected SQL readback, then complete browser checkout and publishing acceptance.
