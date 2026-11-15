# Pass 55 — Booking advisory-lock transaction invariants

Baseline: Pass 54 verified master. All 21983 original files retained.

## Implemented
- Advisory lock manager rejects empty key sets, preventing unprotected booking writes.
- Advisory acquisition and release are prohibited while PDO reports an active transaction.
- Lock key types are validated before sorting; malformed mixed-type keys cannot cause partial acquisition.
- Strict MySQL GET_LOCK/RELEASE_LOCK result checks accept only the integer/string 1 returned by PDO; other values fail closed.
- Partial-lock acquisition cleanup and uncertain-release retention are preserved.
- Legacy staff and booking IDs are limited to supported signed 32-bit range in the relevant key paths.
- Four historical Pass 47 lock fixtures now respect pre-transaction lock acquisition.

## Verification
- 1,039/1,039 executable PHP regression scripts passed (full recursive test discovery excluding shared fixture/bootstrap files).
- 24 new focused pass55 tests passed.
- PHP lint passed for every new or changed PHP file.
- ZIP CRC integrity and preservation tests passed.
- Original private/cleanto source modifications: 0.
- Real MySQL appointments executed: 0 (no pdo_mysql/server or Docker in execution environment).
- Public booking writes remain disabled. Production readiness not established.

## Measured source changes (before report/manifest)
- Added: 25 files
- Modified: 8 files
- Removed: 0 files
- Original previous entries preserved by SHA-256: 21975 unchanged; 8 intentionally modified.

## Next milestone
Execute integration/mysql/lifecycle against an isolated Docker-capable MariaDB host and establish a real booking create/readback/conflict/rollback/cancel/reschedule result.
