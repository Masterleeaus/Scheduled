# Pass 36: Atomic Cleanto writer boundary
The existing `BookingWriteCoordinator` remains intact for its legacy callers. New internal-only `BookingAtomicPersist` reuses the original Cleanto table writers, price guard and scheduling guard **inside an outer redemption claim transaction**. The `CommitLegacyGateway` accepts an injected writer and lock lease; `CommitOrchestrator` now holds advisory locks through commit or rollback. **Do not publicly route the gateway.** The release gates intentionally require independently captured MySQL, concurrency, price parity, tenant and schema evidence. Source-level tests are not live booking evidence.

## Required live validation
Run on disposable MySQL with `pdo_mysql`: verify schema, start/rollback, staff lock contention, original pricing and availability against fixtures, booking/claim atomicity, replay, failure rollback, 2-process concurrency, and persisted Cleanto record readback. No live MySQL driver was available during this pass.
