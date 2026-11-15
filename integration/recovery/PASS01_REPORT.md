# PlusAgency + Cleanto — Pass 01 source recovery evidence

## Outcome
**Recovery comparison and source inventory performed; no historic code was automatically overlaid.** The unchanged Pass 130 source remains authoritative in this candidate. The only new executable files are read-only lineage/template validation utilities with regression tests. Pass 01 is **partially accepted** because Pass 58 provenance and real MariaDB acceptance are not resolved.

## Confirmed source preservation
- Pass 130 contains all 14,618 non-secret original PlusAgency installer paths. The only original installer path absent is `core/.env`, intentionally excluded from distribution; do not reintroduce secrets.
- Pass 130 contains 5,637 original Cleanto paths under `private/cleanto/`; the only excluded original path is `extension/GoogleCalendar/error_log` (a runtime log).
- Pass 130 contains all file paths from the Pass 52 master, Pass 91 merged recovery archive, Passes 53–57 and 59–61 delta archives, and the Pass 90/91 imported-theme delta packages.
- Pass 90 and 91 theme delta contents, including their imported media, match Pass 130 byte-for-byte by ZIP CRC and uncompressed size.
- No historical-only paths were found in those compared full archives/deltas. Existing-path changes are not blindly overwritten because later fixes may supersede them.

## Template findings
- Fifteen website preset documents parse as JSON and satisfy basic required-field checks.
- The imported theme asset folders in Pass 130 contain: clinar (4), cleanmate (4), handypro (3), prinash (4), klano (4) assets. Presence does not establish quality, licenses, image usability or complete front-end wiring.
- The current SiteDocument editor has a fixed field allowlist, lacks a general section-tree data model and imposes a 65,535-byte document limit. Universal editing and structured AI mutations are **not implemented** by this recovery pass.

## Not verified / blockers
- Pass 58 original source was not recovered in this pass; differences cannot be proven complete without it or alternate source provenance.
- PlusAgency updater bundle has a different `updater/` root and requires a semantic compatibility audit rather than treating its files as an overlay.
- No MariaDB instance, real payment provider or live browser acceptance was exercised.
- No live service was changed and no external credential was used.
- Initial partial Library traversal was not an exhaustive audit of every nested Library / Google Drive file.

## Pass 02 dependency / acceptance
Implement a single canonical section/component document model with safe migrations or compatibility adapters for the fixed SiteDocument fields. Wire all 15 website templates and 10 campaign templates to instantiate, persist, edit, preview and publish real records; verify every template in integration/browser tests. Preserve imported original media and send branding/media quality cases to the later Branding Agent pass.

## Repeatable verification
Run `python3 integration/recovery/source_recovery_audit.py PASS130.zip --historical HISTORICAL.zip --output report.json` using local paths. No ZIP entry is executed or extracted. Run `python3 -m unittest discover -s integration/recovery -p 'test_*.py'` for the audit tool's regression suite.
