# PlusAgency + Cleanto — Pass 116 report

Base: Pass 115 cumulative recovery candidate.

## Functional change
The public checkout now collects postcode, city/suburb and state in addition to street address, and hands all fields to Cleanto's original eight-key client_personal_info encoding. Older callers that do not supply the three new fields are supported. Email length is rejected at 200 characters, matching the existing native writer, rather than accepted at the form's old 254-character limit. Invalid contact control characters are rejected instead of silently normalized. No tenant, price or payment authority was added.

## Evidence
- New structured-address PHP runtime: 24 assertions passed.
- New browser-widget source contracts: 11 assertions passed.
- Pass 88 checkout validation: 12 cases passed.
- Pass 99 phone normalization: 15 assertions passed.
- PHP syntax and JavaScript syntax: passed for changed source and new test files.
- Historical Pass 92 native handoff test: fails at its old 'Cleanto phone size' expectation. Pass 99 subsequently normalized this example into a valid 13-digit international phone number; do not claim a full historical suite pass.
- MariaDB/Docker/pdo_mysql: unavailable, so no DB transactions or browser E2E tests were executed.
- Pass 58 remains missing. Production launch remains blocked.

## Scope
4 existing production files changed; 2 tests added. This pass does not activate booking writes. Original Cleanto booking logic, Laravel platform and installed templates preserved.
