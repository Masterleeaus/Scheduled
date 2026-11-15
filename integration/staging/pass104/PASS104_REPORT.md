# Pass 104 — Genuine Cleanto installer-schema recovery

Source: `private/cleanto/class_configure.php` from Pass 103 cumulative ZIP.

## What was recovered

- Extracted 36 unique literal `CREATE TABLE` declarations from the native Cleanto installer.
- Original installer source SHA-256: `ec69004d6f364c3037bb771a86f4e3396fd10b07f6b679e1ff56232c78266ce9`.
- Recovered core tables: ct_bookings, ct_order_client_info, ct_services, ct_services_methods_units_rate, ct_payments, ct_users.
- Missing from original installer DDL (separate integration schema required): titan_booking_intent_claims.
- The original installer also includes ALTER/update logic and dynamically executed encoded code, which the extraction intentionally does NOT execute or reconstitute.
- Existing PlusAgency `installer/database.sql` defines PlusAgency tables and is **not** the Cleanto schema.

## Acceptance status

Table DDL recovered, source comparison performed, test assertions run. No MariaDB server, docker or `pdo_mysql` is available in this session. No schema import, transactional booking commit, concurrency test or browser acceptance succeeded. Missing Pass 58 remains unresolved. This is a source recovery checkpoint, not release certification.
