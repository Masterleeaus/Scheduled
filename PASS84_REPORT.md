# Pass 84 — Post-lock future-time validation

Baseline: Pass 83 cumulative recovery candidate. Pass 58 provenance still unresolved.

## Application repairs
- `BookingWriteCoordinator.php`: recheck appointment future time inside the transaction after the idempotency replay branch and immediately after conflict/availability verification, before creating order and appointment.
- `BookingRescheduleCoordinator.php`: recheck future time after booking and staff lock waits / transaction row read, and after final availability evaluation but before mutation ledger claim and update.
- Replays remain eligible for their existing proof-based handling, without newly applying a future restriction to already-committed bookings.

## Test evidence
- Pass 84 targeted PHP guard and source-order test: PASS (14 assertions reported).
- Existing pass15_reschedule_future, pass17_future_reject, pass17_future_accept: PASS.
- Focused additional historical PHP test sweep using partial code extraction: 36 passed, 12 failed due to missing dependency code from partial extraction; these failures have not been diagnosed against a full unpacked application and are NOT accepted as application regressions.
- Changed PHP files: php -l passed.
- ZIP CRC integrity verified after packaging.

## Not tested / blockers
- No MariaDB server or PHP pdo_mysql driver available. Real booking commit, rollback, and two-customer simultaneous booking tests NOT run.
- Public checkout, Stripe webhook delivery and website publishing NOT browser-tested.
- Missing Pass 58 source remains unverified, so cumulative archive must not be promoted to certified master.
- This is a narrow production-code safety repair, not end-to-end booking completion.
