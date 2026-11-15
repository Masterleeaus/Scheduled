# PlusAgency + Cleanto — Pass 90

## Scope

Native Cleanto service-address and customer-notes preservation in the booking transaction writer. The original Cleanto code was inspected; its `ct_order_client_info.client_personal_info` column holds base64-encoded serialized arrays with `zip`, `address`, `city`, `state`, `notes`, `vc_status`, `p_status`, and `contact_status`. Pass 89's writer previously inserted an empty string into this column.

## Changes

- Added `BookingPersonalInfo`: strict allowlist, UTF-8 / length / control-character validation, serialized Cleanto-compatible eight-field format; status fields are not user-controlled.
- `BookingCommand` carries a validated, optional `personal_info` array in trusted internal commands.
- `BookingSharedPersist` forwards the verified data to the existing Cleanto client writer inside the existing transaction; no second write and no new booking engine.
- `BookingLegacyClientWriter` inserts encoded location/notes into the pre-existing `client_personal_info` column using its prepared statement. Legacy callers default to an empty but correctly formatted structure.
- `BookingLiveWriteCommandPolicy` revalidates this data before SQL persistence.
- `CheckoutCommitCompatibility` checks address/notes can be represented, but the public checkout controller remains validation-only. The public redemption pipeline has **not** yet been wired to pass those fields, and the public booking write gate remains off.

## Tested

- 16 real PHP assertions: native personal-info format, validation, unsafe fields, Unicode and backwards compatibility.
- 13 PDO-subclass tests: actual writer's prepared SQL parameter values, field encoding, legacy default and invalid input rejection (no real database).
- 7 source integration contract checks.
- PHP syntax lint of all six changed production PHP files and three regression files.
- Cumulative and delta ZIP integrity validation.

## Blockers — not release ready

1. MariaDB and PHP `pdo_mysql` unavailable; no SQL commit or independent readback was executed.
2. Missing Pass 58 remains unresolved in the recovered master lineage.
3. Public checkout still validates only. Full transactional redemption, authorization, tenant isolation, booking-confirmation and payments need independent real-world verification before write activation.
4. Cleanto's real production column capacity for the serialized field must be confirmed through a real schema introspection and a transaction readback.
