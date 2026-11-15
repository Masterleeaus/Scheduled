# Pass 54 — Database acceptance fail-closed correctness

Baseline: Pass 53 verified cumulative archive.

Implemented `CheckedProbeSql` to reject silent PDO query, prepare and execute failures and missing scalar values; wired it into ten infrastructure probes and the separate lifecycle connection verifier. Hardened rollback probe to require a clean transaction, verified transaction start and verified rollback success. This prevents a false-positive acceptance signal on PDO configured with ERRMODE_SILENT and prevents leftover probe transactions.

New focused tests: 24 real executable failure and success cases covering SQL query/prepare/execute, metadata reads, session/tenant probes, transaction rollback, storage isolation, and advisory lock failures.

Regression: 1,015/1,015 executable PHP test files passed with PHP CLI. No MySQL driver, Docker runtime, or local MySQL server is available. The real lifecycle and concurrency runners were NOT executed; zero committed booking tests. The code is NOT production-ready, and public writes remain disabled.

Production PHP files: 12 (11 modified + 1 added). Focused test scripts: 24 added plus a shared test bootstrap. Master and delta include this report and machine-readable manifest.

All previous Pass 53 files retained. All unchanged previous files byte-for-byte verified by SHA-256. Original Cleanto source unchanged.
