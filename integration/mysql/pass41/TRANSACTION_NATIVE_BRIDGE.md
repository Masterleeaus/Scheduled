# Pass 41 – transaction-native Cleanto bridge

`CommitCleantoNativeGateway` connects the existing one-time claim to `BookingSharedTransactionWriter`. The native writer deliberately reuses the existing Cleanto pricing, availability, conflict, order/customer and booking writers without starting another transaction or recording a second idempotency claim.

Server-only prerequisites: verified owner and tenant database, `booking_writes_enabled`, `booking_schema_verified`, `booking_rate_mapping_verified`, `booking_concurrency_verified`, `verified_pricing_settings`, a disposable database loaded with Cleanto and integration tables, and trusted server release evidence. Staff locks remain held across the ledger claim and booking commit; no public route is enabled.

The gateway is a source-level integration and **has not** been exercised with MySQL here. Run the Pass 40 disposable MariaDB acceptance harness and two-process booking test before enabling writes.
