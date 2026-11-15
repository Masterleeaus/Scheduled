# PlusAgency + Cleanto — Pass 96 (tenant-specific checkout readiness)

Base: Pass 95 cumulative recovery candidate. Missing Pass 58 provenance persists.

## Production changes
- Public booking widget now advertises reservation submission only when current verified hostname resolves to a configured, independently gated tenant and PHP has pdo_mysql.
- Public submission performs the same trusted tenant-specific readiness check before invoking the server-owned native booking executor.
- Readiness fails closed when domain resolution, configuration, or verification fails. No public payload supplies readiness evidence.
- All existing availability, checkout, and native gateway components remain intact.

## Verification
- New tenant readiness regression: 18/18 assertions passed.
- PHP lint on changed PHP files passed.
- ZIP integrity and delta manifest verified.
- Live MariaDB booking persistence and two-worker concurrency: NOT RUN; pdo_mysql and MariaDB server unavailable.
- End-to-end browser booking: NOT RUN.
- Public writes remain disabled by default.

## Release status
RECOVERY CANDIDATE only. Pass 58 remains unverified; transactional, payment, browser, and tenant acceptance gates remain open.
