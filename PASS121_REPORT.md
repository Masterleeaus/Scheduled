# Pass 121 — Native Cleanto booking recovery correction

## Scope
Correct the read-only recovery reader's order proof to query authentic `ct_order_client_info` instead of nonexistent `ct_orders`. Keep claim and appointment identity verification; distinguish current booking states using Cleanto codes A/C, CO, RS, CC/CS, R and MN. The public recovery widget displays confirmed, completed, rescheduled, cancelled, rejected and no-show records without claiming that a non-confirmed reservation is confirmed. Status recovery remains read-only.

## Files
Two recovery PHP implementation files, one public widget JS file, one updated Pass 119 test and one new Pass 121 PHP regression. The Pass 120 JS contract test was inspected and found byte-identical, so it was excluded from the exact delta. Details and SHA-256 hashes are in `PASS121_MANIFEST.json`.

## Tests completed
- `pass121_recovery_native_schema_test.php`: 36 assertions passed (PDO test doubles; no live database).
- `pass119_recovery_test.php`: 9 assertions passed.
- `pass120_recovery_widget_test.js`: 12 assertions passed.
- PHP syntax for changed PHP files: passed.
- JavaScript syntax for changed widget: passed.
- ZIP CRC/integrity: passed.

## Release blockers
No real MariaDB transaction or browser end-to-end booking was run. Missing Pass 58 provenance, unverified payment reconciliation and tenant-wide release acceptance remain unresolved. The package is a recovery candidate, not a launch release.
