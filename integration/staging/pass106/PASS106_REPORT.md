# PlusAgency + Cleanto — Pass 106 report

## Baseline
Pass 105 cumulative recovery candidate (Pass 58 remains absent). No historical source is deleted.

## Substantive changes
- Added a provenance-controlled fixture generator reading Pass 104's native table inventory and Pass 105's five synthetic seed rows.
- Enforces required native schema columns and seed bounds before generating review material.
- Generates independent customer booking and rescheduling windows in Australia/Melbourne, plus proposed weekday staff scheduling rows.
- Creates a machine-readable review status clearly marking MariaDB import, booking commit, schedule semantics and upgrade reconciliation **incomplete**.
- Added executable tests covering isolation, schema failure, unsafe seed rejection and non-certification.

## Tests actually executed
- Python Pass 106 test: 12/12 assertions passed.
- Python source syntax: passed.
- Schema-grounded fixture generation: passed; outputs NOT imported to a DB.
- PHP PDO MySQL driver: unavailable; MariaDB server and Docker CLI: unavailable.
- Actual MariaDB schema import, booking/order commit, readback, concurrency and customer browser checkout: **NOT EXECUTED**.
- Pass 58 source provenance: unresolved.

## Critical observed schema issue
Pass 05 and Pass 12 `titan_booking_requests` definitions differ materially and must not be indiscriminately combined. The installer upgrade ALTERs and historical migration inventory need precise ordering review before a valid disposable staging schema can be claimed. Proposed staff-week convention in Pass 106 is not verified against actual Cleanto scheduling rules.

## Release decision
BLOCKED — do not enable public writes or represent these synthetic fixtures as acceptance evidence.
