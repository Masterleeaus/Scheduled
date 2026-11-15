# Pass 107 — Cleanto native schedule and ledger audit

**Source verification:** `private/cleanto/objects/class_dayweek_avail.php` maps weekday 1 to Monday and 7 to Sunday. Its weekly schedule insertion stores `provider_id`, `week_id`, `weekday_id`, `day_start_time`, `day_end_time`, `off_day` and `provider_schedule_type`; weekly seed conventions use `week_id=1`. This resolves the earlier weekday-number uncertainty **at the source level only**.

**Fixed:** Pass 106 previously scheduled rescheduling exactly one calendar day after the original. A Friday booking would reschedule Saturday outside the Monday–Friday seed availability. Pass 107 selects the next weekday, including Monday after Friday. Tests sweep 14 starting days across calendar-week boundaries.

**Ledger:** `pass05_reservation_ledger.mysql.sql` uses `command_hash` and `status`, while the canonical `pass12_ledger_canonical.mysql.sql` uses `fingerprint` and `state`. New disposable databases should use only the reviewed canonical schema. Existing rows require an audited, data-preserving migration—not an automatic overwrite.

**Still unverified:** Cleanto's *runtime* availability, full migration ordering, SQL execution, actual booking/order persistence, and multi-tenant separation. No database release evidence was produced. The missing Pass 58 lineage remains unresolved.

To test: `python integration/staging/pass107/test_pass107.py` and run `ledger_and_schedule_audit.py integration/schema integration/staging/pass107/native_schedule_source.php`.
