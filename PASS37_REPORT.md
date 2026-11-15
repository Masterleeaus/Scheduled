# Pass 37 — transactional lock and replay integrity

{
  "pass": 37,
  "base": "PlusAgency_Cleanto_Master_Pass36.zip",
  "substantive_changed": 29,
  "added": 24,
  "modified": 5,
  "focused_tests_passed": 15,
  "php_lint_passed": 29,
  "live_mysql_tested": false,
  "public_booking_enabled": false,
  "original_cleanto_modified": 0,
  "features": [
    "lock acquisition cleanup and retention on release failure",
    "replay persisted selection checks",
    "customer control character rejection",
    "explicit independent write evidence gate"
  ],
  "regression_scripts_passed": 633,
  "regression_scripts_failed": 0,
  "verification_note": "Legacy release test checks both gateway delegation and delegated release policy."
}

**MySQL execution:** unavailable; bookings remain disabled.
