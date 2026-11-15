# Pass 77 — Website features and lead capture

Added reusable gallery/testimonial/team sections to all ten published site templates, optional site-document validation, a structured content editing surface, consent-based customer lead form, throttled public POST route, a per-site private admin lead inbox, and site lead schema. The form only displays on published site (not preview), validates service against published snapshot and persists leads keyed by site_id and language_id. The former contact sections remain as historical layout; enquiry form is appended after each of ten theme layouts.

## Validation

Local PHP lint and source-contract tests execute in the build. Database and browser acceptance remain unverified. Existing starter model uses language/site scoping, NOT proven tenant isolation. Existing Pass 58 gap persists. No payment or booking writes were enabled.

## Known limitations

- Needs MariaDB migrations, CSRF/throttle and lead persistence browser acceptance.
- Website visual GrapesJS builder integration is still incomplete; this pass provides structured form editing alongside the legacy JSON field.
- No email notification, customer portal or authenticated SaaS ownership mapping.
- No image upload pipeline; gallery accepts validated URL fields.
