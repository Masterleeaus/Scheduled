# Pass 39 — first direct Laravel/Cleanto merge slice

## Completed
- Moved the **read-only service/method/unit/staff catalogue queries** into a native Laravel `NativeCleantoCatalogue` service using configured MySQL connections.
- Reused the current Laravel `CatalogSnapshot` response validation and all existing site/public gateway callers.
- Opt-in **per authenticated owner ID** only: `booking_internal.native_catalogue_tenants`, disabled by default.
- Verify registry database reference, configured connection database, MySQL driver, and the live `SELECT DATABASE()` value before any tenant catalogue query. Errors fail closed; no automatic tenant fallback.
- Same query shapes and row limits as sidecar. No Cleanto schema changes, booking writes or payment logic changed.

## Operational prerequisites
1. Register an isolated, least-privileged **read-only MySQL Laravel connection** in protected server configuration. Do not accept credentials, owner IDs or DSN from public requests.
2. Verify each `booking_tenant_instances.database_reference` equals the configured database name and owner assignment is correct.
3. For accepted owner ID only, set `booking_internal.native_catalogue_tenants[OWNER_ID] = ['enabled'=>true,'connection'=>'configured_name','database'=>'exact_database_name']` in trusted private configuration; deploy via config cache rebuild.
4. Exercise catalogues for several isolated tenants and compare to signed sidecar outputs before enabling broadly. Verify names containing markup, empty catalogue, limits and orphan relations.
5. Leave booking writes, pricing/staff mutations, availability and checkout on the existing path pending live database tests.

## Limitations
**This is NOT a full direct merge.** The sidecar remains for other operations and as the default catalogue path. No MariaDB/PDO MySQL service or browser was available for live verification. No claim of simple production installation or live booking acceptance.

## Scoreboard
50 business verticals; 19 website templates (15 cleaning + 4 newer); 10 landing templates; all 29 partially editable; 0/29 fully browser-certified. Original 18-pass plan exhausted; development pass 39.
