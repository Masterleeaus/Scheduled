# Test environment check for the Titan integration

Scope: read-only survey of the sandbox and the test suites in Scheduled, Central, Skilled, Sorted and Supercharged. No installs, no migrations, no live systems, no secrets. Checked on 2026-11-15 from the `origin/main` snapshots.

## Sandbox tooling

| Tool | Status | Version or note |
|---|---|---|
| docker | CLI present, daemon not running | `/var/run/docker.sock` missing, so no Docker-based fixtures or compose stacks |
| psql | Present | PostgreSQL 16.15 client |
| Postgres server | Binaries present, no running server | `/usr/lib/postgresql/16/bin` has `initdb`, `createdb`, `createuser`, `clusterdb`. A throwaway local cluster could be started with `initdb` and `pg_ctl`, but none was started here |
| redis-server | Present | `/usr/bin/redis-server`, not started |
| mysql / mariadb | Missing | No server or client binary |
| PHP | 8.3.6 (NTS) | Extensions include `pdo_mysql`, `mysqli`, `pdo_pgsql`, `pgsql`, `curl`, `sodium`, `zip`, `intl`. `ext-curl` is the only extension Scheduled requires |
| composer | 2.8.12 | Present |
| Node | v22.23.4 | Meets `engines: node >=22` in Central and Skilled. Supercharged's `--experimental-transform-types` flag is available on this version |
| pnpm | 10.28.0 | Meets Skilled (`pnpm@10.0.0`). Sorted pins `pnpm@9.12.0`, so pnpm 9 would be the matching version there |
| npm | Present | Ships with Node 22 |
| gh | Present | Used for PR creation via REST |

No `node_modules` or `vendor` directories exist in any of the five clones. Every Node suite needs `npm ci` or `pnpm install` first, which requires registry access through the agent proxy.

## Suites by repository

### Scheduled (`/home/claude/scan/scheduled`)

- **Runner:** `composer test`, which runs `php tests/run.php`. It has no third-party dependencies and no `vendor/` directory.
- **Needs:** PHP 8.2 or later, no database, no network.
- **Run here:** Yes. Ran `php tests/run.php` on the B2 worktree: **58/58 Central integration checks passed, exit 0.**
- **Not covered by `run.php`:** `tests/` also holds 38 standalone files (37 `.php` files matching `pass*.php` or `*_test.php`, plus `pass44_auth_contract.py`) and 8 subdirectories (`pass53-marker-fixture/`, `pass71/`, `pass72/`, `pass90/`, `pass115/`, `pass116/`, `pass117/`, `pass124/`). `run.php` does not require them. Several reference `PDO`, `mysqli` or `DB_` configuration (for example `pass34-installer-security.php`, `pass55-database-port.php`, `pass56-install-database-identity.php`). These were not run in this check. Their runner and database needs are not documented in the repo, so each would need to be read before it can be run.
- **Lint:** `php -l` on `core/app/Central/Events/SignedEventVerifier.php` passed.

### Central (`/home/claude/scan/central`)

- **Node unit tests:** `npm test` runs `npm run build`, then `node --test` for control-plane, control-api, launcher, and `packages/config-engine/test/*.test.mjs`. It needs `npm ci` first and Node 22 or later. No database is needed for most of these.
- **Postgres-related unit tests:** `apps/control-api/test/product-link-nonce-pg.test.mjs` and `oidc-state-pg.test.mjs` use a fake pool and a mocked client. They do not need a live database and can run after `npm ci`.
- **Database-bound suites:** `central/tests/integration/staging_approval_concurrency.py` reads `TITAN_SHARED_CI_DATABASE_URL`. It needs a real shared database and must not be run here.
- **Python SQL and migration tests:** `central/tests/*.py` (migrate, backfill, retention, staging approval) and `central/tests/sql/` exercise migrations or SQL. They need a Postgres database and must not be run here against any live target. A disposable local cluster would be needed.
- **Deploy preflight:** `npm run test:preflight` runs `python3 -m unittest discover -s deploy/tests`. This is file-based and was not run in this check.

### Skilled (`/home/claude/scan/skilled`)

- **Backend:** `apps/backend` `npm test` runs `vitest run`, then `tsx --test 'tests/**/*.test.{mjs,cjs}'`. Needs pnpm 10 and `pnpm install`. Some files, such as `src/tests/central-sso-db.test.ts`, are database-oriented. Their gating was not confirmed in this check.
- **Frontend:** `apps/frontend` `npm test` runs `vitest run`. `test:unit` and `test:brand` use `node --test`. `test:browser` needs a running app and a browser, so it cannot run here.
- **Local stack:** `docker-compose.local-dev.yml` defines `postgres:16-alpine`, `redis:7-alpine`, MinIO and MailHog. Docker is needed for it.
- **Run here:** Unit-level Vitest and `node --test` suites can run after `pnpm install`. Anything needing Postgres is blocked by the missing daemon.

