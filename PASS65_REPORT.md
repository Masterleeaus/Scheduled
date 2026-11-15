# PlusAgency + Cleanto — Pass 65 booking lock contention repair

Baseline: Pass 64 recovery candidate. Pass 58 remains absent. This cumulative is not a certified Master.

## Repaired behavior
BookingLockManager previously used the full lock timeout separately for every staff key (up to 12 keys). During simultaneous bookings, a multi-staff request could wait roughly the timeout multiplied by number of keys. It now uses one monotonic deadline across the entire set; subsequent GET_LOCK requests receive the remaining fractional seconds. An explicit zero timeout remains a non-blocking attempt. Existing acquisition failure cleanup remains fail closed. Every request is still serialized on the same database-scoped staff advisory-lock keys.

## Verified
- Six new executable regression scripts under integration/tests/pass65: shared budget, contention cleanup, SQL failure cleanup, zero-timeout semantics, three-lock total budget, malformed-key validation.
- 6/6 new scripts passed with fake PDO/statement doubles (not a live MariaDB test).
- 4/4 selected existing Pass 55 lock regression scripts passed.
- PHP lint on modified manager passed.
- ZIP integrity and SHA256 evidence generated at archive build.

## Not verified / blockers
- Missing Pass 58 from reconstruction lineage.
- pdo_mysql and MariaDB server unavailable in this environment; no genuine concurrent booking transaction, database commit, rollback, order readback, or isolation proof.
- No browser E2E performed.
- PHP MySQL GET_LOCK accepts fractional timeout in intended MySQL/MariaDB versions, but requires verification against target DB server.

## Next
1. Recover Pass 58; compare source/hashes and reconcile.
2. Run Pass 64 acceptance + pass65 real two-worker contention on a disposable MariaDB instance with pdo_mysql.
3. Capture SQL readback and prove exactly one booking winner, proper rollback and idempotency under load.
4. Only then proceed to public booking flow and payments.
