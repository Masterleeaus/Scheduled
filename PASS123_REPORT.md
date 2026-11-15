# Pass 123 — Real booking recovery identity handoff and date readback

## Actual changes
1. Fixed the production HTTP recovery-controller callback dropping the trusted customer identity argument. The Pass 122 identity verification had existed in the reader but was bypassed by the controller's four-argument closure. The controller now forwards the mapped identity into the reader.
2. Removed the recovery reader's optional empty identity default. Identity is mandatory and rejected if name/email/phone are missing or empty. The persisted Cleanto `ct_order_client_info` row must match all three.
3. Recovery also reads `ct_bookings.order_date` and requires it to equal the intent-selected appointment's date, mirroring the writer's persisted-row proof.
4. Extended the executable PDO-stub reader test to 48 assertions and added six source-wiring guard assertions against the actual controller and reader implementation.

## Verified execution
- PHP: Pass 121 native recovery: 48 assertions passed (PDO stub, not MariaDB).
- PHP: Pass 119 recovery: 9 assertions passed.
- PHP: Pass 123 controller/reader wiring guard: 6 assertions passed.
- Syntax: changed PHP files passed `php -l`.
- ZIP CRC validation: both cumulative and delta archives passed `testzip()`.

## Unresolved blocking facts
- Real MySQL/MariaDB acceptance, reservation persistence and concurrency remain **UNEXECUTED**, because this environment has no MariaDB server, Docker or PHP `pdo_mysql`.
- Full browser acceptance and payment reconciliation have not been demonstrated.
- Pass 58 source provenance remains missing. This package is **not production launch approved**.

## Packaging
Cumulative ZIP = previous Pass 122 full archive with these exact changes applied; delta ZIP = changed/new files plus this report and manifest. Original Cleanto and PlusAgency code is otherwise retained.
