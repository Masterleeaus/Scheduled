# Pass 32 — Native weekly shift administration

Source master: `Master-Copy-PlusAgency-Cleanto-Pass31-NATIVE-STAFF-ASSIGNMENTS-CUMULATIVE.zip`.

## Changes

- Restrained existing weekly row edit for disabled Cleanto staff; tenant-gated, owner-authenticated, signed sidecar.
- Guarded against any staff booking history, schedule breaks and time-off rows; transactional locked writes plus verified readback.
- Updated native staff overview with schedule row IDs and schedule type to allow exact row targeting.
- Added owner-only edit form, controller and throttled route.
- Added regression checks and separate schedule-management documentation.

## Archive counts

- Modified: 6 files. Added: 2 files. Removed: 0.
- Tests: PHP syntax all changed PHP; `php integration/tests/pass32_schedule_admin.php`; Pass 31 regression.

## Limitations

- **Not live MariaDB verified.** This environment has no pdo_mysql. Schedule writes independently disabled by default. No claim of active-staff editing, break/timeoff editing or production booking readiness.
- New editor is for staff disabled in native Cleanto with zero booking history and no existing breaks/time off only.
- All 19 website templates, 10 landing templates, 50 verticals retained. 0/29 fully browser accepted.
