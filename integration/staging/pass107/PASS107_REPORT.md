# Pass 107 — Source-backed schedule reconciliation and ledger audit

Baseline: Pass 106 cumulative recovery candidate. No source files discarded.

## Implemented
- Fixed synthetic rescheduling date selection so appointments never move from Friday to an unseeded Saturday; supports Friday -> Monday and stays within the Monday–Friday seed.
- Verified actual Cleanto weekday identifiers using original `class_dayweek_avail.php`; generated an auditable source excerpt.
- Added a fail-closed historical-versus-canonical reservation-ledger schema audit. Existing databases with Pass 05 layout must be migrated deliberately with backup and row-by-row validation.
- Added deterministic tests covering two weeks of starting dates and both booking fixtures.

## Executed checks
- Pass 107 source-backed fixture tests: 102 assertions passed.
- Ledger schema audit: passed, with existing-data migration explicitly blocked.
- Existing Pass 106 tests: see final execution result (report generated with test log).
- MariaDB / pdo_mysql / Docker: unavailable in this runtime; no SQL imports or booking commits executed.
- Pass 58 remains missing. Production certification BLOCKED.

## Integrity and scope
Pass 107 changes only integration/staging/pass106/build_review_bundle.py, plus the new pass107 test/audit/docs files. Any fixture output already packaged in Pass 106 is a historical review artifact and MUST be regenerated with the revised source before use.
