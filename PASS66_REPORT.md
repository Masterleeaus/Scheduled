# PlusAgency + Cleanto — Pass 66: Public booking availability preview

Date: 2026-11-10

Base: Pass 65 recovery candidate (Pass 58 remains unavailable). This pass does NOT enable bookings or payments.

## Implemented
- Rebuilt dependent service, pricing-method and unit selectors with validated tenant catalogue data.
- Added reusable read-only catalogue/slot validation; rejects writes, mismatched dependencies, duplicate IDs/slots and malformed response values.
- Added cancellation and stale-response protection when the visitor changes selections.
- Form now validates selected staff, date (180-day backend horizon) and duration before contacting availability endpoint.
- Displayed times are accessible `<time>` elements; empty and error states explicitly say no booking is reserved.
- Responsive accessible preview styling, loading state, and consistent script ordering.
- Added 15 Node assertions and 10 PHP view/route contract checks.

## Verification
- Node preview-model tests: 15 passed.
- PHP view/route contract tests: 10 passed against baseline route.
- Node syntax checks: passed. PHP syntax test: passed.
- Live browser E2E: NOT RUN. MariaDB write: NOT RUN (`pdo_mysql` unavailable).
- Pass 58 not recovered; cumulative archive is a recovery candidate, NOT a certified master.

## Key blockers
- Public catalogue can return service/price metadata but availability only uses staff/date/duration; pricing/commit/payment remains intentionally disabled.
- Run real browser tests with tenant domains and host-bound catalogue to validate the operational UI.
- Obtain missing Pass 58; run real MariaDB booking acceptance before enabling write routes.

Changed/new files: 8. No existing source files deleted.
