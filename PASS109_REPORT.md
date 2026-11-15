# Pass 109 — Unique index identity audit

Baseline: Pass 108 recovery candidate. Pass 58 provenance remains unresolved.

## Production repair
MySQL INFORMATION_SCHEMA.STATISTICS returns one row per index column. Previous audit treated NON_UNIQUE=0 on each row as proof of single-column uniqueness; this wrongly accepted the first or second column of a composite unique key. The updated audit collects INDEX_NAME and SEQ_IN_INDEX, rejects contradictory/incomplete metadata and only marks a column unique when its index is unique and contains exactly that one column.

## Verification performed
- 13/13 targeted PHP runtime assertions passed (composite, singleton, mixed, scrambled index order, malformed metadata).
- 3/3 changed/new PHP syntax checks passed.
- No MariaDB service, docker runtime or pdo_mysql available; no actual booking commit or SQL readback performed.
- Pass 58 code gap unresolved. Existing ledger migration is not authorized.
- Source-level acceptance does NOT prove a working booking checkout, payment or multi-tenant isolation.

## Files
2 new source/test files; 1 modified production PHP file. No customer-visible changes.
