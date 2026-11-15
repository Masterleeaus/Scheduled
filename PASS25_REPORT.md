# Pass 25 — Verified Vertical Availability Integration

Implemented for Pawfy, Steven Watkins, Yoga/Pilates, and Carzone:

* A tenant-owner-only availability setting on the existing site editor.
* A **dual independently verified domain requirement**: the published tenant-owned website and booking instance must be verified for the exact same hostname; no public or admin-supplied booking-owner ID is accepted.
* Read-only live catalogue and appointment availability widget, reusing the original Cleanto public gateway, rate limits, pricing methods, units, staff and slot calculations. Service options and staff come from the real associated booking instance, not fabricated template data.
* Industry-specific service, staff, appointment, and location labels. The original appointment-request fallback remains available and clearly distinguishes a request from a confirmed reservation.
* Booking writes continue to require the separate existing global and per-tenant server-side release gates, which this pass does not change or enable.

**Important limitations:** Existing Cleanto catalogue fields and appointment/checkout mechanics remain cleaning-oriented internally. Each tenant must configure real services, methods, units, staff and pricing in Cleanto. No automatic mapping from website copy to bookable Cleanto service IDs is claimed. The site owner selects an independently verified matching custom hostname from a server-derived list (no raw booking-owner ID input). This also works when the administrator dashboard is hosted on a separate domain. External MariaDB, real slots, provider payment settlement and browser acceptance remain unverified. The current JS widget uses host-level endpoints and cannot be used on arbitrary shared-domain subpaths without an explicit site-to-instance gateway.
