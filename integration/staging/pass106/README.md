# Pass 106 — Schema-grounded disposable fixture review bundle

This pass adds a deterministic generator of **review-only** Cleanto scheduling rows and two independent synthetic booking fixtures, based on the recovered native installer table inventory and Pass 105 five-row seed. It does not certify the installed Cleanto schema, available times, or a real booking.

## Reproduce

```bash
python3 integration/staging/pass106/test_pass106.py
python3 integration/staging/pass106/build_review_bundle.py integration/staging/pass104/schema_inventory.json integration/staging/pass105/synthetic-seed.REVIEW.sql /tmp/cleanto-pass106-review
```

The generated directory contains `seed-and-schedule.REVIEW.sql`, `lifecycle.json`, `concurrency.json`, and `REVIEW_STATUS.json`.

**Do not supply this generated SQL directly to the Pass 101 staging runner without review.** The native installer contains 36 table definitions and separate ALTER operations; previous upgrades may overlap. The `week_id`, `weekday_id`, and schedule-mode semantics must be independently checked against the Cleanto availability implementation. The Pass 05 and Pass 12 ledger definitions are structurally incompatible and must not both be applied; the canonical ledger should be identified against the actual integration writer before database import. Genuine MariaDB execution and independent order/booking readback are still required.

No production database is accessed and no customer-facing flags are enabled. Pass 58 provenance remains unresolved.
