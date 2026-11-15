# Proposed Central product registry entry for Scheduled

Status: **proposal, not applied**. This file is for the owner to apply to
`Masterleeaus/Central`. Nothing in Central has been changed by this repository.
Refs issue #1 in this repository.

## Status of the registry entry (issue #7)

- **Blocked on Central.** The registry entry is tracked in `Masterleeaus/Central#100`.
  This repository cannot apply it. Nothing below is applied until the Central owner acts.

## Product contract decisions (issue #7, owner decided)

- Repository identity: this repository is the PlusAgency + Cleanto Laravel application.
  The Central integration is a module within it.
- Booking owner: Scheduled owns booking records, reminders and calendar sync (issue #11).
- Database: PostgreSQL. The current code still targets MySQL/MariaDB. The migration is
  tracked in issue #15 and is not done.

Scheduled is its own product, not a module of another product (owner decision).
The product id `scheduled` is a **placeholder** that the owner can change. If it
changes, update `SCHEDULED_PRODUCT_ID` in `scheduled/.env.example`, the audience
below, and the `iss` value Central expects.

## Proposed entry

Shape follows the table in Central `docs/PRODUCT_REGISTRY.md`.

| Field | Proposed value | Notes |
| --- | --- | --- |
| Product id | `scheduled` | Placeholder. Matches `^[a-z][a-z0-9_-]*$`. Owner may change it. |
| Legacy id | none | Scheduled is new, so there is no legacy `ApplicationId`. |
| Display name | `Scheduled` | |
| Repository identity | `Masterleeaus/Scheduled` | Identity only. Never an origin, never grants access. |
| Audience | `urn:titan-central:product:scheduled` | Follows the `urn:titan-central:product:<id>` pattern from issue #1. |
| Draft contract | `0.1.0-draft` | Matches the other products' draft version. |
| Tenant key | `org` | Assumed: the Logto organisation id, taken from the verified `org` claim in the link assertion (issues #2 and #5). Not verified by Central. |
| Declared capabilities | `health`, `version`, `tenant-link:verify` | Required set for all products. Scheduled declares no product-data capabilities yet. |
| Origin env variable | `TITAN_SCHEDULED_ORIGIN` | Proposed name, following `TITAN_FIELD_ORIGIN` and the others. Operator supplies the value. |

## Changes Central would need (for the owner)

1. Add `scheduled` to the closed `ProductId` set in `product-registry.ts`.
2. Add the `scheduled` entry to the canonical products list. Central validates that
   ids, audiences and repository identities are unique and that every canonical
   product is present.
3. Add `TITAN_SCHEDULED_ORIGIN` to `loadProductRegistry` and `deploy/env.example`,
   set together with the other origins or not at all. Add it to
   `TITAN_PRODUCT_TRUSTED_ORIGINS`.
4. Operator config only. No origin, key or credential is committed to either repository.

## Link-assertion key (issue #2)

- Scheduled signs link assertions with an Ed25519 key (`alg` EdDSA, `typ` titan-link+jwt).
- Scheduled produces the public JWK and `kid` with `scheduled/scripts/export-public-jwk.js`.
  The output contains no private material. Give it to the Central operator.
- Central has no field yet for storing a per-product public key. Where it lives
  (registry, operator config or a key set) is a Central decision.

## Open questions for the owner

1. Is `scheduled` the final product id? If not, which id?
2. The assertion `aud` is `titan-central` (issue #2, from Central `docs/LINKING.md`), while
   the product audience is `urn:titan-central:product:scheduled` (issue #1). Confirm these are two
   separate fields and that the assertion `aud` stays `titan-central`.
3. Is the tenant key `org` (Logto organisation id) right? Central marks tenant keys as unverified assumptions.
4. Where should Scheduled's public JWK be registered in Central?

## Not in this proposal

- No Central code, config or registry file is changed.
- No Logto or Central network calls were made.
- No secrets, origins or keys are recorded here.
