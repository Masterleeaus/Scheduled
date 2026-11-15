# PlusAgency + Cleanto — Pass 46

## Scope
Focus: active legacy booking transaction failure handling and advisory-lock lifetime protection. Three existing implementation files modified; 21 test files added (one shared bootstrap plus 20 standalone executable scenarios). Cleanto original files unchanged.

## Corrections
- `BookingTransaction` now fails closed when `beginTransaction()` or `commit()` returns false; it also detects unexpected transaction closure, handles rollback-false/exception, and preserves the underlying error cause.
- `BookingSharedLockLease` rejects release while the booking transaction remains active, rejects acquisition inside a transaction, and requires the exact injected PDO connection.
- `BookingMutationLockBoundary` rejects acquiring locks after a transaction begins and detects callback transaction leaks. It deliberately retains locks on a leaked transaction rather than releasing them before commit. An abandoned connection must be closed/recovered by its owner; this guard is not itself an automatic connection cleanup mechanism.

## Verification
- PHP regression scripts: **797/797 passed**, including **20 new pass-46 scenarios**. Test bootstrap files excluded from executable test count.
- PHP lint: all 24 changed PHP files passed syntax checks.
- ZIP CRC integrity and prior-master unchanged entry checksum comparison: verified.
- Live MySQL/PDO transactions and concurrent appointments: **not run** (no `pdo_mysql`, MySQL server, or Docker in this environment).
- Customer booking confirmation: remains disabled; production readiness: **No**.

## Notes
Unit tests use a PDO test double and do not constitute evidence of successful real MySQL transactions. No customer secrets or live database credentials are bundled in this pass.
