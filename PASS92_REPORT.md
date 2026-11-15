# Pass 92 — Native customer-data handoff (release blocked)

Baseline: verified Pass 91 merged cumulative (booking source plus five imported cleaning themes).

## Implementation
- Added `CheckoutNativeCustomer` to map strictly validated checkout contact data to Cleanto's native identity and `personal_info` arrays, without passing prices, tenant identifiers, or other authority fields.
- Reused the same mapping in precommit checkout compatibility.
- Propagated an optional, server-trusted `personalInfo` argument through the existing `CommitCleantoNativeGateway` → `CommitLegacyGateway` → `CommitLegacyAdapter` chain and into the original native command.
- Original entrypoint signatures remain backward-compatible through the optional empty argument. No public write route or payment gate was enabled.

## Evidence
- Native checkout mapping PHP test: 12/12 runtime assertions passed.
- Source handoff contract: 6/6 assertions passed.
- PHP -l: all changed PHP files passed.
- Full cumulative and delta ZIP CRC integrity: passed.

## Critical release blockers
- MariaDB server and `pdo_mysql` unavailable; no genuine transaction or concurrency evidence.
- Public checkout still performs validation only; no live public commit or payment route enabled.
- Missing Pass 58 source provenance persists from earlier recovery candidate.
- Browser end-to-end booking, publish and tenant isolation unverified.

## Scope limitation
The data-handoff bridge is integrated into the internal trusted writer path but public checkout remains intentionally write-disabled. This is a source-level improvement, not proof of a working booking flow.
