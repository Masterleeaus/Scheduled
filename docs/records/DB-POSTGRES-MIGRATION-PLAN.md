# Scheduled: PostgreSQL Migration Plan (planning record)

Status: planning only. No runtime code is changed by this document.
Tracking issue: #15 "Migrate Scheduled database from MySQL/MariaDB to PostgreSQL".
Related: #7 (database decision and corrections), Central #43 (single Titan database, migration registry), Central #82 (shared PostgreSQL acceptance gate).

## 1. Decision and scope

- Owner decision: the database is PostgreSQL. The Laravel code still targets MySQL/MariaDB.
- This inventory covers `core/` only, as requested. Issue #15 also asks for `private/cleanto/`, `installer/` and `integration/`. Section 3 gives adjacent counts so the next pass can be scoped. Those trees are larger than `core/` and must be inventoried before the plan is final.
- Counts come from grep over `core/` excluding `vendor/`, `node_modules/` and `storage/`. Method counts are occurrences, not distinct files, and the patterns are regex-based. Treat them as close, not exact.

Decisions taken in this plan (for review):
- D1. Scope is `core/` for this pass, with adjacent trees listed as a follow-up inventory.
- D2. Replace each MySQL-only construct with a PostgreSQL construct that keeps the same guarantee, not just the same syntax. Where no equivalent exists, fail closed on pgsql rather than silently degrade.
- D3. Recommend `timestamptz` for new timestamp columns and a per-column decision for legacy ones (section 5.3). This needs owner sign-off.
- D4. Recommend `text` (not `bytea`) for legacy `binary` content columns that hold text (section 5.2). This needs owner sign-off.

## 2. Inventory: `core/`

### 2.1 Configuration

| Item | Location | Finding |
|---|---|---|
| Default connection | `core/config/database.php` line 18 | `env('DB_CONNECTION', 'mysql')` |
| MySQL connection | `core/config/database.php` lines 46-65 | `utf8mb4` charset and `utf8mb4_unicode_ci` collation, `strict => true`, `engine => null`, `PDO::MYSQL_ATTR_SSL_CA` option |
| pgsql connection | `core/config/database.php` lines 66-79 | Already present (`sslmode => prefer`, `charset => utf8`). Not the default |
| Queue DB fallback | `core/config/queue.php` line 83 | `env('DB_CONNECTION', 'mysql')` |
| Env template | `core/.env.example` lines 9-14 | `DB_CONNECTION=mysql`, `DB_PORT=3306` |
| Booking config gates | `core/config/booking_internal.php` lines 11, 19 | Comments gate features on verified MariaDB |
| PHPUnit | `core/phpunit.xml` | 0 database settings |
| Composer | `core/composer.json` | `laravel/framework ^9.0`, `doctrine/dbal ^2.10`, `php ^8.0`. Declares `ext-curl` and `ext-json` only. No `ext-pdo_mysql` or `ext-pdo_pgsql` |

### 2.2 Driver checks and raw SQL in application code

| Construct | Files (under `core/app/`) | Count |
|---|---|---|
| PDO driver-name guard (`ATTR_DRIVER_NAME` must be `mysql`) | `Booking/Diagnostics/BookingSchemaSnapshot.php`, `Booking/Diagnostics/BookingTenantProbe.php`, `Booking/Public/Redemption/CommitConnection.php` | 3 |
| Driver string guard (`driver` must be `mysql`) | `Booking/NativeCleantoCatalogue.php` | 1 |
| `INSERT IGNORE` | `Booking/Public/Redemption/RedeemLedger.php` | 1 |
| `information_schema` queries | `Booking/Diagnostics/BookingSchemaSnapshot.php`, `Support/Install/LegacySqlImporter.php` (plus one string assertion in `core/tests/Booking/pass10_readiness_contract_test.php`) | 2 in app, 1 in tests |
| `DATABASE()` / `VERSION()` | `Booking/Diagnostics/BookingSchemaSnapshot.php`, `Support/Install/LegacySqlImporter.php`, `Support/Install/InstallationDatabaseLock.php` | 3 files |
| `GET_LOCK` / `RELEASE_LOCK` (named advisory locks) | `Support/Install/InstallationDatabaseLock.php` | 1 file, 2 statements |
| Backtick-quoted identifiers built by string concatenation | `Booking/Public/Customer/CustomerBookingReadback.php` | 1 file |
| Raw `CREATE TABLE` with MySQL charset syntax (`CHARACTER SET ascii COLLATE ascii_bin`) | `Booking/Public/Redemption/RedeemSchema.php` | 1 file |
| Executing dump statements verbatim (`$db->query($statement)`) | `Support/Install/LegacySqlImporter.php` line 102; `Support/Install/DatabaseConnectionIdentity.php` line 43 | 2 |
| Files matching a PDO / `->prepare(` pattern | `core/app/` | 27 (some `prepare` calls are non-database, e.g. payment gateway) |
| Files mentioning MySQL / MariaDB / `pdo_mysql` | `core/app/` (Booking/Public 5, Support/Install 4, Http/Controllers 3, Booking/Diagnostics 2, Console/Commands 1, plus 2 Booking files) | 17 |
| `FIND_IN_SET` | Not in `core/app`. Only a string assertion in `core/tests/Unit/BookingOwnerOperationalFiltersTest.php`. Source lives in `private/booking-sidecar/` (section 3) | 0 in app, 1 in tests |

