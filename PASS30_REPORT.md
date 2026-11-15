# Pass 30 — Controlled native Cleanto unit management

Base: Master-Copy-PlusAgency-Cleanto-Pass29-NATIVE-SERVICE-ADMIN-CUMULATIVE.zip.

## Implemented
- Signed private operations `owner_service_units` and `owner_service_unit_update`, dispatched to tenant-selected MySQL DB.
- Explicit dual `service_management_enabled` / `unit_management_enabled` tenant gates; checked database identity and non-emulated PDO prepares.
- Native Cleanto table `ct_service_methods_units` listing of price, `uduration`, method, parent service, with 500-row hard limit.
- Validated decimal base price and 5-minute increment durations; no arbitrary SQL or customer-supplied tenant identifiers.
- MySQL transaction, locked unit and parent service, disabled-service restriction, rejection on any booking history, readback validation, rollback on failure.
- Owner-only PlusAgency controller, verified domain/owner routing, CSRF-protected admin forms, saved-value confirmation.
- Targeted standalone test: 25/25 input and source-contract assertions.

## Not completed / limitations
- This is base-price and duration metadata only, **NOT** complete price rules, tax handling, staff eligibility, working hours or activation.
- Existing rate tables may override base pricing. Native Cleanto booking behavior MUST be validated before editing live prices.
- Live MySQL writes, web UI, fresh installation and confirmed booking/payment are NOT tested in this environment.
- Owner service management and pricing unit management remain disabled by default until explicitly enabled per tenant.
- For the 50 verticals, catalogue and presentation profiles exist, but only four new template variants have added booking flows.
- Existing template counts: 19 website starters, 10 landing starters. All 29 require full browser acceptance; 0 certified.

## Safety / deployment
- Do not install over live customer deployments; staging first with database backups.
- Sidecar must be deployed outside public document root, protected by network ACL and signed requests.
- Tenant config must explicitly set `service_management_enabled: true` and `unit_management_enabled: true` only after acceptance.
