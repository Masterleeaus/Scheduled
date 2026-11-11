# Pass 56 — First-install database identity protection

## Verified defect and fix
The installer explicitly connects to MySQL twice: Laravel PDO for feature migrations and mysqli for the legacy SQL import. Existing settings such as `DATABASE_URL`, `unix_socket`, and read/write replicas could redirect the PDO connection independently. Both connections previously passed independent connection checks without comparing the database *server* identity. This can split fresh-install schema writes across servers.

Pass 56 removes the inherited endpoint overrides from the temporary installer PDO configuration and checks the database name, MySQL server hostname, server port and server ID on both live connections **before** executing any legacy SQL or migrations. It fails closed on missing or mismatched identity. This is not a cryptographic server attestation; duplicate server configurations remain a theoretical limitation.

## Testing
The standalone PHP regression checks identity equality, name/hostname/port/server-ID mismatches, missing values and source ordering; the changed PHP files pass syntax validation. Both archives passed complete ZIP CRC integrity checks. All existing Pass 55 paths are preserved. Live MariaDB/MySQLi and browser installation remain unavailable in this environment and are **not certified**.

## Scoreboard
- Development pass: 56
- Business verticals: 50
- Cleaning website starters: 15
- Other website starters: 4
- Landing templates: 10
- Fully browser-certified templates: 0/29
- Live clean-install, booking and Stripe acceptance: outstanding
