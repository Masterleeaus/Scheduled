# PlusAgency + Cleanto — Pass 29

## Functional changes
- Added server-validated catalogue snapshot, service/method/unit/staff relations, strict IDs and labels, duplicate and size limits, tenant-scope validation, selection checks and HTML-safe JSON.
- Preserved the vendor Cleanto source and retained PlusAgency as master.
- Hardened the existing widget by validating same-origin relative preview endpoints and preventing duplicate initialization.
- Clarified the existing free-text administrative preview as a demonstration rather than pretending it is a live public booking form.
- No anonymous public booking API or appointment write was enabled.

## Verification
- 497 of 497 existing and new executable integration PHP test scripts passed.
- 21 focused Pass 29 tests passed.
- All modified and new PHP linted successfully; unchanged archive entries verified by size and CRC.
- Both ZIP archives tested for integrity.
- MySQL transactions remain untested: no local pdo_mysql or MySQL server.

## Release blockers
- Connect authoritative per-tenant catalogue read endpoint with correct domain-based tenancy and authorization.
- Replace raw administrative IDs in customer widget with actual catalogue-derived choices.
- Run Cleanto booking lifecycle including parallel reservations, pricing parity, payments, recurring jobs, cancellation and rescheduling against isolated MySQL.
- Production readiness: NO.
