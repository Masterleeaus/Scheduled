# Pass 130 — Cleanto payment evidence precision and date verification

Source: Pass 129 recovery candidate. No MariaDB or pdo_mysql available; live booking and payment-provider reconciliation were **not** executed.

## Application repair
The read-only Cleanto `ct_payments` query now retrieves `payment_date`. An accounting row is marked `ambiguous` when the payment date is not a real ISO date, or when gross/net amounts are not positive conventional currency values with at most two fractional digits. This closes the former `is_numeric()` allowance for scientific notation, excess precision, and inconsistent date evidence. Both `Completed` and `Pending` retain their existing semantics for valid rows. Legacy Cleanto's `DOUBLE` storage is preserved, rather than migrated here. No `ct_payments` value can assert provider settlement or authorize a charge.

## Executed checks
- PHP lint: all four changed/new PHP files passed.
- Existing Pass 128 and Pass 129 payment regression suites passed.
- New Pass 130 edge-case assertions passed.
- Full and delta ZIP CRC verification passed during packaging.

## Unresolved critical gates
- MariaDB server and PHP pdo_mysql unavailable in this execution environment.
- Real appointment/order transaction, concurrent reservation, browser checkout, payment-provider settlement, and missing Pass 58 provenance remain unverified.
- This change is a narrow payment-evidence repair, **not** a claim of production readiness.
