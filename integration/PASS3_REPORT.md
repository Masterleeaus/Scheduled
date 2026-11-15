# Pass 03 — Legacy booking interval read adapter

## Done
- Original Cleanto source remains untouched; no booking writes exposed.
- Implemented read-only `busy_intervals` inside the signed, tenant-resolved sidecar.
- Read adapter uses original Cleanto `ct_bookings` and `ct_order_client_info` schema.
- Scopes busy lookup to staff IDs using legacy `staff_ids` CSV list and excludes cancelled/rejected states.
- Correct half-open overlap semantics; rejects missing/invalid duration rather than marking availability free.
- Added request validation, 8-day window cap, 30-day conservative lookback, row cap, timezone parsing and tests.
- Laravel client whitelists operation but does NOT expose a public route or booking writes.

## Important open questions
- `order_duration` units are assumed to be minutes by Cleanto's frontend; verify with seeded runtime data.
- Legacy `staff_ids` semantics and statuses must be validated against real Cleanto records.
- Original per-provider shift configuration (`week_id`, `weekday_id`) and schedule breaks are not yet integrated.
- The 30-day lookback is bounded; exceptionally long legacy reservations are not completely represented.
- Multiple rows per order may yield duplicate busy intervals; caller should not infer uniqueness.
- No staff authorization mapping yet for externally supplied staff identifiers, so do not expose this through public API.
- No PDO database driver locally: database SQL and concurrency tests are NOT verified.
- No booking creation, rescheduling, or payment connection is enabled.

## Next pass
Build staff ownership mapping and source-derived shift/break/off-time lookup; test using disposable MariaDB; then implement transaction-safe reservation with a business-domain service to create legacy-compatible booking records. Never enable public write endpoints without duration-overlap and concurrency checks.
