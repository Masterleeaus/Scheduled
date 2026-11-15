# Pass 104 — Native Cleanto schema recovery

The **original** `private/cleanto/class_configure.php` contains literal `CREATE TABLE` declarations. This pass extracts the **36** definitions into a traceable candidate, without executing its installer (which also contains encoded PHP code) and without fabricating records.

Files:
- `extract_cleanto_schema.py`: repeatable, read-only extraction from the original PHP file.
- `cleanto-8.5-installer-tables.REVIEW.sql`: extracted table DDL; **review candidate, not an install-ready or certified schema**.
- `schema_inventory.json`: table/column inventory and source SHA-256.
- `test_extraction.py`: deterministic regression checks.

Example from the application root:

```bash
python3 integration/staging/pass104/extract_cleanto_schema.py private/cleanto/class_configure.php /tmp/cleanto-ddl-review
python3 integration/staging/pass104/test_extraction.py private/cleanto/class_configure.php
```

**Next required work:** inventory the original installer upgrade/ALTER paths, compare to the real Cleanto 8.5 runtime schema, add integration ledger migrations (including `titan_booking_intent_claims`), prepare a synthetic seed fixture, and run Pass 101 staging on a Docker/MariaDB-capable host. Do not import this candidate directly into production. The original PlusAgency `installer/database.sql` is not a substitute for this Cleanto schema.
