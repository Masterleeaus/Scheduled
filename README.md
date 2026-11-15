# Scheduled

Scheduled combines the PlusAgency website platform with the Cleanto booking system in one Laravel codebase.

The complete cumulative application source is committed to `main`. The archive import was verified against all 22,919 supplied files before the uploaded ZIP was removed. Historical pass reports and manifests have been removed from the repository root; earlier revisions remain available in Git history.

## Application areas

- Business websites and landing pages, template editing, publishing, and enquiry capture.
- Business vertical catalogues and service presentation.
- Cleanto booking integration, including availability, checkout, booking recovery, staff assignment, and payment webhook handlers.
- Business-owner and administrator interfaces.
- Installation, database migration, and deployment verification tools.

Source inclusion does not establish live acceptance. Clean database installation, complete booking flows, template editing and publishing, and provider payments still require verification in a configured environment.

## Repository layout

| Path | Purpose |
| --- | --- |
| `core/` | Laravel application, routes, configuration, migrations, views, and bundled dependencies |
| `assets/` | Website, administrator, and shared static assets |
| `private/cleanto/` | Preserved Cleanto source and booking dependencies |
| `installer/` | Installer assets and base SQL schema |
| `integration/` | Integration contracts, database tooling, deployment notes, and acceptance harnesses |
| `tests/` | Standalone regression and source-contract checks |
| `scripts/`, `tools/` | Preflight and developer utilities |
| `docs/`, `development/` | Technical notes and development history |
| `index.php`, `.htaccess` | Public entry point and Apache routing/access rules |

## Runtime requirements

- PHP 8.1 or later, compatible with the bundled Laravel 9 dependencies.
- MySQL or MariaDB.
- PHP extensions checked by `scripts/preflight.php`: mysqli, PDO, pdo_mysql, OpenSSL, mbstring, tokenizer, JSON, cURL, fileinfo, EXIF, and GMP.
- Apache with rewrite support, or equivalent Nginx routing and access restrictions.
- Composer when installing or rebuilding PHP dependencies.

Use `core/composer.lock` when installing dependencies; do not upgrade the dependency set as part of deployment.

## Installation

1. Clone the repository and configure the web server for the root `index.php` layout.
2. Keep `core/`, `private/`, `integration/`, SQL files, environment files, and developer tooling inaccessible over HTTP. Apache rules are supplied in `.htaccess`; Nginx requires equivalent explicit rules.
3. Ensure the application can write to `core/storage/`, `core/bootstrap/cache/`, and the required media directories.
4. Open `/install` and complete the requirements, environment/database, licence, administrator, and finalization steps using authorised installation credentials.
5. Confirm that `core/.env` contains the correct application URL, database settings, and application key. Use `APP_ENV=production` and `APP_DEBUG=false` for a production deployment.
6. Configure booking tenant connections, internal authentication, and any payment or messaging providers required by the deployment. Verify them with disposable test data before enabling live operations.

The installer coordinates the base database import and feature migrations. Avoid importing the SQL separately into an existing installation without reviewing its state.

## Verification

Run the read-only preflight from the repository root:

```bash
php scripts/preflight.php --json
```

The preflight intentionally reports unresolved manual checks for HTTP access restrictions, database/booking acceptance, and external providers. A successful source import is not a substitute for those checks.

For disposable Docker-based installation testing, see [the installation harness](integration/install/pass47/README.md). It requires explicit disposable-environment acknowledgement and separate database credentials.

Regression checks live in `tests/` and `integration/tests/`. Follow each check's documented runtime and fixture requirements; some require database or provider configuration.

## Development

- Run Laravel commands from `core/`.
- Preserve tenant ownership, authentication, booking concurrency, and idempotency controls when changing integration code.
- Keep credentials, client data, and deployment-specific configuration out of commits.
- Treat older pass documents under `docs/`, `development/`, and `integration/` as historical context; confirm current behaviour against the implementation.
- Keep development reports and manifests out of the repository root.
