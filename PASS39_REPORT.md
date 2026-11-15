# Pass 39 — booking confirmation readback repair

Based on the Pass 38 cumulative master. Fixed first-commit confirmation to use persisted Cleanto booking `id`, associated order duration and customer-order proof rather than a mismatched `booking_id` field. Restricted locked readers to open transactions, rejected duplicate readback rows and centralized active-state validation. Original Cleanto source untouched.

**Changes:** 9 modified implementation files; 22 new tests; 4 corrected older tests; 35 substantive source/test differences versus Pass 38.

**Verification:** 673/673 PHP regression scripts passed against the cumulative source, with syntax checks and two ZIP integrity checks. MySQL driver/server unavailable; no real database booking or concurrency test executed. Customer booking writes remain disabled.
