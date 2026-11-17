# Cleanto / PlusAgency Integration — Pass 1

> **Current architecture notice (2026-11-18):** This directory's Pass 1 notes document inherited Cleanto/PlusAgency integration history, not the accepted final architecture. [Scheduled's root README](../README.md) and [Central ADR-009](https://github.com/Masterleeaus/Central/blob/main/TITAN_ARCHITECTURE_DECISIONS.md) require a **single Laravel booking authority**, central OIDC and a shared physical PostgreSQL target. Retain disposable MySQL harnesses only to reproduce existing-source behaviour until PostgreSQL parity and one-app migration are verified (Scheduled #15/#46/#49/#53). Do not deploy a Cleanto sidecar or duplicate booking writer.


## Directory topology

- `core/`, `assets/`, `installer/`: original PlusAgency layout, except reconciled web routes and stripped `.env`.
- `private/cleanto/`: extracted Cleanto 8.5 original source. **Never map this directory to the HTTP document root.** It is not a PHP package safe to expose to public requests.
- `core/app/Booking/`: newly added Laravel-side tenant-aware integration contract.
- `core/database/migrations/2026_11_09_000001_create_booking_integration_tables.php`: mapping and idempotency schema. Migrations have NOT been executed.
- `integration/tests/`: standalone, no-database integrity tests.

## Security and operational boundaries

- A Laravel booking slots controller has been authored, but **no public route is registered** until PlusAgency business ownership and permissions are confirmed.
- `InternalBookingClient` currently has no corresponding trusted sidecar receiver; it is a stub integration boundary, not functional transport yet. It refuses unconfigured URLs and secrets.
- Cleanto's public API endpoints (`api/*.php`) remain unmodified in the original source and MUST NOT be exposed by a web server. Incoming HMAC needs nonce replay storage, per-tenant DB resolution and tenant-specific Cleanto context before any live booking call.
- `.env` is removed from distributable; supply fresh installation-specific values. Original source archives retain whatever they originally included; protect originals accordingly.
- Cleanto retains its own schema and legacy SQL. Do not combine different customers in the same Cleanto DB without a comprehensive query isolation audit. Prefer distinct database per tenant.
- No payment gateways are enabled by this integration.

## Next action

1. Validate PlusAgency app boot with a safe, throwaway DB and compare updater routes with matching controllers.
2. Build a private sidecar receiver that authenticates requests and resolves per-tenant DB credentials server-side; never use arbitrary caller-supplied database identifiers.
3. Provision a throwaway tenant database and test actual Cleanto slot lookup, then booking lifecycle.
4. Map actual PlusAgency business/role records to `booking_tenant_instances` and authorize tenant web routes.
5. Add nonces and replay prevention, service mapping, then direct widget integration.

## Rollback

Remove newly added integration classes/config/migration and restore the zero-length routes file from the original installation archive if and only if restoring that exact original distribution. Never replace the baseline with the misleading updater package wholesale.
