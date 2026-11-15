# PlusAgency + Cleanto — Pass 78

Source baseline: Pass 77 recovery candidate (Pass 58 remains missing). Implemented service areas, expiring promotional code display and lead validation, niche blog content, OG/canonical metadata, conditional analytics setting (no tracking scripts), and bounded UTM lead attribution with additive migration. Existing Laravel draft/publish snapshot boundary preserved. No production database, browser or payment certification; public booking writes remain disabled. Promotion codes are *enquiry reference codes*, not pricing discounts; authoritative booking pricing must validate discounts independently.

## Acceptance gates still open

- MariaDB migrate/rollback, real lead submission/readback, tenant isolation, concurrent booking commit, Stripe test webhook, visual GrapesJS workflow, and browser device testing.
- Missing Pass 58 lineage; do not promote this recovery archive to certified master.
- Analytics tracking deliberately not loaded: opt-in field is a control/configuration only pending a consent-management implementation.
