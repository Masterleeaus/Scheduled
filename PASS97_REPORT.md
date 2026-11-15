# Pass 97 — Booking idempotency binds customer location to write identity

Baseline: Pass 96 cumulative recovery candidate (Pass 58 still missing).

## Source correction
The native public booking redemption gateway calculated the idempotency fingerprint from tenant, selection and contact identity, while the persisted command also carried customer address and notes. Therefore a request reusing an intent with modified location data could pass the fingerprint equality check and be treated as a replay of the earlier booking. The gateway now includes normalized, allowlisted Cleanto personal information in the fingerprint. Legacy empty personal information retains the exact old fingerprint, protecting older claims. Address and notes remain customer-provided data and never authorize tenant, pricing or staff selection.

## Tested
- 10 direct runtime assertions: legacy compatibility, changed address, changed notes, reordered keys, normalized whitespace, forbidden fields and oversized values.
- Existing Pass 34 and Pass 36 fingerprint regressions passed.
- Three changed or new PHP files passed syntax checks.
- All original archive members retained except two intentionally replaced PHP files. One new test added.
- Both ZIP CRC integrity verified.

## Blockers, not claimed complete
No MariaDB server, PDO MySQL driver or Docker available in this runtime. No actual transaction, two-worker database concurrency, browser checkout, Stripe settlement, or tenant isolation acceptance was executed. Pass 58 provenance is still unresolved. Public booking remains behind server-controlled release gates. This pass is not production-ready.
