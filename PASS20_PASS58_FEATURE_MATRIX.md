# Pass 58 functionality traceability

| Historical requirement | Pass 19 preexisting | Reconciliation | Status |
|---|---|---|---|
| Concurrent process supervisors/barriers | `ConcurrencySupervisor` / `ConcurrencyProcessGroup` | preserved | Present |
| Single-winner overlap readback | `PersistedOverlapReader` + `PersistedSingleWinner` | preserved | Present; live untested |
| Exactly one persisted booking read | `ConcurrencyBookingReadback` + `AcceptanceReadbackSql` | preserved | Present |
| Full appointment service/method fields | `LifecycleFullBookingReader` | now required in concurrency gate | Reconnected |
| Customer-order one-row read | `LifecycleClientOrderReader` | now required in concurrency gate | Reconnected |
| Order/customer linkage | `LifecycleOwnershipEvidence` | now required in concurrency gate | Reconnected |
| Duration matches fixture | `LifecycleDurationEvidence` | now required in concurrency gate | Reconnected |
| Service/method/quantity match fixture | `LifecycleServiceEvidence` | corrected `quantity` mismatch | Fixed |
| Customer identity fingerprint | `LifecycleCustomerEvidence` | now required in concurrency gate | Reconnected |
| Query missing/duplicate/failure rejected | `AcceptanceReadbackSql` | reused | Present; DB untested |
| Original 35 file edits/hashes | Unavailable | cannot infer | Not authenticated |
| Live MariaDB concurrent booking | Unavailable | acceptance still required | Blocked |
