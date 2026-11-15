# PlusAgency + Cleanto — Pass 28

## Actual code improvements
- Recursive canonical command fingerprints with stable associative key ordering and intact list ordering.
- Strict persisted booking IDs and legacy durations, rejecting PHP numeric coercion of corrupt strings.
- Fail-closed idempotency ledger state parsing and valid completed booking identity checks.
- Customer-submitted privileged tenant, payment, price and booking-authority fields rejected.
- Customer control-character and actor-ID bounds validation.
- Original Cleanto files remain unchanged.

## Verified checks
- 476/476 executable PHP regression test scripts passed locally (includes 20 new Pass 28 tests).
- PHP lint applied to every changed PHP file.
- Neither MySQL nor pdo_mysql available: no live booking transaction has been demonstrated.
- Customer-facing bookings remain disabled and production readiness unverified.

## Important remaining gaps
- Run actual MySQL appointment creation, contention, cancellation and rescheduling tests.
- Add tenant-verified customer-facing booking endpoint; existing frontend widget remains preview-only.
- Verify Cleanto price parity, payment handling, and recurrence end to end.
