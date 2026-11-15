# Pass 31 — Native staff service eligibility and schedule inspection

## Delivered
- Native owner-authenticated staff overview reads actual `ct_users.service_ids` and `ct_week_days_available` records through the signed Cleanto sidecar.
- Disabled-service-only staff eligibility add/remove operation, limited to tenant-approved `staff_management_enabled` and `service_management_enabled` flags. Preserves unrelated staff assignments and verifies transaction readback.
- Staff overview and assignment forms in the site's Cleanto services admin screen. No editing of hours, active services, time-off, breaks, or existing bookings.
- Source-level operation allowlist wiring and owner route with tenant domain validation.

## Tests and constraints
- Staff parser and input validation regression: 17 / 17 checks passed (PHP CLI).
- PHP syntax: six changed/new PHP files passed.
- ZIP CRC/integrity and comparison to Pass 30: passed; 4 added, 5 modified, 0 removed.
- Live MariaDB: not available (`pdo_mysql` missing). No runtime acceptance for service writes or schedules.
- Browser/test hosting: not available. Do not interpret the source-level checks as confirmed booking, payments or installation readiness.
- The current assignment editor intentionally refuses to mutate active-service eligibility, and no schedules are writable.

## Development scoreboard
- Pass: 31
- Business verticals: 50
- Website templates registered: 19 (15 cleaning + 4 new)
- Landing templates: 10
- Partially editable: 29
- Fully browser-verified: 0 / 29
- Confirmed booking/payment/live installation: outstanding
