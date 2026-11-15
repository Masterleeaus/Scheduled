# PlusAgency + Cleanto — PASS 100: Checkout Failure Semantics

Baseline: Pass 99 cumulative recovery candidate. The changes are directly applied to the existing Laravel checkout and submission controllers.

## Production repairs
1. The reservation-submit controller now returns HTTP 422 only for malformed/ineligible customer input or signed selections, and returns HTTP 503 for runtime/backend/persistence exceptions. This avoids misclassifying database, locking and gateway errors as customer mistakes.
2. The public validation controller likewise distinguishes invalid user data (422) from unexpected availability/catalogue infrastructure errors (503).
3. All failure responses remain generic and privacy-preserving. Successful new bookings remain 201 and confirmed idempotent replays remain 200. No new booking-payment authority is enabled.

## Evidence
- 10 targeted response-contract assertions: PASS (source-boundary checks, not HTTP integration).
- Existing Pass 98 replay response assertions: PASS (12 assertions, DB not exercised).
- All modified/new PHP files: php -l PASS.
- Both ZIPs: archive integrity test PASS.
- Real MariaDB booking commit, independent SQL readback and browser checkout: NOT EXECUTED; this environment has PDO but no pdo_mysql/MariaDB server.
- Pass 58 historical source gap: UNRESOLVED.
- Pass 99 historical phone-boundary regression was not reproducible in a selectively extracted test tree due to missing companion classes. No failure inferred in the complete app.

## Release gates
Blocked: Live database transaction, atomic order+booking commit, real concurrency, payment settlement, tenanted production smoke test, browser E2E and Pass 58 provenance. Public writes must remain off until verified. This is an integration repair, not a production release.
