# Secret scan and third-party code boundary

Record for Scheduled issue #10. Scan run on 2026-11-15 against `main` at commit 3d9d8920.

No secret values are recorded here. Each finding is listed by file path and rule type only.
Findings are unverified. A rule match is not proof that a key is live.

## Scan

- Tool: gitleaks v8.18.4, default ruleset, run locally with `--redact`.
- Full git history: scanned (the clone holds full history).
- Current tree: scanned with `--no-git`.
- Result: 25 findings in the current tree, across 9 files. 31 findings in full history.

### Current tree (path and rule type)

| File | Rule type | Count |
| --- | --- | --- |
| `installer/database.sql` | flutterwave-encryption-key | 1 |
| `installer/database.sql` | flutterwave-public-key | 1 |
| `installer/database.sql` | flutterwave-secret-key | 1 |
| `core/resources/views/admin/pagebuilder/content.blade.php` | generic-api-key | 1 |
| `private/cleanto/assets/js/datatable/vfs_fonts.js` | aws-access-token | 5 |
| `private/cleanto/assets/js/jquery-ui.min.js` | generic-api-key | 1 |
| `private/cleanto/assets/stripe/README.md` | stripe-access-token | 1 |
| `private/cleanto/assets/stripe/tests/Stripe/ApiRequestorTest.php` | stripe-access-token | 1 |
| `private/cleanto/assets/twilio/docs/usage/rest/phonenumbers.rst` | generic-api-key | 9 |
| `private/cleanto/assets/twilio/docs/usage/rest/recordings.rst` | generic-api-key | 3 |
| `private/cleanto/assets/twilio/tests/RequestValidatorTest.php` | generic-api-key | 1 |

### Full history only (not in the current tree)

- `PASS12_MANIFEST.json`, `PASS53_MANIFEST.json`, `PASS60_MANIFEST.json`: generic-api-key.
- `scheduled/test/linking-contract.test.js`: jwt.

### Likely noise, to confirm

- Vendor SDK docs and test fixtures under `private/cleanto/assets/` (Stripe, Twilio, jQuery UI, datatable fonts).

### Needs owner review

- `installer/database.sql` holds three payment-provider key matches. Confirm whether these are placeholders or live values. If live, rotate them at the provider, then remove them from the file.
- Earlier work (PR #13) removed an SMTP credential from `private/cleanto/demo_email.php`. It remains in past commits and must be rotated at the mail provider by the owner. Neither the current-tree nor the history scan flagged it, so the default rules do not catch it.

## Third-party code boundary

These trees are vendored SDKs or assets, not first-party code. Each needs its own licence check before the repository is relied on for redistribution. This record does not verify those licences.

- `private/cleanto/assets/quickbooks`, `xero`, `messagebird`, `authorize.net`, `stripe`, `stripe_3d`, `twilio`, `twocheckout`, `textlocal`: payment, accounting and messaging SDKs.
- `private/cleanto/assets/js` (jquery-ui, datatable), `css`, `lib`, `webfonts`, `jquery_editor`, `flags`, `images`, `pdf`: front-end libraries and assets.
- `core/vendor/` and `scheduled/node_modules` (not committed): Composer and npm packages, governed by their own licence files.

First-party code: `core/app`, `core/tests`, `core/resources` (except vendored assets), `private/booking-sidecar`, `scheduled/`, `docs/`, `installer/` (except the SQL dump above).

## Open owner actions

1. Confirm the three payment-key matches in `installer/database.sql` are not live. Rotate and remove if they are.
2. Rotate the SMTP credential at the mail provider (from PR #13).
3. Confirm the vendored SDK licences listed above.
4. Decide whether to add a `.gitleaks.toml` allowlist for vendor fixtures so future scans are quieter.
