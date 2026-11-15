# Pass 72 — Builder revision history and live rollback

Baseline: Pass 71 recovery candidate. Pass 58 missing; MariaDB live acceptance blocked.

## Delivered
- Existing PageBuilderController saves now require a SHA-256 revision token, and validate GrapesJS JSON before writing.
- Existing Page/Home save uses a database transaction, scoped language lookup and row lock; conflicts return 409.
- A new reversible migration stores previous live versions in `builder_revisions`, unique by target and revision, with indexed language-scoped lookup.
- A restore endpoint locks the live row, checks editor revision, snapshots outgoing content and restores an earlier version atomically.
- History endpoint returns up to 30 scoped version metadata entries without exposing document payloads.
- GrapesJS editor provides a history action, asks for explicit restoration confirmation and reloads after successful restore.
- Existing Save control is renamed to `Save and publish live` to disclose the original app's live-on-save semantics.

## Verification
- `php -l`: controller, route, migration and test scripts passed.
- Focused source-contract tests: 19/19 passed.
- Full ZIP CRC verification: passed.
- Database migration, row-lock transactions, tenant identity, browser editing and actual restore: NOT tested; pdo_mysql unavailable.

## Limitations and design truth
- This is VERSIONED LIVE PUBLISHING, not draft-versus-published staging. Existing Page/Home records are what the website renders, and save updates those immediately.
- Restore cannot recover changes from before this migration is installed and a first successful save creates an entry.
- A language-scoped Page/Home model may not be a sufficient tenant boundary for independently hosted tenant deployments; verify deployment tenancy before exposing the routes.
- No claim of completed reusable sections, global tokens, responsive preview or full builder milestone.
- Pass 58 remains missing; no promotion to verified Master.
