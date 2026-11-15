# PlusAgency + Cleanto — Pass 47

## Changes
- 35 files added, 6 existing files modified, 0 deleted.
- Patched 6 active booking persistence/locking files to reject failed PDO `prepare`, `execute`, and order allocation `exec` calls rather than continuing with potentially stale insert IDs or missing proof records.
- Added 34 executable failed-SQL, booking proof, claim ledger, order allocation and locking tests, plus a shared test bootstrap.
- Original Cleanto vendor tree remains unchanged.

## Verification
- Executable PHP scripts: **831/831 passed** (test harness excludes bootstrap helpers).
- PHP syntax: **41/41 passed** for 6 modified production files and 35 new test/bootstrap files.
- Existing archive entries preserved: 21726; original entries missing: 0.
- MySQL integration: not executed; PHP PDO has no MySQL driver in this environment.
- Public booking writes remain disabled. Not production-ready.

## Limitations
The regression scripts exercise source-level contracts using test doubles, not committed MySQL appointments. Full booking and concurrency tests must run on the separately configured disposable MariaDB runtime.
