# Pass 15 — Tenant-safe AI proposal governance

This pass closes a concrete cross-tenant authorization defect in the Branding Agent: tenant-bound sites now invoke the same TenantAccess membership check as the site editor. Proposal approvals perform a second tenant check inside the locked transaction. Site-media references must belong to the target site, preventing a valid site-media URL for a different site's generated image from being approved. AI proposal creation and approval/rejection decisions now generate persistent, append-only audit events containing admin, site, tenant, revision and proposal ID. Provider responses also undergo a new source revision check before proposal recording.

## Testing and limitations
PHP lint passed for touched PHP files, the AgentMediaPolicy positive/negative ownership smoke check passed, and ZIP CRC checks are executed on both deliverables. Full PHPUnit, MariaDB migrations, authenticated browser flows, and AI-provider calls **have not been tested** in a live environment. The new migration must be applied before using the updated agent routes. Existing legacy unassigned sites retain their pre-existing administration policy; full multi-tenant isolation remains unproven. Image generation does not yet provide asset lifecycle cleanup or human-readable media galleries. No change in this pass verifies booking writes or payments.

## Metrics
Pass 15 of 18 attempted; 3 planned passes remain. Website presets registered 15/15; landing templates registered 10/10; partial editing code 25/25; fully browser-verified universal editing 0/25; new templates to register 0. This is a **security and auditability pass**, not full AI background orchestration or provider integration acceptance.
