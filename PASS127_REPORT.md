# Pass 127 — Booking replay payment safety

## Production defect
The owner booking API wrapped the booking coordinator output using BookingPaymentBoundary / BookingWriteResponseGuard. That guard unconditionally marked every persisted response as unpaid and required payment processing, even after a verified idempotent replay. Existing bookings could have been settled since creation. The prior response therefore claimed an unsupported payment state and could trigger duplicate collection.

## Changes
- Replay responses now always have `payment_status=unknown`, `requires_payment_processing=false`, and `payment_reconciliation_required=true`; this is not a payment-status lookup and does not certify settlement.
- First-time booking creation remains `payment_status=unpaid` and `requires_payment_processing=true` with no settlement claim.
- The response guard fails closed if booking/order IDs or boolean replay evidence are missing or invalid; it never accepts caller-supplied payment flags as proof.
- Updated the historical Pass 13 / Pass 15 payment regressions and added 16 explicit Pass 127 assertions.

## Execution result
- Pass 127 payment assertions: 16/16 passed.
- Pass 13 payment boundary: passed.
- Pass 15 response rejection: passed.
- Pass 15 payment state: passed.
- PHP syntax for all changed PHP files: passed.
- ZIP CRC integrity: both archives verified.

## Remaining launch blockers
The runtime has PHP PDO but no pdo_mysql, mysqld/mariadb or Docker. Neither a live MariaDB booking transaction nor a real payment processor reconciliation was executed. Historical Pass 58 provenance is still unresolved, and full browser / concurrency acceptance remains incomplete. Payment processing of existing bookings must be preceded by an authoritative transaction/settlement lookup, not merely this replay response.