Fine as-is on PostgreSQL (low risk): `FOR UPDATE` with `LIMIT 2` in `Booking/Public/Redemption/CommitEvidenceReader.php`, `Booking/Public/Redemption/CommitOrderReader.php`, `Booking/Public/Redemption/RedeemLedger.php`. Plain `LIMIT` and prepared statements are portable.

Laravel query builder use: `DB::table` appears in 22 files under `core/app/`. Raw facade calls (`DB::raw`, `DB::select`, `DB::statement`, `DB::unprepared`) appear 0 times in `core/app/`. Query-builder raw methods (`whereRaw`, `selectRaw`, `orderByRaw`, etc.) appear 0 times. This part of the codebase is largely portable.

### 2.3 Migrations (`core/database/migrations/`, 278 files)

File-level structure: 97 files contain `Schema::create`, 181 contain `Schema::table` (a file may contain both). One migration uses the `DB` facade for a data backfill (`2026_11_11_190001_add_published_snapshots_to_titan_landing_campaigns.php`, `DB::table` with `chunkById`). That is portable.

Method usage (occurrences of `->method(` across migrations):

| Method | Count | PostgreSQL note |
|---|---|---|
| `binary` | 24 | Maps to `bytea`. Used for text content (see 5.2) |
| `longText` | 41 | Maps to `text` |
| `decimal` | 43 | Portable |
| `timestamp` | 12 | MySQL `TIMESTAMP` semantics differ (see 5.3) |
| `json` | 2 | MySQL `JSON` maps to `json`, not `jsonb` |
| `enum` | 1 | `event_categories.status` as `enum('status',[0,1])`. Check constraint on pgsql |
| `char` | 3 | Portable (hash columns, length 64) |
| `unsignedBigInteger` / `unsignedInteger` / `unsignedTinyInteger` | 44 / 6 / 7 | PostgreSQL has no `unsigned`; non-negative guarantee is lost |
| `tinyInteger` | 137 | Used as flags and status. Maps to `smallint` |
| `after` | 56 (in 39 files) | MySQL column positioning. Not supported by the pgsql grammar. Verify whether it is ignored silently |
| `change` | 58 | `ALTER COLUMN` via DBAL. Type changes on populated tables need explicit conversion |
| `dropColumn` | 168 | Portable |
| `renameColumn` | 6 | Portable |
| `dropForeign` / `dropIndex` / `dropUnique` | 4 / 1 / 1 | Index names from earlier SQL dumps may differ from Laravel defaults |
| `default` | 247 | Verify any default expressions (not checked individually; see section 6) |
| `comment` | 76 | Supported on pgsql |
| `nullable` | 688 | Portable |

Raw DDL in migrations: 0 `ALTER TABLE` / `ENGINE=` / `AUTO_INCREMENT` / `ON UPDATE` strings. Migrations are Schema-builder only.

Seeds and factories: `core/database/seeds/DatabaseSeeder.php` (1 file, no MySQL tokens). `core/database/factories/` (1 file, no MySQL tokens). There is no `database/seeders/` directory.

Column-type counts from a `$table->` prefix pass (lower bounds): `string` 508, `integer` 133, `tinyInteger` 137, `text` 124, `bigIncrements` 93, `unsignedBigInteger` 44, `timestamps` 72.

