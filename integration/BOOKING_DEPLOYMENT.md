# Cleanto booking integration deployment

This pass wires authenticated business-owner booking list/get/create/cancel/reschedule to the private sidecar. It is **not certified** until live MySQL acceptance tests pass.

## Required runtime
* PHP PDO MySQL for the sidecar and PDO SQLite for the signed-request replay ledger.
* Sidecar must be reachable only by Laravel over authenticated TLS, outside public Cleanto web routes.
* `BOOKING_INTERNAL_ENDPOINT`, identical `BOOKING_INTERNAL_SECRET` on both servers, `BOOKING_TENANTS_FILE` (root-only JSON) and `BOOKING_NONCE_SQLITE` (writeable private file).
* Each tenant JSON record must contain an enabled instance key, valid MySQL DSN/login, `verified_pricing_settings` with six explicit `false` flags.
* Apply tenant-local Cleanto installer schema and additive `titan_booking_orders` and `titan_booking_requests` tables before enabling writes.
* Seed the titan order auto-increment above the largest existing order_id in both Cleanto booking/client tables. Validate old bookings for multi-hour overlaps and unexpected statuses.
* Do not enable `booking_writes_enabled`, `booking_schema_verified`, `booking_rate_mapping_verified`, `booking_concurrency_verified` until real MySQL smoke, concurrency and price-parity tests pass.
* Pass 13 accepts single one-time fixed-price bookings only. It does not take online payments or support recurring booking creation. Existing Cleanto system remains preserved for existing customers.

## Endpoint scope
All owner routes are under PlusAgency `/user` middleware `auth,userstatus,setlang`. The signed internal sidecar provides tenant isolation. Anonymous website bookings are not enabled; the builder form remains preview-only.

## Required acceptance tests
1. Login to tenant A, list its appointments, ensure no tenant B records return.
2. Select a service-price rule and confirm exact amount matches Cleanto checkout.
3. Create a booking, verify order/client/booking rows match original Cleanto expectations.
4. Retry same idempotency key and confirm no new booking; reject altered payload.
5. Run two simultaneous requests for the same cleaner and duration; exactly one may commit.
6. Cancel, rebook, and reschedule without allowing conflicting dates or orphaned client/order rows.
7. Confirm refund/payment/manual invoicing behavior separately.
