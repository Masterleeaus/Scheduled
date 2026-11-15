# Pass 32 — restricted native weekly scheduling

Existing Cleanto weekly shift rows may now be updated through a signed tenant-selected sidecar command and owner-only PlusAgency form. No parallel scheduling database is introduced.

## Safety boundaries

- Independent `schedule_management_enabled=true`, plus the existing `staff_management_enabled` and `service_management_enabled` gates.
- Requires MySQL with native prepared statements and exact tenant database identity.
- Staff account must be **disabled** (`ct_users.status = D`); prevents changing a working professional whose availability is public.
- Existing weekly row only (`week_id=1`, `provider_schedule_type=weekly`); no reconstruction or changes to monthly schedules.
- Reject staff with *any* Cleanto booking history, existing breaks, or time-off rows. This blocks changes to booked or complex calendars.
- Strict 5-minute HH:MM:00 times and no overnight intervals.
- Row locking, transaction, native write and strict database readback. No booking gates or staff activation changed.
- If the native Cleanto schema does not match, the update fails closed.

## Missing operational acceptance

Not tested against a live MariaDB/Cleanto tenant; PHP mysql driver is unavailable in this local environment. Operations disabled by default. No functionality to edit active schedules, weekly breaks, time-off or monthly rotations is claimed. Production booking availability and Stripe acceptance remain outstanding.
