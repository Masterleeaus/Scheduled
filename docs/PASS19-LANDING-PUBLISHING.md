# Pass 19: Landing campaign draft/public separation

## What changed
Landing content updates affect the draft columns only. Public GET/POST resolves the `published_slug` and public GET renders `published_snapshot` captured atomically on explicit publish. Publishing again replaces the snapshot and increments revision; unpublishing removes public access without erasing the last published snapshot. Duplicate campaigns discard the snapshot.

## Migration
Run standard Laravel migrations before routing live traffic. The migration backfills snapshot records for previously published campaigns using their existing content. Take an SQL backup first. Missing snapshots fail closed and do not silently expose draft content.

## Notes
Changing the draft slug does not change the public URL until republishing. Lead submissions remain linked to the original campaign ID. Existing landing campaigns may require external caching invalidation when republished (public cache is up to 60 seconds). Template visual drag-and-drop and live browser/MariaDB acceptance remain open. Re-run staging tests before production deployment.
