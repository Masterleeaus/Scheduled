# Pass 99 — Canonical public phone to native Cleanto boundary

## Scope
Repaired customer phone validation so the public form's formatted numbers are stored as digit-only numbers with an optional leading `+`, within the original native Cleanto `15`-character booking field. No phone digits are silently truncated. The original checkout route, tenant authorization, pricing, database and payment gates are unchanged.

## Release gates
- Live MariaDB commit/readback and concurrent customer booking: **not run**; PHP environment lacks `pdo_mysql` and a MariaDB server.
- Browser checkout/payment settlement: **not run**.
- Pass 58 history: **still missing**; do not promote this archive to verified Master Copy.

## Coverage
The new runtime test checks formatted Australian and international numbers, canonicalization, original native field width, rejected overflow and invalid characters, and handoff into native customer identity policy. This does not certify real phone ownership or E.164 validity.
