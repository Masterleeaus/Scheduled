# PlusAgency + Cleanto — Pass 17

## Executed work

- Added strict appointment-wall-clock disambiguation for nonexistent and duplicated times during daylight-saving transitions.
- Corrected appointment duration to elapsed seconds even when a booking spans a DST clock change.
- Replaced the 30-calendar-day conflict lookback with an exactly bounded 43,200-minute duration horizon.
- Revalidated booking time windows at the staff availability boundary and before mutation orchestration.
- Added preview claims guard, lead-time validation, timezone validation and temporal release audit.
- Added environment audit for pdo_mysql without exposing test credentials.
- Added 25 executable regression tests including Melbourne spring/fall DST cases.

## Results

- Substantive PHP files changed: 41.
- Added 36 files, modified 5 files, deleted 0 files before report/manifest.
- Cumulative PHP tests: 245/245 passed.
- Changed PHP syntax: 41/41 passed.
- MySQL test runner: SKIPPED: environment lacks pdo_mysql; not counted as a passing test.
- Original Cleanto files altered: 0.

## Limitations

No live MySQL appointment was created, cancelled, rescheduled, or concurrently reserved. This pass improves temporal correctness but does not enable booking mutations or certify production readiness.
