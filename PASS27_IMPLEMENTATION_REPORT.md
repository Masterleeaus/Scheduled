# Pass 27 — Real Cleanto service mapping

Implemented an owner-only selector for verified Cleanto services per website (Pawfy, Steven Watkins, yoga/Pilates, Carzone). Mapping is persisted in a new tenant-scoped table and only accepts service IDs returned by the live Cleanto catalogue. The public booking widget filters visible services according to this mapping. Cleanto remains authoritative for actual pricing, staffing, duration and availability. Existing appointment-request forms and fail-closed reservation writes remain untouched.

## Test evidence

Changed PHP/Blade files pass php -l; booking-catalogue JS passes node --check; two targeted executable smoke checks pass; both ZIP CRC checks pass. No live MariaDB, browser or provider acceptance was performed.

## Status

19 website templates and 10 landing templates remain registered. Four imported themes are eligible for the mapping UI. Fully browser-certified templates: 0/29. Existing 50 vertical profiles preserved. Pass 27 is not production certification.
