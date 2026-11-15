# Pass 33 — Installer hardening and fail-closed preflight

## Fixed

- Detect missing `pdo_mysql` before the database wizard.
- Stop legacy schema import on the first failed SQL statement; show a generic user error while logging diagnostic details.
- Refuse destructive imports into a populated database. MySQL DDL is non-transactional; a partially failed import requires a new empty database.
- Remove the installer code paths that overwrote `AppServiceProvider.php` and `routes/web.php` from vendor files.
- Never show `.env` or default `admin/admin` credentials on the finish page.
- Remove hard-coded payment demo credentials and ensure production/debug-off defaults.
- Add `scripts/preflight.php` for a read-only, explicit installation readiness assessment.
- Verify bundled SQL can be tokenized without being executed against a live database.

## Not certified

- The installer has **not** been run against a real MariaDB instance.
- Existing bundled database.sql may contain legacy seed accounts that require manual replacement on staging.
- Admin creation, one-click installation, Cleanto install, Stripe, AI providers, DNS and complete website editing remain unverified.
- Web server configuration is host-specific: reject direct access to `/core`, `/private`, `/integration`, and secrets. The root `.htaccess` does not configure Nginx.
- A SQL import error can leave a partially installed database: start over with a fresh empty database rather than retrying on the same database.
- Installer uses a vendor namespace and will require porting to app-owned installer controllers in the long term.

## Usage

From the extracted project root: `php scripts/preflight.php` or `php scripts/preflight.php --json`.
Exit code 1 means the deployment is **blocked**; it deliberately includes manual security and live integration gates.
