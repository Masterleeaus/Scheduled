# Pass 29 — Native Cleanto Service Metadata Administration

## Verified implementation
- Added authenticated owner-only POST form on existing site service-mapping screen.
- Added signed private sidecar operations `owner_service_create`, `owner_service_update`, explicitly whitelisted in both client and sidecar.
- Sidecar selects DB from its server-side tenant registry, checks MySQL driver, non-emulated prepares, exact database identity, and `service_management_enabled === true`.
- Creates native `ct_services` records **disabled** with parameterised SQL, transaction and readback; update only title/description of an existing locked row.
- Does not write service rates, pricing methods, unit rates, staff schedules, booking orders or payments.
- Service-management feature is disabled unless the tenant sidecar registry includes both `database_name` and `service_management_enabled: true` after an operator validates the live tenant DB. Do not enable globally.

## Caveats
- Requires existing Cleanto schema with `ct_services(id,title,description,color,image,status,position)` as observed in native source; test on staging first.
- Live MariaDB writes, edit UI and end-to-end booking cannot be certified here: no configured per-tenant MySQL connection.
- Existing read-only catalogue returns **active** services only; new disabled services won't appear in the active list until configured and enabled by the original Cleanto admin.
- Pricing/staff/schedule editing remains outside this safe scope; do not label this full native service management.
- No changed PHP/SQL schema files from Cleanto original source.

## Owner management listing
A separate signed operation returns disabled and active records for owner editing. The public catalogue stays active-only.
