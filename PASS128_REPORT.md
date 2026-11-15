# PlusAgency + Cleanto — Pass 128

## Implementation
- Added read-only `CheckoutPaymentRecord` query against canonical Cleanto `ct_payments` filtered by the persisted order ID.
- Exposed the classified legacy payment record status through verified booking recovery.
- Preserved fail-closed booking/customer/address evidence checks.
- Distinguished Cleanto internal `Completed` records from provider-confirmed settlement; `payment_settled` remains false.
- Updated compatibility tests for the recovery reader and booking customer serialization.

## Verification
- PHP Pass 128 payment record executable: 25 assertions passed.
- PHP native booking recovery regression: 64 assertions passed.
- PHP Pass 119 recovery: 9 assertions passed.
- PHP syntax passed for all changed PHP files.
- ct_payments columns checked against native Cleanto recovered installer schema.
- ZIP archives tested with full CRC checks.

## Unverified / release blockers
- No MariaDB server or PHP pdo_mysql extension in current container: no live readback / booking transaction / race tests.
- No payment-provider settlement reconciliation: native record is not authoritative settlement evidence.
- Browser checkout not executed.
- Missing historical Pass 58 source provenance.
- Do not label the application production-ready.
