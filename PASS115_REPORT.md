# Pass 115 — Booking location readback transaction proof

## Change
- Extended `BookingWriteProofReader` to select the stored `client_personal_info` column from the original Cleanto customer-order table under `FOR UPDATE`.
- Added `BookingWriteProofPersonalInfo` to verify the exact canonical base64/PHP-serialized eight-field payload produced by `BookingPersonalInfo::encode`. It uses constant-time `hash_equals` and never deserializes untrusted database data.
- Wired the check into `BookingWriteProofRows`, so a mismatch causes the owning transaction to fail before successful reservation return.
- Added executable test cases for full/empty locations, tampering, each location field, Unicode, and input order.

## Limits
Pure regression tests are not real MariaDB acceptance. The environment has no PDO MySQL driver, MariaDB server, or Docker. The original Pass 58 provenance gap remains open. Do not enable public booking writes based on this change.
