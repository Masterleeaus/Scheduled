# Pass 83 — atomic concurrency acceptance markers

Baseline: Pass 82 recovery candidate (missing historical Pass 58).

## Implemented
- Replace nonexclusive READY and GO file writes with exclusive `fopen('x')` creation, preventing marker replacement during check/write races.
- Reject unsafe marker names and pre-existing symlink markers.
- Preserve idempotent READY registration if a second valid writer wins the race.
- Add real PHP regression cases for overwrite rejection, symlinks, path traversal, idempotency and release tampering.

## Verified locally
- `php integration/tests/pass83_atomic_markers.php`: six cases PASS (and five Pass 81 parent harness scenarios PASS).
- `php integration/tests/pass82_barrier_runtime.php`: five scenarios PASS (and five Pass 81 parent harness scenarios PASS).
- `php -l` modified PHP files: PASS.
- ZIP CRC integrity check: PASS.

## Release blockers
- Real MariaDB booking commit and two-customer conflict test NOT RUN: no MariaDB daemon or PDO MySQL driver present. System package index refresh timed out; no DB was installed.
- Historical Pass 58 remains missing; cumulative ZIP is a recovery candidate, not a certified master.
- Browser booking checkout, payment reconciliation, website publishing and tenant isolation remain unverified.

## Scope
3 modified core acceptance classes, 1 new executable regression script; no booking writes, payment routes or customer-facing functionality enabled.
