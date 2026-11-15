# Pass 79 — Source hardening and honest deployment-gate audit

## Work performed
- Replaced a hard mbstring dependency in the site-document validation path with a Unicode-aware fallback that handles invalid UTF-8 explicitly.
- Normalized site and campaign lead email addresses before storage (without changing validation constraints).
- Prevented the admin site-preview route from passing a non-array/corrupt draft document into Blade.
- Added executable PHP regression checks for Unicode handling, broken UTF-8, site and campaign data contracts.
- Added a deployment preflight which reports prerequisites and exits 77 when a critical prerequisite is unavailable.

## Executed tests
- PASS: 5 Unicode length and malformed UTF-8 assertions.
- PASS: 10 website release source assertions.
- PASS: 7 changed/new PHP source syntax checks.
- NOT EXECUTED: MariaDB migration, booking transaction, simultaneous booking, public checkout, full Laravel/Blade browser acceptance, backups, production deployment.
- Environment: PHP installed; pdo_mysql unavailable; no Docker or mysql CLI found. Source provenance of Pass 58 remains unresolved.

## Release verdict
**BLOCKED — do not release or enable public booking/payment writes.** This pass is a focused hardening pass, not completion of the full original Pass 79 production-hardening milestone. Language/site scoping is not verified as SaaS tenancy isolation; published site templates are not browser certified. Existing source files and the Pass 78 cumulative payload are retained except the explicit overlays listed in the manifest.
