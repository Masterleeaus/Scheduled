# Pass 13 — Read-only booking operations

Implemented tenant-scoped booking filters (date, status, exact staff membership), status summary and bounded CSV export on the existing Cleanto gateway. Added filtering controls to existing owner booking UI. Removed admin-guard routes that incorrectly called a user-guard booking controller, preventing cross-guard owner identity confusion.

This does NOT add CRM mirroring, staff assignment mutations or actual notification deliveries: those remain unverified and need MariaDB staging. It does not enable any booking mutation gate. CSV export is capped to 1,000 rows per request, and customer-provided fields receive spreadsheet formula escaping.

Outstanding: live MariaDB smoke tests; browser filtering/export tests; staff, CRM and notification end-to-end integration; template browser tests. Website templates: 15 registered with partial editing. Landing templates: 10 registered with partial editing. Fully browser-verified editable templates: 0. Passes remaining after this pass: 5 of 18.
