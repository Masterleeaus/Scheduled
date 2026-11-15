# Pass 53 — Atomic first-install completion marker

## Reproduced risk
The installer first checks for a missing `storage/installed` marker and then calls `InstalledFileManager::update()`. The previous implementation appended a success line if the marker already existed, allowing a race between two completion requests to report success.

## Fix
Use exclusive `fopen(..., 'x')` marker creation and fail closed if the marker exists. Preserve the helper `update()` method to retain existing controller compatibility. Write and flush the marker, restrict its permissions, and remove only an incomplete marker created by this request when writing fails. No existing successful installation is overwritten or appended to.

## Evidence
- Executable isolated regression: **10/10 assertions passed** (new marker, repeated request, existing marker, directory collision).
- PHP lint: passed for changed helper and test.
- Laravel CLI bootstrap and route discovery: see packaging validation summary.
- Full archive comparison: all source archive paths retained; only stated helper replaced.
- Archive CRC/integrity: validated for cumulative and delta.

## Outstanding
No MariaDB driver/server is installed in this environment. Real concurrent HTTP finalisation and complete MariaDB installation are not certified.

## Product scoreboard
50 verticals; 19 website templates (15 cleaning, 4 other); 10 landing pages; 29 partially editable; 0/29 fully browser-certified.
