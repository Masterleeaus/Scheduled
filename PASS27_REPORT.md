# PlusAgency + Cleanto — Pass 27

## Focus
Mutation post-commit integrity for shared-order sibling bookings, pricing identity, order duration, staff assignments, and persisted status. Readback is performed through the independent acceptance connection.

## Changes
- 37 source/test files added or modified before manifests (12 new validation/integration PHP components, 24 focused tests, one runner modified).
- Original Cleanto files modified: 0.
- No Pass 26 entries removed.
- Full PHP regression scripts passed: 456/456.
- MySQL execution: NOT RUN; environment has no pdo_mysql driver or MySQL server.
- Booking writes remain disabled. Not production ready.

## New acceptance conditions
- Independently read the mutated booking after reschedule/cancellation.
- Detect modification or addition/removal of any sibling appointment sharing an order.
- Reject changes in stored order duration, booking customer/service/price identity, cancelled booking start time or staff assignment.
- Validate persisted reschedule/cancel transitions against Cleanto status codes.
- Bound sibling readbacks at 500; reject truncation and malformed records.

## Database note
These checks are source- and contract-tested but do not prove MySQL parity. The lifecycle runner remains CLI-only and restricted to disposable database settings.
