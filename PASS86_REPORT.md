# Pass 86 — PDO Booking Evidence Readback Hardening

Baseline: Pass 85 cumulative recovery candidate. Pass 58 provenance remains missing.

## Production repair
`CommitLegacyEvidenceReader::read` now explicitly rejects `prepare()` returning false and `execute()` returning false, even if PDO is in silent/warning error mode. It also guards the fetch result and retains exact-one-row and transaction-boundary requirements.

## Tests
7 PDO stub cases: invalid ID, absent transaction, failed prepare, failed execute, missing row, duplicate row, and successful readback. `php -l` passed for both changed production file and new executable test. These are fake PDO driver tests, NOT MariaDB transaction acceptance.

## Operational gates
Real PDO MySQL driver absent; MariaDB server and Docker absent. No database commit, concurrent two-customer booking, payment webhook, browser checkout or tenant-isolation E2E evidence was obtained. Public booking writes remain disabled. Missing Pass 58 is unresolved. Not release-ready.
