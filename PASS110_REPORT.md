# PlusAgency + Cleanto — Pass 110

Baseline: Pass 109 full cumulative recovery candidate.

## Verified change
A new staging input preflight checks native+sidecar table coverage, seed write allowlist, disposable tenant identity, business-hours scheduling, future appointment dates, non-overlap between booking tests, and optional SHA-256 provenance. The existing Pass 101 Docker launcher now invokes this preflight before containers are created.

## Verification
- 11/11 local input preflight tests passed against the original Pass 108 files.
- Bash syntax passed for changed launcher.
- Python syntax passed for new scripts.
- `apt-get update` timed out (12 seconds); no Docker, MariaDB server or PHP MySQL PDO extension available here. No database import, booking commit, concurrency transaction or browser checkout was performed.
- The imported native SQL is still a review candidate, with historical migrations unverified. Missing Pass 58 provenance remains unresolved.

## Release status
BLOCKED. No production booking authorization was created or enabled. This is a staging-input hardening pass, not a booking integration acceptance.

## Next exact execution
On a Docker-capable staging host, review schema/migrations and synthetic fixtures, export PA101_* variables, include PA101_REVIEW_MANIFEST when using the unchanged Pass 108 bundle, then execute integration/staging/pass101/run.sh. Independent SQL readback and real browser testing remain mandatory.
