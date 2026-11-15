# Pass 122 — Booking recovery order-customer identity proof

## Change
The read-only recovery path already required a committed intent claim, a matching booking, and an order linked by `order_id`. It did not require the stored `ct_order_client_info` name/email/phone to equal the customer's validated booking identity. Pass 122 forwards the trusted normalized customer identity to the reader, and rejects missing or inconsistent persisted customer fields. Failed proof yields an unavailable response, never confirmation.

## Tests
- Pass 121 PDO-stub native recovery test: 42 assertions, including matching identity, wrong name/email/phone, missing phone, and forwarding the normalized identity.
- Pass 119 recovery regression test: 9 assertions.
- PHP syntax checks: 3/3 changed PHP files.
- ZIP integrity checks: must pass for master and delta.

## Limitations
No real MariaDB booking, order readback, or browser test was executed. The PHP runtime lacks pdo_mysql; missing Pass 58 provenance remains unresolved. This remains a recovery candidate, not a production release.
