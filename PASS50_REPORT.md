# PASS 50 - Legacy booking persistence proof and mutation readback

Base: Pass 49 cumulative ZIP.

## Implementation
- Imported an atomic persisted-booking proof into the older `BookingAtomicPersist` appointment writer.
- Readback proves the booking and customer-order records before the surrounding redemption transaction commits.
- Unified both locked mutation readers on a fail-closed, single-row `SELECT ... FOR UPDATE` reader.
- Rejects missing, duplicate, incorrect and invalid booking rows, failed SQL execution, and reads outside an active transaction.
- Rejects malformed persisted booking timestamps and out-of-range proof IDs.
- Original Cleanto source was not changed.

## Verification
- Full discovered PHP regression scripts: **905/905 passed**, excluding bootstrap files.
- Added files before manifest/report: **28**; modified: **5**; deleted: **0**.
- Live MySQL transactions: **0** (this environment lacks `pdo_mysql` and MySQL).
- Public booking writes remain disabled; this archive is not production ready.

## Manifest
`PASS50_MANIFEST.json` includes SHA-256 for every changed source/test file.
