# PlusAgency + Cleanto — Pass 69 customer booking status boundary

## Scope
Implemented read-only, tenant-scoped customer booking access primitives. This is a source-level security and readback boundary, **not** a deployed self-service customer portal or a working booking confirmation email.

## Implemented
- Signed opaque customer read grant (15-minute lifetime), with purpose-separated HMAC and strict expiry/tenant validation.
- Grant issuer that requires an authoritative matching booking row before granting access.
- Tenant+booking-scoped lookup callback and safe persistence evidence guard.
- Prepared PDO readback adapter with restricted SQL identifiers and exactly-one-row policy. Actual Cleanto schema mapping requires MariaDB integration.
- Customer status projection that only calls `confirmed` when persisted status is `confirmed`; pending, refunded, completed and cancelled do not masquerade as confirmations.
- Private/no-store response header policy.
- Test suite covering tampering, cross-tenant access, absent records, status mapping and unsafe identifiers.

## Verification
- 28/28 focused PHP assertions passed locally.
- 13/13 added PHP files pass `php -l` (12 classes + 1 test).
- New code does not publish a route or enable write, cancellation, reschedule or payment settlement.
- No MariaDB fixture or browser E2E test available, no email dispatch verification.
- Pass 58 delta still missing in reconstructed lineage; cumulative remains recovery candidate, **not production-ready**.

## Next acceptance gates
1. Recover Pass 58 and reconcile original master lineage.
2. Map source-of-truth Cleanto schema and run tenant-scoped lookup against disposable MariaDB.
3. Only issue a customer grant after a real persisted booking; test missing and cross-tenant records in database.
4. Add authorization, ownership-proof and CSRF/abuse protections for customer reschedule/cancel routes, followed by browser tests.
5. Validate customer confirmation email and payment webhook lifecycle in a real staging environment.
