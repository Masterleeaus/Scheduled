# Pass 26 — Vertical booking profiles and industry terminology

## Delivered
- New verified catalogue-derived booking display profiles for all **50 verticals across 10 groups**.
- Overrides for pet care, vehicle detailing, training, yoga, tutoring, nutrition, beauty, outdoor, and property operations.
- Availability widget labels now use the site's recorded vertical: service, professional, units, duration, location and appointment type. The widget previously contained cleaning-only strings.
- Booking widget JavaScript status messages now use the vertical's professional term rather than “cleaner”.
- An admin-side booking profile panel explains required Cleanto configuration and avoids inventing pricing or available staff.
- Machine-readable 50-vertical capability catalogue and executable standalone source integration test.

## Safety and limitations
- **No change** to the reservation-write gates, payment reconciliation, tenant-domain verification or booking transaction semantics.
- Actual service/pricing/staff records must be configured in Cleanto; these are *not* generated or populated automatically.
- Live availability embedding still applies to the four imported themes and requires their verified site/booking domain binding.
- This is not a completed 50-vertical booking engine: full industry-specific service configuration and live MariaDB/browser acceptance remain outstanding.
- No original Pass 25 archive entries were deleted or replaced beyond the listed changed paths.

## Verification
- PHP syntax checks on changed PHP, Blade and test files passed.
- Node JavaScript syntax check passed.
- Standalone executable test passed for **50/50 vertical profiles**, **10/10 groups**, expected units and UI contracts.
- Both archives underwent CRC integrity checks and all prior paths were preserved.

## Template progress
- Website templates registered: **19** (15 cleaning-oriented + 4 newer variants).
- Landing page templates registered: **10**.
- Templates with some editing integration in source: **29**.
- Fully browser-verified templates: **0/29**.
- Original 18-pass plan remaining: **0**; development pass number **26**.
