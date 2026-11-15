# PlusAgency + Cleanto — Pass 95 report

Baseline: Pass 94 merged cumulative recovery candidate.

## Scope
- Added same-origin CSRF-protected booking JSON transport, included before the public widget.
- Connected customer checkout validation to server-gated submission, without allowing customer supplied release authority.
- Added in-flight request cancellation, response proof checks and fail-closed confirmation rendering.
- Added feature-flagged submission UI data from server configuration and a bound executor.
- Retained previous booking engine, website builder and 15 registered designs.

## Verification
- Node Pass 95 transport: 22 assertions passed.
- Pass 87 slot intent: 14 passed; Pass 88 checkout: 12 passed.
- Pass 66 integration contract: 11 passed.
- PHP syntax and JS syntax of changed files pass.
- Real MariaDB transactions: NOT EXECUTED (PDO mysql driver/server unavailable).
- Browser E2E: NOT EXECUTED.
- Missing Pass 58 provenance: UNRESOLVED.
- Public booking writes remain default-off in booking_internal configuration.

## Release status
RECOVERY CANDIDATE ONLY. Actual database persistence, contention, pricing, tenant isolation, webhook settlement and browser checkout acceptance are NOT certified. Source tests do not constitute live booking evidence.
