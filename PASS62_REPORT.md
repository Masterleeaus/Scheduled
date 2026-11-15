# PlusAgency + Cleanto Pass 62 — Independent lifecycle table footprints

- Baseline: Pass 61 cumulative master.
- Source changes: 39 added and 1 modified, no deleted files.
- Original Cleanto files modified: 0.
- Executable PHP regression scripts: **1,238/1,238 passed**.
- PHP syntax: **40/40 changed PHP files passed**.
- Live MySQL transactions performed: **0**. Docker, MySQL server and PHP MySQL driver unavailable.
- Public booking writes: disabled. Production-ready: no.

## Functional change

`LifecycleRunner` now obtains committed, read-only independent counts of `ct_bookings` and `ct_order_client_info` from the second acceptance connection. It demands exactly one additional booking and one additional order after the first successful creation and no additional rows after idempotent replay, rejected conflicting booking, rescheduling or cancellation. Rejects failed SQL statements, ambiguous aggregates, invalid values, overflows, and attempts to read proof inside an active transaction. Reports verified lifecycle stages without customer data.

## Scope of evidence

This proves acceptance-runner assertion behavior using test doubles; it does NOT demonstrate actual successful MariaDB booking writes or concurrent transaction safety. The added assertions must be executed on the disposable MariaDB environment prior to enabling public booking creation.
