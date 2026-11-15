# Pass 28 — Cleanto service configuration evidence

## Implemented
- Added an owner-only, read-only configuration readiness assessment tied to the verified tenant Cleanto catalogue.
- Displays per-mapped-service method and unit counts, missing services, stale selections and no-staff warnings.
- Provides specific remediation guidance; avoids claiming payment, staff eligibility, booking writes or durations are verified by the catalogue.
- Fixed a hardcoded "Loading cleaners" label in the vertical booking widget.

## Important current capability boundary
The actual booking sidecar's allowlist supports `booking_catalogue`, availability and owner booking operations, but DOES NOT support creating services, modifying pricing methods or assigning staff. Do not claim this pass implements those write operations. They require a designed, validated, transactional native Cleanto owner-write interface and a live MariaDB acceptance environment.

## Validation
- Executable PHP structural service-readiness test: 10 assertions.
- PHP syntax checks for changed PHP sources.
- Source-path and ZIP integrity checks.
- Live database and browser acceptance still untested: `pdo_mysql` unavailable in the test environment.

## Project status
50 verticals; 19 website templates; 10 landing templates. 0/29 fully browser-certified.
