# Scheduled — booking and website-builder subsystem guide

**Architecture guide, 18 November 2026.** The following describes intended capabilities and verification gates, not a certified production implementation. Start with [Scheduled README](../README.md), [integration guide](../integration/README.md) and [Titan Zero integration handbook](https://github.com/Masterleeaus/Central/blob/main/docs/INTEGRATION_HANDBOOK.md).

## Two product surfaces, one application target

**Booking/calendar:** services, staff, resources, locations, availability, holds, customer booking, rescheduling, cancellation, payments and events. **Website/landing-page builder:** business sites, page templates, editing, publishing, leads and forms. Both belong to Scheduled's intended integrated Laravel application. Imported PlusAgency and Cleanto code is source material requiring controlled migration; do not deploy an independent Cleanto sidecar as a second booking writer.

## Booking lifecycle and authority

| Step | Responsible system | Verification |
| --- | --- | --- |
| Service and staff configuration | Scheduled | Company scoping, permissions, schedule/timezone rules |
| Availability search | Scheduled | DST, working hours, exclusions, staff/resource capacity |
| Price/deposit quote | Scheduled deterministic rules | Tax, currency, discounts, provider constraints |
| Booking commitment | Scheduled transaction | Atomic last-slot exclusion, idempotent replay, stable booking ID |
| Changes and cancellation | Scheduled | Version/state checks, notification, refunds and reconciliation |
| Job creation | Sorted consumer | Signed/versioned booking event; duplicate-safe job intake |
| Conversation | Front Desk/Supercharged | Approved tools only; no independent calendar write |

Front Desk has a standalone local-booking mode. Switching to Scheduled as integrated authority requires an explicit cutover and reconciliation; never run both booking writers for the same tenant concurrently.

## Website and landing-page builder

Document and test template catalogue (target: **at least ten usable templates**), editor data model, section/components, media upload, form/lead handling, mobile and accessibility rendering, draft/preview/publish versioning, custom domains/SSL, tenant isolation and rollback. Distinguish editor-only UI from actual persisted, published pages. Publishing must not expose secrets or private Cleanto files. See [Scheduled #48](https://github.com/Masterleeaus/Scheduled/issues/48).

## Existing source and transitional tests

- `core/`: Laravel runtime and imported web application.
- `private/cleanto/`: preserved original booking source; migration input, not an approved independent production service.
- `installer/`: inherited installation workflow, with legacy MySQL assumptions.
- `integration/mysql/` and `integration/mysql-tests/`: **disposable legacy MySQL tests**, not shared PostgreSQL cutover evidence.
- `integration/staging/`: staged verification records; inspect actual dates, fixture requirements and pass/fail output.
- `scripts/preflight.php`: source/environment preflight, not a production release certificate.

## Production acceptance checklist

- [ ] Single integrated booking authority, verified in code and staging
- [ ] Central OIDC/company context and shared PostgreSQL migration cutover tested
- [ ] Two-company authorization and data isolation tested
- [ ] Concurrent booking, replay, cancellation, reschedule and timezone tests passed
- [ ] Payment callbacks, deposits, refunds and recovery verified in provider sandbox
- [ ] Front Desk standalone/integrated authority switch and rollback verified
- [ ] Ten-template builder, persisted editor and published-site parity verified
- [ ] Mobile accessibility, forms, domains, backups, monitoring and rollback verified

These boxes describe **release gates**, not issue completion. Only check them after collecting concrete test evidence in the corresponding GitHub issues. Key trackers: [booking #46](https://github.com/Masterleeaus/Scheduled/issues/46), [availability #47](https://github.com/Masterleeaus/Scheduled/issues/47), [builder #48](https://github.com/Masterleeaus/Scheduled/issues/48), [integration #56](https://github.com/Masterleeaus/Scheduled/issues/56).
