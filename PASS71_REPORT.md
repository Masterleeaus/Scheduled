# PlusAgency + Cleanto — Pass 71 builder save integrity

Baseline: Pass 70 recovery candidate. Missing Pass 58 remains unresolved.

## Changed
- Hardened original PageBuilderController save: exact type/id, language scoping, bounded payload, JSON state validation, optimistic revision conflict rejection (409).
- Scoped page content fetch to requested language; home creation behavior unchanged.
- Reworked original GrapesJS save command with bounded in-flight state, revision token, verified JSON response, 409/error handling and spinner cleanup.
- Added 15 static source-contract assertions.

## Limitations
- This is NOT full page publish, responsive preview or rollback acceptance. No browser E2E, MariaDB test, or site publishing performed.
- The original page builder persists arbitrary editor HTML. Its published HTML sanitization and tenant-level authorisation need full application-runtime security audit.
- Existing customers with non-language-linked pages may need migration; this change deliberately fails closed rather than writing mismatched language pages.
- Pass 58 remains missing; no booking, payment or production gate is released.
