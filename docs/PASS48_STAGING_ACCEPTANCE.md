# Pass 48 — Staging acceptance connectivity and reproducible evidence

## Scope
- Preserves the Pass 47 source, application, migrations and booking safeguards.
- Repairs the staging-only Docker network: application has controlled Docker egress for vendor licence verification, while MariaDB remains on internal private network only.
- Adds explicit Docker Compose, port and noninteractive evidence checks.
- Extends HTTP deny probes to additional nonpublic project paths.
- Adds a regression contract test with 22 assertions.

## Not verified
- No Docker runtime or MariaDB/PHP MySQL driver on the execution host.
- Clean database import, admin login, all 29 templates, Cleanto booking and Stripe transactions remain untested.
- This is a staging harness, not a deployable production configuration.
