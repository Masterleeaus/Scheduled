# PlusAgency + Cleanto — Pass 59 report

Baseline: Pass 58 cumulative master.

## Functional changes
- Added calendar-accurate persisted timestamp parser to reject nonexistent dates, malformed times and years outside the supported range.
- Enforced signed 32-bit positive Cleanto IDs on persisted booking/order rows, rather than accepting overflowed PDO strings.
- Centralized exact-one-record checks for appointment, order and concurrency readbacks to reject false, missing, duplicated or empty results.
- Applied strict date and staff ID checks to concurrency fixture identity prior to claiming successful readback evidence.
- Cleanto original source not modified. Public booking writes remain disabled.

## Verification
- Full PHP regression suite: 1144/1144 tests passed (15 bootstrap files excluded from standalone execution).
- 28 focused Pass 59 tests passed.
- No live MariaDB appointment created; PHP pdo_mysql, Docker, and database server absent.
- Production ready: NO.

## Source diffs (before report generation)
- Added: 32; modified: 6; deleted: 0.
