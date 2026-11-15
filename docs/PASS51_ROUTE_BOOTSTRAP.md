# Pass 51 — Database-free Laravel route bootstrap

## Fixed defect
`php artisan route:list` failed before database import because the legacy controllers queried `basic_settings` and payment gateway tables in constructors. The Laravel route inspector instantiates controllers; before MySQL is connected this triggers an uncaught database-driver exception.

## Implementation
30 frontend, authentication, and payment controllers now perform the existing database-backed configuration inside their controller middleware callbacks, not while their instances are constructed. Existing `guest` middleware registrations are preserved outside those callbacks. The callbacks run for HTTP actions before those actions execute; no booking, payment or customer database schema was changed.

## Evidence
- `php artisan route:list`: **PASS**, 810 routes without a MySQL driver
- `php artisan route:list --json`: **PASS**, 810 parseable routes
- `php artisan --version`: **PASS** (Laravel 9.52.15)
- PHP lint: **30/30 changed controllers passed**
- `python tools/test-pass51-route-bootstrap.py`: **PASS**
- Installer completion route remains POST-only

## Limits
- No PHP `pdo_mysql`/`mysqli` or MariaDB server in this runtime. No database-backed installation, login, payment, or booking has been certified.
- The bundled Laravel/Monolog libraries print PHP 8.4 deprecation warnings on stderr. Compatibility should be validated on the target PHP runtime.
- Deferring initialization affects the timing of legacy payment configuration; test checkout gateway webhooks and authenticated redirects in staging before production.
