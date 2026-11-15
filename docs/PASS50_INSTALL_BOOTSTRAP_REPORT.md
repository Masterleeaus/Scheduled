# Pass 50 — pre-install Laravel bootstrap repair

Base: Pass 49 master (`Master-Copy-PlusAgency-Cleanto-Pass49-PRIVATE-SOURCE-HTTP-PROTECTION-CUMULATIVE.zip`).

## Verified defects and repairs

1. `AppServiceProvider::boot()` previously used HTTP `header()` and `exit` to divert uninstalled traffic; this also aborted Artisan, preventing installer-driven migrations. Removed redirect from provider boot and moved it into global HTTP middleware (`RedirectUninstalledToInstaller`).
2. `core/routes/web.php` performed two `Permalink` queries while registering routes, before the fresh-install schema exists. Wrapped those legacy dynamic route groups in a check for the installed marker; the static installer and application routes are retained. These dynamic routes register normally once the application is installed.

## Tests executed here

- `php -l` on changed PHP source: PASS.
- `php tests/pass50_bootstrap_before_install.php`: 8/8 PASS.
- `php core/artisan --version` from the core directory, before installed marker: **PASS** (`Laravel Framework 9.52.15`). This had failed with `could not find driver` before the fix.
- `php core/artisan route:list`: **BLOCKED**. Laravel's command reflects a frontend controller whose constructor queries the database; no `pdo_mysql` driver is installed here. No assertion of comprehensive Artisan or installed HTTP functionality.
- Live migration/import, first administrator login, Cleanto booking and Stripe: NOT TESTED (no MySQL PHP driver or MariaDB server).

## Scope

Four runtime source files changed or added: `AppServiceProvider.php`, `Kernel.php`, `RedirectUninstalledToInstaller.php`, and `routes/web.php`, plus a targeted test and this report. No production database or external service was modified.

## Ongoing limitations

Installed-site route cache/build requires database-backed dynamic route registration; the application still needs a live clean installation and browser acceptance test. The application remains a staging candidate, not a verified one-click production installer.
