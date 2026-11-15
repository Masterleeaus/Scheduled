# Pass 108: Cleanto + canonical booking ledger disposable schema candidate

This is a **review-only database input bundle**, never an automatically approved production migration.

## Problem resolved

The Pass 104 native SQL has **36** table declarations, but one (`ct_staff_commission`) uses `CREATE TABLE` instead of `CREATE TABLE IF NOT EXISTS`. The new validation handles both original forms without dropping or rewriting the vendor statement. It reconciles all 36 names with the source inventory and adds the **two current sidecar tables** from `BookingWriteSchema.php`, not the incompatible Pass 05 ledger.

## Reproduce (offline, no database write)

From the application root:

```bash
python3 integration/staging/pass108/build_candidate.py --root . --out /tmp/cleanto-pass108-review
python3 integration/staging/pass108/test_candidate.py
```

The generated folder contains `cleanto-plus-ledger.REVIEW.sql`, `seed-and-schedule.REVIEW.sql`, `lifecycle.json`, `concurrency.json` and `REVIEW_MANIFEST.json` (source and output SHA-256 hashes). No test credentials, personal records, live database operations or release authorization are created by this generator.

## Why this is not ready to import automatically

* Cleanto's 36 installer ALTERs and historical updater statements have not been matched against an actual installed Cleanto 8.5 database. The generated SQL contains native installer CREATE declarations **and two canonical sidecar CREATE declarations**, but **not** a certified 8.5 migration path.
* The original legacy Pass 05 ledger must not be loaded alongside the current `fingerprint/state` ledger. Do not overwrite existing tenant data.
* Schedule semantics and fixture compatibility require verification in a disposable MariaDB before any public booking activation.
* No database server, PHP `pdo_mysql` driver or Docker is available in the current execution environment.
* Missing Pass 58 provenance still prevents calling the cumulative archive a fully reconciled master.

## Staging execution gate

On a Docker-capable host, first compare this candidate with a fresh genuine Cleanto 8.5 installation and establish the exact ordered upgrades needed. Only after explicit review, supply the approved SQL and fixtures to `integration/staging/pass101/run.sh` using its documented `PA101_*` inputs. Do not substitute the unreviewed candidate as a verified database or treat shell exit codes as booking evidence. Capture actual appointment and order readback and two-worker concurrency evidence.
