# PlusAgency + Cleanto — Pass 70 lock-budget continuity repair

## Baseline and scope
Source: Pass 69 cumulative recovery candidate. Pass 58 is still unaccounted for; this archive remains a recovery candidate. This pass fixes a specific issue in the existing rescheduling workflow: the coordinator acquired a booking lock, performed a record read, then requested staff locks with a fresh timeout. The two phases could exceed the nominal 5-second acquisition budget.

## Implementation
- BookingLockManager::acquire accepts an optional absolute monotonic deadline. Its own timeout is still enforced, so the new parameter can only shorten a lock wait, never extend one.
- BookingRescheduleCoordinator captures a single monotonic deadline before its first lock and propagates it to both lock calls.
- Existing failure cleanup and finally release paths are preserved.
- Ten PHP regression scripts exercise the shared deadline, expired deadlines, cap, nonblocking behavior, partial cleanup, reused locks and coordinator source contract.

## Scope limits
- This is a concurrency safety fix, **not** a completed scheduling GUI, payment integration, or MariaDB acceptance.
- The reschedule operation remains write-gated. There is no proof of a database commit and no real dual-customer browser test.
- Pass 58 cannot be reconstructed from the available archive alone.

## Measured verification
- PHP lint: 13 passed.
- New tests: 10 passed.
- Prior Pass 65 regression tests: 6 passed.
- Live MariaDB: unavailable (pdo_mysql not installed); not run.
