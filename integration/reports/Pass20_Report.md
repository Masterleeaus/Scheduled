# Pass 20: disposable booking lifecycle acceptance

- **46 source/test/documentation files** changed: 46 created, 0 modified.
- **305/305 PHP regression scripts passed** using the complete cumulative source tree.
- **44/44 changed PHP files passed syntax lint.**
- Live MySQL lifecycle: **SKIPPED**, because `pdo_mysql` is not installed in this runtime. No appointment created.
- Original Cleanto vendor files modified: **0**.
- Production readiness: **NO**.

This pass adds a CLI-only, explicitly authorised disposable-database acceptance runner covering real appointment creation, idempotent replay, sequential overlap rejection, rescheduling and cancellation. It validates source fixture shape and database scope before writes, creates unique request keys, reports evidence, and fails closed. It does **not** validate simultaneous booking concurrency, payment settlement or production readiness. Do not run against customer data.

Run instructions: `integration/mysql/lifecycle/README.md`.
