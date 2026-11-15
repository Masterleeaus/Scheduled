# Pass 87 — Public availability to signed preview intent

## Functional change
The public booking widget now lets a visitor select an offered slot and explicitly validate it using the existing POST /titan-booking/intent route. The browser sends service, pricing method, unit, quantity, staff, duration, timezone and selected local date-time to the existing server-side catalogue and availability proof pipeline. A signed preview token is checked for expected fail-closed response flags, but is neither stored nor treated as a reservation.

## Non-negotiable limits
No booking is created, no payment is captured, and there is no persistent customer order. The runtime has PDO but no pdo_mysql driver, MariaDB server or Docker. Missing Pass 58 still prevents complete source-provenance certification. The browser-level flow and timezone/DST edge cases are not yet validated in a real browser. Do not enable public booking commits.

## Tests
Pass 87 contract assertions: 14. JS syntax: checked. ZIP CRC and exact delta: checked.

## Next
Run the full browser selection flow and actual MariaDB acceptance tests on an isolated capable host. Only after successful concurrent transaction, idempotency and payment webhook acceptance should a real checkout path be enabled.
