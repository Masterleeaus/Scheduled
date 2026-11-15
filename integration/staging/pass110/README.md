# Pass 110: disposable staging input preflight

Runs **before Docker startup**. Rejects unexpected DDL/seed writes, wrong table inventory, invalid fixtures, windows outside Mon–Fri 08:00–18:00, overlapping windows, expired/far-future slots, and optional Pass 108 provenance hash mismatches. `PA101_REVIEW_MANIFEST` may point to the Pass 108 review manifest. Preflight is not SQL import evidence or booking acceptance. All input artifacts require human review before staging use.

Regenerate/approve future-dated fixture JSON when the fixed 2026-11-17/18 fixtures expire; do not bypass the temporal check. Execute `python3 integration/staging/pass110/test_inputs.py` to run the local scenarios.
