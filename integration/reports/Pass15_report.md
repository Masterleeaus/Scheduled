# Pass 15 — Booking mutation integration and owner identity hardening

This pass modifies existing production booking paths rather than expanding placeholder architecture.

## Direct functional changes
- Creation acquires **tenant-qualified staff locks** using the selected database identity.
- Reschedule acquires **tenant-qualified booking and old/new staff locks**; removes legacy cross-tenant key usage.
- Owner mutation fingerprint uses a configured authenticated owner ID rather than fixed ID 1.
- Owner operations fail closed unless `owner_operations_enabled=true` and `authenticated_owner_id` is a positive integer in the server-side tenant registry.
- Every owner write still requires four explicit booking write readiness flags; writes are **not automatically enabled**.
- Creation additionally requires a server-provided verified-pricing settings structure.
- Rescheduling requires a future appointment within a year.
- Payment response is always marked **unpaid** until a separate verified payment capture.
- Removed a spurious unused date calculation in conflict guard.
- Health response now enumerates booking release requirements, not a claim of readiness.

## Verification
- Entire PHP script suite: 200 / 200 passed.
- New Pass 15 executable tests: 20.
- Changed PHP syntax: 34 / 34 passed.
- Original Cleanto files changed: none.
- Live MySQL, concurrent reservations, tenant provisioning, payment capture: NOT verified.
- These API changes require server-side tenant registry configuration before owner operations can be used.

## Critical outstanding items
1. Run MySQL transactional/concurrency tests on disposable database with Cleanto schema.
2. Confirm price parity and availability against original Cleanto routines; do not flip write flags prematurely.
3. Authenticate owner and customer identity end-to-end through Laravel application permissions.
4. Review booking customer foreign-key semantics and legacy Cleanto data before real bookings.
5. Confirm production payment handling and notify workflows; no paid confirmations should be inferred.

## Change manifest
See `integration/reports/Pass15_manifest.json` for full SHA-256 diff, additions, modifications, and deletions.
