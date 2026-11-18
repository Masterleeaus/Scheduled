# ScheduledAt.Work — bookings, websites and landing pages

**Repository:** `Masterleeaus/Scheduled` · **Application:** ScheduledAt.Work · **Maintained architecture review:** 18 November 2026

Scheduled is Titan Zero's service-business **booking and website-building application**. It combines the imported PlusAgency website/page-builder platform and Cleanto's scheduling capabilities. The **target** is one Laravel application, one booking/calendar authority and Central-backed company identity. The source currently contains legacy installer and database assumptions; being committed to `main` is **not** proof that the integrated system is production-ready.

## What Scheduled is responsible for

| Area | Intended capability | Delivery distinction |
| --- | --- | --- |
| Booking | Services, staff, locations, calendars, availability, holds, atomic booking, rescheduling, cancellation and waitlists | Existing imported functionality requires full lifecycle and concurrency acceptance ([#46](https://github.com/Masterleeaus/Scheduled/issues/46), [#47](https://github.com/Masterleeaus/Scheduled/issues/47), [#58](https://github.com/Masterleeaus/Scheduled/issues/58)) |
| Commerce | Deposits, checkout, payment callbacks, refunds and recovery | Provider signing, idempotency and sandbox tests are outstanding ([#64](https://github.com/Masterleeaus/Scheduled/issues/64)) |
| Websites | Business websites, landing pages, templates, editor, publishing and lead capture | Integrated **ten-template** builder and end-to-end publishing acceptance remain open ([#48](https://github.com/Masterleeaus/Scheduled/issues/48)) |
| Operations | Assignment, recurrence, route constraints, recovery queues, failed jobs and operational observability | Finish with runnable tests ([#63](https://github.com/Masterleeaus/Scheduled/issues/63), [#65](https://github.com/Masterleeaus/Scheduled/issues/65)) |
| Cross-app booking | Versioned availability, booking commitment, events and Front Desk handoff | Scheduled is the **integrated** booking source of truth; never permit two concurrently writable authorities ([#56](https://github.com/Masterleeaus/Scheduled/issues/56), [#67](https://github.com/Masterleeaus/Scheduled/issues/67)) |

AI may **recommend** slots, teams or routes, but the deterministic booking engine enforces capacity, permissions, pricing and time constraints. Supercharged owns customer communications and autonomous agents; Sorted owns jobs and invoicing; Skilled owns training eligibility.

## Titan Zero architecture and deployment modes

The [Central architecture decisions](https://github.com/Masterleeaus/Central/blob/main/TITAN_ARCHITECTURE_DECISIONS.md), particularly ADR-009, define the **target** for the five connected products (Central, Scheduled, Sorted, Skilled and Supercharged):

- **One physical PostgreSQL database** with one centrally coordinated, immutable migration ledger and separate least-privilege credentials, not five separate app databases.
- Logto-backed **Central OIDC** sign-in and canonical UUID `company_id`; every product must enforce company membership and role/permission boundaries.
- Scheduled becomes **one in-process Laravel application**. `private/cleanto/` is retained upstream code to integrate or migrate, not a separately deployed ongoing booking sidecar.
- Booking, payment and customer handoffs require signed/versioned commands or events, replay/idempotency protection and migration/reconciliation tests.
- The independently installed **Scheduling & Enquiries Manager** WordPress plugin continues to support fully local Free booking if disconnected. An integrated site switches authority only through a tested, explicit mode negotiation and cutover; no silent dual-write.

**This target is not a completed migration.** The inherited README/deployment setup still contains MySQL/MariaDB assumptions. [#15](https://github.com/Masterleeaus/Scheduled/issues/15), [#49](https://github.com/Masterleeaus/Scheduled/issues/49) and [#53](https://github.com/Masterleeaus/Scheduled/issues/53) track conversion to PostgreSQL, installer changes and MySQL-specific booking SQL. Never treat the legacy MySQL installer steps as the final Central-integrated production procedure.

## Repository map

| Path | Contents |
| --- | --- |
| `core/` | Laravel 9 application, controllers, routes, migrations, views and runtime |
| `private/cleanto/` | Preserved Cleanto source for controlled in-process integration; not a second booking authority |
| `assets/` | Site/editor and admin static assets |
| `installer/` | Imported base installation/SQL and install workflow; subject to security and PostgreSQL migration review |
| `integration/` | Contract fixtures, database/install/staging harnesses and integration guidance |
| `tests/` | Standalone PHP security, installation and booking regression scripts |
| `scripts/`, `tools/` | Preflight and source/deployment tools |
| `docs/`, `development/` | Architecture evidence and historical development records |

A large number of README files under `core/vendor/` and `private/cleanto/` belong to upstream dependencies. Preserve their copyright, licence and provenance instead of rewriting them as Scheduled product specifications.

## Development and verification

**Legacy runtime currently reflected in source:** PHP 8.1 (Composer's platform target) and Laravel 9; Apache rewrite support or equivalent Nginx protections; imported installer code expects MySQL/MariaDB and PHP MySQL extensions. These requirements describe the **existing source environment**, not the final PostgreSQL integrated deployment.

```bash
php scripts/preflight.php --json
# From core/ when a locally configured Laravel install exists:
php artisan --version
```

Review [integration/install/pass47/README.md](integration/install/pass47/README.md) for disposable installation verification and [integration/README.md](integration/README.md) for contract tooling. Use isolated databases and authorised test providers. Keep `core/`, `private/`, `integration/`, `installer/`, secret files and SQL dumps outside public web access. Do not run legacy installer imports against a populated production database. Preserve booking data and immutable applied migration history.

**Acceptance gates:** fresh install and upgrade; PostgreSQL migration and two-company RLS; DST/timezone; simultaneous last-slot booking; retries/replay; payment webhook verification; Front Desk outage recovery; editor-to-published-page parity; browser/device accessibility; service provider sandbox tests; required CI; staging operational go/no-go.

## Open delivery priorities

1. **P0 booking and architecture:** [#46](https://github.com/Masterleeaus/Scheduled/issues/46), [#47](https://github.com/Masterleeaus/Scheduled/issues/47), [#49](https://github.com/Masterleeaus/Scheduled/issues/49), [#56](https://github.com/Masterleeaus/Scheduled/issues/56).
2. **P0 credentials and database safety:** [#54](https://github.com/Masterleeaus/Scheduled/issues/54) for an imported PayUmoney gateway salt (never reproduce its value); [#15](https://github.com/Masterleeaus/Scheduled/issues/15) and [#53](https://github.com/Masterleeaus/Scheduled/issues/53) for schema/SQL conversion.
3. **P1 website and commercial flows:** [#48](https://github.com/Masterleeaus/Scheduled/issues/48), [#62](https://github.com/Masterleeaus/Scheduled/issues/62), [#64](https://github.com/Masterleeaus/Scheduled/issues/64), [#65](https://github.com/Masterleeaus/Scheduled/issues/65).
4. **Outstanding route runtime acceptance:** [#76](https://github.com/Masterleeaus/Scheduled/issues/76) (source-level public `/backup` removal is not the same as tested HTTP authorization).

**Tracking:** [all open Scheduled issues](https://github.com/Masterleeaus/Scheduled/issues?q=is%3Aissue+is%3Aopen). Mark Markdown checklist tasks `[x]` only with actual source and test evidence; close only when every required task is checked. An approved architecture is not a shipped integration.

## Licensing and deployment safety

Retain imported upstream notices and verify the origin/redistribution rights of every donor. Keep gateway credentials, site records and customer data out of Git. Never deploy directly from a historical pass ZIP or overwrite a live database. See the seven-repository [architecture and backlog audits](https://github.com/Masterleeaus/Central/tree/main/docs/audits).

## Find every README and developer guide

- **[Complete README directory](docs/README_DIRECTORY.md)** — direct links to every README-style file in this repository, including component, test, vendor and historical documentation.
- **[Documentation index](docs/README.md)** — curated entry point to the main technical guides and developer workflows.
- **[Seven-repository platform map](https://github.com/Masterleeaus/Central/blob/main/docs/PLATFORM_MAP.md)** — cross-product architecture, ownership and integration boundaries.
