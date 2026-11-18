# MySQL/MariaDB inventory for the PostgreSQL move (issue #15, task 1)

Scope: files on `origin/main` (c06a5493) that name MySQL, MariaDB or mysqli, or use MySQL-only SQL. Vendor directories are excluded. Counts come from `grep -rIl` and `grep -rIE -c` run from the repository root. This is an inventory only. It changes no runtime code and no schema.

Scope note: the counts cover files that mention MySQL or MariaDB. Some of those files are historical records or test fixtures, not runtime code. The categories below say which is which.

## Totals

- About 440 files mention MySQL, MariaDB or mysqli.
- About 275 files contain `ENGINE=`, `AUTO_INCREMENT` or `UNSIGNED` in SQL text (about 544 `AUTO_INCREMENT` occurrences).
- `installer/database.sql` (6,445 lines) holds 84 `CREATE TABLE` statements, each with `ENGINE=`.
- `integration/schema/` holds two `*.mysql.sql` files with 1 `CREATE TABLE` each.
- `pdo_mysql` appears in about 55 places, and `mysqli_` in about 1,777 places.

## A. Laravel runtime (core/)

Runtime code that opens a MySQL connection or checks the driver name.

| File | MySQL-specific behaviour |
|---|---|
| `core/config/database.php` | Default connection is `mysql` (line 18). Defines the `mysql` connection with `PDO::MYSQL_ATTR_SSL_CA`. |
| `core/config/queue.php` | Database queue default falls back to `DB_CONNECTION=mysql`. |
| `core/config/installer.php` | Requires extensions `mysqli` and `pdo_mysql`. |
| `core/.env.example` | `DB_CONNECTION=mysql`. |
| `core/app/Http/Controllers/Admin/BackupController.php` | Shells out to `mysqldump` with the `mysql` connection credentials. |
| `core/app/Booking/Diagnostics/BookingSchemaSnapshot.php` | Rejects any PDO driver other than `mysql`. |
| `core/app/Booking/Diagnostics/BookingTenantProbe.php` | Rejects any PDO driver other than `mysql`. |
| `core/app/Booking/NativeCleantoCatalogue.php` | Reads Cleanto tables only through a `mysql` driver connection. |
| `core/app/Booking/Public/Checkout/CheckoutTenantReadiness.php` | Takes a `$mysql` flag from `extension_loaded('pdo_mysql')`. |
| `core/app/Booking/Public/Checkout/CheckoutSubmissionExecutor.php` | Written for verified MariaDB acceptance only. |
| `core/app/Booking/Public/Checkout/NativeCheckoutSubmissionExecutor.php` | Notes that the Cleanto writer is MySQL-only. |
| `core/app/Booking/Public/Redemption/CommitCleantoNativeGateway.php` | Calls `BookingDriverGuard::assertMySql`. |
| `core/app/Booking/Public/Redemption/CommitLegacyRelease.php` | Gate fields named `mysql_lifecycle_passed`. |
| `core/app/Booking/Events/BookingEventPublisher.php` | Treats SQLSTATE 23000 as duplicate key (MySQL and SQLite) and 23505 (PostgreSQL). |
| `core/app/Central/Events/DatabaseProcessedEventStore.php` | Duplicate-key detection covers MySQL `ER_DUP_ENTRY` and SQLite codes. |
| `core/app/Support/Install/LegacySqlImporter.php` | Uses `mysqli`. Imports the MySQL schema with executable MySQL comments. |
| `core/app/Support/Install/InstallationDatabaseLock.php` | MySQL connection-scoped lock (`mysqli`). |
| `core/app/Support/Install/DatabaseConnectionIdentity.php` | `mysqli` identity check. |
| `core/app/Support/Install/DatabaseEndpoint.php` | MariaDB endpoint validation. |
| `core/app/Console/Commands/VerifyCleantoTenants.php` | Requires `pdo_mysql`. |
| `core/app/Http/Controllers/Front/BookingPublicSubmissionController.php` | Requires `pdo_mysql` to enable public submission. |
| `core/app/Http/Controllers/Front/BookingPublicRecoveryController.php` | Requires `pdo_mysql` for recovery eligibility. |
| `core/database/migrations/2026_11_15_000003_create_central_user_links.php` | Already portable. Uses SHA-256 digests so uniqueness works on MySQL and PostgreSQL. No MySQL-only SQL. |
| `core/routes/web.php` | Comment only: booking writes disabled until MySQL concurrency tests pass. |
| `core/resources/views/vendor/installer/environment-wizard.blade.php` | Installer wizard labels for MySQL. |
| `core/resources/lang/en/installer_messages.php` | Installer label strings. |

