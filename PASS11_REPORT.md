# Pass 11 — Public booking experience: source implementation

**Delivery:** Better customer booking clarity, business-timezone-aware calendar validation, reset UX, browser-preview write gating, accessibility and regression tests. The existing native booking engine and submission controls are preserved, with no release flags enabled.

**Acceptance:** Partial — frontend source and Node contract tests passed; full booking, cancellation/rescheduling, notifications, MariaDB writes and browser staging acceptance remain outstanding.

**Previous Pass 10:** Read-only database diagnostics only; missing pdo_mysql and MariaDB mean no live booking acceptance has been run.

**Metrics:** 7 of 18 planned passes remain after Pass 11; 15 website templates and 10 landing templates registered; 25 templates with partial editing source; zero with full browser acceptance. No new templates to register; 25 still need full editing acceptance.
