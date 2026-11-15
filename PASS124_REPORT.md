# Pass 124 — Cleanto location persistence across all booking writers

## Root cause and changes
`BookingCommand::create()` normalizes `personal_info`, and `BookingWriteProofRows` insists the persisted eight-field Cleanto information matches it. Two creation paths (`BookingAtomicPersist` and `BookingWriteCoordinator`) incorrectly passed only three arguments to `BookingLegacyClientWriter::insert()`, triggering its default empty location payload. As a result, bookings with a supplied service address would fail persistence proof and roll back; the shared writer already supplied the fourth argument.

Location normalization now also rejects NUL, TAB, LF and CR control characters rather than allowing them into serialized customer data. Both paths now explicitly forward `$cmd['personal_info']`. No table definitions, vendor files, customer-facing disclaimers, or payment behaviour changed.

## Verification
Run `php tests/pass124/cleanto_customer_location_persistence_test.php` and `php -l` on the three files. Test includes a PHP PDO fake that captures the exact SQL parameter bindings, decodes Cleanto's original eight-field serialized structure, checks the three production call sites, and tests rejection of invalid location data.

## Limits
No actual MariaDB runtime, PDO MySQL driver, or Docker is installed here. The test proves the writer's parameter mapping and call-site wiring, not live database commit, concurrent booking behaviour, real browser checkout, or payments. Missing historical Pass 58 source provenance also remains unresolved.
