# Pass 39 — Environment-before-DDL installer ordering

## Confirmed defect

The Pass 38 `EnvironmentController::saveWizard()` imported the legacy SQL schema **before** `EnvironmentManager::saveFileWizard()` wrote the chosen database and application configuration. If the environment write failed, the database could already contain partially or fully imported tables while the wizard returned an error.

## Changes

- Preflight the shipped SQL and require an empty target database before any mutation.
- Save and validate the final environment configuration before the first schema DDL statement.
- Keep the validated MySQL connection open across validation, configuration and import.
- Reuse the importer’s independent empty-database check and post-import identity-table verification.
- Preserve existing licensing, CSRF/session, administrator-creation and fail-closed completion controls.

## Evidence and limitations

This is an ordering fix, **not** a successful clean install. PHP in the execution environment lacks `pdo_mysql` and `mysqli`, and there is no MariaDB service. The targeted source-contract checks exercise ordering and retained safeguards; they do not simulate MySQL DDL, browser requests or a real installation. An interrupted SQL import may still leave a partially created schema and require a new empty database.

## Acceptance gate

Run fresh-install acceptance with supported PHP extensions and disposable MariaDB: new database, installer session, admin bootstrap, login, app dashboard, vertical/site creation, Cleanto catalogue, booking persistence, Stripe test mode and tenant isolation. Do not enable live writes before those pass.

## Template scoreboard

- Website starters: 19 (15 cleaning-oriented and 4 pet/fitness/yoga/vehicle)
- Landing starters: 10
- Registered verticals: 50
- Fully browser-certified: 0 of 29
