# PlusAgency + Cleanto Pass 67 — Public intent issuance security

Base: Pass 66 recovery candidate. Pass 58 remains missing. No live booking writes enabled.

## Concrete fixes
- Public preview intent issuer now resolves the tenant and checks all four chosen IDs against the current, host-scoped catalogue.
- Validates service → pricing method → unit relationships and unique staff membership; rejects duplicates and cross-tenant identifiers.
- Rechecks selected cleaner/day/duration against current sidecar availability and rejects stale or absent slots.
- Requires strictly formatted timezone-offset availability timestamps; blocks issue for past appointments.
- Bounds token length; tightens expiry/issued-at validation and adds private no-store response headers.
- Preserves server-side preview-only status. Neither client-side price nor browser-controlled IDs are accepted as a binding booking or quote.

## Tests actually executed
- PHP syntax of modified and new PHP files: PASS.
- Pass 67 security regression tests: 23/23 PASS.
- Pass 66 PHP view/route contract checks: 10/10 PASS.
- MariaDB booking transaction: NOT RUN (`pdo_mysql` unavailable). Browser checkout: NOT RUN.
- Pass 58: NOT RECOVERED. Never treat archive as a certified master.

## Important limitations
- Client booking flow remains availability-only; signed preview intent is not booking confirmation.
- Price calculation, payment settlement, real concurrency proof, server release gate and end-to-end browser test remain pending.
- A live environment must additionally verify tenant domain/route behavior and server availability after configuration.
- This pass repairs a specific backend security boundary; it does not meet the planned full checkout acceptance gate.
