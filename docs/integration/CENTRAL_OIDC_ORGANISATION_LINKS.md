# Scheduled: Central OIDC organisation-scoped account links

This change adds an explicit company boundary to Central SSO, without claiming complete Central membership revocation or PostgreSQL cutover.

## Trust boundary
1. Scheduled verifies the OIDC ID token signature, issuer, audience, nonce, subject and expiration before it considers company membership.
2. The ID token must carry `titan_organisation_id` as a UUID. This claim is taken **only from the verified token**, never from browser query strings, session input, an email address or the local user's company. If absent, login fails closed.
3. The approved local link lookup requires the hash of the verified issuer, hash of verified subject **and verified organisation UUID**. Revoked links are excluded; malformed IDs are refused.
4. Existing unscoped approved links receive a NULL company in additive migration `2026_11_15_000004`. NULL matches no token; no automatic assignment or migration from email is performed.
5. A trusted administrator must establish Central membership, prove ownership of an existing Scheduled user and explicitly assign `central_organisation_id` to that approved record. This repository does **not** add a public provisioning or backfill route. For multiple-company users, each company requires a separately approved scoped link.
6. Central organisation revocation and company switching are **not yet enforced for an already active Scheduled web session**. They must be implemented and tested before enabling this flow for production tenants.

## DB schema and release safety
- The 000004 additive migration adds `central_organisation_id`, replaces the previous global issuer+subject uniqueness with a unique issuer+subject+company constraint, and indexes company/revocation.
- Existing unscoped entries remain inactive rather than being automatically trusted. No destructive rollback is provided.
- Native PostgreSQL migration, pre-existing MySQL installs and live two-company membership tests remain separate acceptance requirements. See Scheduled #5, #7, #15, #49 and Central #82.
- Migration 000004 has been added to the strict initial feature migration registry. Do not run it against a production database without an approved backup and migration rehearsal.

## Operations and release gate
- Configure Logto to emit signed `titan_organisation_id` in the scheduled application ID token, with a correctly scoped issuer/audience.
- Verify the on-disk migration manifest and all CI tests.
- Confirm a valid member can sign in through an approved same-company link, the same verified subject cannot cross company, and a removed membership is denied before session issuance and for existing sessions.
- Until that final membership check exists, treat this as a fail-closed integration increment, **not a certification of production-ready SSO**.
