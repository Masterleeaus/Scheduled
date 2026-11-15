# Pass 21 — Concurrency acceptance and test-only admission fix

- Based on verified Pass 20 cumulative ZIP.
- 40 files added, 1 files modified, 0 files removed before report/manifest.
- 40 substantive PHP files created or changed (new implementations, executable tests, and lifecycle fix).
- Entire cumulative PHP test suite: **328 scripts passed, 0 failed**.
- Newly introduced tests: **24/24 passed**.
- New concurrency runner: correctly skipped with exit code **77** because this runtime has no `pdo_mysql`.
- Live database appointments created: **0**; live two-process races executed: **0**.
- Original Cleanto source: **unchanged**.
- Production ready: **No**.

## Functional changes

1. Replace unconditional verified-flag assignment in `LifecycleRunner` with explicit disposable-only, acknowledged acceptance settings.
2. Introduce distinct-process concurrency worker orchestration with two separately opened MySQL sessions.
3. Add synchronization barrier and unique idempotency tokens per worker while preserving the same appointment/staff request.
4. Enforce exactly one committed booking and one conflict-related rejection; unrelated failures fail the test.
5. Protect temporary files, bound readiness waits, validate process outcomes and redact errors.
6. Keep production feature flags gated, and label test override as unverified acceptance-only.

## Usage

Read `integration/mysql/lifecycle/CONCURRENCY_README.md`. Provision a **disposable** MySQL test database with Cleanto schema and required integration migrations, configure the same environment as Pass 20 lifecycle runner, plus `BOOKING_ACCEPT_TEST_ONLY=YES_DISPOSABLE_ONLY`. Execute `php integration/mysql/lifecycle/run_concurrency.php`. The runner must not be used on a production database.

## Limitations

This is executable test infrastructure, not evidence of completed concurrency testing. Both booking mutation paths remain unavailable to public customers. Even a future passed two-process test is only one release gate: pricing parity, cross-tenant isolation, side-effect cleanup and lifecycle tests must also pass.
