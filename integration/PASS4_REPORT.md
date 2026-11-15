# Pass 04 — Staff availability integration (read-only)

## Implemented
- Cleanto original sources preserved; no modifications under `private/cleanto/`.
- Staff weekly/monthly shift selectors, scheduled breaks, off-time records, explicit off-day safeguards, duration-aware booked intervals, interval subtraction, candidate slots.
- Private `availability` operation enabled with HMAC/replay checks inherited from existing sidecar.
- Tenant database is chosen only server-side; Laravel operation remains internal, no unauthorised public route enabled.

## Safety gates / known constraints
- Availability read-only. No appointment writes, payment capture, booking confirmations or public calendar route.
- Day-off status is conservatively interpreted as blocked when a row exists; legacy status semantics must be confirmed before production.
- `weekday_id` Sunday=1 and monthly `week_id` day-of-month bucket are inferred from bundled Cleanto scheduling source and need seeded data validation.
- Overnights are rejected rather than incorrectly treated as available; DST transition days require dedicated integration tests.
- Original conflict reader has a 30-day lookback and status filter; live database verification pending.
- No MySQL PDO driver available in environment: unit/contract tests do not prove database or full runtime compatibility.
