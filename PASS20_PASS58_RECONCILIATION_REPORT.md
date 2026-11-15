# Pass 20 — Pass 58 provenance / functional recovery

## Evidence
The historical conversation records Pass 58 as a concurrency readback enhancement verifying the Cleanto appointment, order, customer linkage, duration, service details and reject-ambiguous-reads semantics. It reportedly changed 35 substantive files and passed 1,115/1,115 PHP regression scripts. However **no original Pass 58 source archive or manifest was recovered**. Pass 59's manifest names `PlusAgency_Cleanto_Master_Pass58.zip` as its baseline, but does not reproduce that baseline. Original code hashes and exact 35-file set are unavailable. Consequently **historical provenance is unresolved; functional coverage has been reconstructed and tested**.

## Inspectable current code
Pass 19 already included `ConcurrencyBookingReadback`, `LifecycleFullBookingReader`, `LifecycleClientOrderReader`, exact-one row query helpers, `LifecycleFullEvidence` (linkage, duration, service, customer), and concurrency overlap checks. The concurrency winner verification path used `ConcurrencyDatabaseEvidence`, which only read booking id/service/start/staff/status. It **did not enforce full order/customer/duration/method verification** in the concurrency success path even though lifecycle verification had those components.

## Changes
- New `ConcurrencyFullReadbackEvidence` performs independent full appointment and order readback on the pre-existing fresh PDO acceptance connection, delegating SQL exact-one checks to existing readers; verifies appointment winner, service, staff, date, active status, order relationship, duration, method, quantity and customer fingerprint. Returns a PII-free structured proof with payment unverified.
- `ConcurrencyDatabaseEvidence` now requires full proof before reporting a committed winner; original lightweight verification remains as an additional check.
- `LifecycleServiceEvidence` now understands the actual `BookingPriceInput` command key `quantity`, as well as legacy `method_unit_qty`, and rejects conflicting supplied values. Previously valid creation fixtures with `quantity` would fail its acceptance check.
- Added a PHP source-level regression test with 21 positive and negative scenarios (including wrong booking/order/customer, duration, service/method/quantity, staff, time and status).

## Verification
- Four changed PHP files: `php -l` passed.
- `integration/tests/pass58_recovery/run.php`: 21 scenarios passed.
- Existing Pass 59 + Pass 60 standalone tests: 53/53 passed.
- All original Pass 19 paths retained, ZIP CRC checks passed.
- Original Cleanto files untouched; public booking-write release guards unchanged.

## Important limitations
- No MariaDB driver or disposable database was available. Cannot verify actual DB queries, concurrent booking commits, or regression involving running Laravel.
- Does **not** claim byte-for-byte restoration of the historical Pass 58 ZIP or certified provenance.
- Does not verify Stripe payment capture/settlement, all templates/editing, or production deployment.

## Remaining acceptance
Run disposable MariaDB concurrency acceptance with genuine original Cleanto schema and fixtures; validate independent customer/order/appointment reads, failure handling, rollback, and isolation. Retain deployment fail-closed until verified.
