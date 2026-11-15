# Pass 68 — Stripe test-mode webhook reconciliation boundary

## New source (not production enabled)

- Added a signature validator on Stripe raw webhook bodies with a bounded timestamp and strict payload limits.
- Added explicit test-only PaymentIntent success parsing. Rejects live-mode payment events, incomplete payment intents, malformed identifiers and floating-point amounts.
- Added strict booking/tenant ID normalization, monetary minor-unit/currency validation, and authoritative persisted booking matching.
- Added replay comparison and a fail-closed release gate. Durable replay protection **requires** a unique-indexed receipt table and a transaction in the actual application. The current replay utility is not a substitute for database uniqueness.
- Added a verified event *candidate* reader. It does not alter the booking database, create payments, issue refunds, expose a public webhook route, or mark any booking paid.

## Validation performed

- `php integration/tests/pass68/webhook_test.php`: **30/30 assertions passed**.
- `php -l`: **11/11 new PHP source/test files passed**.
- Cumulative archive integrity: checked during packaging.
- Real MariaDB commit and two-customer concurrency: **NOT run**, unavailable `pdo_mysql`.
- Real provider webhook, checkout, refunds, paid booking: **NOT run**. No Stripe API credentials/environment configured.
- Missing Pass 58: **NOT recovered**. Base remains a recovery candidate.

## Next release gates

1. Recover/compare Pass 58 with the newer sources before certifying the cumulative master.
2. Establish real disposable MariaDB booking commits, rollback, idempotency, and concurrency evidence.
3. Bind metadata to a server-created payment intent from an authoritative, persisted booking/order; independently retrieve the PaymentIntent from Stripe in test mode.
4. Design and migrate unique provider event/payment receipt tables and reconcile with a single transaction; replay and conflict tests against the real DB.
5. Implement refunds, failures, pending/abandoned payments, customer confirmation ordering, and real browser payment E2E.

**Release status: not bookable, payment collection OFF, not production ready.**
