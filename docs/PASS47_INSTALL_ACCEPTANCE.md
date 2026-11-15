# Pass 47 — Disposable HTTP / MariaDB Installation Acceptance Harness

## Scope
Introduced a test-only isolated application and MariaDB stack that can run the real installer and HTTP security probes on a Docker-capable machine. No changes to runtime booking or payment write gates.

## Source changes
- PHP 8.2/Apache test image with pdo_mysql, mysqli, curl, mbstring, intl, gd, zip, bcmath, exif, rewrite enabled.
- MariaDB 10.11 isolated on an internal-only network without any host-exposed database port; tmpfs database discarded on exit.
- Site published only on host loopback; application running from a disposable copy of the read-only cumulative source.
- Explicit acknowledgment, strong distinct DB credentials, and teardown with docker compose down --volumes after the test.
- HTTP probes for private SQL, application composer file, core environment and test script; server must deny with 403/404.
- Source regression contract for 19 security and reproducibility properties.

## Executed checks
- `php tests/pass47-staging-contract.php`: **19/19 PASS**.
- `bash -n` on shell entrypoint, runner and HTTP probe: **PASS**.
- `php -l tests/pass47-staging-contract.php`: **PASS**.
- Full ZIP integrity and preserved-file CRC comparisons: see archive manifest.
- Docker executable: **UNAVAILABLE**. PHP MySQL extensions and MariaDB on this host: **UNAVAILABLE**.

## Instructions
Read `integration/install/pass47/README.md`. Run `bash integration/install/pass47/run.sh` on a trusted Docker host with explicit disposable acknowledgement and unique secrets; inspect and manually complete the installer against the disposable DB. Report live admin-login, migration, templates and booking results separately.

## Release gates
This does **not** certify the install, any of 29 templates, confirmed bookings, native staff updates, or Stripe payments. Vendor licence verification must use authorised credentials. No secrets or client data included.

## Scoreboard
50 business verticals; 15 cleaning website templates; 4 other website templates; 10 landing templates; 29 partially editable; **0/29 browser-certified**. Latest development pass: 47.
