# PASS 52 — Post-mutation Cleanto persistence proof and safe replay

Baseline: Pass 51 cumulative master.

## Core implementation
- Adds an in-transaction booking state proof for cancellation and rescheduling, rereading Cleanto's booking row before completing the idempotency ledger.
- Verifies persisted cancellation target (CC or CS), reschedule status (RS), local start timestamp and canonical staff IDs.
- Rejects replay if its stored booking ID does not match the request, or the current stored mutation state no longer matches. Replay reads each booking record only once.
- Extends rescheduling's concurrent-change snapshot to include start time and order ID, not only status and staff.
- Bounds cancellation and mutation IDs to the legacy signed 32-bit limit, and requires a transaction in the cancellation writer.
- Corrects an early status-writer fake-PDO test to start the required transaction.
- Original imported Cleanto source files are not modified; public booking mutation remains disabled.

## Verification
- Entire recursively discovered integration PHP suite (excluding bootstrap files): **960/960 passed**, including **30/30** new Pass 52 focused scripts.
- Source and test files changed before manifest/report: **32 added, 7 modified, 0 deleted**.
- Identical prior entries preserved: **21879**; original Cleanto files modified: **0**.
- MySQL live transactions: **0**; driver, database server and Docker unavailable in this session. No production-ready or live booking claim.

## Reproduce
`find integration/tests -type f -name '*.php' ! -name bootstrap.php` — execute each script using PHP CLI.

The checksum manifest is `PASS52_MANIFEST.json`.
