# PlusAgency + Cleanto Pass 85 — Public Redemption Last-Moment Time Validation

The public redemption writer now checks the appointment instant again after potentially slow authoritative rate and availability checks and before creating rows.

**Verification:** 9/9 new source-boundary checks passed; PHP lint passed. No MariaDB commit, browser checkout or payment execution was performed. Missing Pass 58 remains unresolved.

**Changed production files:** 1. New executable regression tests: 1. This is a narrow booking safety correction, not a complete workflow milestone.
