# Pass 18 — Safe deployment and recovery runbook

## Status
This release is a **recovery candidate, NOT production certified** until all six live acceptance gates pass. Archive/syntax/regression results are not operational proof. Do not enable booking commits, payment settlement or auto-publishing because the archive passes offline tests.

## Preconditions
- Dedicated staging environment with licensed application source and verified deployment authority.
- PHP runtime and extensions compatible with locked dependencies (including PDO MySQL, PDO SQLite, XML/DOM, cURL, mbstring, fileinfo).
- MariaDB server with restricted least-privilege accounts and isolated tenant fixture datasets.
- Reverse proxy with TLS, correct public document root (`core/public`), storage and private booking sidecar inaccessible to the public.
- Separate Laravel `core/.env`, secrets manager entries, provider test credentials, and queue/scheduler supervision.
- Full database and filesystem backups and an independently tested recovery destination.

## Deployment order
1. Compute and record SHA-256 of the ZIP and confirm a trusted archive origin.
2. Extract to a new versioned release directory, **not** over the active release.
3. Run `python3 integration/release/preflight.py . --output source_preflight.json`.
4. Run `bash integration/release/deploy_preflight.sh`, supply secrets using the environment manager; never place credentials in ZIPs.
5. Install Composer dependencies from the lockfile using the deployment-approved PHP version. Validate exact dependency/PHP compatibility first.
6. Record a consistent transaction-safe MariaDB backup, plus public uploads, private media, and relevant sidecar state. Verify restoration to a disposable database.
7. Run `php artisan migrate:status` from `core` against **staging first**. Inspect pending migrations and test application and sidecar schema compatibility. Do not blindly run `migrate --force` in production.
8. Run non-mutating app smoke checks, authenticated admin tests, and all 25 template editor/browser tests using controlled test tenants.
9. Run real MariaDB booking concurrency tests, Stripe provider test-mode checkout/webhook/refund reconciliation, provider-backed AI and image save/replace tests, and tenant isolation tests.
10. Store signed or attributable evidence files for each gate. Populate `acceptance_evidence.example.json` into a separate evidence file. Include `executed_at` (ISO timestamp), `environment`, `operator`, `evidence_path`, `artifact_sha256`, and `passed: true` only after a real test succeeds.
11. Run `python3 integration/release/acceptance_gate.py --evidence evidence.json --output acceptance.json`. **Only proceed on exit code 0** after human review of the evidence.
12. Promote the immutable artifact to production and monitor, initially without public traffic. Confirm queue/scheduler, proxy, TLS and post-deploy smoke checks before routing customers.

## Recovery/rollback
- If no schema migration happened, switch traffic back to previous immutable release, restart process workers, clear incompatible caches, and confirm smoke checks.
- If schema migration occurred, **do not** assume code rollback restores compatibility. Freeze writes; restore the matching DB backup and uploaded media snapshot to a tested recovery target. Reconcile sidecar ledgers, payment events and booking attempts before resuming traffic.
- Never replay signed payment webhooks manually without idempotency protections. Use provider readback to reconcile uncertain payment states.
- Record rollback timing, affected tenants, queues, unique payment event IDs and booking reference checks.

## Blocking criteria
Production remains blocked by any missing database acceptance, booking concurrency gate, browser editor acceptance (25/25), AI provider acceptance, Stripe settlement proof, or cross-tenant isolation proof. This package makes the blockers testable; it does not resolve unavailable external services by assumption.
