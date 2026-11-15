# Pass 101 — disposable MariaDB acceptance kit

This is an executable staging runner, **not passing MariaDB evidence**. It does not turn on production booking writes.

## Host prerequisites

Docker Compose with ability to pull `mariadb:11.4` and `php:8.2-cli-bookworm`. The database network has no exposed host port and uses tmpfs storage. Provide `PA101_DB_PASSWORD`, a distinct `PA101_ROOT_PASSWORD`, and `PA101_FIXTURES_DIR` (absolute path). The directory must contain two separately reviewed `lifecycle.json` and `concurrency.json` fixture files, each mapped to actual disposable Cleanto service/rate/staff/customer records and nonoverlapping future appointment times. Start from `integration/mysql/lifecycle/fixture.example.json`; update IDs to match real fixture rows.

**Database provisioning is mandatory:** The original PlusAgency `installer/database.sql` is not the Cleanto booking schema. Supply `PA101_SCHEMA_SQL` with authentic, reviewed Cleanto schema and required integration migrations (in dependency order) and `PA101_SEED_SQL` with only synthetic test customer, service, rate and staff records. Both must be absolute paths. The launcher imports the files into a **fresh disposable** database on each run. Do not use production customer exports.

Run on a Docker-capable staging host:

```sh
cd integration/staging/pass101
export PA101_FIXTURES_DIR=/absolute/path/to/disposable-fixtures
export PA101_SCHEMA_SQL=/absolute/path/to/cleanto-schema-and-integration-migrations.sql
export PA101_SEED_SQL=/absolute/path/to/synthetic-test-rows.sql
export PA101_DB_PASSWORD='unique-disposable-password'
export PA101_ROOT_PASSWORD='different-disposable-password'
export PA101_DISPOSABLE_ACK=YES_DESTROY_TEST_DATABASE
bash run.sh
```

The launcher validates all input files before starting containers, starts and health-checks MariaDB, imports both SQL files using its internal client via stdin, then runs the existing schema, lifecycle and concurrency acceptance programs. On any failure or completion it removes the disposable database container and volume. Do not interpret a successful shell exit alone as independent SQL proof; inspect test evidence and independently query booking/order records as required by the release gate. A SQL file is a trusted reviewed input and must not include `DROP DATABASE`, unexpected schema alterations, or private production data.

## Evidence rules

Only the actual lifecycle and concurrency JSON output from the two PHP scripts, plus independent database readback, can satisfy booking acceptance. A running container, syntax check, or a skipped exit 77 does not. No payment acceptance is implied. Pass 58 source lineage remains unverified. Do not change production release evidence until all gates pass.

## Pass 102 structured evidence gate

`acceptance.sh` now captures the existing database infrastructure, real booking lifecycle, and two-worker concurrency JSON results in a private temporary directory. The separate `verify_reports.php` rejects truncated/malformed reports, missing lifecycle stages, absent persisted booking evidence, an unexpected database, and two-worker reports that do not show exactly one overlapping booking. Only after all three independent programs exit successfully and these checks pass does the launcher print an acceptance summary and the raw reports. The temporary directory is removed on exit.

This is a *consistency check* of the existing program output, **not** a substitute for independent SQL readback or a live browser test. It cannot certify production payment processing, tenant isolation or full booking availability. Synthetic reports used by the new regression test never count as MariaDB evidence.

## Pass 103 concurrent-run isolation

The launcher generates a cryptographically random per-run Compose project name (`pa101_` plus 24 hex digits) and passes it to **every** Compose command, including the exit-trap teardown. This prevents two independent staging runs using the same source folder from tearing each other's containers down. For repeatable test automation, an explicit `PA101_COMPOSE_PROJECT=pa101_` followed by 10–40 lowercase alphanumeric characters is supported; callers must ensure an overridden project name is not shared by concurrent runs. Do not point overrides at non-disposable projects.
