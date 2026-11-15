# Pass 105 — Original Cleanto schema upgrade reconciliation

This is an **evidence-based migration review and synthetic seed candidate**, **not** a working database installation or verified booking.

## Files

- `inventory_native_upgrades.py` reads (does not execute) PHP SQL string assignments from the original Cleanto installer and version updater. Emits table names, statement types, source file SHA-256, column operations and overlaps with the 36 native CREATE definitions.
- `upgrade_inventory.json` is the generated SQL provenance and conflict inventory. Some expressions and dynamically assembled SQL are not captured; always review original PHP before migrations.
- `generate_synthetic_seed.py` builds five explicitly synthetic, catalog/staff-only rows, validating columns against the recovered native schema.
- `synthetic-seed.REVIEW.sql` is **not** a complete acceptance fixture. No bookings, payments, customer personal data or live credentials are created. Staff schedules, ownership, settings and independent concurrency fixtures still need review.
- `test_pass105.py` provides executable deterministic checks.

## Reproduce

```bash
python3 integration/staging/pass105/inventory_native_upgrades.py private/cleanto/class_configure.php private/cleanto/objects/class_version_update.php integration/staging/pass104/schema_inventory.json /tmp/pass105-schema-audit
python3 integration/staging/pass105/generate_synthetic_seed.py integration/staging/pass104/schema_inventory.json /tmp/pass105-synthetic-seed.sql
python3 integration/staging/pass105/test_pass105.py
```

## Release gate

Do **not** combine all historical upgrades into an executable migration without establishing actual Cleanto 8.5 installation path and installed version. Do **not** run the seed SQL against a live tenant. Next: review DDL differences, explicitly assemble schema and integration ledger SQL for disposable MariaDB, add an actual staff schedule and two nonoverlapping JSON fixture windows, run real inserts/readback and two independent workers. This environment lacks MariaDB/Docker/pdo_mysql; no transaction evidence was obtained. Missing Pass 58 remains unresolved.