### Sorted (`/home/claude/scan/sorted`)

- **Root scripts:** `pnpm test` runs `pnpm -r test`. `pnpm test:unit` runs `pnpm -r test:unit`. `pnpm test:integration` runs `pnpm -r --if-present test:integration`. `pnpm test:e2e` runs Playwright and needs browsers installed.
- **Gate:** `scripts/gate.sh` starts an ephemeral Postgres in Docker on port 15432 and sets `TEST_DATABASE_URL`. Full gate needs Docker. `pnpm gate:fast` (lint, typecheck, build, unit) avoids the database phase but still needs `pnpm install`.
- **Scale:** about 764 `*.test.*` files in the repo (counted with `find`, not broken down by runner). Packages use Vitest or `node --test`, per their `package.json` files.
- **Run here:** Unit phases can run after `pnpm install` with pnpm 9. Integration and gate phases need Docker.
- **Database gating:** `packages/storage/src/company-storage-resolver.test.ts` and `packages/titan-platform/src/storage/adapter-selection.ts` reference `DATABASE_URL`. Not investigated further.

### Supercharged (`/home/claude/scan/supercharged`)

- **Runner:** `npm test` runs `node --test --experimental-transform-types $(find tests -name '*.test.*')`. Needs `npm ci` and Node 22 or later.
- **Scale:** about 510 test files under `tests/`.
- **Postgres suites:** `tests/postgres/*.test.mjs` use `{skip: !enabled}`. `enabled` is true only when `INBOX_PG_DISPOSABLE=true` and `INBOX_PG_INTEGRATION_URL` is set. When unset, these tests are skipped. They need a disposable Postgres to run for real.
- **Other DB-using tests:** several under `tests/modules/`, `tests/middleware/`, `tests/security/` and `tests/infrastructure/` reference `DATABASE_URL`. Gating was not checked for each.
- **Run here:** Non-DB files can run after `npm ci`. `npm run check` runs `tsc`. The Postgres-gated files skip cleanly when their env vars are unset.

## How to run each suite

| Repo | Command | Prerequisites | Runs here |
|---|---|---|---|
| Scheduled | `composer test` (or `php tests/run.php`) | PHP 8.2+ | Yes, 58/58 |
| Central | `npm ci && npm test` | Node 22+ | Yes after install. `npm test` runs only `.mjs` tests, so the shared-DB `central/tests/integration/staging_approval_concurrency.py` is not run by it. That file needs `TITAN_SHARED_CI_DATABASE_URL` and must not be run here |
| Central | `npm run test:preflight` | Python 3 | Yes, not run in this check |
| Skilled backend | `pnpm install && cd apps/backend && npm test` | Node 22+, pnpm 10 | Yes after install, DB-bound files excluded |
| Skilled frontend | `pnpm install && cd apps/frontend && npm test` | Node 22+, pnpm 10 | Yes after install |
| Sorted | `pnpm install && pnpm test:unit` | Node 20.9+, pnpm 9.12 | Yes after install |
| Sorted | `pnpm gate` | Docker | No |
| Supercharged | `npm ci && npm test` | Node 22+ | Yes after install, Postgres files skip |
| Supercharged Postgres | `INBOX_PG_DISPOSABLE=true INBOX_PG_INTEGRATION_URL=... node --test tests/postgres/*.test.mjs` | Disposable Postgres | No |

## Blockers

- No Docker daemon, so Docker-based suites (Sorted gate, Skilled local stack) cannot run.
- No MySQL or MariaDB server. Scheduled's database-touching PHP files cannot run against a real MySQL.
- No `node_modules` or `vendor` installed. Node suites need a dependency install first, which was out of scope for this read-only check.
- Postgres binaries are installed, so a disposable local cluster is possible. That would be a new setup step, not part of this check.

## Recommendations

1. Start a throwaway Postgres 16 cluster (from the installed binaries, on a non-standard port, with a fixture database only) for the Central and Supercharged Postgres suites. This does not touch any live database.
2. Document a single command for Scheduled's standalone `tests/pass*.php` files, or fold them into `run.php`, so the non-runner files are not silently skipped.
3. Install Node dependencies in a CI job rather than in this sandbox, using the pinned pnpm and Node versions above.
