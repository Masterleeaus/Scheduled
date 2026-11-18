# Third-party code boundary

This page records which parts of the Scheduled repository are third-party code, how each is kept, and what Scheduled's own licence does and does not cover. It was written for issue #10. It describes the tree as committed on `main` on 2026-11-18.

## Scheduled's own code

- `core/app/`, `core/routes/`, `core/config/`, `core/database/`, `core/resources/` (except `resources/views/vendor`), `core/tests/`, `tests/`, `installer/` (except the upstream installer package in `core/vendor`), and `integration/` are Scheduled code.
- The repository licence is MIT, in `LICENSE` at the repository root (PR #14). `composer.json` declares `"MIT"`.

## Third-party trees

| Path | Kind | How it is kept | Licence files in tree |
| --- | --- | --- | --- |
| `core/vendor/` | Composer dependencies for the Laravel application | Managed by `core/composer.lock`; installed from the lockfile | Upstream licence files as shipped by each package |
| `private/cleanto/vendor/` | Composer autoload stub for the Cleanto source | Only `autoload.php` and `composer/` are present | One licence file |
| `private/cleanto/assets/quickbooks/vendor/` | QuickBooks SDK dependencies | Two `composer.lock` files | 33 licence files |
| `private/cleanto/assets/xero/vendor/` | Xero SDK and its dependencies (firebase/php-jwt, guzzle, league, monolog, paragonie, psr, symfony, xeroapi) | No lockfile in tree | 14 licence files |
| `private/cleanto/assets/messagebird/vendor/` | MessageBird SDK | No lockfile in tree | 3 licence files |
| `private/cleanto/assets/stripe_3d/vendor/` | Stripe PHP SDK | No lockfile in tree | 2 licence files |
| `private/cleanto/assets/stripe/` | Stripe library source, with its own README, VERSION and CHANGELOG | Kept as shipped | 1 licence file |
| `private/cleanto/assets/authorize.net/` | Authorize.Net SDK | Kept as shipped | 4 licence files |

Other third-party material sits in the tree with its own README, including the `README` files under `core/vendor/` and `private/cleanto/`. The full list of README-style files is in `docs/README_DIRECTORY.md`.

## Rules for third-party code

1. Do not edit a vendored tree to change Scheduled behaviour. Wrap it in Scheduled code, or update it from its upstream release.
2. Keep each upstream copyright, licence and provenance file in place. Do not merge these into the MIT licence text.
3. Changes to a vendored SDK must be recorded in the pull request that makes them, with the upstream version it came from.
4. Code under `private/cleanto/assets/` is not reviewed as Scheduled product code. Its licence status is recorded per tree above, not inferred.

## Open items

- The vendored trees under `private/cleanto/assets/` have no single upstream version record. Each needs its own version and licence check before any further redistribution. This is not done here.
- The full-history secret scan in issue #10 is a separate item and is not covered by this page.
