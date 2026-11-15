# PlusAgency + Cleanto Pass 105 — Native schema reconciliation checkpoint

**Baseline:** Pass 104 recovery candidate. **Outcome:** source-derived Cleanto installer/version-update migration inventory and schema-aligned synthetic catalog/staff seed candidate. **Release state:** BLOCKED (MariaDB acceptance and Pass 58 lineage).

## Source findings

- Native installer `private/cleanto/class_configure.php`: 36 CREATE, 36 ALTER and 14 INSERT literal SQL assignments recognised (86 total).
- Version-update `private/cleanto/objects/class_version_update.php`: 16 CREATE, 110 ALTER and 49 INSERT literal SQL assignments recognised (175 total).
- 40 version-updater ALTER statements propose adding columns already present in Pass 104's recovered installer CREATE schema. Historical upgrades cannot be replayed blindly.
- Extractor is conservative: SQL assembled dynamically and stateful version paths are outside its proof. Statement counts are recognised literal assignments, **not** a complete executable migration sequence.
- Added synthetic service/method/unit/rate/staff seed SQL with five rows and a strict column-name cross-check. It does not constitute valid booking fixture evidence and must be reviewed for real Cleanto staff/availability settings.

## Tests run

- `python3 integration/staging/pass105/test_pass105.py`: 3/3 passed.
- `python3 -m py_compile` on new Python files: passed.
- No MariaDB server, Docker or `pdo_mysql`: no real SQL import, transaction commit, checkout or competing-customer test completed.
- Source lineage Pass 58 unresolved. Public writes remain disabled.

## Next delivery gate

Review all 40 overlapping upgrade statements and additional dynamic SQL/version paths; produce a deliberate version-specific schema SQL. Add matching disposable staff schedule, owner and booking JSON fixtures; run Pass 101 staging acceptance on MariaDB-capable infrastructure. Verify committed booking/order/client IDs through independent SQL queries and two-customer conflict results before enabling reservation writes.
