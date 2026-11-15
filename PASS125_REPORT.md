# PlusAgency + Cleanto Pass 125 — Recovery ownership and service-location integrity

## Actual source corrections
- Recovery SQL now identifies the Cleanto customer-order row using **both** `ct_bookings.client_id` and `ct_bookings.order_id`, rather than matching only order number. It selects the order client primary key and refuses mismatched row linkage. This follows the native booking writer's proof contract.
- Recovery corroborates the original Cleanto `client_personal_info` string against the **validated customer service address, suburb/city, state, postcode and notes**. Comparison uses existing canonical `BookingPersonalInfo::encode`, without PHP unserialization of a database value.
- `CheckoutRecovery::lookup` forwards validated personal information through the actual HTTP controller to the read-only persistence reader.
- Existing Pass 121 and Pass 123 tests updated and expanded with adversarial mismatched-link, tampered-address, and positive-address cases.

## Verified results
- Pass 121 native recovery: **58 assertions passed**.
- Pass 123 HTTP controller source wiring: **6 assertions passed**.
- Pass 119 base recovery: **9 assertions passed**.
- All five modified PHP files: syntax passes.
- ZIP file integrity and delta manifest verified during packaging.

## Release blockers
- **Not production-ready**. The execution environment has no MariaDB server, Docker, or PDO MySQL driver; actual booking commit and concurrent booking acceptance have not run.
- Payment reconciliation and full-browser checkout remain unverified. Missing historical Pass 58 provenance has not been recovered.
- This pass strengthens recovery evidence, but it does not itself demonstrate any live database commit.
