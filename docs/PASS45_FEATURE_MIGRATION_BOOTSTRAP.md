# Pass 45 — New Feature Schema Bootstrap

## Reproduced defect
The bundled legacy `installer/database.sql` contains the legacy `migrations` ledger and PlusAgency tables, but **none** of the new `titan_sites`, `titan_landing_campaigns`, `titan_business_tenants`, or booking extension tables. The first-install wizard imported the SQL and continued directly to administrator creation without applying the 2026 migrations. A completed install therefore lacked required current-app tables.

## Remediation
- Run exclusively the bundled `2026_*.php` feature migrations after successful legacy import, using Laravel 9 `migrate --path=* --realpath --force`.
- Fail installation on nonzero migration result or an incomplete migration ledger.
- Recheck seven critical feature tables before showing the final installed state.
- Do not rerun pre-2026 legacy migrations; those are already represented by the imported SQL dump.
- Maintain prior licence checks, admin bootstrap, session and CSRF protections.

## Testing limitations
No MariaDB server or PHP mysql drivers are installed here. Executable test covers migration discovery and fake Artisan/DB/Schema orchestration, not real DDL. A partially applied migration remains a failed installation and requires operator database recovery; MySQL DDL is not transactional.
