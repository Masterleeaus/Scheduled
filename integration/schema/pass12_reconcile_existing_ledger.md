# Ledger migration safety

The prior `pass05_reservation_ledger.mysql.sql` uses `command_hash` / `status`, but the actual booking writer uses `fingerprint` / `state`. Do **not** run the old schema on a new deployment. Before migrating, back up the tenant database and inspect `information_schema.COLUMNS` for `titan_booking_requests`.

For a fresh database, review and apply `pass12_ledger_canonical.mysql.sql`. For existing rows, stop and perform an explicit data-preserving migration; this change intentionally does **not** silently rename or discard columns. Run the read-only CLI audit against the tenant before enabling mutations. MySQL transactional and concurrency tests remain mandatory.