## 3. Adjacent surfaces (outside `core/`, for the follow-up inventory)

Counts are files that match MySQL/MariaDB tokens (`mysql`, `mariadb`, `pdo_mysql`, `information_schema`, `GET_LOCK`, `FIND_IN_SET`, `ENGINE=`, `utf8mb4`, `AUTO_INCREMENT`, `ON DUPLICATE KEY`), out of total files in the tree.

| Tree | MySQL-touching files | Total files | Notes |
|---|---|---|---|
| `private/cleanto/` | 133 | 1797 | Named in #15 |
| `private/booking-sidecar/` | 32 | 257 | Holds the `FIND_IN_SET` source (`BookingOwnerFilters.php`, `BookingStaff.php`, `BookingConflictReadbackSql.php`, `CleantoStaffAdministration.php`) |
| `integration/` | 247 | 1540 | Includes staging and review SQL |
| `installer/` | 1 | 38 | `installer/database.sql` |

SQL files in the repo: 9 total. 1 in `installer/`, 8 in `integration/` (2 named `*.mysql.sql`). None under `core/`.

`pdo_mysql` is mentioned in 46 files repo-wide. `core/tests/Release/pass17_release_security.php` asserts `extension_loaded('pdo_mysql')`. The MariaDB installer endpoint is validated in `core/app/Support/Install/DatabaseEndpoint.php`.

## 4. Risks (highest first)

1. Silent data-type change for text content. 24 `binary` columns (content, html, css, body, description, instructions, copyright text and similar) become `bytea` on PostgreSQL. Text queries, `LIKE`, collation and string comparison will not behave the same. Severity: high.
2. `INSERT IGNORE` on the redemption claim path (`RedeemLedger.php`). It is a concurrency guard. MySQL `INSERT IGNORE` suppresses all errors. PostgreSQL `ON CONFLICT DO NOTHING` only suppresses the conflict named. The equivalent must be tested for exactly-one-winner under concurrency. Severity: high (double redemption risk).
3. Driver guards fail closed by design. `BookingSchemaSnapshot`, `BookingTenantProbe`, `NativeCleantoCatalogue` and `CommitConnection` reject non-MySQL connections. On PostgreSQL the booking path will refuse to run until each guard is reimplemented. This is correct behavior but blocks the feature. Severity: high (functional).
4. Legacy SQL dump import (`LegacySqlImporter`, `DatabaseConnectionIdentity`). Runs dump statements verbatim. MySQL dumps will not load on PostgreSQL, and the importer needs a new path or removal. Severity: high for installation.
5. Named advisory locks. `GET_LOCK` / `RELEASE_LOCK` in `InstallationDatabaseLock.php` have no direct equivalent. Use `pg_try_advisory_lock` with an integer key derived from the lock name. Severity: medium (install race conditions).
6. Catalog queries. `information_schema` with `DATABASE()` / `VERSION()` in `BookingSchemaSnapshot` and `LegacySqlImporter`. Needs `current_schema()` / `current_database()` / `version()` and pg-catalog-aware filters. Severity: medium.
7. Backtick identifiers in `CustomerBookingReadback.php`. Interpolated identifiers need PostgreSQL quoting (double quotes) and must respect case. Severity: medium.
8. Timestamp semantics (12 `timestamp` columns, `timestamps()` in 72 files). MySQL `TIMESTAMP` converts to and from UTC on the connection; PostgreSQL `timestamp` without time zone does not. Mixed behavior if not decided. Severity: medium.
9. Unsigned columns (57 occurrences). Non-negative guarantee lost unless check constraints are added. Severity: medium.
10. Column positioning (`after`, 56 uses). If ignored silently on pgsql, column order drifts. Only matters if code depends on `SELECT *` order (e.g. `CustomerBookingReadback`). Severity: low to medium.
11. `change()` on populated tables (58 uses). Type changes need explicit `USING` on PostgreSQL. Severity: medium for existing installs, low for fresh boot.
12. Test suite. 7 files under `core/tests` mention MySQL / MariaDB and some assert MySQL-specific strings (`FIND_IN_SET`, `information_schema.COLUMNS`). They will fail or give false passes. Severity: medium.
13. Configuration defaults. `DB_CONNECTION` default is `mysql` in `database.php`, `queue.php` and `.env.example`. Composer does not declare `ext-pdo_pgsql`. Severity: low, but easy to miss on deploy.

