# PASS 51 — Booking mutation and ledger fail-closed execution

Base: Pass 50 cumulative archive.

## Changes
- The idempotency request ledger now requires an active transaction, rejects SQL failures, checks claimed/completed affected-row counts, and detects duplicate lookup rows.
- The reschedule order reader verifies one locked duration row and rejects shared orders with more than one booking.
- The reschedule snapshot reader rejects failed queries, mismatched identifiers and duplicate rows.
- The reschedule coordinator now enforces its single-booking-per-order guard before mutating staff or times.
- The duration and reschedule writers reject failed SQL operations; the duration writer verifies exactly one affected row.
- Cancellation acquires the full tenant-qualified booking lock list, rather than selecting a single element manually.
- Customer booking ownership requires an active transaction, valid bounded identifiers and a matching persisted booking ID.
- Legacy mutation record parsing rejects invalid calendar dates and out-of-range identifiers.
- Updated early fake-PDO ledger test to reflect transaction and single-use fetch semantics.
- Original Cleanto files are unchanged.

## Verification
- Full recursive integration PHP regression scripts (excluding bootstrap files): **930/930 passed**.
- New focused Pass 51 tests: **25/25 passed**.
- Source and test file changes before manifest/report: **37** (26 new, 11 modified); **0 deleted**.
- Previous archive files preserved: **21847** unchanged and **11** updated.
- Original Cleanto modifications: **0**.
- Live MySQL transactions: **0**, not available in this environment.
- Public booking confirmations remain disabled. Not production ready.

## Reproduce
`find integration/tests -type f -name '*.php' ! -name bootstrap.php` (run each through PHP separately).

## Manifest
`PASS51_MANIFEST.json` SHA-256 records each changed file.
