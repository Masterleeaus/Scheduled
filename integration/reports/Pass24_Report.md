# Pass 24 — Persisted booking evidence / timezone hardening

## Implementation
- Preserve original Cleanto and PlusAgency code.
- Reject ambiguous and nonexistent persisted local appointment times, including DST clock folds and gaps.
- Calculate legacy maximum-duration SQL lookback using exact elapsed time plus a 48-hour civil-time cushion.
- Require independent database scan count to be present and bounded in winning-booking evidence.
- Route the existing persisted interval, SQL bounds, acceptance evidence and gate through the hardened checks.
- Update one pre-existing regression assertion for the stronger evidence contract.

## Verification
- 394/394 PHP integration regression scripts passed using full Pass 23 source plus the edits.
- Original Cleanto vendor source changed: 0 files.
- Live MySQL booking execution: NOT run (PHP lacks pdo_mysql; no MySQL server).
- Public booking writes: still disabled; production readiness: NO.
- 33 substantive source and test files changed: 28 added and 5 modified.
- This pass makes no claim of a completed live booking.

## Next
- Use a disposable MySQL environment to execute the prebuilt create/concurrent booking/cancel/reschedule harness; correct any schema or persistence defects exposed.
