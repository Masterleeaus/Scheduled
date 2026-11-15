# PlusAgency + Cleanto — Pass 108 execution report

**Baseline:** Pass 107 cumulative recovery candidate; **scope:** original Cleanto schema and canonical booking-ledger assembly. This is not production certification.

## Production code changes

None. This pass adds a deterministic schema candidate compiler, safety regression tests, source provenance, actual generated review artifacts and operational handoff documentation. The original PlusAgency application, booking engine and website templates remain untouched.

## Finding and repair

Previous simplistic CREATE parsers can omit `ct_staff_commission`, because its original installer declaration is `CREATE TABLE` rather than `CREATE TABLE IF NOT EXISTS`. Pass 108 tests exact table-name agreement with the 36-table inventory, includes the original declaration, and appends the only two canonical booking sidecar CREATE statements. The historical Pass 05 ledger is explicitly excluded.

## Checks

* Pass 108: **8/8** executable Python regression cases passed (positive assembly, schema and ledger drift, unsafe/incorrect fixtures).
* Pass 105: **3/3** tests passed with native source files present.
* Pass 106: **12** assertions passed.
* Pass 107: **102** assertions passed.
* Python syntax checks passed.
* Fresh candidate: **36** native Cleanto tables plus **2** canonical sidecar tables, **5** synthetic service/staff statements plus weekday schedule, **2** distinct booking fixture JSON documents.
* Docker, MariaDB and `pdo_mysql` unavailable; **no real database import, booking write, SQL readback, browser checkout or payment test executed**.
* Pass 58 missing; original installed schema and historical upgrades unreconciled.

## Release decision

**BLOCKED** — review-only assembly, not an executable certification, and no booking release authorization. Next must verify the correct ordered migrations on a disposable MariaDB and run the actual booking lifecycle and concurrency programs.
