# PlusAgency + Cleanto — Pass 44: First-admin authentication security

## Base and scope
- Base: `Master-Copy-PlusAgency-Cleanto-Pass43-WEBROOT-SECURITY-CUMULATIVE.zip`.
- This pass addresses a real first-administrator login and account suspension issue discovered while pursuing installation acceptance.
- The bundled SQL creates a bootstrap administrator with a nullable role_id. The previous CheckStatus middleware tested role_id before checking whether the administrator was disabled. The previous login controller did not constrain status and did not regenerate the session after authentication.

## Changed application code
1. `core/app/Http/Controllers/Admin/LoginController.php`: only authenticate `status = 1` admin accounts, regenerate the successful login session, invalidate the logout session and rotate its CSRF token.
2. `core/app/Http/Middleware/CheckStatus.php`: enforce the active status for every authenticated administrator, including the role-less bootstrap admin, log out disabled users, invalidate session and rotate CSRF token.

## Added test assets
- `tests/pass44_admin_auth_smoke.php`: exercises five middleware scenarios with facade stubs, including disabled bootstrap admin.
- `tests/pass44_auth_contract.py`: nine checks of controller and middleware authentication contracts.

## Environment feasibility
- PHP CLI 8.4.24 available.
- `pdo_mysql`, `mysqli` and the MariaDB server are **not** available in the current container.
- Package installation attempt (`apt-get update`) timed out; no database installation was achieved.
- Hence clean MariaDB installation, HTTP administrator login and Cleanto booking/payment acceptance remain **NOT VERIFIED**.

## Tested
- PHP syntax validation of both modified application files and test stub.
- Five executable middleware scenarios passed.
- Nine contract checks passed.
- Cumulative source retention and ZIP integrity are checked at package time.

## Project scoreboard
- Current pass: 44.
- Business verticals: 50 registered.
- Website starters: 19 (15 cleaning, 4 other).
- Landing starters: 10.
- Partially editable starters: 29; fully browser-certified starters: 0.
- Fresh install, bookings and Stripe acceptance: not yet certified.

## Caution
- This test uses a stubbed Laravel facade for the middleware. A real browser-session test and database-backed authentication are still mandatory before production use.
