# PlusAgency + Cleanto — Pass 117

## Scope

Baseline: Pass 116 cumulative recovery candidate. This pass repairs the *native persisted booking proof*, not payment processing or public booking activation.

## Finding and production repair

`BookingLegacyAppointmentWriter` inserts both `ct_bookings.order_date` and `booking_date_time`, but `BookingWriteProofReader` selected only the appointment timestamp for verification. Thus a corrupted `order_date` could escape post-write/replay validation. Pass 117 now selects `order_date` and validates it is the same calendar day as the normalized local appointment start, strictly formatted YYYY-MM-DD and calendar-valid. A missing or contradictory date now fails closed before booking success is accepted.

## Executed tests

- Pass 117 order-date unit and source-wiring test: 13 assertions PASS.
- Pass 117 full persisted-row proof with booking, order, personal data: 7 assertions PASS.
- Pass 115 location readback regression: 14 assertions PASS.
- PHP syntax for five changed/new PHP files: PASS.
- ZIP CRC integrity for delta and cumulative: PASS.
- Actual MariaDB booking commit, rollback, concurrency and browser acceptance: NOT EXECUTED. `pdo_mysql`, MariaDB and Docker unavailable in this environment.

## Outstanding blockers

- Pass 58 cumulative provenance missing; archive remains a recovery candidate.
- Live schema import and real transaction/readback not performed.
- Payment webhook reconciliation and browser end-to-end checkout unverified.
- Do not enable public writes or payment capture from these test results.

## Exact change scope

Two production PHP files modified; three PHP files added (one guard, two tests), plus this report and JSON manifest. Unrelated original application and imported website templates retained verbatim.
