# Pass 114 — Readback probe semantic correctness

## Scope
Corrected the actual disposable MariaDB read-only probe to reject synthetic catalogue and schedule rows that would otherwise pass numerical counts while not offering a valid booking slot. Preserves the PlusAgency and original Cleanto application source. This is a staging/test correctness change, not a booking-feature completion.

## Changes
- Check enabled `ct_services`, `ct_services_method`, and `ct_service_methods_units`, positive rate, staff booking enabled and weekly schedule.
- Confirm the five expected schedule rows are five **distinct** weekdays, 08:00–18:00, enabled, on the expected weekly schedule, with no unexpected staff schedule rows.
- Confirm all four booking/order and sidecar-ledger tables are empty before lifecycle acceptance.
- Extend fail-closed pure-component test coverage and keep the live probe read-only.

## Evidence
- `php -l` passed for the 3 modified PHP files.
- `php integration/staging/pass113/test_live_contract.php` passed 34 assertions in isolated test execution.
- Recovered Cleanto DDL checked for `ct_week_days_available` schedule column names.
- Real MariaDB import, transaction, independent readback, concurrency, browser checkout, and payment remain **NOT RUN**. `mariadbd`, `docker`, and `pdo_mysql` unavailable.
- Missing Pass 58 provenance unresolved. No public release authorization.

## Next requirement
Use a disposable MariaDB-capable host to run the imported reviewed schema/fixtures through the existing integration/staging/pass101/run.sh and collect true booking/order SQL evidence. Review historical Cleanto migrations before treating the recovered DDL as a production schema.
