# PlusAgency + Cleanto — Pass 04 image uploading and flexible image placement

## Implemented
- Added site-scoped authenticated image upload endpoint in the existing `Pages` permission route group, with CSRF, throttling, file-size cap (5MB), extension/MIME verification, image decoding validation, and unpredictable file names.
- Upload controls for template image overrides (the five imported themes), standard gallery, and social-sharing SEO image.
- `image_blocks` persisted in draft/published SiteDocument, with per-block URL, alt text, safe selector, placement (before/after), width and immutable block identifier.
- Site editor supports creation/removal of inserted image blocks, uploads, selection, edit and undo history.
- Draft preview supports click-target selection using same-origin browser messaging; public pages render the image blocks with escaped URLs and DOM element insertion.
- Uploaded assets accessible at `/uploads/site-media/{site_id}/...`; uploads are not automatically deleted when removing a block, to preserve revision history.

## Verification
- PHP syntax: all modified/new PHP files passed.
- JavaScript syntax: both files passed `node --check`.
- Four standalone server-side validator checks passed.
- Both delivered ZIP files checked for CRC integrity.
- A PHPUnit test class has been added, but not executed with the Laravel test runner.
- Authenticated browser, MariaDB and storage permissions not live-tested.

## Limitations / follow-up
- Arbitrary placement is selector-based DOM insertion, with click-to-select in draft preview. This is not yet a full WYSIWYG canvas or breakpoint-specific media positioning.
- Selection uses CSS paths and may need re-selection after a template structure change.
- Image URLs are stored per site; uploaded binaries are stored in public site-specific folders. Full tenant-level access and asset lifecycle remain future milestones.
- Public writable media paths should be served with the web server's script execution disabled; the upload endpoint never accepts PHP/SVG.
- Template image fields exist for five imported themes; the other ten can insert images via gallery or image blocks.
- No image generation provider or AI branding workflow was integrated in this pass.

## Progress tracking
- Planned passes: 18; completed through Pass 04; 14 remaining.
- Website presets present: 15/15; landing presets registered: 10/10.
- Website templates with some content editing source: 15/15.
- Website templates with new arbitrary image-block insertion source: 15/15.
- Imported themes with upload-to-existing-image-fields support: 5/5 (other 10 do not expose named theme-image fields).
- Landing page templates with new image-block insertion integration: 0/10.
- Templates with fully verified universal visual editing in browser: 0/25.
- Known templates still requiring full universal-editor browser acceptance: 25.
