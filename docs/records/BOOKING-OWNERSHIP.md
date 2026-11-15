# Booking ownership: Scheduled and Supercharged

Status: decided by the owner. Refs issue #11. Referenced by issue #7 and PR for issue #7.

## Decision

Scheduled owns:

- booking records (the Cleanto booking, order and request data in `private/cleanto/` and `private/booking-sidecar/`),
- reminders for bookings,
- calendar sync for bookings.

Supercharged does not own booking records, booking reminders or calendar sync for this product line.
This repository does not change Supercharged.

## Context

- Supercharged's README lists "Appointment Setting" ("automate meeting scheduling and confirmations").
  That overlaps with Scheduled's booking capabilities.
- The decision above resolves the overlap in favour of Scheduled for the three named capabilities.

## Migration path if the two overlap

- Supercharged appointment features that create or confirm bookings for the same customers
  should move to Scheduled's booking records, or call Scheduled's booking interface.
  This is not started. It needs a separate issue and owner review.
- Supercharged's existing data is not moved by this record.

## Not done here

- Central product registry entry (Masterleeaus/Central#29): not changed by this repository. Owner action in Central.
- No code changes. No change to Supercharged.