`core/database/migrations/` has no `ENGINE=` or `AUTO_INCREMENT` text. Laravel's schema builder produces the DDL, so the driver-specific behaviour sits in the connection configuration above.

## B. Booking sidecar (private/booking-sidecar/)

24 files mention MySQL or mysqli. Notable:

- `private/booking-sidecar/BookingDriverGuard.php` defines `assertMySql`, which is the hard driver gate.
- `private/booking-sidecar/migrations/0001_titan_booking_requests.sql` is MySQL DDL.

## C. Legacy Cleanto application (private/cleanto/)

132 files mention MySQL or mysqli, most of them PHP using `mysqli_*` calls directly. Largest groups:

- `private/cleanto/objects/` (36 files)
- `private/cleanto/assets/` (32 files, mostly AJAX PHP handlers under `assets/lib/` that call `mysqli_fetch_array` and similar)
- `private/cleanto/api/` (25 files)
- `private/cleanto/admin/` (20 files)
- `private/cleanto/front/` (6 files)
- `private/cleanto/extension/` (4 files)

Top-level PHP files that carry direct MySQL calls include `class_configure.php`, `ct_install.php`, `edit_booking.php`, `get_settings.php`, `index.php`, `manual_booking.php`, `settings_data.php`, `config_index.php`, and `index_one_step.php`.

`FOR UPDATE` appears 29 times across the tree. `LAST_INSERT_ID` is not used. `ON DUPLICATE KEY` appears once. `FIND_IN_SET` appears 16 times, `GROUP_CONCAT` 4 times, `SHOW TABLES` once, and `information_schema` 21 times. Each of these needs a PostgreSQL equivalent or a rewrite.

## D. Installer and SQL schema

- `installer/database.sql`: full MariaDB schema, 84 tables, `ENGINE=` on each. Referenced by `scripts/preflight.php` and by `tests/pass47-staging-contract.php`.
- `integration/schema/pass05_reservation_ledger.mysql.sql` and `integration/schema/pass12_ledger_canonical.mysql.sql`: MySQL ledger schemas.
- `integration/install/pass47/compose.yaml`: MariaDB service definition.
- `scripts/preflight.php`: requires `mysqli` and `pdo_mysql`.

## E. Integration, staging and test fixtures (integration/, tests/)

About 231 files under `integration/` and 8 under `tests/` mention MySQL. Breakdown under `integration/`:

- `integration/tests/` (138)
- `integration/staging/` (35)
- `integration/mysql/` (35)
- `integration/reports/` (9)
- `integration/install/` (4)
- `integration/release/` (3)
- `integration/mysql-tests/` (3)

These are test harnesses, staging evidence, and historical reports. They need to be re-pointed or retired during the port. They are not runtime code. They also hold evidence for the earlier MariaDB acceptance runs, so decide whether those records stay as history.

## F. Documentation that describes MySQL as current

- `README.md` (requires MySQL/MariaDB and manual tenant connections)
- `README_DIRECTORY.md`
- `BOOKING_DEPLOYMENT.md` and `BOOKING_AND_BUILDER_GUIDE.md`
- `CENTRAL-REGISTRY-PROPOSAL.md`
- `integration/BOOKING_DEPLOYMENT.md`, `integration/README.md`, `integration/contracts/`

## G. Not in scope of the inventory

- `core/composer.lock` mentions `mysql` only as a package keyword. No change needed.
- Docker and compose files under `integration/install/`, `integration/mysql/pass40/docker/` and `integration/staging/pass101/` define MariaDB services. They are test and staging harnesses and are listed in section E.

## Open items for the port (not decided here)

1. Whether `private/cleanto/` (legacy mysqli code) is ported or retired. The decision changes most of section C.
2. Whether `integration/mysql/` and `integration/mysql-tests/` are kept as historical evidence or converted.
3. Whether `installer/database.sql` is replaced by a PostgreSQL schema or kept for the legacy importer.
