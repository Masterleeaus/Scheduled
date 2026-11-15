# Pass 10: Cleanto MariaDB acceptance (operator runbook)

## Current result

The application contains a native Cleanto booking executor. A release gate deliberately keeps anonymous booking writes OFF unless trusted tenant release evidence is configured. Pass 10 adds a read-only, server-side diagnostic command; it does not provide or manufacture the missing live acceptance evidence.

## Required environment

- PHP `pdo_mysql` enabled with native prepared statements (`PDO::ATTR_EMULATE_PREPARES=false`).
- Actual Cleanto 8.5 tenant database restored on isolated staging MariaDB.
- A trusted Laravel connection for each tenant with least-privilege schema-read access for this check.
- Server-owned tenant release profile configured separately; do not accept tenant database/owner names from public requests.
- Keep `TITAN_BOOKING_PUBLIC_COMMIT_ENABLED=false` throughout read-only inspection.

## Read-only inspection

From `core/`:

```bash
php artisan booking:verify-tenants --json > /secure/reports/booking-schema.json
php artisan booking:verify-tenants --owner=1 --json
```

The command checks connection driver, native prepared statements, selected DB identity, and the columns required by the booking sidecar. It reads `information_schema.COLUMNS`; it does **not** write booking records or prove pricing, concurrency or payment settlement. Output is for trusted operators and may contain database names.

## Tests still mandatory before enabling commits

1. Run a disposable Cleanto DB fixture using the real schema and service, staff, pricing and opening-hours records.
2. Verify the preview slot and quoted price against original Cleanto calculations.
3. Submit a real reservation and read back order, client, booking and staff identifiers from the database using an independent connection.
4. Launch simultaneous reservation attempts for the same staff/time and require exactly one successful reservation (or the documented availability capacity).
5. Retry the identical signed intent: verify idempotent replay and no duplicate native rows.
6. Test separate tenants that use different databases and reject mismatched connection mappings.
7. Test cancellation and rescheduling and check lock release and overlap prevention.
8. Trigger a transaction failure after some writes and ensure all native tables roll back together.
9. Verify booking confirmation does not falsely imply payment settlement; reconcile actual payment-provider webhooks separately.
10. Collect commands, environment versions, test traces and independent persisted-readback evidence. Only a responsible operator may then configure release evidence and enable the appropriate tenant.

## Local environment limitation

The implementation environment used for this pass has `PDO` but **no `pdo_mysql` extension and no MariaDB executable/server**, therefore all checks against a live MariaDB tenant remain unexecuted. Do not cite PHP syntax, schema mocks, or archive integrity as live booking certification.

## Pre-existing legacy test issues

Historical tests `pass89_checkout_commit_compatibility_test.php` and `pass92_checkout_native_handoff_test.php` expect a long formatted telephone string to be rejected; current customer normalisation accepts it. Verify intended column storage bounds before changing production validation. `pass100_response_contract_test.php` constructs its source path relative to the tests directory instead of Laravel root; fix independently from booking persistence verification.
