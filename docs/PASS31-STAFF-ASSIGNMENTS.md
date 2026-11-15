# Pass 31 — native Cleanto staff eligibility and read-only schedules

- Uses the original `ct_users.service_ids` CSV, `ct_services.status` and `ct_week_days_available` rows.
- Does not create a shadow staff or schedule database.
- A gated, tenant-specific `owner_staff_service_assignment` operation changes only membership of an **existing disabled** service in an enabled staff member's original `service_ids` record. Transactions, row locks and readback are required.
- A separate signed owner-only `owner_staff_overview` operation displays current staff and native weekly shifts. No working-hours writes are exposed.
- Requires `service_management_enabled=true` **and** `staff_management_enabled=true` for the selected verified MySQL tenant, and the existing verified website/booking domain, owner permissions and signed sidecar connection.
- The current native `service_ids` encoding was inspected in `private/cleanto/objects/class_adminprofile.php`, where Cleanto writes `ct_users.service_ids`; source-level validation is not a substitute for live DB acceptance.
- Existing operational schedules may involve breaks, off-days and staff overrides beyond weekly shifts. These remain governed by the native booking engine, not this administration screen.
- The real-time slot engine and release gates are unchanged. This pass does **not** certify live bookings, working-hour writes or Stripe payments.