## 5. Target mapping (proposed; needs owner sign-off where marked)

### 5.1 General

- `bigIncrements` / `unsignedBigInteger` IDs: `bigint` identity. Portable.
- `tinyInteger` flags and status: `smallint`. Consider `boolean` for true flags (owner sign-off on which ones).
- `unsigned*`: add `CHECK (col >= 0)` where the MySQL column was unsigned.
- `enum`: `varchar` with check constraint. Already the Laravel pgsql behavior; verify.
- `json`: `jsonb` for new columns, if query needs arise. Keep `json` for pure storage.
- `longText` / `text`: `text`.

### 5.2 Legacy `binary` text columns (owner sign-off: D4)

Recommend `text` for the 24 `binary` columns that hold HTML, CSS, body or description text. Convert with a data-preserving step (`convert_from(bytea, 'UTF8')` in a pgsql migration, or in the data-copy script). Verify that every value is valid UTF-8 first.

### 5.3 Timestamps (owner sign-off: D3)

Recommend `timestamptz` for new columns. For legacy `timestamp` columns, convert with the session time zone set to UTC during data copy, and confirm read paths in Laravel (`app.timezone`) produce the same values.

## 6. Ordered steps

1. Record scope and decisions D1-D4 in #15 (as a comment, via the PR link).
2. Complete the inventory for `private/cleanto/`, `private/booking-sidecar/`, `installer/` and `integration/` using the same method as section 2. Add the counts here.
3. Decide type mappings in 5.1-5.3 and record them in a target schema document. Pending owner sign-off on D3 and D4.
4. Verify default expressions in migrations (247 `default` calls). List any that use raw SQL or MySQL functions.
5. Introduce a connection gate that accepts `pgsql` and keeps `mysql` only where still needed. Replace the four driver guards (section 2.2) with a gate keyed on the configured driver, and keep fail-closed behavior on any unknown driver.
6. Replace MySQL-only SQL in application code:
   a. `INSERT IGNORE` to `INSERT ... ON CONFLICT (key) DO NOTHING` backed by an explicit unique constraint on the claim key. Confirm the same winner semantics.
   b. `GET_LOCK` / `RELEASE_LOCK` to `pg_try_advisory_lock` / `pg_advisory_unlock` with a documented key derivation.
   c. `information_schema` / `DATABASE()` / `VERSION()` to pgsql catalog equivalents.
   d. Backtick quoting to Laravel's grammar wrapping or PostgreSQL double quoting.
   e. `RedeemSchema` `CREATE TABLE` rewritten as pgsql DDL, with the same constraints.
7. Replace `LegacySqlImporter` and the dump-execution path with a pgsql-aware import or remove it (owner decision).
8. Migrations:
   a. Fresh-boot migrations run cleanly on PostgreSQL.
   b. Review the 58 `change()` calls for PostgreSQL `USING` conversions.
   c. Decide how `after()` is handled on pgsql (ignore, or document as cosmetic).
   d. Add check constraints for former unsigned columns.
9. Tests: rewrite the 7 MySQL-specific test files. Add a pgsql test run to phpunit with `ext-pdo_pgsql`.
10. Configuration: default `DB_CONNECTION=pgsql` in `database.php`, `queue.php`, `.env.example`. Add `ext-pdo_pgsql` to `composer.json`. Keep MySQL config only for the legacy-read path if needed.
11. Data migration for existing installations: export from MySQL, transform (5.2, 5.3), load into PostgreSQL, validate, then cut over. Keep the MySQL source read-only during the window.
12. Rollback path: the MySQL source remains intact until the post-cutover window closes. Record the rollback trigger and the steps to point the app back.
13. Pass the Central #82 acceptance gate and register Scheduled-owned migrations with Central #43.

## 7. Verification required

