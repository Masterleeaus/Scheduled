# PlusAgency + Cleanto — Pass 126 development report

## Implemented
- Corrected `BookingWriteCoordinator` idempotent replay to include the original Cleanto `order_id`, matching the newly committed booking response's order identifier.
- `BookingCreationReplayProof::verifiedOrderId` validates the *locked, persisted* booking and customer order first, then returns the order ID from the verified booking row, not a client-supplied value.
- Added an executable PDO-compatible row-result regression test that covers accepted replay, mismatched order links and customer records, altered date/status/staff and corrupted encoded location information.

## Verification performed
- PHP syntax validation: all 3 changed/new PHP files passed.
- Executable PHP replay evidence test: 10/10 assertions passed with a PDO result double.
- Archive CRC integrity: both master and delta verified by Python `ZipFile.testzip`.
- Exact-file SHA-256 manifest included in delta and master.

## Important limitations
- No MariaDB engine, MySQL PDO extension, or Docker is available in the current container. Repository DNS resolution also failed. No database commit, real concurrency acceptance, or browser checkout was executed.
- Replay intentionally does not claim an `unpaid` payment status; an existing booking may have been paid since creation. This pass only supplies its verified order ID.
- The missing Pass 58 historical provenance remains unresolved. Production launch is NOT certified.
