# Pass 61 — bounded acceptance readbacks and persisted-identity safety

Baseline: Pass 60 verified cumulative ZIP.

## Implemented

- Centralized checked, bounded, exactly-one-row SQL acceptance reads in `AcceptanceReadbackSql.php`.
- The committed-booking reader now checks prepare/execute/fetch, rejects missing/duplicate rows, bounds the query and rejects overflow identifiers.
- Full booking, customer-order and concurrent-winner readers use the shared checked readback primitive.
- Separate-session verification rejects malformed connection identities.
- Booking creation, idempotent replay and mutation-result identities enforce signed-32-bit Cleanto limits.
- Added 24 distinct executable acceptance regression scenarios, plus shared PDO statement test fixtures.

## Validation

- Full recursively discovered PHP tests: 1208 passed, 0 failed (excluding shared non-executable fixture support.php).
- Changed/added PHP lint: 33 passed, 0 failed.
- Substantive PHP files added/modified: 32 (plus one shared test fixture).
- Old entries preserved unchanged: 22152; modified: 7; added before report/manifest: 26; deleted: 0.
- Original Cleanto source modifications: 0.
- Live MySQL booking transactions: 0 (no local `pdo_mysql`, MariaDB or Docker runtime).
- Public booking writes: remain disabled; production readiness not established.
