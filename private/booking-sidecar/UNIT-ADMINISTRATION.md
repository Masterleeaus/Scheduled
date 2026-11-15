# Cleanto native unit administration

New internal signed operations: `owner_service_units` (no payload) and `owner_service_unit_update` (JSON payload with integer `unit_id`, decimal-string `base_price`, integer `duration_minutes`). Requires dual explicit tenant flags, named MySQL tenant DB, and verified company ownership in PlusAgency controller.

Native writes are restricted to disabled services, units never used in `ct_bookings`, and row-locking/transactional readback. Does not edit rate tiers, pricing rules, taxes, staff shifts, or confirmed bookings. Enable only after staging DB acceptance.
