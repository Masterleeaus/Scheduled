# PlusAgency + Cleanto — Pass 94 | Server-Owned Native Booking Executor

## Scope
Pass 94 reconciles the public checkout commit interface with Cleanto's native transaction gateway. The Laravel service provider binds an executor only when a server-owned global release switch is enabled and trusted tenant profiles are configured. Defaults remain write-disabled. This is a code integration candidate, not evidence of any live booking commit.

## Files
- 6 existing source/test/config files modified.
- 3 new production/test files.
- No deletions.

## Validation actually performed
- Pass 94 native executor test: 16 assertions passed.
- Pass 89 checkout/native persistence compatibility: 8/8 passed.
- Pass 92 native checkout handoff: 12 assertions passed.
- Pass 88 checkout validation: 12 cases passed.
- All modified/added PHP files passed php -l.
- Both ZIP files passed CRC/integrity verification.

## Release blockers
- No proven live MariaDB transaction / independent SQL readback; `pdo_mysql` not installed in this environment.
- No verified competing-booking database transaction.
- Public checkout browser E2E and payment reconciliation not demonstrated.
- Missing Pass 58 source-history provenance.
- Trusted per-tenant release evidence cannot be inferred from passing unit or source tests. Leave the commit gate off.

## Deployment safety
Do not enable TITAN_BOOKING_PUBLIC_COMMIT_ENABLED or populate `public_commit_tenants` until disposable MariaDB tests establish the stated security and persistence gates. No customer-supplied release flags, tenant connection names or release evidence are accepted.
