# Pass 74 — Site Starter Installation / Draft & Publish Snapshots

Implemented a site starter manager inside the existing Laravel application. Ten niche-specific starter documents are provided. A scoped editor can clone a starter into a draft, edit bounded site content, save a revision with row locks, publish a fixed snapshot, unpublish, preview and restore an earlier draft. Public renders use published snapshots only. Admin routes retain existing Pages permission guard. Each site is keyed by language_id and slug. This **does not establish real SaaS tenant isolation**; a separate authenticated tenant/site owner map and isolated-tenant DB checks remain mandatory.

## Tests

PHP syntax on all changed PHP files: passed. Contract checks: passed. Archive CRC: checked at build. No MariaDB driver, transactional SQL commit or browser E2E was run. Raw JSON document is currently the admin editing surface, not a polished GrapesJS component editor. No payment or public booking write has been enabled. Missing Pass 58 persists.

## Known issues

1. Language/site-scoping only, not a verified multi-tenant security boundary.
2. Publishing is versioned and snapshot-based, but has not been DB/browser tested.
3. Unique language/slug checks rely on the DB constraint; handle duplicate-create exceptions gracefully in next pass.
4. Full ten-template site preview test and host/domain publishing not completed.
5. Legacy baseline lineage is still missing Pass 58.