- Fresh boot: `php artisan migrate:fresh` on an empty PostgreSQL database completes with no errors. Then `php artisan migrate --pretend` output is reviewed for MySQL-only SQL.
- Schema diff: compare information_schema columns from a MySQL-built instance and a PostgreSQL-built instance against the type mapping in section 5. Zero unexplained differences.
- Data copy: row counts and per-table checksums match. For the 24 former `binary` text columns, byte-for-byte round trip of UTF-8 content.
- Redemption concurrency: N parallel claims for the same key on PostgreSQL produce exactly one winner, and the losers receive the same response as the MySQL path.
- Advisory locks: two concurrent installer processes cannot both acquire the install lock.
- Timestamp round trip: values written through Laravel read back identically across a UTC session and the app timezone.
- Unsigned guard: negative inserts into former unsigned columns are rejected.
- Driver gate: the booking path refuses to run on an unsupported driver and runs on pgsql once reimplemented.
- Static gate in CI: fail on `INSERT IGNORE`, `GET_LOCK`, `information_schema`, `ATTR_DRIVER_NAME` checks that hard-code `mysql`, and backtick-quoted identifiers in `core/app/`.
- Acceptance: Central #82 gate passes for Scheduled.

## 8. Open items for the owner

- D3: timestamp mapping for legacy columns.
- D4: `text` versus `bytea` for legacy binary content columns.
- Whether the legacy SQL dump importer is kept, moved to a pgsql path, or removed.
- Whether MySQL remains supported in parallel during transition, or PostgreSQL is the only target.

## 9. Adjacent inventory (completes step 6.2)

Scope: `private/cleanto/`, `private/booking-sidecar/`, `integration/` and `installer/`, as named in #15.
Method: case-insensitive grep per token, excluding `vendor/`, `node_modules/` and `storage/`. Counts are files that contain the token, not distinct statements. They are a scoping aid, not a defect count. Section 3 used a different token set, so its totals differ slightly.

| Token or construct | `private/cleanto/` (1797 files) | `private/booking-sidecar/` (260 files) | `integration/` (1542 files) | `installer/` (38 files) |
|---|---|---|---|---|
| `mysql` (any case) | 132 | 26 | 210 | 0 |
| `mariadb` | 0 | 3 | 44 | 0 |
| `information_schema` | 0 | 6 | 4 | 0 |
| `GET_LOCK` / `RELEASE_LOCK` | 0 | 1 | 10 | 0 |
| `FIND_IN_SET` | 2 | 4 | 1 | 0 |
| `ENGINE=` | 4 | 4 | 13 | 1 |
| `utf8mb4` | 0 | 3 | 8 | 1 |
| `AUTO_INCREMENT` | 3 | 1 | 7 | 1 |
| `INSERT IGNORE` | 0 | 0 | 1 | 0 |
| `ATTR_DRIVER_NAME` | 0 | 4 | 2 | 0 |
| `DATABASE()` / `VERSION()` | 16 | 9 | 11 | 0 |

Findings:

- F1. The same constructs as `core/` appear in the booking sidecar (`GET_LOCK`, `information_schema`, `ATTR_DRIVER_NAME`, `FIND_IN_SET`). That sidecar is the booking write path, so it is the highest-risk tree after `core/`.
- F2. `private/cleanto/` has the largest count of `mysql` mentions (132 files). Most are likely configuration, connection code or comments. It has no `GET_LOCK`, `INSERT IGNORE` or `information_schema` hits, so the risk there is mainly connection config and DDL (`ENGINE=`, `AUTO_INCREMENT`).
- F3. `integration/` is the largest tree. It holds staging, review and seed SQL (`integration/staging/`, `integration/mysql/`, `integration/mysql-tests/`). Most of its hits are likely test data or historical evidence. Do not port these by search and replace. Decide per directory whether each is kept, moved to a pgsql path or archived.
- F4. `installer/database.sql` holds the MariaDB dump (`ENGINE=`, `utf8mb4`, `AUTO_INCREMENT`). It is the install schema, so it needs a PostgreSQL equivalent before fresh installs work on pgsql.
- F5. Correction to section 3: the repository has 10 SQL files outside `vendor/`, not 9. They are 1 in `installer/`, 8 in `integration/` (2 `*.mysql.sql` under `integration/schema/`, 6 under `integration/staging/`) and 1 in `private/booking-sidecar/migrations/`.

Next steps for this inventory (not done in this pass):

1. Open each `integration/` hit by directory, and mark it as keep, port or archive. Owner review is needed for the archive decisions.
2. Port `installer/database.sql` and the booking sidecar migration to PostgreSQL DDL, under the type mapping in section 5.
3. Replace the four booking driver guards and the sidecar's `GET_LOCK` and `information_schema` use, as in section 6.

This section does not change any runtime code. It is planning evidence only.
