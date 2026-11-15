# Pass 102 — Structured MariaDB booking acceptance evidence gate

## Source baseline
Pass 101 cumulative recovery archive. Only the staging acceptance workflow is changed. No application runtime booking writes were enabled.

## Actual work
- Replace unchecked, scattered acceptance output with private temporary JSON reports captured from existing infrastructure, two-worker concurrency and full booking-lifecycle executables.
- Add a fail-closed verifier requiring infrastructure readiness and true persisted appointment/concurrency evidence, exactly one overlapping winning booking, all lifecycle phases, and the exact disposable DB name.
- Preserve CLI nonzero exits and cleanup on failure; print raw acceptance reports only after structured checks complete.
- Add executable negative regression tests for missing, malformed and contradictory evidence.

## Verification performed
- 22/22 synthetic evidence regression checks passed.
- Existing Pass 101 safety contract: 19/19 passed.
- PHP lint and Bash syntax passed for changed scripts.
- Both ZIP archive CRCs will be verified after packaging.
- **Real MariaDB execution: NOT RUN** — no Docker/MariaDB or PHP pdo_mysql in this environment. No observed booking commit, independent SQL readback, or browser checkout evidence.

## Release blockers
Missing Pass 58 provenance; authentic Cleanto schema and synthetic fixtures not provided; real MariaDB transaction/concurrency, browser E2E, payment reconciliation and SaaS tenant isolation not certified. Public booking writes must remain default-off.
