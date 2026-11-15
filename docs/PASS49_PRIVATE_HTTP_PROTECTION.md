# Pass 49 — Private source HTTP exposure hardening

## Real defect
Pass 48 Apache policy left `/private/` reachable. That directory contains Cleanto implementation and bundled dependencies. The documented Nginx policy also omitted `/integration/` and root manifest/report files. A source file exposed as a downloadable JSON or text asset would leak application internals.

## Remediation
- Apache denies `/private/`, `/integration/`, `/development/`, `/scripts/`, `/PASS05_EVIDENCE/`, core app source, and root pass/recovery manifests and reports.
- Nginx example aligned with these private-source restrictions; it must be installed manually on Nginx servers.
- Local install preflight requires new Apache rules.
- Docker staging HTTP security probe attempts real paths within Cleanto vendor and root release evidence.
- New source-level regression test ensures Apache and Nginx stay aligned.

## Verification scope
PHP lint and source contract tests run locally. Docker, MariaDB PHP drivers and live HTTP testing remain unavailable in this environment. No customer login, booking transaction, Stripe payment or full template browser acceptance was performed. Do not use these checks as production certification.
