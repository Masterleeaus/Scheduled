# PlusAgency + Cleanto — Pass 89

## Actual integration change

The customer checkout validation endpoint now cross-checks its validated signed selection and customer identity against the **existing** native booking redemption contracts. `CheckoutCommitCompatibility` verifies the native claim-key format, appointment selection/duration and Cleanto customer field limits, as well as the stricter intermediate booking adapter. This closes a demonstrable gap between preview checkout acceptance and the eventual booking transaction writer.

The checkout endpoint **does not commit**. It remains an intent-validation endpoint; no public booking, payments or confirmation routes are enabled. The validated service address and notes are **not yet mapped to Cleanto persistence** and must be mapped before public booking release.

## Evidence

- PHP syntax: all three changed/added PHP files pass.
- Focused native-writer compatibility harness: 8/8 pass (valid checkout; overlong phone; overlong email; combined name overflow; control characters; unsupported duration; invalid staff; invalid intent nonce).
- PHP PDO MySQL driver: unavailable; MariaDB transaction, concurrency and SQL readback NOT run.
- Real browser flow: NOT run.
- Missing Pass 58: unresolved; archive remains an incomplete recovery lineage.

## Release blockers

1. Implement and verify address/notes persistence without lossy data mapping.
2. Establish actual MariaDB transaction evidence and two-worker booking collision proof.
3. Verify public booking submission, tenant ownership, payment and confirmation in a real browser.
4. Recover/reconcile Pass 58 source provenance.

This is a focused app integration pass, **not** a completed reservation checkout or a production release.
