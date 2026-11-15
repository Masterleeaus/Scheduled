# PlusAgency + Cleanto Pass 103 — staging Compose project isolation

Baseline: cumulative recovery Pass 102. Source-history Pass 58 remains unresolved.

## Verified repair
The staging `run.sh` used the implicit Compose project for every invocation. Two acceptance runs from the same directory could share containers and volumes, and either EXIT cleanup could destroy the other run's ephemeral MariaDB. Pass 103 allocates a fresh per-invocation random project name, consistently used for config, startup, imports, test execution and teardown. Validated CI overrides are supported; operators must give each concurrent run a distinct override.

## Tests actually executed
- Pass 101 staging contract: 19/19 passed.
- Pass 102 evidence consistency regression: 22/22 passed (synthetic reports only).
- Pass 103 fake-Docker subprocess test: 4/4 passed (two independent generated projects, stable cleanup identity, valid override, bad override rejected).
- Shell syntax: passed.
- Real MariaDB booking, SQL readback, browser checkout, payment and tenant acceptance: **not run**. No Docker/MariaDB/pdo_mysql available in this environment, and independently reviewed authentic Cleanto schema/fixtures are not packaged.

## Release blockers
Pass 58 provenance, actual Cleanto schema+synthetic fixtures, real transaction and conflicting booking, payments, tenant isolation and browser tests. Booking writes should remain gated. This pass is staging test isolation, not booking release certification.
