# PASS 53 — Create/replay persisted-evidence parity and cancellation replay ownership

Baseline: verified Pass 52 cumulative master.

## Production corrections
- Before completing the legacy booking-creation idempotency record, the coordinator now verifies the just-inserted Cleanto booking and linked customer-order rows within its database transaction. A failed proof rejects the transaction instead of reporting an active booking.
- Creation replays independently read the existing persisted booking and customer-order record under the caller transaction, rejecting cancelled, rescheduled, missing, duplicated, or altered records. Replay compares stored rate with itself rather than repricing a historical booking using potentially changed catalog prices; its other creation fields must match the original command.
- Cancellation replay now rechecks authenticated customer ownership instead of bypassing the ownership check for completed idempotency keys.
- Replay booking IDs are constrained to Cleanto's signed 32-bit key range.
- Existing post-insert proof reader remains active and requires a live transaction.

## Verification
- Entire recursively discovered PHP integration suite: **991/991 passed** (including **31/31** new Pass 53 scenario tests).
- PHP lint passed on every newly created or modified PHP file.
- Source files added or modified: **6 production PHP files** (one created and five modified); **31 executable scenario tests** plus one test bootstrap.
- Existing Pass 52 files removed: **0**. Original imported Cleanto files changed: **0**.
- MySQL booking transactions executed: **0**. PHP PDO MySQL extension, MariaDB server and Docker runtime are unavailable. Public booking writes remain disabled and production readiness is unverified.

## Reproduction
Run every `integration/tests/**/*.php` other than `bootstrap.php` with PHP CLI, working directory set to the application root. Database-backed acceptance must run on a disposable MySQL/MariaDB test host.

See `PASS53_MANIFEST.json` for exact SHA-256 changes.
